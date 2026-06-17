<?php

namespace App\Services;

use App\Models\Deal;
use Illuminate\Database\Eloquent\Collection;

class DealService
{
    public function list(): Collection
    {
        return Deal::with(['company', 'contact'])->latest()->get();
    }

    public function create(array $data): Deal
    {
        $deal = Deal::create($data);

        return $deal->load(['company', 'contact']);
    }

    public function update(Deal $deal, array $data): Deal
    {
        $deal->update($data);

        return $deal->load(['company', 'contact']);
    }

    public function delete(Deal $deal): void
    {
        $deal->delete();
    }
}