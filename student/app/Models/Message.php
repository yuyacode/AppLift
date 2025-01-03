<?php

namespace App\Models;

use App\Enums\MessageIsSent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'common';

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
