/**
 * Laravel MVC AJAX Quiz JavaScript
 * 
 * This file contains all AJAX functionality for the quiz application
 * following the requirement of minimal JS in a single file.
 */

// Namespace for quiz functions
const QuizApp = {
    currentSession: null,
    currentQuestion: null,
    selectedOptionId: null,
    isAnswerSubmitted: false,

    // Initialize CSRF token for all AJAX requests
    init() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            this.csrfToken = token.getAttribute('content');
        }
    },

    // Generic AJAX helper with error handling
    async makeRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const config = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, config);
            const contentType = response.headers.get('content-type');
            
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Invalid response format');
            }
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || data.error || 'Request failed');
            }
            
            return data;
        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    },

    // User authentication functions
    async loginUser(name) {
        return this.makeRequest('/api/login', {
            method: 'POST',
            body: JSON.stringify({ name })
        });
    },

    async getCurrentUser() {
        return this.makeRequest('/api/user');
    },

    async logoutUser() {
        return this.makeRequest('/api/logout', {
            method: 'POST'
        });
    },

    // Quiz management functions
    async getQuizList() {
        return this.makeRequest('/api/quizzes');
    },

    async getQuizDetails(quizId) {
        return this.makeRequest(`/api/quiz/${quizId}`);
    },

    async getQuizLeaderboard(quizId) {
        return this.makeRequest(`/api/quiz/${quizId}/leaderboard`);
    },

    async getUserResults(quizId) {
        return this.makeRequest(`/api/quiz/${quizId}/results`);
    },

    // Quiz session functions
    async startQuizSession(quizId) {
        const data = await this.makeRequest('/api/quiz-session/start', {
            method: 'POST',
            body: JSON.stringify({ quiz_id: quizId })
        });
        
        this.currentSession = data.session_id;
        return data;
    },

    async getCurrentQuestion() {
        if (!this.currentSession) {
            throw new Error('No active quiz session');
        }
        
        return this.makeRequest(`/api/quiz-session/${this.currentSession}/question`);
    },

    async submitAnswer(optionId) {
        if (!this.currentSession) {
            throw new Error('No active quiz session');
        }
        
        return this.makeRequest(`/api/quiz-session/${this.currentSession}/answer`, {
            method: 'POST',
            body: JSON.stringify({ option_id: optionId })
        });
    },

    async skipCurrentQuestion() {
        if (!this.currentSession) {
            throw new Error('No active quiz session');
        }
        
        return this.makeRequest(`/api/quiz-session/${this.currentSession}/skip`, {
            method: 'POST'
        });
    },

    async moveToNextQuestion() {
        if (!this.currentSession) {
            throw new Error('No active quiz session');
        }
        
        return this.makeRequest(`/api/quiz-session/${this.currentSession}/next`, {
            method: 'POST'
        });
    },

    async getSessionProgress() {
        if (!this.currentSession) {
            throw new Error('No active quiz session');
        }
        
        return this.makeRequest(`/api/quiz-session/${this.currentSession}/progress`);
    },

    async getSessionResults() {
        if (!this.currentSession) {
            throw new Error('No active quiz session');
        }
        
        return this.makeRequest(`/api/quiz-session/${this.currentSession}/results`);
    },

    // UI Helper functions
    showLoading(element, message = 'Loading...') {
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }
        
        element.innerHTML = `
            <div class="loading">
                <div class="loading-spinner"></div>
                <p>${message}</p>
            </div>
        `;
    },

    showError(message, container = null) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.innerHTML = `
            <strong>Error:</strong> ${message}
            <button onclick="this.parentElement.remove()" style="float: right;">&times;</button>
        `;
        
        const targetContainer = container || document.querySelector('.container');
        targetContainer.insertBefore(alertDiv, targetContainer.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    },

    showSuccess(message, container = null) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success';
        alertDiv.innerHTML = `
            <strong>Success:</strong> ${message}
            <button onclick="this.parentElement.remove()" style="float: right;">&times;</button>
        `;
        
        const targetContainer = container || document.querySelector('.container');
        targetContainer.insertBefore(alertDiv, targetContainer.firstChild);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 3000);
    },

    // Format time helper
    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    },

    // Format percentage helper
    formatPercentage(value) {
        return Math.round(value * 100) / 100;
    },

    // Validate form input
    validateInput(input, rules = {}) {
        const value = input.value.trim();
        const errors = [];

        if (rules.required && !value) {
            errors.push('This field is required');
        }

        if (rules.minLength && value.length < rules.minLength) {
            errors.push(`Minimum length is ${rules.minLength} characters`);
        }

        if (rules.maxLength && value.length > rules.maxLength) {
            errors.push(`Maximum length is ${rules.maxLength} characters`);
        }

        if (rules.pattern && !rules.pattern.test(value)) {
            errors.push('Invalid format');
        }

        return errors;
    },

    // Storage helpers for offline functionality (optional feature)
    saveToStorage(key, data) {
        try {
            localStorage.setItem(`quiz_${key}`, JSON.stringify(data));
        } catch (error) {
            console.warn('Failed to save to localStorage:', error);
        }
    },

    loadFromStorage(key) {
        try {
            const data = localStorage.getItem(`quiz_${key}`);
            return data ? JSON.parse(data) : null;
        } catch (error) {
            console.warn('Failed to load from localStorage:', error);
            return null;
        }
    },

    removeFromStorage(key) {
        try {
            localStorage.removeItem(`quiz_${key}`);
        } catch (error) {
            console.warn('Failed to remove from localStorage:', error);
        }
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    QuizApp.init();
});

// Export for global access (for backward compatibility)
window.QuizApp = QuizApp;