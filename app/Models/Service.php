<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = ['client_id', 'product_id', 'order_id', 'service_number', 'hostname', 'status', 'starts_at', 'renews_at', 'expires_at'];

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'renews_at' => 'date'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
