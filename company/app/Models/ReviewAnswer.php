<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'common';

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
