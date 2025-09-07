<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Landing page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Quiz display pages (non-AJAX)
Route::get('/quiz/{id}', [QuizController::class, 'show'])->name('quiz.show');

// AJAX API Routes with CSRF protection
Route::middleware(['web'])->group(function () {
    // User management
    Route::post('/api/login', [HomeController::class, 'login'])->name('api.login');
    Route::get('/api/user', [HomeController::class, 'getCurrentUser'])->name('api.user');
    Route::post('/api/logout', [HomeController::class, 'logout'])->name('api.logout');
    
    // Quiz information
    Route::get('/api/quizzes', [QuizController::class, 'list'])->name('api.quizzes.list');
    Route::get('/api/quiz/{id}', [QuizController::class, 'getQuiz'])->name('api.quiz.get');
    Route::get('/api/quiz/{id}/leaderboard', [QuizController::class, 'leaderboard'])->name('api.quiz.leaderboard');
    Route::get('/api/quiz/{id}/results', [QuizController::class, 'userResults'])->name('api.quiz.results');
    
    // Quiz session management
    Route::post('/api/quiz-session/start', [QuizSessionController::class, 'start'])->name('api.session.start');
    Route::get('/api/quiz-session/{sessionId}/question', [QuizSessionController::class, 'getCurrentQuestion'])->name('api.session.question');
    Route::post('/api/quiz-session/{sessionId}/answer', [QuizSessionController::class, 'submitAnswer'])->name('api.session.answer');
    Route::post('/api/quiz-session/{sessionId}/skip', [QuizSessionController::class, 'skipQuestion'])->name('api.session.skip');
    Route::post('/api/quiz-session/{sessionId}/next', [QuizSessionController::class, 'nextQuestion'])->name('api.session.next');
    Route::get('/api/quiz-session/{sessionId}/progress', [QuizSessionController::class, 'getProgress'])->name('api.session.progress');
    Route::get('/api/quiz-session/{sessionId}/results', [QuizSessionController::class, 'getResults'])->name('api.session.results');
});
