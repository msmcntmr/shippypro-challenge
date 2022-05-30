<?php
    
    namespace Database\Factories;
    
    use App\Models\Airport;
    use Illuminate\Database\Eloquent\Factories\Factory;
    
    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
     */
    class AirportFactory extends Factory
    {
        
        /**
         * The name of the factory's corresponding model.
         *
         * @var string
         */
        protected $model = Airport::class;
        
        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition()
        {
            /**
             * These are not exactly valid cities with a corresponding local airport.
             * These are just random city names with completely unrelated and made airport codes.
             * The only constraint is that the code must be unique, as specified  in the table migration.
             * Lat and Lng are also completely random.
             */
            return [
                'name' => $this->faker->city(),
                'code' => strtoupper($this->faker->unique()->lexify('???')),
                'lat'  => $this->faker->latitude(),
                'lng'  => $this->faker->longitude()
            ];
        }
    }
