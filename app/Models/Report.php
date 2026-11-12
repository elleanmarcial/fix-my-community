<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barangay_id',
        'title',
        'description',
        'category',
        'location',
        'latitude',
        'longitude',
        'photo_path',
        'is_anonymous',
        'status',
        'assigned_to',
        'admin_notes',
        'reported_at',
        'resolved_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ReportNote::class)
            ->latest();
    }

    public const OVERDUE_AFTER_DAYS = 7;

    public function isOverdue(): bool
    {
        if (in_array($this->status, ['Resolved', 'Rejected'], true)) {
            return false;
        }

        $since = $this->reported_at ?? $this->created_at;

        return $since !== null
            && $since->diffInDays(now()) > self::OVERDUE_AFTER_DAYS;
    }
}