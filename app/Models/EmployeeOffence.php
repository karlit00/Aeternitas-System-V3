<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Ramsey\Uuid\Uuid;

class EmployeeOffence extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'employee_offences';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'employee_id',
        'offence_type',
        'description',
        'offence_date',
        'severity',
        'status',
        'reported_by',
        'action_taken',
        'action_date',
        'notes',
    ];

    protected $casts = [
        'offence_date' => 'date',
        'action_date' => 'date',
    ];

    public function newUniqueId()
    {
        return (string) Uuid::uuid4();
    }

    public function uniqueIds()
    {
        return ['id'];
    }

    /**
     * Get the employee that owns the offence.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope for filtering by company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
    }

    /**
     * Scope for pending offences
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for verified offences
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope for filtering by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('offence_date', [$startDate, $endDate]);
    }

    /**
     * Get severity badge color
     */
    public function getSeverityBadgeClassAttribute()
    {
        return match($this->severity) {
            'minor' => 'bg-yellow-100 text-yellow-800',
            'major' => 'bg-orange-100 text-orange-800',
            'serious' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'verified' => 'bg-green-100 text-green-800',
            'dismissed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
