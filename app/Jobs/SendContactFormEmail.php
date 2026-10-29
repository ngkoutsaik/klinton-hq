<?php

namespace App\Jobs;

use App\Mail\ContactFormMail;
use App\Models\ContactForm;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SendContactFormEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $contactFormId) {}

    public function handle(): void
    {
        $contactForm = ContactForm::find($this->contactFormId);

        if ($contactForm === null || $contactForm->sent) {
            return;
        }

        $mail = Mail::to(Config::string('contact.recipient'));

        if ($contactForm->send_copy) {
            $mail->bcc($contactForm->email);
        }

        $mail->send(new ContactFormMail($contactForm));

        $contactForm->sent = true;
        $contactForm->save();
    }
}
