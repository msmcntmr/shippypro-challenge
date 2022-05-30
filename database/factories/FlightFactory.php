<?php
    
    namespace Database\Factories;
    
    use App\Models\Airport;
    use App\Models\Flight;
    use Illuminate\Database\Eloquent\Factories\Factory;
    
    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
     */
    class FlightFactory extends Factory
    {
        
        /**
         * The name of the factory's corresponding model.
         *
         * @var string
         */
        protected $model = Flight::class;
        
        
        private const MIN_TICKET_FARE = 30;
        private const MAX_TICKET_FARE = 400;
        
        private array $generated_code_pairs = [];
        
        
        /**
         * Recursive function to seed the flights table.
         * We return a pair of airport codes that define a flight route
         * that has not been inserted already.
         * I.e. LAX -> TRW
         *      TRW -> LAX
         *      TRW -> FRR
         *
         * These are all valid.
         * But we can't have more than one exact pair such as LAX -> TRW,
         * nor we can have a flight going from/to the same airport.
         *
         * @return array
         */
        public function generateRandomFlight(): array
        {
            $airport_codes = Airport::retrieveAllCodes();
            
            $picked_codes = array_map(fn($k) => $airport_codes[$k], array_rand($airport_codes, 2));
            
            $code_pair = implode('', $picked_codes);
            
            if (in_array($code_pair, $this->generated_code_pairs)) {
                return $this->generateRandomFlight();
            }
            
            $this->generated_code_pairs[] = $code_pair;
            
            return $picked_codes;
        }
        
        
        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition()
        {
            $picked_codes = $this->generateRandomFlight();
            
            return [
                'code_departure' => $picked_codes[0],
                'code_arrival'   => $picked_codes[1],
                'price'          => $this->faker->randomFloat(2, self::MIN_TICKET_FARE, self::MAX_TICKET_FARE)
            ];
        }
    }
