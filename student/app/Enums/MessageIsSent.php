<?php

namespace App\Enums;

enum MessageIsSent: int
{
    case UNSENT = 0;
    case SENT   = 1;

    public function label(): ?string
    {
        return match ($this) {
            self::UNSENT => '送信予定',
            self::SENT   => '送信済み',
            default      =>  null,
        };
    }
}
