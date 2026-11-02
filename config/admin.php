<?php

return [
    'email' => env('DEFAULT_ADMIN_EMAIL'),
    'mfa_enabled' => (bool) env('ADMIN_MFA_ENABLED', true),
];
