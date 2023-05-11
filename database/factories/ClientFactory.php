<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'username' => Str::slug($this->faker->name . time()),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'country' => 'Ghana',
            'city' => $this->faker->randomElement([
                "Accra",
                "Bawku",
                "Berekum",
                "Bolgatanga",
                "Cape Coast",
                "Home",
                "Koforidua",
                "Kumasi",
                "Legon",
                "Mampong",
                "Navrongo",
                "Sunyani",
                "Takoradi",
                "Tema",
                "Wa",
                "Winneba"
            ]),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'age' => $this->faker->randomElement([18, 20, 21, 22, 25, 30, 34, 32, 25])
        ];
    }
}
