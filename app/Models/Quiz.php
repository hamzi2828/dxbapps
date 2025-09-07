<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Quiz extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'description',
        'time_limit',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'time_limit' => 'integer'
    ];

    /**
     * Get the questions for the quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('display_order');
    }

    /**
     * Get the quiz sessions for the quiz.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(QuizSession::class);
    }

    /**
     * Scope to get only active quizzes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get quiz statistics using SQL aggregates.
     *
     * @return object
     */
    public function getStatistics()
    {
        return DB::table('quiz_sessions')
            ->select([
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_attempts'),
                DB::raw('AVG(CASE WHEN status = "completed" THEN TIMESTAMPDIFF(SECOND, started_at, completed_at) END) as avg_completion_time')
            ])
            ->where('quiz_id', $this->id)
            ->first();
    }

    /**
     * Get total points available in quiz.
     *
     * @return int
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }
}
