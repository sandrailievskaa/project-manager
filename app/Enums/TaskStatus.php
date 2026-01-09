<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TaskStatus: string implements HasColor, HasIcon, HasLabel
{
    case TO_DO = 'to_do';
    case IN_PROGRESS = 'in_progress';
    case QA = 'qa';
    case DONE = 'done';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TO_DO => 'To do',
            self::IN_PROGRESS => 'In Progress',
            self::QA => 'QA',
            self::DONE => 'Done',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TO_DO => 'gray',
            self::IN_PROGRESS => 'warning',
            self::QA => 'info',
            self::DONE => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::TO_DO => 'heroicon-o-clipboard-document-list',
            self::IN_PROGRESS => 'heroicon-o-arrow-path',
            self::QA => 'heroicon-o-magnifying-glass',
            self::DONE => 'heroicon-o-check-circle',
        };
    }
}
