<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyInfo extends Model
{
    use SoftDeletes;

    protected $connection = 'common';

    public function companyInfoViewLogs()
    {
        return $this->hasMany(CompanyInfoViewLog::class);
    }
}
