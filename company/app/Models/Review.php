<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory, SoftDeletes;

    protected $connection = 'common';

    public function user()
    {
        // memo：setConnectionがなくてもいけるか確認する
        // return $this->belongsTo(User::class, 'company_user_id', 'id')->setConnection('company');
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(ReviewAnswer::class);
    }

}
