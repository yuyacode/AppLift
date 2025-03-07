<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $connection = 'company';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_info_id',
        'name',
        'email',
        'password',
        'department',
        'occupation',
        'position',
        'join_date',
        'introduction',
        'is_master',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function companyInfo()
    {
        return $this->belongsTo(CompanyInfo::class);
    }

    public function messageApiCredential()
    {
        return $this->hasOne(MessageApiCredential::class);
    }

    public function messageThreads()
    {
        return $this->hasMany(MessageThread::class, 'company_user_id', 'id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'company_user_id', 'id');
    }
}
