<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $quiz->title }} - Laravel MVC AJAX Quiz</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .quiz-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            min-height: 500px;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .question-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .question-card {
            margin-bottom: 30px;
        }
        
        .question-text {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 25px;
            color: #333;
        }
        
        .options-list {
            list-style: none;
        }
        
        .option-item {
            margin-bottom: 15px;
        }
        
        .option-button {
            width: 100%;
            padding: 15px 20px;
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
        }
        
        .option-button:hover {
            border-color: #667eea;
            background: #f0f2ff;
        }
        
        .option-button.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .option-button.correct {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .option-button.incorrect {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
            transform: translateY(-2px);
        }
        
        .results-section {
            display: none;
            text-align: center;
        }
        
        .score-display {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0;
        }
        
        .results-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .stat-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .stat-item h4 {
            font-size: 1.5rem;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-item p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .quiz-container {
                padding: 20px;
            }
            
            .question-text {
                font-size: 1.1rem;
            }
            
            .controls {
                flex-direction: column;
                gap: 15px;
            }
            
            .results-stats {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $quiz->title }}</h1>
            <p>{{ $quiz->description }}</p>
        </div>

        <div class="quiz-container">
            <!-- Loading State -->
            <div id="loading" class="loading">
                <h3>Loading quiz...</h3>
                <p>Please wait while we prepare your questions.</p>
            </div>

            <!-- Quiz Interface -->
            <div id="quiz-interface" style="display: none;">
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                
                <div class="question-info">
                    <span id="question-counter">Question 1 of 10</span>
                    <span id="question-points">Points: 1</span>
                </div>
                
                <div class="question-card">
                    <div class="question-text" id="question-text"></div>
                    <ul class="options-list" id="options-list"></ul>
                </div>
                
                <div class="controls">
                    <a href="/" class="btn btn-secondary">← Back to Home</a>
                    <div>
                        <button id="skip-btn" class="btn btn-warning" onclick="skipQuestion()">Skip Question</button>
                        <button id="submit-btn" class="btn btn-primary" onclick="submitAnswer()" disabled>Submit Answer</button>
                        <button id="next-btn" class="btn btn-primary" onclick="nextQuestion()" style="display: none;">Next Question →</button>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div id="results-section" class="results-section">
                <h2>🎉 Quiz Completed!</h2>
                <div class="score-display" id="final-score">0%</div>
                
                <div class="results-stats" id="results-stats">
                    <div class="stat-item">
                        <h4 id="correct-answers">0</h4>
                        <p>Correct Answers</p>
                    </div>
                    <div class="stat-item">
                        <h4 id="total-points">0</h4>
                        <p>Points Earned</p>
                    </div>
                    <div class="stat-item">
                        <h4 id="time-taken">0</h4>
                        <p>Minutes</p>
                    </div>
                </div>
                
                <div class="controls">
                    <a href="/" class="btn btn-secondary">← Back to Home</a>
                    <button class="btn btn-primary" onclick="startNewQuiz()">Take Quiz Again</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentSession = null;
        let currentQuestion = null;
        let selectedOptionId = null;
        let isAnswerSubmitted = false;

        // Initialize quiz
        document.addEventListener('DOMContentLoaded', function() {
            startQuizSession();
        });

        async function startQuizSession() {
            try {
                const response = await fetch('/api/quiz-session/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ quiz_id: {{ $quiz->id }} })
                });

                const data = await response.json();
                
                if (response.ok) {
                    currentSession = data.session_id;
                    loadCurrentQuestion();
                } else {
                    showError('Failed to start quiz session. Please try again.');
                }
            } catch (error) {
                console.error('Error starting quiz:', error);
                showError('Connection error. Please check your internet connection.');
            }
        }

        async function loadCurrentQuestion() {
            if (!currentSession) return;

            try {
                const response = await fetch(`/api/quiz-session/${currentSession}/question`);
                const data = await response.json();
                
                if (data.completed) {
                    showResults();
                    return;
                }
                
                currentQuestion = data.question;
                displayQuestion(data);
                
                document.getElementById('loading').style.display = 'none';
                document.getElementById('quiz-interface').style.display = 'block';
                
            } catch (error) {
                console.error('Error loading question:', error);
                showError('Failed to load question.');
            }
        }

        function displayQuestion(data) {
            // Update progress
            const progress = ((data.current_index) / data.total_questions) * 100;
            document.getElementById('progress-fill').style.width = progress + '%';
            
            // Update question info
            document.getElementById('question-counter').textContent = 
                `Question ${data.current_index + 1} of ${data.total_questions}`;
            document.getElementById('question-points').textContent = 
                `Points: ${data.question.points}`;
            
            // Display question
            document.getElementById('question-text').textContent = data.question.text;
            
            // Display options
            const optionsList = document.getElementById('options-list');
            optionsList.innerHTML = '';
            
            data.question.options.forEach(option => {
                const li = document.createElement('li');
                li.className = 'option-item';
                
                const button = document.createElement('button');
                button.className = 'option-button';
                button.textContent = option.text;
                button.onclick = () => selectOption(option.id, button);
                
                // Check if this option was previously selected
                if (data.already_answered === option.id) {
                    button.classList.add('selected');
                    selectedOptionId = option.id;
                    document.getElementById('submit-btn').disabled = false;
                }
                
                li.appendChild(button);
                optionsList.appendChild(li);
            });
            
            // Reset state
            isAnswerSubmitted = false;
            document.getElementById('skip-btn').style.display = 'inline-block';
            document.getElementById('submit-btn').style.display = 'inline-block';
            document.getElementById('next-btn').style.display = 'none';
        }

        function selectOption(optionId, button) {
            if (isAnswerSubmitted) return;
            
            // Remove previous selection
            document.querySelectorAll('.option-button').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            // Select current option
            button.classList.add('selected');
            selectedOptionId = optionId;
            document.getElementById('submit-btn').disabled = false;
        }

        async function submitAnswer() {
            if (!selectedOptionId || isAnswerSubmitted) return;
            
            try {
                const response = await fetch(`/api/quiz-session/${currentSession}/answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ option_id: selectedOptionId })
                });

                const data = await response.json();
                
                if (response.ok) {
                    isAnswerSubmitted = true;
                    showAnswerResult(data);
                } else {
                    showError(data.error || 'Failed to submit answer.');
                }
            } catch (error) {
                console.error('Error submitting answer:', error);
                showError('Failed to submit answer.');
            }
        }

        function showAnswerResult(data) {
            // Highlight correct/incorrect answers
            document.querySelectorAll('.option-button').forEach(button => {
                const optionId = parseInt(button.getAttribute('data-option-id') || 
                    Array.from(document.querySelectorAll('.option-button')).indexOf(button) + 1);
                
                if (optionId === data.correct_option_id) {
                    button.classList.add('correct');
                } else if (optionId === selectedOptionId && !data.is_correct) {
                    button.classList.add('incorrect');
                }
                
                button.disabled = true;
            });
            
            // Update controls
            document.getElementById('skip-btn').style.display = 'none';
            document.getElementById('submit-btn').style.display = 'none';
            document.getElementById('next-btn').style.display = 'inline-block';
        }

        async function skipQuestion() {
            if (isAnswerSubmitted) {
                nextQuestion();
                return;
            }
            
            if (confirm('Are you sure you want to skip this question? You will receive 0 points.')) {
                try {
                    const response = await fetch(`/api/quiz-session/${currentSession}/skip`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const data = await response.json();
                    
                    if (data.completed) {
                        showResults();
                    } else {
                        loadCurrentQuestion();
                    }
                } catch (error) {
                    console.error('Error skipping question:', error);
                    showError('Failed to skip question.');
                }
            }
        }

        async function nextQuestion() {
            try {
                const response = await fetch(`/api/quiz-session/${currentSession}/next`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.completed) {
                    showResults();
                } else {
                    loadCurrentQuestion();
                }
            } catch (error) {
                console.error('Error moving to next question:', error);
                showError('Failed to load next question.');
            }
        }

        async function showResults() {
            try {
                const response = await fetch(`/api/quiz-session/${currentSession}/results`);
                const data = await response.json();
                
                // Hide quiz interface
                document.getElementById('quiz-interface').style.display = 'none';
                
                // Show results
                document.getElementById('results-section').style.display = 'block';
                document.getElementById('final-score').textContent = data.summary.score_percentage + '%';
                document.getElementById('correct-answers').textContent = data.summary.correct_answers;
                document.getElementById('total-points').textContent = data.summary.total_points_earned;
                document.getElementById('time-taken').textContent = data.summary.time_taken;
                
            } catch (error) {
                console.error('Error loading results:', error);
                showError('Failed to load results.');
            }
        }

        function startNewQuiz() {
            window.location.reload();
        }

        function showError(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-error';
            alertDiv.textContent = message;
            
            const container = document.querySelector('.quiz-container');
            container.insertBefore(alertDiv, container.firstChild);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>