<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $attributes = [
        'activated' => false,
        'used' => false,
    ];

    protected $guarded = ['token'];

    protected $casts = [
        'activated' => 'boolean',
        'used' => 'boolean',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
