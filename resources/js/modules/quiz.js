/**
 * Quiz Module - Quiz taking functionality
 * 
 * Handles quiz sessions, questions, answers, and progress
 */

window.QuizSession = {
    currentSession: null,
    currentQuestion: null,
    selectedOptionId: null,
    isAnswerSubmitted: false,
    
    // Initialize quiz module
    init() {
        this.bindEvents();
        console.log('QuizSession initialized');
    },

    // Bind quiz events
    bindEvents() {
        // Start quiz button clicks
        document.addEventListener('click', (event) => {
            if (event.target.matches('.start-quiz-btn')) {
                event.preventDefault();
                const quizId = event.target.getAttribute('data-quiz-id');
                this.startQuiz(parseInt(quizId));
            }
            
            // Option selection
            if (event.target.matches('.option-button')) {
                event.preventDefault();
                const optionId = event.target.getAttribute('data-option-id');
                this.selectOption(parseInt(optionId), event.target);
            }
            
            // Submit answer
            if (event.target.matches('#submit-btn')) {
                event.preventDefault();
                this.submitAnswer();
            }
            
            // Skip question
            if (event.target.matches('#skip-btn')) {
                event.preventDefault();
                this.skipQuestion();
            }
            
            // Next question
            if (event.target.matches('#next-btn')) {
                event.preventDefault();
                this.nextQuestion();
            }
        });

        // Handle quiz form submissions
        document.addEventListener('submit', (event) => {
            if (event.target.classList.contains('quiz-start-form')) {
                event.preventDefault();
                const formData = new FormData(event.target);
                const quizId = formData.get('quiz_id');
                this.startQuiz(parseInt(quizId));
            }
        });
    },

    // Start quiz session
    async startQuiz(quizId) {
        if (!QuizAuth.isLoggedIn()) {
            QuizCore.showError('Please log in to take the quiz');
            return;
        }

        try {
            QuizCore.showLoading('#quiz-interface');
            
            const response = await QuizCore.request('/api/quiz-session/start', {
                method: 'POST',
                body: JSON.stringify({ quiz_id: quizId })
            });

            this.currentSession = response.session_id;
            QuizCore.showSuccess(response.message);
            
            await this.loadCurrentQuestion();
            
        } catch (error) {
            QuizCore.showError('Failed to start quiz: ' + error.message);
        } finally {
            QuizCore.hideLoading('#quiz-interface');
        }
    },

    // Load current question
    async loadCurrentQuestion() {
        if (!this.currentSession) {
            QuizCore.showError('No active quiz session');
            return;
        }

        try {
            const response = await QuizCore.request(`/api/quiz-session/${this.currentSession}/question`);
            
            if (response.completed) {
                await this.showResults();
                return;
            }
            
            this.currentQuestion = response.question;
            this.displayQuestion(response);
            
            // Show quiz interface
            const loadingDiv = document.getElementById('loading');
            const quizInterface = document.getElementById('quiz-interface');
            
            if (loadingDiv) loadingDiv.style.display = 'none';
            if (quizInterface) quizInterface.style.display = 'block';
            
        } catch (error) {
            QuizCore.showError('Failed to load question: ' + error.message);
        }
    },

    // Display question in UI
    displayQuestion(data) {
        // Update progress bar
        const progress = ((data.current_index) / data.total_questions) * 100;
        const progressFill = document.getElementById('progress-fill');
        if (progressFill) {
            progressFill.style.width = progress + '%';
        }
        
        // Update question info
        const questionCounter = document.getElementById('question-counter');
        const questionPoints = document.getElementById('question-points');
        
        if (questionCounter) {
            questionCounter.textContent = `Question ${data.current_index + 1} of ${data.total_questions}`;
        }
        
        if (questionPoints) {
            questionPoints.textContent = `Points: ${data.question.points}`;
        }
        
        // Display question text
        const questionText = document.getElementById('question-text');
        if (questionText) {
            questionText.textContent = data.question.text;
        }
        
        // Display options
        const optionsList = document.getElementById('options-list');
        if (optionsList) {
            optionsList.innerHTML = '';
            
            data.question.options.forEach((option, index) => {
                const li = document.createElement('li');
                li.className = 'option-item';
                
                const button = document.createElement('button');
                button.className = 'option-button';
                button.textContent = option.text;
                button.setAttribute('data-option-id', option.id);
                
                // Check if this option was previously selected
                if (data.already_answered === option.id) {
                    button.classList.add('selected');
                    this.selectedOptionId = option.id;
                    const submitBtn = document.getElementById('submit-btn');
                    if (submitBtn) submitBtn.disabled = false;
                }
                
                li.appendChild(button);
                optionsList.appendChild(li);
            });
        }
        
        // Reset state
        this.isAnswerSubmitted = false;
        this.updateControlsVisibility('question');
    },

    // Select an option
    selectOption(optionId, button) {
        if (this.isAnswerSubmitted) return;
        
        // Remove previous selection
        document.querySelectorAll('.option-button').forEach(btn => {
            btn.classList.remove('selected');
        });
        
        // Select current option
        button.classList.add('selected');
        this.selectedOptionId = optionId;
        
        // Enable submit button
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) submitBtn.disabled = false;
    },

    // Submit answer
    async submitAnswer() {
        if (!this.selectedOptionId || this.isAnswerSubmitted) return;
        
        try {
            const response = await QuizCore.request(`/api/quiz-session/${this.currentSession}/answer`, {
                method: 'POST',
                body: JSON.stringify({ option_id: this.selectedOptionId })
            });

            if (response.success) {
                this.isAnswerSubmitted = true;
                this.showAnswerResult(response);
            }
        } catch (error) {
            QuizCore.showError('Failed to submit answer: ' + error.message);
        }
    },

    // Skip current question
    async skipQuestion() {
        if (this.isAnswerSubmitted) {
            this.nextQuestion();
            return;
        }
        
        if (confirm('Are you sure you want to skip this question? You will receive 0 points.')) {
            try {
                const response = await QuizCore.request(`/api/quiz-session/${this.currentSession}/skip`, {
                    method: 'POST'
                });

                if (response.completed) {
                    await this.showResults();
                } else {
                    await this.loadCurrentQuestion();
                }
            } catch (error) {
                QuizCore.showError('Failed to skip question: ' + error.message);
            }
        }
    },

    // Move to next question
    async nextQuestion() {
        try {
            const response = await QuizCore.request(`/api/quiz-session/${this.currentSession}/next`, {
                method: 'POST'
            });

            if (response.completed) {
                await this.showResults();
            } else {
                await this.loadCurrentQuestion();
            }
        } catch (error) {
            QuizCore.showError('Failed to move to next question: ' + error.message);
        }
    },

    // Show answer result
    showAnswerResult(data) {
        // Highlight correct/incorrect answers
        document.querySelectorAll('.option-button').forEach((button, index) => {
            const optionId = parseInt(button.getAttribute('data-option-id'));
            
            if (optionId === data.correct_option_id) {
                button.classList.add('correct');
            } else if (optionId === this.selectedOptionId && !data.is_correct) {
                button.classList.add('incorrect');
            }
            
            button.disabled = true;
        });
        
        // Show result message
        const message = data.is_correct 
            ? `Correct! You earned ${data.points_earned} points.`
            : `Incorrect. The correct answer has been highlighted.`;
            
        QuizCore.showInfo(message);
        
        // Update controls
        this.updateControlsVisibility('answered');
    },

    // Show final results
    async showResults() {
        try {
            const response = await QuizCore.request(`/api/quiz-session/${this.currentSession}/results`);
            
            // Hide quiz interface
            const quizInterface = document.getElementById('quiz-interface');
            if (quizInterface) quizInterface.style.display = 'none';
            
            // Show results section
            const resultsSection = document.getElementById('results-section');
            if (resultsSection) {
                resultsSection.style.display = 'block';
                this.displayResults(response);
            }
            
        } catch (error) {
            QuizCore.showError('Failed to load results: ' + error.message);
        }
    },

    // Display results in UI
    displayResults(data) {
        const elements = {
            'final-score': data.summary.score_percentage + '%',
            'correct-answers': data.summary.correct_answers,
            'total-points': data.summary.total_points_earned,
            'time-taken': data.summary.time_taken
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) element.textContent = value;
        });
    },

    // Update control button visibility
    updateControlsVisibility(state) {
        const skipBtn = document.getElementById('skip-btn');
        const submitBtn = document.getElementById('submit-btn');
        const nextBtn = document.getElementById('next-btn');
        
        if (state === 'question') {
            if (skipBtn) skipBtn.style.display = 'inline-block';
            if (submitBtn) submitBtn.style.display = 'inline-block';
            if (nextBtn) nextBtn.style.display = 'none';
        } else if (state === 'answered') {
            if (skipBtn) skipBtn.style.display = 'none';
            if (submitBtn) submitBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'inline-block';
        }
    },

    // Start new quiz (reload page)
    startNewQuiz() {
        window.location.reload();
    },

    // Get session progress
    async getProgress() {
        if (!this.currentSession) return null;
        
        try {
            const response = await QuizCore.request(`/api/quiz-session/${this.currentSession}/progress`);
            return response;
        } catch (error) {
            console.error('Failed to get progress:', error);
            return null;
        }
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    QuizSession.init();
    
    // If on quiz page, start the quiz automatically
    const quizIdMeta = document.querySelector('meta[name="quiz-id"]');
    if (quizIdMeta) {
        const quizId = parseInt(quizIdMeta.getAttribute('content'));
        QuizSession.startQuiz(quizId);
    }
});