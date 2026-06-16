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

it('valideert de velden bij het aanmaken van een deal', function (array $overrides, string $invalidField) {
    $company = Company::factory()->create();

    // Een volledig geldige payload als basis...
    $payload = [
        'company_id' => $company->id,
        'title' => 'Geldige deal',
        'value' => 1_000_000,
        'stage' => DealStage::Lead->value,
    ];

    // ...waar we per geval precies één ding aan stukmaken
    $payload = array_merge($payload, $overrides);

    $this->postJson('/api/deals', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors([$invalidField]);
})->with([
    'titel ontbreekt'        => [['title' => ''], 'title'],
    'titel te lang'          => [['title' => str_repeat('a', 256)], 'title'],
    'waarde ontbreekt'       => [['value' => null], 'value'],
    'waarde is geen getal'   => [['value' => 'veel geld'], 'value'],
    'waarde is negatief'     => [['value' => -500], 'value'],
    'fase ontbreekt'         => [['stage' => ''], 'stage'],
    'fase is ongeldig'       => [['stage' => 'banaan'], 'stage'],
    'bedrijf bestaat niet'   => [['company_id' => 99999], 'company_id'],
    'contact bestaat niet'   => [['contact_id' => 99999], 'contact_id'],
]);

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