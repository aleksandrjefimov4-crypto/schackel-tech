<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliatePartner extends Model
{
    protected $fillable = [
        'user_id',
        'display_name',
        'website_url',
        'promotion_description',
        'status',
        'public_code',
        'approved_at',
    ];

    protected $casts = ['approved_at' => 'datetime'];
}
