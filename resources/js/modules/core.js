/**
 * Core Module - Base functionality for the quiz application
 * 
 * This module provides core utilities, AJAX handling, and common UI functions
 * following modular JavaScript architecture principles.
 */

// Core namespace
window.QuizCore = {
    // Configuration
    config: {
        apiBaseUrl: '/api',
        csrfToken: null,
        debugMode: false
    },

    // Initialize core functionality
    init() {
        this.setupCSRF();
        this.setupGlobalErrorHandling();
        this.setupEventListeners();
        console.log('QuizCore initialized');
    },

    // Setup CSRF token for all requests
    setupCSRF() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            this.config.csrfToken = token.getAttribute('content');
        }
    },

    // Setup global error handling
    setupGlobalErrorHandling() {
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled promise rejection:', event.reason);
            this.showError('An unexpected error occurred');
        });

        window.addEventListener('error', (event) => {
            console.error('Global error:', event.error);
            if (this.config.debugMode) {
                this.showError(`JavaScript Error: ${event.error.message}`);
            }
        });
    },

    // Setup global event listeners
    setupEventListeners() {
        // Handle all forms with data-ajax attribute
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (form.hasAttribute('data-ajax')) {
                event.preventDefault();
                this.handleFormSubmit(form);
            }
        });

        // Handle all buttons with data-action attribute
        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-action]');
            if (button) {
                const action = button.getAttribute('data-action');
                const handler = this.actionHandlers[action];
                if (handler) {
                    handler.call(this, button, event);
                }
            }
        });
    },

    // Action handlers for data-action attributes
    actionHandlers: {
        'show-modal': function(button) {
            const modalId = button.getAttribute('data-target');
            this.showModal(modalId);
        },
        'hide-modal': function(button) {
            const modal = button.closest('.modal');
            if (modal) {
                this.hideModal(modal.id);
            }
        },
        'toggle-theme': function(button) {
            this.toggleTheme();
        }
    },

    // HTTP Request wrapper with error handling
    async request(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        };

        const config = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, config);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || data.error || `HTTP ${response.status}`);
            }
            
            return data;
        } catch (error) {
            console.error('Request failed:', error);
            throw error;
        }
    },

    // Handle AJAX form submissions
    async handleFormSubmit(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const url = form.getAttribute('action') || window.location.pathname;
        const method = form.getAttribute('method') || 'POST';

        try {
            this.showLoading(form);
            const result = await this.request(url, {
                method: method.toUpperCase(),
                body: JSON.stringify(data)
            });

            // Dispatch custom event for form success
            form.dispatchEvent(new CustomEvent('ajax-success', { 
                detail: result 
            }));

            this.showSuccess('Form submitted successfully');
        } catch (error) {
            // Dispatch custom event for form error
            form.dispatchEvent(new CustomEvent('ajax-error', { 
                detail: error 
            }));

            this.showError(error.message);
        } finally {
            this.hideLoading(form);
        }
    },

    // UI Helper functions
    showError(message, container = null) {
        this.showAlert('error', message, container);
    },

    showSuccess(message, container = null) {
        this.showAlert('success', message, container);
    },

    showInfo(message, container = null) {
        this.showAlert('info', message, container);
    },

    showAlert(type, message, container = null) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
            <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
        `;
        
        const targetContainer = container || document.querySelector('.container');
        if (targetContainer) {
            targetContainer.insertBefore(alertDiv, targetContainer.firstChild);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    },

    showLoading(element) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        
        if (element) {
            element.classList.add('loading');
            const loadingSpinner = document.createElement('div');
            loadingSpinner.className = 'loading-spinner';
            loadingSpinner.innerHTML = '<div class="spinner"></div>';
            element.appendChild(loadingSpinner);
        }
    },

    hideLoading(element) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        
        if (element) {
            element.classList.remove('loading');
            const spinner = element.querySelector('.loading-spinner');
            if (spinner) {
                spinner.remove();
            }
        }
    },

    // Modal functions
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
        }
    },

    hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
    },

    // Theme functions
    toggleTheme() {
        const body = document.body;
        const isDark = body.classList.contains('dark-theme');
        
        if (isDark) {
            body.classList.remove('dark-theme');
            localStorage.setItem('theme', 'light');
        } else {
            body.classList.add('dark-theme');
            localStorage.setItem('theme', 'dark');
        }
    },

    // Storage helpers
    storage: {
        set(key, value) {
            try {
                localStorage.setItem(`quiz_${key}`, JSON.stringify(value));
            } catch (error) {
                console.warn('Failed to save to localStorage:', error);
            }
        },

        get(key) {
            try {
                const data = localStorage.getItem(`quiz_${key}`);
                return data ? JSON.parse(data) : null;
            } catch (error) {
                console.warn('Failed to load from localStorage:', error);
                return null;
            }
        },

        remove(key) {
            try {
                localStorage.removeItem(`quiz_${key}`);
            } catch (error) {
                console.warn('Failed to remove from localStorage:', error);
            }
        }
    },

    // Utility functions
    utils: {
        formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        },

        formatPercentage(value, decimals = 1) {
            return Math.round(value * Math.pow(10, decimals)) / Math.pow(10, decimals);
        },

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            }
        }
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    QuizCore.init();
    
    // Load saved theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
});

// CSS for loading states and spinners
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    .loading {
        position: relative;
        pointer-events: none;
        opacity: 0.7;
    }
    
    .loading-spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .alert-close {
        float: right;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: inherit;
        opacity: 0.7;
        margin-left: 10px;
    }
    
    .alert-close:hover {
        opacity: 1;
    }
`;
document.head.appendChild(styleSheet);