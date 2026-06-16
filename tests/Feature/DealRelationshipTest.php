<?php

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('koppelt deals aan een bedrijf', function () {
    $company = Company::factory()->create();
    Deal::factory()->count(3)->create(['company_id' => $company->id]);

    expect($company->deals)->toHaveCount(3);
    expect($company->deals->first())->toBeInstanceOf(Deal::class);
});

it('koppelt contacten aan een bedrijf', function () {
    $company = Company::factory()->create();
    Contact::factory()->count(2)->create(['company_id' => $company->id]);

    expect($company->contacts)->toHaveCount(2);
});

it('geeft het bedrijf terug vanuit een deal', function () {
    $company = Company::factory()->create(['name' => 'Acme Holding B.V.']);
    $deal = Deal::factory()->create(['company_id' => $company->id]);

    expect($deal->company)->toBeInstanceOf(Company::class);
    expect($deal->company->name)->toBe('Acme Holding B.V.');
});

it('verwijdert contacten en deals als het bedrijf verdwijnt (cascade)', function () {
    $company = Company::factory()->create();
    $contact = Contact::factory()->create(['company_id' => $company->id]);
    $deal = Deal::factory()->create(['company_id' => $company->id]);

    $company->delete();

    // Opnieuw uit de database kijken — niet vertrouwen op het geheugen
    $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    $this->assertDatabaseMissing('deals', ['id' => $deal->id]);
});

it('behoudt de deal maar maakt het contact leeg als het contact verdwijnt (null on delete)', function () {
    $company = Company::factory()->create();
    $contact = Contact::factory()->create(['company_id' => $company->id]);
    $deal = Deal::factory()->create([
        'company_id' => $company->id,
        'contact_id' => $contact->id,
    ]);

    $contact->delete();

    // Deal bestaat nog, maar contact_id is nu null
    $this->assertDatabaseHas('deals', [
        'id' => $deal->id,
        'contact_id' => null,
    ]);

    expect($deal->fresh()->contact_id)->toBeNull();
});

it('cast de fase naar een DealStage enum', function () {
    $deal = Deal::factory()->create(['stage' => 'negotiation']);

    expect($deal->stage)->toBe(App\Enums\DealStage::Negotiation);
    expect($deal->stage->label())->toBe('Onderhandeling');
});