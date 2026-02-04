<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BHDMStatistics extends Model
{
    use HasFactory;

    protected $table = 'bhdm_statistics';

    protected $fillable = [
        'total_projects_funded',
        'total_amount_funded',
        'total_amount_recycled',
        'total_jobs_created',
        'active_users',
        'statistics_by_country',
        'statistics_by_sector',
        'initial_fund',
        'current_fund',
        'repayment_rate',
        'statistics_date',
        'is_published'
    ];

    protected $casts = [
        'total_projects_funded' => 'integer',
        'total_amount_funded' => 'decimal:2',
        'total_amount_recycled' => 'decimal:2',
        'total_jobs_created' => 'integer',
        'active_users' => 'integer',
        'statistics_by_country' => 'array',
        'statistics_by_sector' => 'array',
        'initial_fund' => 'decimal:2',
        'current_fund' => 'decimal:2',
        'repayment_rate' => 'decimal:2',
        'statistics_date' => 'date',
        'is_published' => 'boolean'
    ];

    // Accessors
    public function getFormattedTotalAmountFundedAttribute()
    {
        return number_format($this->total_amount_funded, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedTotalAmountRecycledAttribute()
    {
        return number_format($this->total_amount_recycled, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedCurrentFundAttribute()
    {
        return number_format($this->current_fund, 0, ',', ' ') . ' FCFA';
    }

    public function getRepaymentRatePercentageAttribute()
    {
        return round($this->repayment_rate * 100, 2) . '%';
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('statistics_date', 'desc');
    }

    // Méthodes
    public function calculateRecyclingRatio()
    {
        if ($this->total_amount_funded > 0) {
            return round(($this->total_amount_recycled / $this->total_amount_funded) * 100, 2);
        }
        return 0;
    }

    public function getTopCountries($limit = 5)
    {
        $countries = $this->statistics_by_country ?? [];
        arsort($countries);
        return array_slice($countries, 0, $limit, true);
    }

    public function getTopSectors($limit = 5)
    {
        $sectors = $this->statistics_by_sector ?? [];
        arsort($sectors);
        return array_slice($sectors, 0, $limit, true);
    }
}