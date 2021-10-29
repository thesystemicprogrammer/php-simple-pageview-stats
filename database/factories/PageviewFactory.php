<?php

namespace Database\Factories;

use App\Services\TimeBucket\TimeBucketCalculatorService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\Pageview;

class PageviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pageview::class;

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
            'timestamp' => $calculatorService->calculateTimeBucketTimestamp(Carbon::now()->timestamp),
            'views' => 5,
        ];
    }

    public function setAlternativeUri() {
        return $this->state(function (array $attributes) {
            return [
                'uri' => '/my-second-page',
                'views' => 10,
            ]; 
        });
    }
}
