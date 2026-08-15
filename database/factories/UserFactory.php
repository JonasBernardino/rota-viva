<?php

namespace Database\Factories;

use App\Models\Municipio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'municipio_id' => null,
            'can_access_admin_panel' => false,
            'can_manage_platform' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user can access the municipal administration panel.
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'can_access_admin_panel' => true,
            'can_manage_platform' => false,
        ]);
    }

    /**
     * Indicate that the user manages a specific municipality.
     */
    public function managerFor(Municipio|int $municipality): static
    {
        $municipalityId = $municipality instanceof Municipio
            ? $municipality->id
            : $municipality;

        return $this->manager()->state(fn (array $attributes) => [
            'municipio_id' => $municipalityId,
        ]);
    }

    /**
     * Indicate that the user can manage the whole platform.
     */
    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'municipio_id' => null,
            'can_access_admin_panel' => false,
            'can_manage_platform' => true,
        ]);
    }
}
