<?php

namespace App\Models;

use App\Enums\MessageIsSent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory, SoftDeletes;

    protected $connection = 'common';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // 後で定義する
    ];

    protected function casts(): array
    {
        return [
            'is_sent' => MessageIsSent::class,
        ];
    }

    public function messageThread()
    {
        return $this->belongsTo(MessageThread::class);
    }
}
