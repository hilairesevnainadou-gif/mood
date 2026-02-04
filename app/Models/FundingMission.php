<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundingMission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'funding_request_id',
        'mission_type',
        'mission_number',
        'title',
        'description',
        'objectives',
        'assigned_to',
        'supervised_by',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'status',
        'progress_percentage',
        'findings',
        'recommendations',
        'challenges',
        'solutions_proposed',
        'evaluation_score',
        'evaluation_comments',
        'mission_budget',
        'actual_expenses',
        'report_file_path',
        'photos_path',
        'location',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'mission_budget' => 'decimal:2',
        'actual_expenses' => 'decimal:2',
        'evaluation_score' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    // Relations
    public function funding()
    {
        return $this->belongsTo(Funding::class, 'funding_request_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervised_by');
    }

    // Accessors
    public function getMissionTypeLabelAttribute()
    {
        $labels = [
            'mission_1' => 'Mission 1',
            'mission_2' => 'Mission 2',
            'follow_up' => 'Suivi',
            'audit' => 'Audit',
            'other' => 'Autre'
        ];

        return $labels[$this->mission_type] ?? $this->mission_type;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'planned' => 'Planifiée',
            'in_progress' => 'En cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            'delayed' => 'Retardée'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getDurationDaysAttribute()
    {
        if ($this->actual_start_date && $this->actual_end_date) {
            return $this->actual_start_date->diffInDays($this->actual_end_date);
        } elseif ($this->planned_start_date && $this->planned_end_date) {
            return $this->planned_start_date->diffInDays($this->planned_end_date);
        }
        return 0;
    }

    public function getIsDelayedAttribute()
    {
        if ($this->status === 'delayed') {
            return true;
        }
        
        if ($this->planned_end_date && now()->gt($this->planned_end_date) && $this->status !== 'completed') {
            return true;
        }
        
        return false;
    }

    public function getExpensesDifferenceAttribute()
    {
        if ($this->mission_budget && $this->actual_expenses) {
            return $this->actual_expenses - $this->mission_budget;
        }
        return null;
    }

    // Méthodes d'action
    public function startMission()
    {
        $this->update([
            'status' => 'in_progress',
            'actual_start_date' => now()
        ]);
    }

    public function completeMission($data = [])
    {
        $this->update(array_merge([
            'status' => 'completed',
            'actual_end_date' => now(),
            'progress_percentage' => 100
        ], $data));
    }

    public function updateProgress($percentage)
    {
        $this->update([
            'progress_percentage' => min(100, max(0, $percentage)),
            'status' => $percentage >= 100 ? 'completed' : 'in_progress'
        ]);
    }

    // Génération du numéro de mission
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($mission) {
            if (!$mission->mission_number) {
                $mission->mission_number = static::generateMissionNumber();
            }
        });
    }

    public static function generateMissionNumber()
    {
        $prefix = 'BHDM-MIS-';
        $datePart = date('Ymd');
        $lastMission = static::where('mission_number', 'like', $prefix . $datePart . '-%')
            ->orderBy('mission_number', 'desc')
            ->first();

        if ($lastMission) {
            $lastNumber = intval(substr($lastMission->mission_number, -4));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $datePart . '-' . $nextNumber;
    }
}