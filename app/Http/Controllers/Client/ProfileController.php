<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Affiche le profil avec débogage
     */
    public function profile()
    {
        $user = Auth::user();
        
        // DÉBOGAGE : Vérifier l'état de la photo
        if ($user->profile_photo) {
            $photoPath = 'profiles/' . $user->profile_photo;
            $exists = Storage::disk('public')->exists($photoPath);
            $fullPath = storage_path('app/public/' . $photoPath);
            $url = $user->profile_photo_url;
            
            Log::info('Debug Photo Profil', [
                'user_id' => $user->id,
                'profile_photo_db' => $user->profile_photo,
                'photo_path' => $photoPath,
                'exists_storage' => $exists,
                'full_path' => $fullPath,
                'file_exists_php' => file_exists($fullPath),
                'generated_url' => $url,
            ]);
        }
        
        return view('client.profile', compact('user'));
    }

    /**
     * Met à jour le profil complet (infos + photo + mot de passe)
     * VERSION CORRIGÉE ET OPTIMISÉE
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // DEBUG : Log des données reçues
        Log::info('Update Profile Request', [
            'has_file' => $request->hasFile('photo'),
            'file_valid' => $request->file('photo') ? $request->file('photo')->isValid() : false,
            'current_photo_db' => $user->profile_photo,
        ]);

        // Validation
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if ($request->filled('new_password')) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Préparation des données
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
        ];

        // GESTION DE LA PHOTO - VERSION CORRIGÉE ET SIMPLIFIÉE
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            try {
                // 1. Supprimer l'ancienne photo si existe
                if ($user->profile_photo) {
                    $oldPath = 'profiles/' . $user->profile_photo;
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                        Log::info('Ancienne photo supprimée', ['path' => $oldPath]);
                    }
                }

                // 2. Générer un nom unique avec extension sécurisée
                $file = $request->file('photo');
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = 'profile_' . $user->id . '_' . time() . '.' . $extension;

                // 3. S'assurer que le dossier existe
                if (!Storage::disk('public')->exists('profiles')) {
                    Storage::disk('public')->makeDirectory('profiles');
                    Log::info('Dossier profiles créé');
                }

                // 4. Stocker la photo et récupérer le chemin
                $storedPath = $file->storeAs('profiles', $filename, 'public');
                
                if ($storedPath) {
                    $userData['profile_photo'] = $filename;
                    Log::info('Photo sauvegardée avec succès', [
                        'stored_path' => $storedPath,
                        'filename' => $filename,
                        'full_url' => asset('storage/' . $storedPath),
                    ]);
                } else {
                    throw new \Exception('Le stockage a retourné false');
                }
                
            } catch (\Exception $e) {
                Log::error('Erreur traitement photo', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return redirect()->back()
                    ->with('error', 'Erreur lors de la sauvegarde de la photo: ' . $e->getMessage())
                    ->withInput();
            }
        }

        // Mise à jour mot de passe si fourni
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                    ->withInput();
            }

            $userData['password'] = Hash::make($request->new_password);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'security',
                'title' => 'Mot de passe modifié',
                'message' => 'Votre mot de passe a été changé avec succès.',
            ]);
        }

        // Sauvegarde en base
        $user->update($userData);
        
        // Recharger l'utilisateur pour avoir les données à jour
        $user->refresh();

        Log::info('Profil mis à jour', [
            'user_id' => $user->id,
            'new_photo' => $user->profile_photo,
            'photo_url' => $user->profile_photo_url,
        ]);

        return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Change uniquement le mot de passe
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'security',
            'title' => 'Mot de passe changé',
            'message' => 'Votre mot de passe a été changé avec succès.',
        ]);

        return redirect()->back()->with('success', 'Mot de passe changé avec succès !');
    }

    /**
     * Supprime la photo de profil - CORRIGÉ
     */
    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            $path = 'profiles/' . $user->profile_photo;
            
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('Photo supprimée', ['path' => $path]);
            }
            
            $user->update(['profile_photo' => null]);
        }

        return redirect()->back()->with('success', 'Photo de profil supprimée.');
    }
}