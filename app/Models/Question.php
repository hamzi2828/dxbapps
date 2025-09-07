<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'quiz_id',
        'question_text',
        'display_order',
        'points'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_order' => 'integer',
        'points' => 'integer'
    ];

    /**
     * Get the quiz that owns the question.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the options for the question.
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('display_order');
    }

    /**
     * Get the correct option for the question.
     */
    public function correctOption()
    {
        return $this->options()->where('is_correct', true)->first();
    }

    /**
     * Get user answers for this question.
     */
    public function userAnswers(): HasMany
    {
        return $this->hasMany(UserAnswer::class);
    }

    /**
     * Check if a given option is correct for this question.
     *
     * @param int $optionId
     * @return bool
     */
    public function isCorrectOption(int $optionId): bool
    {
        return $this->options()
            ->where('id', $optionId)
            ->where('is_correct', true)
            ->exists();
    }
}
