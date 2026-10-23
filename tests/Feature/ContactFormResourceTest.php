<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactForms\ContactFormResource;
use App\Filament\Resources\ContactForms\Pages\ListContactForms;
use App\Models\ContactForm;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->withAppAuthentication()->create());
    }

    public function test_an_admin_sees_contact_forms_newest_first(): void
    {
        $older = ContactForm::factory()->create(['created_at' => '2026-01-01 10:00:00']);
        $newer = ContactForm::factory()->create(['created_at' => '2026-02-01 10:00:00']);

        Livewire::test(ListContactForms::class)
            ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
    }

    public function test_an_admin_can_read_a_contact_form(): void
    {
        $contactForm = ContactForm::factory()->create(['message' => 'I would like to talk.']);

        $this->get(ContactFormResource::getUrl('view', ['record' => $contactForm]))
            ->assertOk()
            ->assertSeeText('I would like to talk.');
    }

    public function test_an_admin_can_delete_selected_contact_forms(): void
    {
        $selected = ContactForm::factory()->create();
        $unselected = ContactForm::factory()->create();

        Livewire::test(ListContactForms::class)
            ->selectTableRecords([$selected])
            ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
            ->assertNotified();

        $this->assertModelMissing($selected);
        $this->assertModelExists($unselected);
    }

    public function test_contact_forms_cannot_be_edited(): void
    {
        $contactForm = ContactForm::factory()->create();

        $this->get(ContactFormResource::getUrl('index').'/'.$contactForm->id.'/edit')
            ->assertNotFound();
    }
}
