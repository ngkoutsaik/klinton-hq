{{-- TODO: replace this placeholder with the final contact email content --}}
<div>
    <p><strong>From:</strong> {{ $contactForm->email }}</p>
    <p><strong>Received:</strong> {{ $contactForm->created_at->format('j F Y, H:i') }}</p>
    <p><strong>Subject:</strong> {{ $contactForm->subject }}</p>
    <p>{{ $contactForm->message }}</p>
</div>
