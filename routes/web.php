<?php
    
    use Illuminate\Support\Facades\Route;
    
    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */
    
    Route::get('/', function () {
        return view('app');
        /*$cheapest_route = new \App\Http\Controllers\FlightController();
        return $cheapest_route->getCheapestFare('XGO', 'MAR', 0);*/
    });
    
    Route::get('/airports', fn() => \App\Models\Airport::getAllAirports());

