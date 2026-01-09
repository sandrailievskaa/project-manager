<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserExperience: string implements HasColor, HasIcon, HasLabel
{
    case JUNIOR = 'junior';
    case MIDDLE = 'middle';
    case SENIOR = 'senior';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::JUNIOR => 'Junior',
            self::MIDDLE => 'Middle',
            self::SENIOR => 'Senior',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::JUNIOR => 'gray',
            self::MIDDLE => 'warning',
            self::SENIOR => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::JUNIOR => 'heroicon-o-academic-cap',
            self::MIDDLE => 'heroicon-o-briefcase',
            self::SENIOR => 'heroicon-o-star',
        };
    }
}
