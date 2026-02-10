<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Exportable;

class Product extends Model
{
    use Exportable;

    protected $fillable = [
        'name',
        'description',
        'barcode',
        'current_stock',
        'sale_price',
        'status',
        'business_id',
        'category_id'
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'current_stock' => 'integer',
        'status' => 'boolean',
        'business_id' => 'integer',
        'category_id' => 'integer'
    ];

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(Category::class);
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
            'barcode',
            'category_name',
            'current_stock',
            'sale_price',
            'status',
            'business_id',
            'created_at',
            'updated_at'
        ];
    }
}
