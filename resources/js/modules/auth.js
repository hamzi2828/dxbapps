/**
 * Auth Module - User authentication functionality
 * 
 * Handles user login, logout, and session management
 */

window.QuizAuth = {
    currentUser: null,
    
    // Initialize auth module
    init() {
        this.checkCurrentUser();
        this.bindEvents();
        console.log('QuizAuth initialized');
    },

    // Bind authentication events
    bindEvents() {
        // Login form submission
        document.addEventListener('ajax-success', (event) => {
            const form = event.target;
            if (form.classList.contains('login-form')) {
                const userData = event.detail.user;
                if (userData) {
                    this.setCurrentUser(userData);
                    this.showUserInterface();
                }
            }
        });

        // Logout button click
        document.addEventListener('click', (event) => {
            if (event.target.matches('.logout-btn')) {
                event.preventDefault();
                this.logout();
            }
        });
    },

    // Check for current user session
    async checkCurrentUser() {
        try {
            const response = await QuizCore.request('/api/user');
            if (response.user) {
                this.setCurrentUser(response.user);
                this.showUserInterface();
            } else {
                this.showLoginInterface();
            }
        } catch (error) {
            console.error('Error checking user:', error);
            this.showLoginInterface();
        }
    },

    // Set current user
    setCurrentUser(user) {
        this.currentUser = user;
        QuizCore.storage.set('current_user', user);
        
        // Update navigation
        const navUserInfo = document.getElementById('nav-user-info');
        const navUserName = document.getElementById('nav-user-name');
        
        if (navUserInfo && navUserName) {
            navUserName.textContent = user.name;
            navUserInfo.style.display = 'block';
        }
    },

    // Clear current user
    clearCurrentUser() {
        this.currentUser = null;
        QuizCore.storage.remove('current_user');
        
        // Update navigation
        const navUserInfo = document.getElementById('nav-user-info');
        if (navUserInfo) {
            navUserInfo.style.display = 'none';
        }
    },

    // Login user
    async login(name) {
        try {
            const response = await QuizCore.request('/api/login', {
                method: 'POST',
                body: JSON.stringify({ name: name.trim() })
            });

            if (response.success && response.user) {
                this.setCurrentUser(response.user);
                this.showUserInterface();
                QuizCore.showSuccess(`Welcome back, ${response.user.name}!`);
                return true;
            } else {
                throw new Error('Login failed');
            }
        } catch (error) {
            QuizCore.showError('Login failed: ' + error.message);
            return false;
        }
    },

    // Logout user
    async logout() {
        try {
            await QuizCore.request('/api/logout', {
                method: 'POST'
            });

            this.clearCurrentUser();
            this.showLoginInterface();
            QuizCore.showSuccess('Logged out successfully');
            
            // Redirect to home if on quiz page
            if (window.location.pathname.includes('/quiz/')) {
                window.location.href = '/';
            }
        } catch (error) {
            QuizCore.showError('Logout failed: ' + error.message);
        }
    },

    // Show user interface elements
    showUserInterface() {
        const loginSection = document.getElementById('login-form');
        const userSection = document.getElementById('user-info');
        const quizSection = document.getElementById('quiz-section');
        const currentUserName = document.getElementById('current-user-name');

        if (loginSection) loginSection.style.display = 'none';
        if (userSection) userSection.style.display = 'block';
        if (quizSection) quizSection.style.display = 'block';
        
        if (currentUserName && this.currentUser) {
            currentUserName.textContent = this.currentUser.name;
        }
    },

    // Show login interface elements
    showLoginInterface() {
        const loginSection = document.getElementById('login-form');
        const userSection = document.getElementById('user-info');
        const quizSection = document.getElementById('quiz-section');

        if (loginSection) loginSection.style.display = 'block';
        if (userSection) userSection.style.display = 'none';
        if (quizSection) quizSection.style.display = 'none';
        
        // Clear login form
        const nameInput = document.getElementById('user-name');
        if (nameInput) nameInput.value = '';
    },

    // Check if user is logged in
    isLoggedIn() {
        return this.currentUser !== null;
    },

    // Get current user
    getCurrentUser() {
        return this.currentUser;
    },

    // Require authentication for actions
    requireAuth(callback) {
        if (this.isLoggedIn()) {
            return callback();
        } else {
            QuizCore.showError('Please log in to continue');
            this.showLoginInterface();
            return false;
        }
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    QuizAuth.init();
});