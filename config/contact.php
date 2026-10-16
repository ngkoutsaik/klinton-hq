<?php

return [
    'enabled' => (bool) env('CONTACT_FORM_ENABLED', false),

    /*
     * Where contact form messages are delivered. Keep this a public address
     * (not a private one): a visitor who asks for a copy is Bcc'd, and the
     * copy still exposes this "To" address. Falls back to the admin email.
     */
    'recipient' => env('CONTACT_RECIPIENT', env('DEFAULT_ADMIN_EMAIL')),
];
