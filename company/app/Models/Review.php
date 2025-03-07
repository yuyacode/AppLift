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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_user_id',
        'title',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function reviewAnswer()
    {
        return $this->hasMany(ReviewAnswer::class);
    }

}
