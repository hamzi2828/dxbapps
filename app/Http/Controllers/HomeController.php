<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QuizService;
use App\Services\UserService;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    protected $quizService;
    protected $userService;

    public function __construct(QuizService $quizService, UserService $userService)
    {
        $this->quizService = $quizService;
        $this->userService = $userService;
    }

    /**
     * Display the landing page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $quizzes = $this->quizService->getActiveQuizzes();
        $statistics = $this->quizService->getPlatformStatistics();

        return view('home', compact('quizzes', 'statistics'));
    }

    /**
     * Handle user login/registration
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:255|regex:/^[a-zA-Z\s]+$/'
        ]);

        try {
            $result = $this->userService->findOrCreateUser($request->name);
            
            // Store user_id in session (per requirement)
            Session::put('user_id', $result['user']['id']);

            return response()->json($result, 200, [
                'Content-Type' => 'application/json'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create user account'
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    /**
     * Get current user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentUser()
    {
        $userId = Session::get('user_id');
        
        if (!$userId) {
            return response()->json(['user' => null], 200, [
                'Content-Type' => 'application/json'
            ]);
        }

        $result = $this->userService->getUserById($userId);
        
        return response()->json($result, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Session::forget('user_id');
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
