<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetAdminMultiFactorAuth extends Command
{
    protected $signature = 'app:reset-admin-mfa {email}';

    protected $description = 'Remove an admin\'s authenticator app so they set it up again on their next login';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if ($user === null) {
            $this->error("No user found with email {$email}.");

            return self::FAILURE;
        }

        $user->saveAppAuthenticationSecret(null);

        $this->info("Reset multi-factor authentication for {$email}.");

        return self::SUCCESS;
    }
}
