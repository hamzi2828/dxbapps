<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizSession;
use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\DB;

class QuizSessionService
{
    /**
     * Start or resume a quiz session
     *
     * @param int $userId
     * @param int $quizId
     * @return array
     */
    public function startOrResumeSession(int $userId, int $quizId): array
    {
        $quiz = Quiz::findOrFail($quizId);

        // Check if user has an active session
        $activeSession = QuizSession::where('user_id', $userId)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeSession) {
            return [
                'session_id' => $activeSession->id,
                'message' => 'Resuming existing quiz session',
                'current_question_index' => $activeSession->current_question_index
            ];
        }

        // Create new session
        $session = QuizSession::create([
            'user_id' => $userId,
            'quiz_id' => $quiz->id,
            'current_question_index' => 0,
            'status' => 'in_progress'
        ]);

        return [
            'session_id' => $session->id,
            'message' => 'Quiz session started',
            'current_question_index' => 0
        ];
    }

    /**
     * Get current question for session
     *
     * @param int $sessionId
     * @param int $userId
     * @return array
     */
    public function getCurrentQuestion(int $sessionId, int $userId): array
    {
        $session = $this->getActiveSession($sessionId, $userId);

        $question = $session->quiz->questions()
            ->with('options')
            ->skip($session->current_question_index)
            ->first();

        if (!$question) {
            return [
                'completed' => true,
                'message' => 'Quiz completed'
            ];
        }

        // Check if already answered
        $existingAnswer = UserAnswer::where('quiz_session_id', $session->id)
            ->where('question_id', $question->id)
            ->first();

        return [
            'question' => [
                'id' => $question->id,
                'text' => $question->question_text,
                'points' => $question->points,
                'options' => $question->options->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'text' => $option->option_text
                    ];
                })
            ],
            'current_index' => $session->current_question_index,
            'total_questions' => $session->quiz->questions->count(),
            'already_answered' => $existingAnswer ? $existingAnswer->option_id : null
        ];
    }

    /**
     * Submit answer for current question
     *
     * @param int $sessionId
     * @param int $userId
     * @param int $optionId
     * @return array
     */
    public function submitAnswer(int $sessionId, int $userId, int $optionId): array
    {
        $session = $this->getActiveSession($sessionId, $userId);
        $question = $session->getCurrentQuestion();

        if (!$question) {
            throw new \Exception('No current question');
        }

        $isCorrect = $question->isCorrectOption($optionId);
        $pointsEarned = $isCorrect ? $question->points : 0;

        // Check if answer already exists
        $existingAnswer = UserAnswer::where('quiz_session_id', $session->id)
            ->where('question_id', $question->id)
            ->first();

        if ($existingAnswer) {
            // Update existing answer
            $existingAnswer->update([
                'option_id' => $optionId,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
                'answered_at' => now()
            ]);
        } else {
            // Create new answer
            UserAnswer::create([
                'quiz_session_id' => $session->id,
                'question_id' => $question->id,
                'option_id' => $optionId,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned
            ]);
        }

        return [
            'success' => true,
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned,
            'correct_option_id' => $question->correctOption()->id
        ];
    }

    /**
     * Skip current question
     *
     * @param int $sessionId
     * @param int $userId
     * @return array
     */
    public function skipQuestion(int $sessionId, int $userId): array
    {
        $session = $this->getActiveSession($sessionId, $userId);
        $question = $session->getCurrentQuestion();

        if (!$question) {
            throw new \Exception('No current question');
        }

        // Check if answer already exists for this question
        $existingAnswer = UserAnswer::where('quiz_session_id', $session->id)
            ->where('question_id', $question->id)
            ->first();

        if (!$existingAnswer) {
            // Create a skipped answer record with 0 points
            UserAnswer::create([
                'quiz_session_id' => $session->id,
                'question_id' => $question->id,
                'option_id' => null, // null indicates skipped
                'is_correct' => false,
                'points_earned' => 0
            ]);
        }

        // Move to next question
        return $this->moveToNextQuestion($sessionId, $userId);
    }

    /**
     * Move to next question
     *
     * @param int $sessionId
     * @param int $userId
     * @return array
     */
    public function moveToNextQuestion(int $sessionId, int $userId): array
    {
        $session = $this->getActiveSession($sessionId, $userId);
        $totalQuestions = $session->quiz->questions->count();
        $nextIndex = $session->current_question_index + 1;

        if ($nextIndex >= $totalQuestions) {
            // Complete the quiz
            $session->markAsCompleted();
            
            $finalScore = $session->getProgress();
            $scorePercentage = $session->getScorePercentage();

            return [
                'completed' => true,
                'final_score' => $finalScore,
                'score_percentage' => round($scorePercentage, 2)
            ];
        }

        // Move to next question
        $session->update(['current_question_index' => $nextIndex]);

        return [
            'success' => true,
            'current_question_index' => $nextIndex
        ];
    }

    /**
     * Get quiz session progress
     *
     * @param int $sessionId
     * @param int $userId
     * @return array
     */
    public function getSessionProgress(int $sessionId, int $userId): array
    {
        $session = QuizSession::where('id', $sessionId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $progress = $session->getProgress();
        $totalQuestions = $session->quiz->questions->count();

        return [
            'current_question_index' => $session->current_question_index,
            'total_questions' => $totalQuestions,
            'answered_count' => $progress->answered_count,
            'correct_count' => $progress->correct_count,
            'total_points' => $progress->total_points,
            'status' => $session->status
        ];
    }

    /**
     * Get session results
     *
     * @param int $sessionId
     * @param int $userId
     * @return array
     */
    public function getSessionResults(int $sessionId, int $userId): array
    {
        $session = QuizSession::where('id', $sessionId)
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->with(['quiz', 'userAnswers.question', 'userAnswers.option'])
            ->firstOrFail();

        $results = DB::table('user_answers as ua')
            ->join('questions as q', 'ua.question_id', '=', 'q.id')
            ->leftJoin('options as o', 'ua.option_id', '=', 'o.id')
            ->where('ua.quiz_session_id', $session->id)
            ->select([
                'q.question_text',
                'q.points as max_points',
                DB::raw('CASE WHEN ua.option_id IS NULL THEN "Skipped" ELSE o.option_text END as selected_answer'),
                'ua.is_correct',
                'ua.points_earned',
                DB::raw('(SELECT option_text FROM options WHERE question_id = q.id AND is_correct = 1) as correct_answer')
            ])
            ->get();

        $summary = $session->getProgress();
        $scorePercentage = $session->getScorePercentage();

        return [
            'quiz_title' => $session->quiz->title,
            'summary' => [
                'total_questions' => $results->count(),
                'correct_answers' => $summary->correct_count,
                'total_points_earned' => $summary->total_points,
                'score_percentage' => round($scorePercentage, 2),
                'time_taken' => $session->started_at->diffInMinutes($session->completed_at)
            ],
            'detailed_results' => $results
        ];
    }

    /**
     * Get active session for user validation
     *
     * @param int $sessionId
     * @param int $userId
     * @return QuizSession
     */
    private function getActiveSession(int $sessionId, int $userId): QuizSession
    {
        return QuizSession::where('id', $sessionId)
            ->where('user_id', $userId)
            ->where('status', 'in_progress')
            ->firstOrFail();
    }
}