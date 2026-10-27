<div>
    <p><strong>From:</strong> {{ $contactForm->email }}</p>
    <p><strong>Received:</strong> {{ $contactForm->created_at->format('j F Y, H:i') }}</p>
    <p><strong>Message:</strong>
    <p>{{ $contactForm->message }}</p>
</div>
