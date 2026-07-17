<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum PokemonType: string
{
    case Normal = 'normal';
    case Fire = 'fire';
    case Water = 'water';
    case Electric = 'electric';
    case Grass = 'grass';
    case Ice = 'ice';
    case Fighting = 'fighting';
    case Poison = 'poison';
    case Ground = 'ground';
    case Flying = 'flying';
    case Psychic = 'psychic';
    case Bug = 'bug';
    case Rock = 'rock';
    case Ghost = 'ghost';
    case Dragon = 'dragon';
    case Dark = 'dark';
    case Steel = 'steel';
    case Fairy = 'fairy';

    public function icon(): string
    {
        return match ($this) {
            self::Normal => '⚪',
            self::Fire => '🔥',
            self::Water => '💧',
            self::Electric => '⚡',
            self::Grass => '🌿',
            self::Ice => '❄️',
            self::Fighting => '🥊',
            self::Poison => '☠️',
            self::Ground => '🌍',
            self::Flying => '🕊️',
            self::Psychic => '🔮',
            self::Bug => '🐛',
            self::Rock => '🪨',
            self::Ghost => '👻',
            self::Dragon => '🐉',
            self::Dark => '🌑',
            self::Steel => '⚙️',
            self::Fairy => '✨',
        };
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /**
     * All cases formatted for frontend consumption (dropdowns, filters, etc.)
     */
    public static function forFrontend(): Collection
    {
        return collect(self::cases())->map(fn($type) => [
            'value' => $type->value,
            'label' => $type->label(),
            'icon' => $type->icon(),
        ]);
    }
}
