<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageApiCredential extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'company';

    protected $fillable = [
        'user_id',
        'client_id',
        'client_secret',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
