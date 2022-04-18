<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = ['answered'];

    public $casts = [
        'answered' => 'boolean',
    ];

    public $attributes = [
        'answered' => false,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
