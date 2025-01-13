<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $connection = 'common';

    public function companyUser()
    {
        return $this->belongsTo(CompanyUser::class, 'company_user_id', 'id');
    }

    public function reviewAnswers()
    {
        return $this->hasMany(ReviewAnswer::class);
    }
}
