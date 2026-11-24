<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'service_type', 'contact_email', 'monthly_cost', 'status'];
    protected function casts(): array { return ['monthly_cost' => 'decimal:2']; }
}
