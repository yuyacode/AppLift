<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewAnswer extends Model
{
    use HasFactory;

    protected $connection = 'common';

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function reviewItem()
    {
        return $this->belongsTo(ReviewItem::class);
    }
}
