<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyInfo extends Model
{
    use SoftDeletes;

    protected $connection = 'common';

    public function scopeSearchByName($query, $keyword)
    {
        return $query->where('name', 'like', '%' .$keyword .'%');
    }

    public function companyUsers()
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function companyInfoViewLogs()
    {
        return $this->hasMany(CompanyInfoViewLog::class);
    }
}
