<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'content',
        'type',
        'duration_minutes',
        'quiz_questions',
        'is_active',
        'order',
        'short_description',
        'category',
        'subcategory',
        'level',
        'duration_unit',
        'price',
        'discount_price',
        'currency',
        'instructor_name',
        'instructor_bio',
        'cover_image',
        'video_url',
        'start_date',
        'end_date',
        'registration_deadline',
        'max_participants',
        'current_participants',
        'language',
        'format',
        'prerequisites',
        'learning_objectives',
        'certification_included',
        'certification_name',
        'is_featured',
        'is_popular',
        'rating',
        'reviews_count',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'quiz_questions' => 'array',
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'order' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'certification_included' => 'boolean'
    ];

    // Relations
    public function users()
    {
        return $this->belongsToMany(User::class, 'training_user', 'training_id', 'user_id')
            ->withPivot('enrolled_at', 'completed_at', 'status', 'progress', 'certificate_id')
            ->withTimestamps();
    }

    public function modules()
    {
        return $this->hasMany(TrainingModule::class)->orderBy('order');
    }

    public function resources()
    {
        return $this->hasMany(TrainingResource::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    // Accessors
    public function getDurationAttribute()
    {
        if ($this->duration_minutes >= 60) {
            $hours = floor($this->duration_minutes / 60);
            $minutes = $this->duration_minutes % 60;
            
            if ($minutes > 0) {
                return $hours . 'h' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
            }
            return $hours . 'h';
        }
        
        return $this->duration_minutes . 'min';
    }

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/trainings/' . $this->cover_image);
        }
        return asset('images/default-training.jpg');
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->price == 0) {
            return 'Gratuit';
        }

        if ($this->discount_price) {
            return number_format($this->discount_price) . ' ' . $this->currency;
        }

        return number_format($this->price) . ' ' . $this->currency;
    }

    public function getOriginalPriceAttribute()
    {
        if ($this->price > 0) {
            return number_format($this->price) . ' ' . $this->currency;
        }
        return null;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_price && $this->price > 0) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getDurationFormattedAttribute()
    {
        if ($this->duration_minutes) {
            if ($this->duration_minutes >= 60) {
                $hours = floor($this->duration_minutes / 60);
                $minutes = $this->duration_minutes % 60;
                
                if ($minutes > 0) {
                    return $hours . 'h' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                }
                return $hours . 'h';
            }
            return $this->duration_minutes . 'min';
        }
        return 'Variable';
    }

    public function getStatusBadgeAttribute()
    {
        if (!$this->is_active) {
            return '<span class="badge bg-secondary">Inactif</span>';
        }

        $now = now();
        if ($this->start_date && $this->end_date) {
            if ($now < $this->start_date) {
                return '<span class="badge bg-info">À venir</span>';
            } elseif ($now > $this->end_date) {
                return '<span class="badge bg-warning">Terminé</span>';
            } else {
                return '<span class="badge bg-success">En cours</span>';
            }
        }

        return '<span class="badge bg-primary">Actif</span>';
    }

    public function getStatusTextAttribute()
    {
        if (!$this->is_active) {
            return 'Inactif';
        }

        $now = now();
        if ($this->start_date && $this->end_date) {
            if ($now < $this->start_date) {
                return 'À venir';
            } elseif ($now > $this->end_date) {
                return 'Terminé';
            } else {
                return 'En cours';
            }
        }

        return 'Actif';
    }

    public function getIsUpcomingAttribute()
    {
        return $this->start_date && now() < $this->start_date;
    }

    public function getIsOngoingAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return $this->is_active;
        }
        return now() >= $this->start_date && now() <= $this->end_date;
    }

    public function getIsCompletedAttribute()
    {
        return $this->end_date && now() > $this->end_date;
    }

    // Scopes
    public function scopeMandatory($query)
    {
        return $query->where('type', 'mandatory');
    }

    public function scopeOptional($query)
    {
        return $query->where('type', 'optional');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('price', '>', 0);
    }

    public function scopeWithAvailability($query)
    {
        return $query->where(function($q) {
            $q->whereNull('max_participants')
              ->orWhereColumn('current_participants', '<', 'max_participants');
        });
    }

    // Méthodes
    public function isMandatory()
    {
        return $this->type === 'mandatory';
    }

    public function isOptional()
    {
        return $this->type === 'optional';
    }

    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        // Vérifier les dates d'inscription
        if ($this->registration_deadline && now() > $this->registration_deadline) {
            return false;
        }

        // Vérifier la capacité maximale
        if ($this->max_participants && $this->current_participants >= $this->max_participants) {
            return false;
        }

        return true;
    }

    public function getRemainingSeats()
    {
        if (!$this->max_participants) {
            return null;
        }
        return max(0, $this->max_participants - $this->current_participants);
    }

    public function incrementParticipants()
    {
        if ($this->max_participants && $this->current_participants < $this->max_participants) {
            $this->increment('current_participants');
            return true;
        }
        return false;
    }

    public function decrementParticipants()
    {
        if ($this->current_participants > 0) {
            $this->decrement('current_participants');
            return true;
        }
        return false;
    }

    public function getProgressPercentage($userId)
    {
        $user = $this->users()->where('user_id', $userId)->first();
        return $user ? $user->pivot->progress : 0;
    }

    public function getEnrollmentStatus($userId)
    {
        $user = $this->users()->where('user_id', $userId)->first();
        return $user ? $user->pivot->status : null;
    }

    public function hasQuiz()
    {
        return !empty($this->quiz_questions) && count($this->quiz_questions) > 0;
    }

    public function getQuizQuestionsCount()
    {
        return $this->quiz_questions ? count($this->quiz_questions) : 0;
    }

    public function isUserEnrolled($userId)
    {
        return $this->users()
            ->where('user_id', $userId)
            ->wherePivotIn('status', ['enrolled', 'completed'])
            ->exists();
    }

    public function isUserCompleted($userId)
    {
        return $this->users()
            ->where('user_id', $userId)
            ->wherePivot('status', 'completed')
            ->exists();
    }

    // Calculer la moyenne des notes
    public function calculateAverageRating()
    {
        // Cette méthode pourrait être implémentée si vous avez une table de ratings
        return $this->rating;
    }

    // Récupérer les avis
    public function getReviews()
    {
        // À implémenter si vous avez une table de reviews
        return collect();
    }

    // Vérifier si la formation est populaire
    public function isPopular()
    {
        return $this->current_participants > 50 || $this->is_popular;
    }

    // Vérifier si la formation est gratuite
    public function isFree()
    {
        return $this->price == 0;
    }

    // Obtenir le prix avec réduction
    public function getFinalPrice()
    {
        return $this->discount_price ? $this->discount_price : $this->price;
    }

    // Formater la date de début
    public function getFormattedStartDate()
    {
        return $this->start_date ? $this->start_date->format('d/m/Y') : null;
    }

    // Formater la date de fin
    public function getFormattedEndDate()
    {
        return $this->end_date ? $this->end_date->format('d/m/Y') : null;
    }

    // Vérifier si les inscriptions sont ouvertes
    public function isRegistrationOpen()
    {
        if (!$this->registration_deadline) {
            return true;
        }
        return now() <= $this->registration_deadline;
    }
}