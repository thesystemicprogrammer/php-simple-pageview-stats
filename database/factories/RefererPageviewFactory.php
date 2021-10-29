<?php

namespace Database\Factories;

use App\Services\TimeBucket\TimeBucketCalculatorService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\RefererPageview;

class RefererPageviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RefererPageview::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $calculatorService = new TimeBucketCalculatorService();
        return [
            'uri' => '/my-page',
            'refererHash' => uniqid(),
            'timestamp' => $calculatorService->calculateTimeBucketTimestamp(Carbon::now()->timestamp)
        ];
    }

    public function setAlternativeUri() {
        return $this->state(function (array $attributes) {
            return [
                'uri' => '/my-second-page',
            ]; 
        });
    }
}
