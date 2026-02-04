<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'training_id',
        'mission_1_completed',
        'mission_1_completed_at',
        'required_documents',
        'mission_2_completed',
        'mission_2_completed_at',
        'current_phase'
    ];

    protected $casts = [
        'mission_1_completed' => 'boolean',
        'mission_1_completed_at' => 'datetime',
        'required_documents' => 'array',
        'mission_2_completed' => 'boolean',
        'mission_2_completed_at' => 'datetime'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    // Accessors
    public function getPhaseLabelAttribute()
    {
        $labels = [
            'registration' => 'Inscription',
            'mission_1' => 'Formation 5 clés',
            'mission_2' => 'Documents et projet',
            'submission' => 'Soumission',
            'committee_review' => 'Examen comité',
            'approved' => 'Approuvé',
            'funded' => 'Financé',
            'in_progress' => 'Projet en cours'
        ];

        return $labels[$this->current_phase] ?? $this->current_phase;
    }

    public function getProgressPercentageAttribute()
    {
        $phases = [
            'registration' => 0,
            'mission_1' => 20,
            'mission_2' => 40,
            'submission' => 60,
            'committee_review' => 80,
            'approved' => 90,
            'funded' => 95,
            'in_progress' => 100
        ];

        return $phases[$this->current_phase] ?? 0;
    }

    // Méthodes
    public function completeMission1($trainingId)
    {
        $this->update([
            'training_id' => $trainingId,
            'mission_1_completed' => true,
            'mission_1_completed_at' => now(),
            'current_phase' => 'mission_1'
        ]);
    }

    public function completeMission2()
    {
        $this->update([
            'mission_2_completed' => true,
            'mission_2_completed_at' => now(),
            'current_phase' => 'submission'
        ]);
    }

    public function advanceToPhase($phase)
    {
        $this->update(['current_phase' => $phase]);
    }

    public function updateRequiredDocuments($documentType, $documentId)
    {
        $requiredDocs = $this->required_documents ?? [];
        $requiredDocs[$documentType] = [
            'uploaded' => true,
            'document_id' => $documentId,
            'uploaded_at' => now()->toDateTimeString()
        ];
        
        $this->update(['required_documents' => $requiredDocs]);
    }

    public function hasRequiredDocuments()
    {
        $requiredDocs = $this->required_documents ?? [];
        $mandatoryDocs = ['identity', 'proof_address'];
        
        foreach ($mandatoryDocs as $docType) {
            if (!isset($requiredDocs[$docType]) || !$requiredDocs[$docType]['uploaded']) {
                return false;
            }
        }
        
        return true;
    }
}