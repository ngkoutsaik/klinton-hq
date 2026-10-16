<?php

namespace Tests\Feature;

use App\Jobs\SendContactFormEmail;
use App\Models\ContactForm;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ContactFormSpamProtectionTest extends TestCase
{
    use RefreshDatabase;

    private const string ADMIN_EMAIL = 'test@example.com';

    private const int MINIMUM_SECONDS = 2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Queue::fake();
        Config::set('admin.email', self::ADMIN_EMAIL);
        Config::set('contact.enabled', true);
        Config::set('honeypot.enabled', true);
        Config::set('honeypot.randomize_name_field_name', false);
        Config::set('honeypot.amount_of_seconds', self::MINIMUM_SECONDS);
    }

    public function test_the_form_renders_the_honeypot_fields(): void
    {
        $this->createOwnersResume();

        $this->get('/')
            ->assertOk()
            ->assertSee('name="my_name"', escape: false)
            ->assertSee('name="valid_from"', escape: false);
    }

    public function test_it_silently_rejects_a_submission_with_a_filled_honeypot(): void
    {
        $response = $this->submit(['my_name' => 'bot@spam.test']);

        $this->assertSilentlyRejected($response);
    }

    public function test_it_silently_rejects_a_submission_missing_the_honeypot_fields(): void
    {
        $response = $this->post(URL::route('contact.store'), [
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'I would like to talk.',
            'privacy_consent' => '1',
        ]);

        $this->assertSilentlyRejected($response);
    }

    public function test_it_silently_rejects_a_submission_that_arrives_too_quickly(): void
    {
        $response = $this->submit($this->honeypotFieldsFromForm());

        $this->assertSilentlyRejected($response);
    }

    public function test_it_accepts_a_submission_after_the_minimum_time(): void
    {
        $fields = $this->honeypotFieldsFromForm();

        $this->travel(self::MINIMUM_SECONDS + 1)->seconds();
        $response = $this->submit($fields);

        $response->assertRedirect(URL::to('/').'#contact');
        $this->assertDatabaseCount(ContactForm::class, 1);
        Queue::assertPushed(SendContactFormEmail::class);
    }

    private function assertSilentlyRejected(TestResponse $response): void
    {
        $response->assertRedirect(URL::to('/').'#contact')
            ->assertSessionHas('contact_status');
        $this->assertDatabaseEmpty(ContactForm::class);
        Queue::assertNotPushed(SendContactFormEmail::class);
    }

    /**
     * @param  array<string, string>  $honeypotFields
     */
    private function submit(array $honeypotFields)
    {
        return $this->post(URL::route('contact.store'), [
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'I would like to talk.',
            'privacy_consent' => '1',
            ...$honeypotFields,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function honeypotFieldsFromForm(): array
    {
        $this->createOwnersResume();
        $html = $this->get('/')->assertOk()->getContent();

        preg_match('/name="valid_from"\s+type="text"\s+value="([^"]+)"/', $html, $matches);

        return ['my_name' => '', 'valid_from' => $matches[1]];
    }

    private function createOwnersResume(): Resume
    {
        $owner = User::factory()->create(['email' => self::ADMIN_EMAIL]);

        return Resume::factory()->published()->for($owner)->create();
    }
}
