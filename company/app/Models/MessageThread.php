<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageThread extends Model
{
    /** @use HasFactory<\Database\Factories\MessageThreadFactory> */
    use HasFactory, SoftDeletes;

    protected $connection = 'common';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // 後で定義する
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
