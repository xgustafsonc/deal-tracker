<?php

namespace App\Enums;

enum DealStage: string
{
    case Lead = 'lead';
    case Qualified = 'qualified';
    case Proposal = 'proposal';
    case Negotiation = 'negotiation';
    case Won = 'won';
    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::Lead        => 'Lead',
            self::Qualified   => 'Gekwalificeerd',
            self::Proposal    => 'Voorstel',
            self::Negotiation => 'Onderhandeling',
            self::Won         => 'Gewonnen',
            self::Lost        => 'Verloren',
        };
    }

    // Handig straks in Vue/Tailwind voor badge-kleuren
    public function color(): string
    {
        return match ($this) {
            self::Lead        => 'gray',
            self::Qualified   => 'blue',
            self::Proposal    => 'indigo',
            self::Negotiation => 'amber',
            self::Won         => 'green',
            self::Lost        => 'red',
        };
    }
}