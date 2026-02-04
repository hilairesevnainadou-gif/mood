<?php

namespace App\Http\Controllers;

use App\Models\FundingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FundingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $fundings = FundingRequest::where('user_id', $user->id)
            ->with(['committeeDecision'])
            ->latest()
            ->paginate(10);
            
        return view('client.funding.index', compact('fundings'));
    }
    
    public function create()
    {
        return view('client.funding.create');
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1000',
            'description' => 'required|string',
            'type' => 'required|in:personal,business,education,health',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $funding = FundingRequest::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'amount_requested' => $request->amount,
            'description' => $request->description,
            'type' => $request->type,
            'status' => 'pending',
            'request_number' => 'FUND-' . time() . '-' . rand(1000, 9999),
        ]);
        
        return redirect()->route('client.funding.show', $funding->id)
            ->with('success', 'Demande de financement créée avec succès');
    }
    
    public function show($id)
    {
        $funding = FundingRequest::where('user_id', Auth::id())
            ->with(['committeeDecision', 'payments'])
            ->findOrFail($id);
            
        return view('client.funding.show', compact('funding'));
    }
    
    public function edit($id)
    {
        $funding = FundingRequest::where('user_id', Auth::id())
            ->findOrFail($id);
            
        return view('client.funding.edit', compact('funding'));
    }
    
    public function update(Request $request, $id)
    {
        $funding = FundingRequest::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $funding->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);
        
        return redirect()->route('client.funding.show', $funding->id)
            ->with('success', 'Financement mis à jour avec succès');
    }
    
    public function destroy($id)
    {
        $funding = FundingRequest::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $funding->delete();
        
        return redirect()->route('client.funding.index')
            ->with('success', 'Financement supprimé avec succès');
    }
}