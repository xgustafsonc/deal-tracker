<?php

namespace Database\Seeders;

use App\Enums\DealStage;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Vaste bedrijven
        $marktlink = Company::create([
            'name' => 'Marktlink Investment Partners',
            'industry' => 'Financiën',
            'website' => 'marktlink.com',
        ]);

        $acme = Company::create([
            'name' => 'Acme Holding B.V.',
            'industry' => 'Productie',
            'website' => 'acme-holding.nl',
        ]);

        // 2. John Doe als vast contact
        $john = Contact::create([
            'company_id' => $acme->id,
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john.doe@acme-holding.nl',
            'phone'      => '+31 6 12345678',
        ]);

        // 3. Vaste deals
        Deal::create([
            'company_id'          => $acme->id,
            'contact_id'          => $john->id,
            'title'               => 'Overname productielijn Noord',
            'value'               => 4_500_000,
            'stage'               => DealStage::Negotiation,
            'expected_close_date' => now()->addMonths(2),
            'notes'               => 'Wacht op due diligence rapport.',
        ]);

        Deal::create([
            'company_id'          => $marktlink->id,
            'title'               => 'Buy-and-build software cluster',
            'value'               => 12_000_000,
            'stage'               => DealStage::Proposal,
            'expected_close_date' => now()->addMonths(4),
        ]);

        // 4. Bulk dummy data: 8 bedrijven, elk met 3 contacten en 2 deals
        Company::factory(8)
            ->has(Contact::factory()->count(3))
            ->has(Deal::factory()->count(2))
            ->create();
    }
}