<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(ActivityType::getValues()),
            'starts' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'ends' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'from' => fake()->regexify('[A-Z]{3}'),
            'to' => fake()->regexify('[A-Z]{3}'),
            'activity_remark' => fake()->regexify('[A-Z]{2}[0-9]{3,4}'),
            'row' => '',
        ];
    }

}
