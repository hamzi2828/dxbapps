<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizSession;
use Illuminate\Support\Facades\DB;

class QuizService
{
    /**
     * Get all active quizzes with question counts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveQuizzes()
    {
        return Quiz::active()->withCount('questions')->get();
    }

    /**
     * Get quiz details with statistics
     *
     * @param int $quizId
     * @return array
     */
    public function getQuizWithStatistics(int $quizId): array
    {
        $quiz = Quiz::active()
            ->with(['questions' => function($query) {
                $query->orderBy('display_order');
            }])
            ->findOrFail($quizId);

        $stats = $this->getQuizStatistics($quizId);

        return [
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'time_limit' => $quiz->time_limit,
                'questions_count' => $quiz->questions->count(),
                'total_points' => $quiz->total_points,
                'statistics' => $stats
            ]
        ];
    }

    /**
     * Get quiz statistics using SQL aggregates
     *
     * @param int $quizId
     * @return object
     */
    public function getQuizStatistics(int $quizId)
    {
        return DB::table('quiz_sessions')
            ->where('quiz_id', $quizId)
            ->select([
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_attempts'),
                DB::raw('AVG(CASE WHEN status = "completed" THEN 
                    TIMESTAMPDIFF(SECOND, started_at, completed_at) 
                END) as avg_time_seconds')
            ])
            ->first();
    }

    /**
     * Get quiz leaderboard using SQL aggregates
     *
     * @param int $quizId
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getQuizLeaderboard(int $quizId, int $limit = 10)
    {
        return DB::table('quiz_sessions as qs')
            ->join('users as u', 'qs.user_id', '=', 'u.id')
            ->leftJoin('user_answers as ua', 'qs.id', '=', 'ua.quiz_session_id')
            ->where('qs.quiz_id', $quizId)
            ->where('qs.status', 'completed')
            ->groupBy('qs.id', 'u.id', 'u.name', 'qs.completed_at', 'qs.started_at')
            ->select([
                'u.name',
                DB::raw('SUM(ua.points_earned) as score'),
                DB::raw('COUNT(CASE WHEN ua.is_correct = 1 THEN 1 END) as correct_answers'),
                DB::raw('COUNT(ua.id) as total_answers'),
                DB::raw('TIMESTAMPDIFF(SECOND, qs.started_at, qs.completed_at) as time_taken'),
                DB::raw('qs.completed_at')
            ])
            ->orderByDesc('score')
            ->orderBy('time_taken')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's results for a specific quiz
     *
     * @param int $quizId
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getUserQuizResults(int $quizId, int $userId)
    {
        return DB::table('quiz_sessions as qs')
            ->leftJoin('user_answers as ua', 'qs.id', '=', 'ua.quiz_session_id')
            ->where('qs.quiz_id', $quizId)
            ->where('qs.user_id', $userId)
            ->where('qs.status', 'completed')
            ->groupBy('qs.id', 'qs.started_at', 'qs.completed_at')
            ->select([
                'qs.id as session_id',
                DB::raw('SUM(ua.points_earned) as score'),
                DB::raw('COUNT(CASE WHEN ua.is_correct = 1 THEN 1 END) as correct_answers'),
                DB::raw('COUNT(ua.id) as total_questions'),
                DB::raw('TIMESTAMPDIFF(SECOND, qs.started_at, qs.completed_at) as time_taken'),
                'qs.completed_at'
            ])
            ->orderByDesc('qs.completed_at')
            ->get();
    }

    /**
     * Get overall platform statistics
     *
     * @return object|null
     */
    public function getPlatformStatistics()
    {
        return DB::table('quiz_sessions as qs')
            ->join('quizzes as q', 'qs.quiz_id', '=', 'q.id')
            ->select([
                DB::raw('COUNT(DISTINCT qs.user_id) as total_users'),
                DB::raw('COUNT(qs.id) as total_attempts'),
                DB::raw('AVG(CASE WHEN qs.status = "completed" THEN 
                    (SELECT SUM(ua.points_earned) * 100.0 / SUM(qu.points)
                     FROM user_answers ua
                     JOIN questions qu ON ua.question_id = qu.id
                     WHERE ua.quiz_session_id = qs.id)
                END) as avg_score')
            ])
            ->first();
    }

    /**
     * Get quiz list with completion statistics
     *
     * @return array
     */
    public function getQuizzesWithStats(): array
    {
        $quizzes = Quiz::active()
            ->withCount('questions')
            ->get()
            ->map(function ($quiz) {
                $stats = DB::table('quiz_sessions')
                    ->where('quiz_id', $quiz->id)
                    ->select([
                        DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                        DB::raw('COUNT(*) as total_attempts'),
                        DB::raw('AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100 as completion_rate')
                    ])
                    ->first();

                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'description' => $quiz->description,
                    'questions_count' => $quiz->questions_count,
                    'time_limit' => $quiz->time_limit,
                    'statistics' => $stats
                ];
            });

        return ['quizzes' => $quizzes];
    }
}