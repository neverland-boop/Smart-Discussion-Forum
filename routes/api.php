<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\TopicController;
use Illuminate\Support\Facades\Route;

// FOR THE JAVA TEAM: base URL is {APP_URL}/api
// Throttled to prevent brute-force login/registration attempts.
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/lecturer', [AuthController::class, 'registerLecturer']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function () {
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            // Spatie roles access
            'roles' => $user->getRoleNames(), 
        ]);
    });

Route::post('/logout', [AuthController::class, 'logout']);

});

Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('/quizzes', QuizController::class);
    Route::apiResource('/topics', TopicController::class);

    Route::post('/quizzes/{quiz}/attempt', [QuizController::class, 'submitAttempt']
    );
});