<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageApiCredential extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'student';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
