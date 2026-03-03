<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = "products";//ovim kazem o ova klasa je vezana za tabelu products u bazi
    protected $fillable = [
        'name',
        'price',
        'stock',
        'category',
        'brand',
        'on_sale',
        'description'
    ];//polja koja se mogu uneti, modifikovati itd.

    // Scope za filtriranje po kategoriji
    public function scopeByCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    // Scope za filtriranje po brendu
    public function scopeByBrand($query, $brand)
    {
        if ($brand) {
            return $query->where('brand', $brand);
        }
        return $query;
    }

    // Scope za filtriranje po ceni
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    // Scope za filtriranje dostupne stavke (stock > 0)
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
