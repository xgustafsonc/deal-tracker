<?php

namespace App\Models;

use App\Enums\DealStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'contact_id', 'title', 'value',
        'stage', 'expected_close_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'stage'               => DealStage::class,
            'value'               => 'decimal:2',
            'expected_close_date' => 'date',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}