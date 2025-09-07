<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Support\Facades\Session;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * Display quiz page
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $quiz = Quiz::with(['questions.options'])->findOrFail($id);
        
        // Check if user is logged in
        $userId = Session::get('user_id');
        if (!$userId) {
            return redirect()->route('home')->with('error', 'Please login to take the quiz');
        }

        return view('quiz.show', compact('quiz'));
    }

    /**
     * Get quiz details via AJAX
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuiz($id)
    {
        try {
            $result = $this->quizService->getQuizWithStatistics($id);
            
            return response()->json($result, 200, [
                'Content-Type' => 'application/json'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Quiz not found'
            ], 404, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    /**
     * Get list of available quizzes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        try {
            $result = $this->quizService->getQuizzesWithStats();
            
            return response()->json($result, 200, [
                'Content-Type' => 'application/json'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load quizzes'
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    /**
     * Get quiz leaderboard using SQL aggregates
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function leaderboard($id)
    {
        try {
            $leaderboard = $this->quizService->getQuizLeaderboard($id);
            
            return response()->json([
                'leaderboard' => $leaderboard
            ], 200, [
                'Content-Type' => 'application/json'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load leaderboard'
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    /**
     * Get user's previous results for a quiz
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userResults($id)
    {
        $userId = Session::get('user_id');
        
        if (!$userId) {
            return response()->json([
                'error' => 'User not logged in'
            ], 401, [
                'Content-Type' => 'application/json'
            ]);
        }

        try {
            $results = $this->quizService->getUserQuizResults($id, $userId);
            
            return response()->json([
                'results' => $results
            ], 200, [
                'Content-Type' => 'application/json'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load user results'
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }
}
