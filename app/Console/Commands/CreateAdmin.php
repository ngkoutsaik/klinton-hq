<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Create an admin user, or promote and reset the password of an existing one';

    public function handle(): int
    {
        $email = text(
            label: 'Email',
            required: true,
            validate: fn (string $value) => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : 'Enter a valid email address.',
        );
        $firstName = text(label: 'First name', required: true);
        $lastName = text(label: 'Last name', required: true);
        $password = password(
            label: 'Password',
            required: true,
            validate: fn (string $value) => strlen($value) >= 12 ? null : 'Use at least 12 characters.',
        );

        $user = User::firstOrNew(['email' => $email]);
        $user->fill([
            'name' => "{$firstName} {$lastName}",
            'first_name' => $firstName,
            'last_name' => $lastName,
            'password' => $password,
        ]);
        $user->forceFill(['is_admin' => true])->save();

        $this->info(($user->wasRecentlyCreated ? 'Created' : 'Updated')." admin {$email}.");

        return self::SUCCESS;
    }
}
