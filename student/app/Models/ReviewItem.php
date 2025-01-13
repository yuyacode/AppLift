<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewItem extends Model
{
    use HasFactory;

    protected $connection = 'common';

    public function reviewAnswers()
    {
        return $this->hasMany(ReviewAnswer::class);
    }
}
