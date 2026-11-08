<?php

namespace Tests\Feature;

use App\Jobs\SendContactFormEmail;
use App\Mail\ContactFormMail;
use App\Models\ContactForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendContactFormEmailTest extends TestCase
{
    use RefreshDatabase;

    private const string RECIPIENT = 'owner@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Config::set('contact.recipient', self::RECIPIENT);
    }

    public function test_it_emails_the_admin_and_marks_the_form_sent(): void
    {
        $contactForm = ContactForm::factory()->create(['sent' => false]);

        (new SendContactFormEmail($contactForm->id))->handle();

        Mail::assertSent(ContactFormMail::class, 1);
        Mail::assertSent(
            ContactFormMail::class,
            fn (ContactFormMail $mail): bool => $mail->hasTo(self::RECIPIENT) && $mail->contactForm->is($contactForm),
        );
        $this->assertTrue($contactForm->refresh()->sent);
    }

    public function test_the_admin_can_reply_directly_to_the_visitor(): void
    {
        $contactForm = ContactForm::factory()->create(['email' => 'visitor@example.com', 'sent' => false]);

        (new SendContactFormEmail($contactForm->id))->handle();

        Mail::assertSent(
            ContactFormMail::class,
            fn (ContactFormMail $mail): bool => $mail->hasReplyTo('visitor@example.com') && ! $mail->hasBcc('visitor@example.com'),
        );
    }

    public function test_it_does_nothing_for_an_already_sent_form(): void
    {
        $contactForm = ContactForm::factory()->sent()->create();

        (new SendContactFormEmail($contactForm->id))->handle();

        Mail::assertNothingSent();
    }
}
