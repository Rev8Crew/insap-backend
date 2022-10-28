<?php

namespace Database\Factories;

use App\Enums\ActiveStatus;
use App\Models\File;
use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Models\RecordData;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RecordDataFactory extends Factory
{
    protected $model = RecordData::class;

    public function definition(): array
    {
        return [
            'name' => 'Экспедиция ' . $this->faker->words(2, true),
            'description' => $this->faker->text(),
            'order' => $this->faker->randomNumber(),
            'is_active' => ActiveStatus::create(ActiveStatus::ACTIVE)->getValue(),
            'creator_user_id' => User::inRandomOrder()->first()->id,
            'date_start' => Carbon::now(),
            'date_end' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
