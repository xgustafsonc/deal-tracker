<?php

use App\Models\Company;
use App\Models\Deal;
use App\Services\DealService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new DealService();
});

it('maakt een deal aan via de service', function () {
    $company = Company::factory()->create();

    $deal = $this->service->create([
        'company_id' => $company->id,
        'title' => 'Service deal',
        'value' => 2_000_000,
        'stage' => 'lead',
    ]);

    expect($deal)->toBeInstanceOf(Deal::class);
    expect($deal->title)->toBe('Service deal');
    $this->assertDatabaseHas('deals', ['title' => 'Service deal']);
});

it('laadt de relaties mee bij het aanmaken', function () {
    $company = Company::factory()->create();

    $deal = $this->service->create([
        'company_id' => $company->id,
        'title' => 'Met relaties',
        'value' => 1_000,
        'stage' => 'lead',
    ]);

    // relationLoaded bewijst dat de service eager-loadt — geen N+1 verderop
    expect($deal->relationLoaded('company'))->toBeTrue();
    expect($deal->company->id)->toBe($company->id);
});

it('werkt een deal bij via de service', function () {
    $deal = Deal::factory()->create(['title' => 'Voor']);

    $updated = $this->service->update($deal, ['title' => 'Na']);

    expect($updated->title)->toBe('Na');
    $this->assertDatabaseHas('deals', ['id' => $deal->id, 'title' => 'Na']);
});

it('verwijdert een deal via de service', function () {
    $deal = Deal::factory()->create();

    $this->service->delete($deal);

    $this->assertDatabaseMissing('deals', ['id' => $deal->id]);
});

it('geeft alle deals terug met relaties', function () {
    Deal::factory()->count(3)->create();

    $deals = $this->service->list();

    expect($deals)->toHaveCount(3);
    expect($deals->first()->relationLoaded('company'))->toBeTrue();
});