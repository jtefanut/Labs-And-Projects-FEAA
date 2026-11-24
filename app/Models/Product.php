<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'slug', 'category', 'summary', 'cpu_cores', 'ram_mb', 'storage_gb', 'price', 'billing_period', 'features', 'is_active', 'is_featured', 'sort_order'];

    protected function casts(): array
    {
        return ['features' => 'array', 'is_active' => 'boolean', 'is_featured' => 'boolean', 'price' => 'decimal:2'];
    }

    public function localizedName(): string
    {
        $key = 'messages.products.'.$this->slug.'.name';
        $value = __($key);

        if ($value !== $key) {
            return $value;
        }

        $catalogKey = 'catalog_products.'.$this->slug.'.name';
        $catalogValue = __($catalogKey);

        return $catalogValue === $catalogKey ? $this->name : $catalogValue;
    }

    public function localizedSummary(): string
    {
        $key = 'messages.products.'.$this->slug.'.summary';
        $value = __($key);

        if ($value !== $key) {
            return $value;
        }

        $catalogKey = 'catalog_products.'.$this->slug.'.summary';
        $catalogValue = __($catalogKey);

        return $catalogValue === $catalogKey ? $this->summary : $catalogValue;
    }

    public function localizedFeatures(): array
    {
        $key = 'messages.products.'.$this->slug.'.features';
        $value = __($key);

        return is_array($value) ? $value : ($this->features ?? []);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
