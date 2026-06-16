<?php

use App\Enums\DealStage;
use App\Models\Company;
use App\Models\Deal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('toont alle deals', function () {
    Deal::factory()->count(3)->create();

    $this->getJson('/api/deals')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('toont één deal met de juiste structuur', function () {
    $deal = Deal::factory()->create();

    $this->getJson("/api/deals/{$deal->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $deal->id)
        ->assertJsonStructure([
            'data' => [
                'id', 'title', 'value',
                'stage' => ['value', 'label', 'color'],
                'company' => ['id', 'name'],
            ],
        ]);
});

it('maakt een deal aan', function () {
    $company = Company::factory()->create();

    $this->postJson('/api/deals', [
        'company_id' => $company->id,
        'title' => 'Overname Testbedrijf',
        'value' => 5_000_000,
        'stage' => DealStage::Lead->value,
        'expected_close_date' => '2026-12-01',
    ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Overname Testbedrijf');

    $this->assertDatabaseHas('deals', [
        'title' => 'Overname Testbedrijf',
        'company_id' => $company->id,
    ]);
});

it('vereist de verplichte velden', function () {
    $this->postJson('/api/deals', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['company_id', 'title', 'value', 'stage']);
});

it('weigert een ongeldige fase', function () {
    $company = Company::factory()->create();

    $this->postJson('/api/deals', [
        'company_id' => $company->id,
        'title' => 'Test',
        'value' => 1000,
        'stage' => 'banaan',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['stage']);
});

it('werkt een deal bij', function () {
    $deal = Deal::factory()->create(['title' => 'Oude titel']);

    $this->putJson("/api/deals/{$deal->id}", [
        'company_id' => $deal->company_id,
        'title' => 'Nieuwe titel',
        'value' => $deal->value,
        'stage' => $deal->stage->value,
    ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Nieuwe titel');

    $this->assertDatabaseHas('deals', [
        'id' => $deal->id,
        'title' => 'Nieuwe titel',
    ]);
});

it('verwijdert een deal', function () {
    $deal = Deal::factory()->create();

    $this->deleteJson("/api/deals/{$deal->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('deals', ['id' => $deal->id]);
});