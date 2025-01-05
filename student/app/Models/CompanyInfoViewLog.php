<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfoViewLog extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'user_id',
        'company_info_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function companyInfo()
    {
        return $this->belongsTo(CompanyInfo::class);
    }
}
