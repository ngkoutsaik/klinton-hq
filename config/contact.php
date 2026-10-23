<?php

return [
    'enabled' => (bool) env('CONTACT_FORM_ENABLED', false),
    'recipient' => env('CONTACT_RECIPIENT', env('DEFAULT_ADMIN_EMAIL')),
];
