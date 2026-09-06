<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Products extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products'; // ovim kazem o ova klasa je vezana za tabelu products u bazi

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'price',
        'stock',
        'category',
        'brand',
        'on_sale',
        'description',
        'image_path',
    ]; // polja koja se mogu uneti, modifikovati itd.

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    protected static function booted(): void
    {
        static::saving(function (Products $product): void {
            if ($product->isDirty('name') || blank($product->slug)) {
                $product->slug = static::uniqueSlug($product->name, $product->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: Str::random(8);
        $slug = $baseSlug;
        $counter = 2;

        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

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
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    // Scope za filtriranje dostupne stavke (stock > 0)
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->where('stock', '<=', $threshold);
    }
}
