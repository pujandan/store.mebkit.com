<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'tags',
        'category_id'
    ];


    // scope fillter search
    public function scopeFilter($query, array $filters)
    {
        $filter = $filters['filter'];
        $query
            ->when($filter['price_from'] ?? false, function ($query, $price_from) {
                return $query->where('price', '>=', $price_from);
            })
            ->when($filter['price_to'] ?? false, function ($query, $price_to) {
                return $query->where('price', '<=', $price_to);
            })
            ->when($filter['search'] ?? false, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    return $query->where('name', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%")
                        ->orWhere('tags', 'like', "%$search%");
                });
            });
    }


    // relationship to many galleries
    public function galleries()
    {
        return $this->hasMany(ProductGallery::class, 'product_id', 'id');
    }

    // relationship to belongs category
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
}
