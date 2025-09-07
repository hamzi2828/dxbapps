<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create PHP Quiz
        $quiz = Quiz::create([
            'title' => 'PHP Programming Quiz',
            'description' => 'Test your knowledge of PHP programming language, syntax, and best practices.',
            'time_limit' => 1200, // 20 minutes
            'is_active' => true
        ]);

        $this->createPHPQuestions($quiz);
    }

    /**
     * Create PHP questions - exactly 10 questions with 4 options each
     */
    private function createPHPQuestions(Quiz $quiz): void
    {
        $questions = [
            [
                'text' => 'What does PHP stand for?',
                'options' => [
                    ['text' => 'Personal Home Page', 'correct' => false],
                    ['text' => 'PHP: Hypertext Preprocessor', 'correct' => true],
                    ['text' => 'Private Home Page', 'correct' => false],
                    ['text' => 'Professional Hypertext Processor', 'correct' => false]
                ],
                'points' => 1
            ],
            [
                'text' => 'Which of the following is the correct way to start a PHP block?',
                'options' => [
                    ['text' => '<?php', 'correct' => true],
                    ['text' => '<php>', 'correct' => false],
                    ['text' => '<?', 'correct' => false],
                    ['text' => '<script language="php">', 'correct' => false]
                ],
                'points' => 1
            ],
            [
                'text' => 'How do you declare a variable in PHP?',
                'options' => [
                    ['text' => 'var myVariable;', 'correct' => false],
                    ['text' => '$myVariable;', 'correct' => true],
                    ['text' => 'declare myVariable;', 'correct' => false],
                    ['text' => 'variable myVariable;', 'correct' => false]
                ],
                'points' => 1
            ],
            [
                'text' => 'Which function is used to output text in PHP?',
                'options' => [
                    ['text' => 'print()', 'correct' => false],
                    ['text' => 'write()', 'correct' => false],
                    ['text' => 'echo', 'correct' => true],
                    ['text' => 'display()', 'correct' => false]
                ],
                'points' => 1
            ],
            [
                'text' => 'What is the correct way to create an array in PHP?',
                'options' => [
                    ['text' => '$array = array(1, 2, 3);', 'correct' => true],
                    ['text' => '$array = {1, 2, 3};', 'correct' => false],
                    ['text' => '$array = (1, 2, 3);', 'correct' => false],
                    ['text' => '$array = list(1, 2, 3);', 'correct' => false]
                ],
                'points' => 2
            ],
            [
                'text' => 'Which superglobal variable is used to collect form data sent with POST method?',
                'options' => [
                    ['text' => '$_GET', 'correct' => false],
                    ['text' => '$_POST', 'correct' => true],
                    ['text' => '$_REQUEST', 'correct' => false],
                    ['text' => '$_FORM', 'correct' => false]
                ],
                'points' => 1
            ],
            [
                'text' => 'What is the difference between == and === in PHP?',
                'options' => [
                    ['text' => 'No difference', 'correct' => false],
                    ['text' => '== checks value only, === checks value and type', 'correct' => true],
                    ['text' => '=== is faster than ==', 'correct' => false],
                    ['text' => '== checks type only, === checks value', 'correct' => false]
                ],
                'points' => 2
            ],
            [
                'text' => 'Which PHP function is used to connect to a MySQL database?',
                'options' => [
                    ['text' => 'mysql_connect()', 'correct' => false],
                    ['text' => 'mysqli_connect()', 'correct' => true],
                    ['text' => 'db_connect()', 'correct' => false],
                    ['text' => 'connect_mysql()', 'correct' => false]
                ],
                'points' => 2
            ],
            [
                'text' => 'What is the correct way to add a comment in PHP?',
                'options' => [
                    ['text' => '<!-- This is a comment -->', 'correct' => false],
                    ['text' => '// This is a comment', 'correct' => true],
                    ['text' => '# This is a comment', 'correct' => false],
                    ['text' => '/* This is a comment', 'correct' => false]
                ],
                'points' => 1
            ],
            [
                'text' => 'Which of the following is used to handle errors in PHP?',
                'options' => [
                    ['text' => 'try-catch', 'correct' => true],
                    ['text' => 'if-else', 'correct' => false],
                    ['text' => 'switch-case', 'correct' => false],
                    ['text' => 'do-while', 'correct' => false]
                ],
                'points' => 2
            ]
        ];

        $this->createQuestions($quiz, $questions);
    }

    /**
     * Helper method to create questions with options
     */
    private function createQuestions(Quiz $quiz, array $questions): void
    {
        foreach ($questions as $index => $q) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $q['text'],
                'display_order' => $index + 1,
                'points' => $q['points'] ?? 1
            ]);

            foreach ($q['options'] as $optIndex => $opt) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['correct'],
                    'display_order' => $optIndex + 1
                ]);
            }
        }
    }
}