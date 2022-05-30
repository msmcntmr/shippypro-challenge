<?php
    
    namespace Database\Seeders;
    
    use App\Models\Airport;
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    
    class Airports extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            Airport::factory(100)->create();
        }
    }
