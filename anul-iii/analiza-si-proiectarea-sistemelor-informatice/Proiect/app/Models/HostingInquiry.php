<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingInquiry extends Model
{
    protected $fillable = ['name', 'email', 'company', 'message'];
}
