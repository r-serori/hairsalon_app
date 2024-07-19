<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Enums\Roles;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(1, 100),
            'email' => $this->faker->unique()->safeEmail(1, 200),
            'phone_number' => $this->faker->unique()->phoneNumber(1, 20),
            'password' => $this->faker->password,
            'role' => Roles::$OWNER || Roles::$STAFF || Roles::$MANAGER,
            'isAttendance' => 0 | 1,
            'created_at' => now(),
            'updated_at' => now(),
            'email_verified_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user should have a personal team.
     */
    // public function withPersonalTeam(): static
    // {
    //     if (!Features::hasTeamFeatures()) {
    //         return $this->state([]);
    //     }

    //     return $this->has(
    //         Team::factory()->create()->id
    //             ->state(function (array $attributes, User $user) {
    //                 return ['name' => $user->name . '\'s Team', 'user_id' => $user->id, 'personal_team' => true];
    //             }),
    //         'ownedTeams'
    //     );
    // }
}
