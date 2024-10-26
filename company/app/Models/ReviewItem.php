<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'common';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_default',
    ];

    public function answers()
    {
        return $this->hasMany(ReviewAnswer::class);
    }
}
