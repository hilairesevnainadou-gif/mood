<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'funding_request_id',
        'evaluation_number',
        'evaluation_type',
        'title',
        'purpose',
        'evaluator_id',
        'reviewer_id',
        'evaluation_date',
        'planned_date',
        'completed_date',
        'status',
        'criteria_scores',
        'overall_score',
        'technical_score',
        'financial_score',
        'social_score',
        'environmental_score',
        'recommendation',
        'recommendation_details',
        'strengths',
        'weaknesses',
        'opportunities',
        'threats',
        'financial_analysis',
        'market_analysis',
        'technical_analysis',
        'risk_analysis',
        'conditions',
        'requirements',
        'suggestions',
        'report_file_path',
        'attachments_path',
        'is_validated',
        'validated_by',
        'validated_at',
        'validation_comments',
        'metadata'
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'planned_date' => 'date',
        'completed_date' => 'date',
        'overall_score' => 'decimal:2',
        'technical_score' => 'decimal:2',
        'financial_score' => 'decimal:2',
        'social_score' => 'decimal:2',
        'environmental_score' => 'decimal:2',
        'criteria_scores' => 'array',
        'is_validated' => 'boolean',
        'validated_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Relations
    public function funding()
    {
        return $this->belongsTo(Funding::class, 'funding_request_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Accessors
    public function getEvaluationTypeLabelAttribute()
    {
        $labels = [
            'initial' => 'Évaluation initiale',
            'technical' => 'Évaluation technique',
            'financial' => 'Évaluation financière',
            'environmental' => 'Évaluation environnementale',
            'social' => 'Évaluation sociale',
            'risk' => 'Évaluation des risques',
            'final' => 'Évaluation finale',
            'mid_term' => 'Évaluation à mi-parcours',
            'end_term' => 'Évaluation de fin de parcours',
            'special' => 'Évaluation spéciale'
        ];

        return $labels[$this->evaluation_type] ?? $this->evaluation_type;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'planned' => 'Planifiée',
            'in_progress' => 'En cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            'pending_review' => 'En attente de revue',
            'approved' => 'Approuvée',
            'rejected' => 'Rejetée'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getRecommendationLabelAttribute()
    {
        $labels = [
            'approve' => 'Approuver',
            'approve_with_conditions' => 'Approuver avec conditions',
            'reject' => 'Rejeter',
            'defer' => 'Reporter',
            'require_more_info' => 'Demander plus d\'informations',
            'modify' => 'Modifier'
        ];

        return $labels[$this->recommendation] ?? $this->recommendation;
    }

    public function getScoreGradeAttribute()
    {
        if (!$this->overall_score) {
            return 'Non évalué';
        }

        if ($this->overall_score >= 90) return 'Excellent';
        if ($this->overall_score >= 80) return 'Très bon';
        if ($this->overall_score >= 70) return 'Bon';
        if ($this->overall_score >= 60) return 'Satisfaisant';
        if ($this->overall_score >= 50) return 'Passable';
        return 'Insuffisant';
    }

    public function getIsCompletedAttribute()
    {
        return in_array($this->status, ['completed', 'approved', 'rejected']);
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsValidatedAttribute()
    {
        return $this->is_validated;
    }

    public function getHasRecommendationAttribute()
    {
        return !empty($this->recommendation);
    }

    public function getCompletionRateAttribute()
    {
        $totalFields = 0;
        $completedFields = 0;
        
        // Vérifier les champs principaux
        $fields = [
            'strengths',
            'weaknesses',
            'opportunities',
            'threats',
            'financial_analysis',
            'market_analysis',
            'technical_analysis',
            'risk_analysis'
        ];
        
        foreach ($fields as $field) {
            $totalFields++;
            if (!empty($this->$field)) {
                $completedFields++;
            }
        }
        
        if ($this->overall_score !== null) $completedFields++;
        if ($this->recommendation !== null) $completedFields++;
        
        $totalFields += 2; // Pour overall_score et recommendation
        
        return $totalFields > 0 ? round(($completedFields / $totalFields) * 100) : 0;
    }

    // Méthodes d'action
    public function startEvaluation()
    {
        $this->update([
            'status' => 'in_progress',
            'evaluation_date' => now()
        ]);
    }

    public function completeEvaluation($data = [])
    {
        $this->update(array_merge([
            'status' => 'completed',
            'completed_date' => now()
        ], $data));
    }

    public function submitForReview()
    {
        $this->update([
            'status' => 'pending_review'
        ]);
    }

    public function approveEvaluation($comments = null, $userId = null)
    {
        $this->update([
            'status' => 'approved',
            'is_validated' => true,
            'validated_by' => $userId ?? auth()->id(),
            'validated_at' => now(),
            'validation_comments' => $comments
        ]);
    }

    public function rejectEvaluation($comments, $userId = null)
    {
        $this->update([
            'status' => 'rejected',
            'is_validated' => false,
            'validated_by' => $userId ?? auth()->id(),
            'validated_at' => now(),
            'validation_comments' => $comments
        ]);
    }

    public function calculateOverallScore()
    {
        $scores = [];
        
        if ($this->technical_score !== null) {
            $scores[] = $this->technical_score * 0.3; // 30% de poids
        }
        
        if ($this->financial_score !== null) {
            $scores[] = $this->financial_score * 0.4; // 40% de poids
        }
        
        if ($this->social_score !== null) {
            $scores[] = $this->social_score * 0.15; // 15% de poids
        }
        
        if ($this->environmental_score !== null) {
            $scores[] = $this->environmental_score * 0.15; // 15% de poids
        }
        
        if (empty($scores)) {
            return null;
        }
        
        $overallScore = array_sum($scores);
        $this->update(['overall_score' => round($overallScore, 2)]);
        
        return $overallScore;
    }

    // Génération du numéro d'évaluation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($evaluation) {
            if (!$evaluation->evaluation_number) {
                $evaluation->evaluation_number = static::generateEvaluationNumber();
            }
        });
    }

    public static function generateEvaluationNumber()
    {
        $prefix = 'BHDM-EVAL-';
        $datePart = date('Ymd');
        $lastEvaluation = static::where('evaluation_number', 'like', $prefix . $datePart . '-%')
            ->orderBy('evaluation_number', 'desc')
            ->first();

        if ($lastEvaluation) {
            $lastNumber = intval(substr($lastEvaluation->evaluation_number, -4));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $datePart . '-' . $nextNumber;
    }
}