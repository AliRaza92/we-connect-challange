<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkedinAutomationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route to import file 
Route::post('/import/list',[LinkedinAutomationController::class,'readProfileLinksFromFile'])->name('importFile');
//Route to login the Linkedin
Route::post('/login-to-linkedin',[LinkedinAutomationController::class,'loginToLinkedIn'])->name('login');
