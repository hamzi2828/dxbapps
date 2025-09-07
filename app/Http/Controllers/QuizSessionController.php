<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizSessionService;
use Illuminate\Support\Facades\Session;

class QuizSessionController extends Controller
{
    protected $sessionService;

    public function __construct(QuizSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Start a new quiz session
     */
    public function start(Request $request)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['error' => 'User not logged in'], 401, ['Content-Type' => 'application/json']);
        }

        $request->validate(['quiz_id' => 'required|integer|exists:quizzes,id']);

        try {
            $result = $this->sessionService->startOrResumeSession($userId, $request->quiz_id);
            return response()->json($result, 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to start quiz session'], 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Get current question for the session
     */
    public function getCurrentQuestion($sessionId)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['error' => 'User not logged in'], 401, ['Content-Type' => 'application/json']);
        }

        try {
            $result = $this->sessionService->getCurrentQuestion($sessionId, $userId);
            return response()->json($result, 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load question'], 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Submit answer for current question
     */
    public function submitAnswer(Request $request, $sessionId)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['error' => 'User not logged in'], 401, ['Content-Type' => 'application/json']);
        }

        $request->validate(['option_id' => 'required|integer|exists:options,id']);

        try {
            $result = $this->sessionService->submitAnswer($sessionId, $userId, $request->option_id);
            return response()->json($result, 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to submit answer'], 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Skip current question
     */
    public function skipQuestion($sessionId)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['error' => 'User not logged in'], 401, ['Content-Type' => 'application/json']);
        }

        try {
            $result = $this->sessionService->skipQuestion($sessionId, $userId);
            return response()->json($result, 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to skip question'], 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Move to next question
     */
    public function nextQuestion($sessionId)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['error' => 'User not logged in'], 401, ['Content-Type' => 'application/json']);
        }

        try {
            $result = $this->sessionService->moveToNextQuestion($sessionId, $userId);
            return response()->json($result, 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to move to next question'], 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Get quiz session progress
     */
    public function getProgress($sessionId)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['error' => 'User not logged in'], 401, ['Content-Type' => 'application/json']);
        }

        try {
            $result = $this->sessionService->getSessionProgress($sessionId, $userId);
            return response()->json($result, 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get progress'], 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Get final results
     */
    public function getResults($sessionId)
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['error' => 'User not logged in'], 401, ['Content-Type' => 'application/json']);
        }

        try {
            $result = $this->sessionService->getSessionResults($sessionId, $userId);
            return response()->json($result, 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get results'], 500, ['Content-Type' => 'application/json']);
        }
    }
}