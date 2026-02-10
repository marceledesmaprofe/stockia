<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Exportable;

class Category extends Model
{
    use Exportable;

    protected $fillable = [
        'name',
        'description',
        'status',
        'business_id'
    ];

    protected $casts = [
        'status' => 'boolean',
        'business_id' => 'integer'
    ];

    // Relationship with products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get headers for CSV export
     *
     * @return array
     */
    public function getCsvHeaders(): array
    {
        return [
            'id',
            'name',
            'description',
            'status',
            'business_id',
            'product_count',
            'created_at',
            'updated_at'
        ];
    }
}
