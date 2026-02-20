<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Exportable;

class Customer extends Model
{
    use Exportable;

    protected $fillable = [
        'document_type',
        'document_number',
        'full_name',
        'phone',
        'address',
        'email',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'user_id' => 'integer',
    ];

    /**
     * Get the user that owns the customer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all sales for this customer.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Scope to filter customers by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to filter inactive customers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Scope to search customers by name or document.
     */
    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('document_number', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Get headers for CSV export
     */
    public function getCsvHeaders(): array
    {
        return [
            'id',
            'document_type',
            'document_number',
            'full_name',
            'phone',
            'address',
            'email',
            'status',
            'user_id',
            'created_at',
            'updated_at'
        ];
    }
}
