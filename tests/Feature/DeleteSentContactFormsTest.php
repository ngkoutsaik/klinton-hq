<?php

namespace Tests\Feature;

use App\Jobs\DeleteSentContactForms;
use App\Models\ContactForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class DeleteSentContactFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_sent_forms_last_updated_more_than_seven_days_ago(): void
    {
        $this->travelTo('2026-10-16 12:00:00');

        $oldSent = ContactForm::factory()->sent()->create(['updated_at' => Date::now()->subDays(8)]);
        $recentSent = ContactForm::factory()->sent()->create(['updated_at' => Date::now()->subDays(5)]);
        $oldUnsent = ContactForm::factory()->create(['sent' => false, 'updated_at' => Date::now()->subDays(30)]);

        (new DeleteSentContactForms)->handle();

        $this->assertModelMissing($oldSent);
        $this->assertModelExists($recentSent);
        $this->assertModelExists($oldUnsent);
    }
}
