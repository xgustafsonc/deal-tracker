<?php

namespace App\Services;

use App\Models\Deal;
use Illuminate\Database\Eloquent\Collection;

class DealService
{
    public function list(array $filters = []): Collection
{
    return Deal::query()
        ->with(['company', 'contact'])
        ->when(
            $filters['search'] ?? null,
            fn ($query, $search) => $query->where('title', 'like', "%{$search}%")
        )
        ->when(
            $filters['stage'] ?? null,
            fn ($query, $stage) => $query->where('stage', $stage)
        )
        ->latest()
        ->get();
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