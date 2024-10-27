<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'common';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'review_id',
        'review_item_id',
        'score',
        'answer'
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function reviewItem()
    {
        return $this->belongsTo(ReviewItem::class);
    }
}
