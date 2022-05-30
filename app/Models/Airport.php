<?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\DB;
    
    class Airport extends BaseModel
    {
        use HasFactory;
        
        
        protected $fillable = [
            'name',
            'code',
            'lat',
            'lng',
        ];
    
    
        /**
         * Returns an array of all airport codes.
         *
         * @return array
         */
        public static function retrieveAllCodes(): array
        {
            $codes = [];
            
            (self::getAllAirports())->each(function ($airport) use(&$codes) {
                $codes[] = $airport->code;
            });
            
            return $codes;
        }
        
        
        /**
         * Returns a collection of airports.
         * We also make use of the cache in order to avoid useless queries,
         * since most likely the airports table will rarely be updated.
         *
         * @return mixed
         */
        public static function getAllAirports(): Collection
        {
            return cache()->remember('airport.all', parent::CACHE_TIMEOUT, function () {
                return DB::table(Airport::tableName())->get();
            });
        }
        
        
    }
