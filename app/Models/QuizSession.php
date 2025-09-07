<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class QuizSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'quiz_id',
        'current_question_index',
        'started_at',
        'completed_at',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'current_question_index' => 'integer'
    ];

    /**
     * Get the user that owns the quiz session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the quiz for this session.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the user answers for this session.
     */
    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Scope for in-progress sessions.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for completed sessions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get the current question for the session.
     */
    public function getCurrentQuestion()
    {
        return $this->quiz->questions()
            ->skip($this->current_question_index)
            ->first();
    }

    /**
     * Get session progress using SQL aggregates.
     *
     * @return object
     */
    public function getProgress()
    {
        $totalQuestions = $this->quiz->questions()->count();
        
        return DB::table('user_answers')
            ->select([
                DB::raw('COUNT(*) as answered_count'),
                DB::raw('SUM(is_correct) as correct_count'),
                DB::raw('SUM(points_earned) as total_points')
            ])
            ->where('quiz_session_id', $this->id)
            ->first();
    }

    /**
     * Calculate score percentage using SQL.
     *
     * @return float
     */
    public function getScorePercentage(): float
    {
        $result = DB::table('user_answers as ua')
            ->join('questions as q', 'ua.question_id', '=', 'q.id')
            ->where('ua.quiz_session_id', $this->id)
            ->select([
                DB::raw('SUM(ua.points_earned) as earned_points'),
                DB::raw('SUM(q.points) as total_points')
            ])
            ->first();

        if (!$result || $result->total_points == 0) {
            return 0;
        }

        return ($result->earned_points / $result->total_points) * 100;
    }

    /**
     * Mark session as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }
}
