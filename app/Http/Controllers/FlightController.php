<?php
    
    namespace App\Http\Controllers;
    
    use App\Models\Airport;
    use App\Models\Flight;
    use Illuminate\Http\Request;
    
    class FlightController extends Controller
    {
        
        /**
         * @param Request $request
         *
         * @return float|null
         */
        public function search(Request $request): ?float
        {
            $validated_data = $request->validate([
                'from'      => 'required|exists:airports,code',
                'to'        => 'required|exists:airports,code',
                'stopovers' => 'required|integer|min:0|max:2'
            ]);
            
            return $this->getCheapestFare(
                $validated_data['from'],
                $validated_data['to'],
                $validated_data['stopovers']
            );
        }
        
        
        /**
         * Returns the cheapest fare.
         * If null is returned, no route is available.
         *
         * This is basically the shortest path problem
         * with weights (flight costs in this case) between nodes.
         * The following solution should be almost identical to Bellman-Ford's
         * algorithm. The first part is just me setting right
         * the starting multidimensional arrays (aka vertex) since
         * I preferred to have columns code_departure and code_arrival as
         * the actual airport codes rather than airports' id column.
         *
         * Note that this function only returns the cheapest fare,
         * it does not return what kind of route it is, nor it returns a list of
         * routes from the cheapest to the most expensive. That would
         * definitely be mandatory to implement in a real search engine,
         * but would also increase the complexity of the algo and the
         * footprint in terms of resources.
         *
         * The time complexity for this function should be roughly O(A * S),
         * excluding the cycles for arrays' setup, where A is the number
         * of airports and S the number of stopovers.
         *
         * @note: this function will not work as expected with
         * duplicated flights as in same source-destination but different price.
         *
         * @param string $from
         * @param string $to
         * @param int $stopovers
         *
         * @return float
         */
        public function getCheapestFare(string $from, string $to, int $stopovers = 0): float
        {
            $airports = Airport::getAllAirports();
            
            $flights = Flight::lazy();
            
            /*
             * Prefix for array keys to avoid conflicts.
             */
            $prefix = 'A';
            
            /*
             * Assigning the highest value to total price
             */
            $total_price = PHP_FLOAT_MAX;
            
            /*
             * Initializing the graph
             */
            $graph = [];
            
            /*
             * Since we have airport codes "???" as references to the airport table,
             * we map these codes to their relative row ID in the airports table
             */
            $mapped_airports = [];
            
            foreach ($airports as $airport) {
                $mapped_airports[$airport->code] = $airport->id;
            }
            
            foreach ($mapped_airports as $code => $id) {
                /*
                 * If departure ID of the current flight is not set
                 * in $graph, we add it as an empty array.
                 */
                if ( ! array_key_exists($prefix . $id, $graph)) {
                    $graph[$prefix . $id] = [];
                }
            }
            
            /*
             * Mapping all flights in a multidimensional array built as follows:
             * $all_flights =   [
             *                      [ "departure" => ID, "arrival" => ID, "price" => PRICE ],
             *                      [ "departure" => ID, "arrival" => ID, "price" => PRICE ],
             *                      [ "departure" => ID, "arrival" => ID, "price" => PRICE ],
             *                      . . .
             *                  ]
             */
            $all_flights = [];
            
            foreach ($flights as $flight) {
                $all_flights[] = [
                    'departure' => $mapped_airports[$flight->code_departure],   // Airport ID based on code_departure
                    'arrival'   => $mapped_airports[$flight->code_arrival],     // Airport ID based on code_arrival
                    'price'     => $flight->price                               // Flight price
                ];
            }
            
            foreach ($all_flights as $mapped_flight) {
                /*
                 * If the arrival airport ID is not set as key inside the array $graph[DEPARTURE_ID],
                 * we add it and set its value to the flight price.
                 */
                if ( ! array_key_exists($prefix . $mapped_flight['arrival'], $graph[$prefix . $mapped_flight['departure']])) {
                    $graph[$prefix . $mapped_flight['departure']][$prefix . $mapped_flight['arrival']] = $mapped_flight['price'];
                }
            }
            
            /*
             * Get IDs of departure and arrival airports.
             */
            $from_airport_id = $prefix . $mapped_airports[$from];
            $to_airport_id   = $prefix . $mapped_airports[$to];
            
            /*
             * Initializing the queue where each entry is an array:
             * [ array_of_graph[departure_id], price, stopovers ]
             */
            $queue = [
                [$graph[$from_airport_id], 0, 0]
            ];
            
            /*
             * Initializing departure airport array.
             */
            $departure_airport = null; // Array
            $destinations      = null; // Array
            $price             = null; // Float
            $updated_price     = null; // Float
            $stops             = null; // Integer
            $next_stops        = null; // Integer
            
            // While the queue is not empty
            while ( ! empty($queue)) {
                // Get the first element from the queue
                $departure_airport = array_shift($queue);
                
                $destinations = $departure_airport[0];
                $price        = $departure_airport[1];
                $stops        = $departure_airport[2];
                
                /*
                 * If the current number of stops is greater than
                 * the maximum number of stopovers provided,
                 * then we don't have to check for this airport
                 * so we can skip to the next one.
                 */
                if ($stops > $stopovers) {
                    continue;
                }
                
                // Increment number of stops.
                $next_stops = $stops + 1;
                
                if ( ! empty($destinations)) {
                    foreach ($destinations as $destination_id => $destination_price) {
                        // We add the current flight price to the price of destination
                        $updated_price = $price + $destination_price;
                        
                        /*
                         * If this arrival airport is the final destination and the current
                         * price is lower than $total_price, then we set the current price to
                         * $total_price.
                         */
                        if ($destination_id === $to_airport_id && $updated_price < $total_price) {
                            $total_price = $updated_price;
                        }
                        
                        // Go to next destination
                        $queue[] = [$graph[$destination_id], $updated_price, $next_stops];
                    }
                }
            }
            
            return ($total_price === PHP_FLOAT_MAX) ? -1 : $total_price;
        }
        
        
    }
