<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['user_id', 'company', 'phone', 'address', 'city', 'country', 'tax_id'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function services(): HasMany { return $this->hasMany(Service::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function tickets(): HasMany { return $this->hasMany(SupportTicket::class); }
}
