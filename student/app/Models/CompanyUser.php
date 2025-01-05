<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    protected $connection = 'company';

    protected $table = 'users';

    public function companyInfo()
    {
        return $this->belongsTo(CompanyInfo::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'company_user_id', 'id');
    }
}
