<?php

namespace Tests\Feature;

use App\Models\ContactForm;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private const string ADMIN_EMAIL = 'test@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Queue::fake();
        Config::set('admin.email', self::ADMIN_EMAIL);
        Config::set('contact.enabled', true);
    }

    public function test_it_shows_the_form_on_the_homepage_when_enabled(): void
    {
        $this->createOwnersResume();

        $this->get('/')
            ->assertOk()
            ->assertSee('action="'.URL::route('contact.store').'"', escape: false)
            ->assertSee('name="_token"', escape: false)
            ->assertSee('popovertarget="privacy-notice"', escape: false)
            ->assertSee('href="#contact"', escape: false);
    }

    public function test_it_hides_the_form_on_the_homepage_when_disabled(): void
    {
        Config::set('contact.enabled', false);
        $this->createOwnersResume();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="contact"', escape: false)
            ->assertDontSee('href="#contact"', escape: false);
    }

    public function test_it_leaves_the_form_out_of_the_pdf(): void
    {
        $resume = $this->createOwnersResume();

        $html = View::make('home', ['resume' => $resume, 'isPdf' => true])->render();

        $this->assertStringNotContainsString('id="contact"', $html);
        $this->assertStringNotContainsString('href="#contact"', $html);
    }

    public function test_a_valid_submission_stores_an_unsent_contact_form(): void
    {
        $response = $this->post(URL::route('contact.store'), [
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'I would like to talk.',
            'send_copy' => '1',
            'privacy_consent' => '1',
        ]);

        $response->assertRedirect(URL::to('/').'#contact')
            ->assertSessionHas('contact_status', 'Thanks, your message has been received.');

        $this->assertDatabaseHas(ContactForm::class, [
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'I would like to talk.',
            'send_copy' => true,
            'sent' => false,
        ]);
    }

    public function test_it_does_not_send_a_copy_when_the_checkbox_is_unticked(): void
    {
        $this->post(URL::route('contact.store'), [
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'I would like to talk.',
            'privacy_consent' => '1',
        ]);

        $this->assertDatabaseHas(ContactForm::class, ['send_copy' => false]);
    }

    public function test_it_ignores_a_submitted_sent_flag(): void
    {
        $this->post(URL::route('contact.store'), [
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'I would like to talk.',
            'privacy_consent' => '1',
            'sent' => '1',
        ]);

        $this->assertDatabaseHas(ContactForm::class, ['sent' => false]);
    }

    public function test_it_rejects_an_empty_submission(): void
    {
        $response = $this->post(URL::route('contact.store'));

        $response->assertSessionHasErrors([
            'email' => 'The email field is required.',
            'subject' => 'The subject field is required.',
            'message' => 'The message field is required.',
            'privacy_consent' => 'Please agree to the privacy notice.',
        ]);
        $this->assertDatabaseEmpty(ContactForm::class);
    }

    /**
     * @return array<string, array{array<string, string>, string, string}>
     */
    public static function invalidSubmissions(): array
    {
        return [
            'invalid email' => [['email' => 'not-an-email'], 'email', 'The email field must be a valid email address.'],
            'subject too long' => [['subject' => str_repeat('a', 256)], 'subject', 'The subject field must not be greater than 255 characters.'],
            'message too long' => [['message' => str_repeat('a', 5001)], 'message', 'The message field must not be greater than 5000 characters.'],
            'consent not given' => [['privacy_consent' => '0'], 'privacy_consent', 'Please agree to the privacy notice.'],
        ];
    }

    /**
     * @param  array<string, string>  $override
     */
    #[DataProvider('invalidSubmissions')]
    public function test_it_rejects_an_invalid_submission(array $override, string $field, string $message): void
    {
        $response = $this->post(URL::route('contact.store'), [
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'I would like to talk.',
            'privacy_consent' => '1',
            ...$override,
        ]);

        $response->assertSessionHasErrors([$field => $message]);
        $this->assertDatabaseEmpty(ContactForm::class);
    }

    public function test_it_escapes_rejected_input_when_showing_the_form_again(): void
    {
        $this->createOwnersResume();

        $response = $this->from('/')->followingRedirects()->post(URL::route('contact.store'), [
            'subject' => '<script>alert(1)</script>',
        ]);

        $response->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', escape: false)
            ->assertDontSee('<script>alert(1)</script>', escape: false);
    }

    public function test_it_returns_404_and_stores_nothing_when_disabled(): void
    {
        Config::set('contact.enabled', false);

        $response = $this->post(URL::route('contact.store'), [
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'I would like to talk.',
            'privacy_consent' => '1',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseEmpty(ContactForm::class);
    }

    private function createOwnersResume(): Resume
    {
        $owner = User::factory()->create(['email' => self::ADMIN_EMAIL]);

        return Resume::factory()->published()->for($owner)->create();
    }
}
