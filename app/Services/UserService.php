<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Find or create user by name
     *
     * @param string $name
     * @return array
     */
    public function findOrCreateUser(string $name): array
    {
        $user = User::firstOrCreate(
            ['name' => trim($name)],
            [
                'email' => $this->generateEmailFromName($name),
                'password' => Hash::make('password')
            ]
        );

        return [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        ];
    }

    /**
     * Get user by ID
     *
     * @param int $userId
     * @return array
     */
    public function getUserById(int $userId): array
    {
        $user = User::find($userId);

        return [
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name
            ] : null
        ];
    }

    /**
     * Generate email from user name
     *
     * @param string $name
     * @return string
     */
    private function generateEmailFromName(string $name): string
    {
        $cleanName = strtolower(str_replace([' ', '.', '@'], ['', '', ''], trim($name)));
        return $cleanName . '@quiz.local';
    }

    /**
     * Validate user name
     *
     * @param string $name
     * @return array
     */
    public function validateUserName(string $name): array
    {
        $errors = [];
        $name = trim($name);

        if (empty($name)) {
            $errors[] = 'Name is required';
        }

        if (strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters long';
        }

        if (strlen($name) > 255) {
            $errors[] = 'Name must be less than 255 characters';
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
            $errors[] = 'Name can only contain letters and spaces';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get user statistics
     *
     * @param int $userId
     * @return array
     */
    public function getUserStatistics(int $userId): array
    {
        $user = User::findOrFail($userId);

        // Get user's quiz statistics using SQL aggregates
        $stats = \DB::table('quiz_sessions')
            ->where('user_id', $userId)
            ->select([
                \DB::raw('COUNT(*) as total_attempts'),
                \DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_quizzes'),
                \DB::raw('AVG(CASE WHEN status = "completed" THEN
                    (SELECT SUM(ua.points_earned) * 100.0 / SUM(q.points)
                     FROM user_answers ua
                     JOIN questions q ON ua.question_id = q.id
                     WHERE ua.quiz_session_id = quiz_sessions.id)
                END) as avg_score_percentage'),
                \DB::raw('MAX(CASE WHEN status = "completed" THEN
                    (SELECT SUM(ua.points_earned) * 100.0 / SUM(q.points)
                     FROM user_answers ua
                     JOIN questions q ON ua.question_id = q.id
                     WHERE ua.quiz_session_id = quiz_sessions.id)
                END) as best_score_percentage')
            ])
            ->first();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name
            ],
            'statistics' => [
                'total_attempts' => (int) $stats->total_attempts,
                'completed_quizzes' => (int) $stats->completed_quizzes,
                'avg_score_percentage' => round($stats->avg_score_percentage ?? 0, 2),
                'best_score_percentage' => round($stats->best_score_percentage ?? 0, 2)
            ]
        ];
    }
}
