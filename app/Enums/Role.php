<?php

namespace App\Enums;

enum Role: string
{
    case REPORTER = 'reporter';
    case EDITOR = 'editor';
    case ADMINISTRATOR = 'administrator';

    public function label(): string
    {
        return match ($this) {
            self::REPORTER => 'Reporter',
            self::EDITOR => 'Editor',
            self::ADMINISTRATOR => 'Administrator',
        };
    }
}
