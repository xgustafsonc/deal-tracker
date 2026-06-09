<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;

class DealController extends Controller
{
    // GET /api/deals
    public function index()
    {
        $deals = Deal::with(['company', 'contact'])->latest()->get();

        return DealResource::collection($deals);
    }

    // POST /api/deals
    public function store(StoreDealRequest $request)
    {
        $deal = Deal::create($request->validated());

        return (new DealResource($deal->load(['company', 'contact'])))
            ->response()
            ->setStatusCode(201);
    }

    // GET /api/deals/{deal}
    public function show(Deal $deal)
    {
        return new DealResource($deal->load(['company', 'contact']));
    }

    // PUT/PATCH /api/deals/{deal}
    public function update(StoreDealRequest $request, Deal $deal)
    {
        $deal->update($request->validated());

        return new DealResource($deal->load(['company', 'contact']));
    }

    // DELETE /api/deals/{deal}
    public function destroy(Deal $deal)
    {
        $deal->delete();

        return response()->noContent();
    }
}