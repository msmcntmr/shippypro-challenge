<?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Support\Facades\DB;
    
    class Flight extends BaseModel
    {
        use HasFactory;
        
        
        protected $fillable = [
            'code_departure',
            'code_arrival',
            'price',
        ];
    
    
        /**
         * @deprecated
         * Alternative solution for $stopovers = 1 only
         * by using Sql.
         *
         * @param string $from
         * @param string $to
         *
         * @return array
         */
        public function getStopoverRoutes(string $from, string $to): array
        {
            $flights_table = self::tableName();
            
            return cache()->remember("flight.stopover.{$from}.{$to}", 3600, function () use($flights_table, $from, $to) {
                return DB::select(
                    "SELECT
                                f1.id AS f1_id,
                                f1.code_departure AS f1_code_departure,
                                f1.code_arrival AS f1_code_arrival,
                                f1.price AS f1_price,
                                f2.id AS f2_id,
                                f2.code_departure AS f2_code_departure,
                                f2.code_arrival AS f2_code_arrival,
                                f2.price AS f2_price,
                                f1.price + f2.price AS sum
                            FROM
                                (SELECT
                                    *
                                FROM
                                    {$flights_table}
                                WHERE
                                    code_departure = '{$from}') AS f1
                                    JOIN
                                (SELECT
                                    *
                                FROM
                                    {$flights_table} f2
                                WHERE
                                    code_arrival = '{$to}') AS f2 ON f1.code_arrival = f2.code_departure
                            ORDER BY sum ASC"
                );
            });
            
        }
        
    }
