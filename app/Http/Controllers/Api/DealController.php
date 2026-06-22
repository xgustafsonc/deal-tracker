<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use App\Services\DealService;

class DealController extends Controller
{
    public function __construct(
        private readonly DealService $deals
    ) {}

    public function index(Request $request)
{
    $filters = $request->only(['search', 'stage']);

    return DealResource::collection($this->deals->list($filters));
}

    public function store(StoreDealRequest $request)
    {
        $deal = $this->deals->create($request->validated());

        return (new DealResource($deal))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Deal $deal)
    {
        return new DealResource($deal->load(['company', 'contact']));
    }

    public function update(StoreDealRequest $request, Deal $deal)
    {
        $deal = $this->deals->update($deal, $request->validated());

        return new DealResource($deal);
    }

    public function destroy(Deal $deal)
    {
        $this->deals->delete($deal);

        return response()->noContent();
    }
}