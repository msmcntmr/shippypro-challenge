<?php
    
    namespace Database\Seeders;
    
    use App\Models\Flight;
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    
    class Flights extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            Flight::factory(2500)->create();
        }
    }
