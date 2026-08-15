<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('platform:create-superadmin {email : E-mail do superadmin} {--name=Superadmin Rota Viva : Nome do usuário} {--password= : Senha inicial com pelo menos 8 caracteres}')]
#[Description('Create or update a platform superadmin user')]
class CreateSuperadminCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = (string) $this->option('name');
        $password = (string) ($this->option('password') ?: $this->secret('Senha inicial do superadmin'));

        if (strlen($password) < 8) {
            $this->error('A senha precisa ter pelo menos 8 caracteres.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'can_access_admin_panel' => false,
                'can_manage_platform' => true,
            ]
        );

        $this->info("Superadmin [{$email}] criado/atualizado com sucesso.");

        return self::SUCCESS;
    }
}
