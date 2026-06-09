<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'first_name', 'last_name', 'email', 'phone'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    // Accessor: $contact->full_name
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }
}