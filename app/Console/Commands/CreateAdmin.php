<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Users\CreateUser;
use App\Enums\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {--name= : Full name} {--email= : Email address} {--password= : Password}';

    protected $description = 'Create an admin account';

    public function handle(CreateUser $createUser): int
    {
        $name = $this->stringOption('name') ?? text('Name', required: true);
        $email = Str::lower($this->stringOption('email') ?? text('Email', required: true));
        $password = $this->stringOption('password') ?? password('Password (min 8, upper, lower, number)', required: true);

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'max:72', Password::min(8)->mixedCase()->numbers()],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->components->error($message);
            }

            return self::FAILURE;
        }

        $createUser->execute($name, $email, $password, Role::Admin);

        $this->components->info("Admin {$email} created.");

        return self::SUCCESS;
    }

    private function stringOption(string $name): ?string
    {
        $value = $this->option($name);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
