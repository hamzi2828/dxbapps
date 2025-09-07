# Laravel MVC AJAX Quiz Application

A modern quiz application built with Laravel 10, featuring AJAX interactions, modular JavaScript architecture, and a Service Layer pattern. The application provides a seamless single-page quiz experience with PHP-related questions.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [Architecture Overview](#architecture-overview)
- [Database Schema](#database-schema)
- [API Documentation](#api-documentation)
- [JavaScript Modules](#javascript-modules)
- [Usage](#usage)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

## Features

- ✅ **AJAX-First Design**: All interactions after initial page load use AJAX
- ✅ **Modular JavaScript Architecture**: Organized into Core, Auth, and Quiz modules
- ✅ **Service Layer Pattern**: Clean separation of business logic
- ✅ **Skip Questions**: Users can skip questions with confirmation
- ✅ **Resume Quiz**: Continue from where you left off
- ✅ **Real-time Progress**: Visual progress bar and question counter
- ✅ **Normalized Database**: 3NF compliance with proper relationships
- ✅ **SQL Aggregates**: Statistics calculated using database aggregation
- ✅ **Session Management**: Secure session handling with minimal session data
- ✅ **Responsive Design**: Works on desktop and mobile devices

## Requirements

- **PHP**: 8.2+ (compatible with 7.3+)
- **Laravel**: 10.x
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **Web Server**: Apache/Nginx with URL rewriting
- **Composer**: Latest version
- **Node.js**: 16+ (for asset compilation, optional)

## Installation

### 1. Clone and Setup

```bash

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Database Configuration

Create your MySQL database:

```sql
CREATE DATABASE mvc_ajax_quiz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mvc_ajax_quiz
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Database Migration and Seeding

```bash
# Run migrations
php artisan migrate

# Seed the database with sample quiz data
php artisan db:seed --class=QuizSeeder
```

### 4. Run the Application

```bash
# Start the development server
php artisan serve --host=0.0.0.0 --port=8000

# Or use Laravel Valet/Herd for local development
# The application will be available at http://localhost:8000
```

## Project Structure

```
mvc-ajax-quiz/
├── app/
│   ├── Http/Controllers/
│   │   ├── HomeController.php          # Landing page and auth
│   │   ├── QuizController.php          # Quiz display and stats
│   │   └── QuizSessionController.php   # Quiz taking functionality
│   ├── Models/
│   │   ├── Quiz.php                    # Quiz model with aggregates
│   │   ├── Question.php                # Question model
│   │   ├── Option.php                  # Option model
│   │   ├── QuizSession.php             # Session tracking
│   │   └── UserAnswer.php              # User answers
│   └── Services/
│       ├── QuizService.php             # Quiz business logic
│       ├── QuizSessionService.php      # Session management
│       └── UserService.php             # User operations
├── database/
│   ├── migrations/                     # Database schema
│   └── seeders/
│       └── QuizSeeder.php              # Sample data
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php       # Main layout
│   │   ├── components/                 # Reusable components
│   │   ├── home.blade.php              # Landing page
│   │   └── quiz/show.blade.php         # Quiz interface
│   └── js/modules/
│       ├── core.js                     # Core utilities
│       ├── auth.js                     # Authentication
│       └── quiz.js                     # Quiz functionality
└── routes/
    └── web.php                         # Application routes
```

## Architecture Overview

### MVC Pattern with Service Layer

```
Browser (AJAX) → Routes → Controllers → Services → Models → Database
                     ↓
                  Views (Blade Templates)
```

### Design Principles

1. **Single Responsibility**: Each class has one clear purpose
2. **Dependency Injection**: Services injected into controllers
3. **Separation of Concerns**: Business logic in Services, not Controllers
4. **AJAX-First**: Seamless user experience after initial page load
5. **Modular JavaScript**: Organized into logical modules with clear APIs

### Key Components

- **Controllers**: Handle HTTP requests/responses, delegate to Services
- **Services**: Contain business logic and coordinate between Models
- **Models**: Handle database operations and relationships
- **JavaScript Modules**: Manage client-side functionality

## Database Schema

### Tables and Relationships

```sql
quizzes (1) ──┐
              ├─→ questions (N) ──┐
              │                   ├─→ options (N)
              │                   └─→ user_answers (N)
              └─→ quiz_sessions (N) ──┐
                                      └─→ user_answers (N)
```

### Table Structures

**quizzes**
- `id`, `title`, `description`, `time_limit`, `is_active`, `timestamps`

**questions**  
- `id`, `quiz_id`, `text`, `points`, `order`, `timestamps`

**options**
- `id`, `question_id`, `text`, `is_correct`, `timestamps`

**quiz_sessions**
- `id`, `quiz_id`, `user_id`, `current_question_index`, `total_score`, `status`, `timestamps`

**user_answers**
- `id`, `session_id`, `question_id`, `option_id`, `points_earned`, `timestamps`

### Indexes

- Foreign keys: Automatic indexes on all foreign key columns
- Performance indexes: `quiz_sessions(user_id, quiz_id)`, `user_answers(session_id)`

## API Documentation

### Authentication Endpoints

```http
POST /api/login
Content-Type: application/json

{
  "name": "John Doe"
}

Response: {
  "success": true,
  "user": { "id": 1, "name": "John Doe" },
  "message": "Login successful"
}
```

```http
POST /api/logout

Response: {
  "success": true,
  "message": "Logged out successfully"
}
```

### Quiz Endpoints

```http
POST /api/quiz-session/start
{
  "quiz_id": 1
}

Response: {
  "success": true,
  "session_id": 123,
  "message": "Quiz started successfully"
}
```

```http
GET /api/quiz-session/{sessionId}/question

Response: {
  "question": {
    "id": 1,
    "text": "What does PHP stand for?",
    "points": 1,
    "options": [...]
  },
  "current_index": 0,
  "total_questions": 10,
  "already_answered": null,
  "completed": false
}
```

```http
POST /api/quiz-session/{sessionId}/answer
{
  "option_id": 4
}

Response: {
  "success": true,
  "is_correct": true,
  "points_earned": 1,
  "correct_option_id": 4
}
```

```http
POST /api/quiz-session/{sessionId}/skip

Response: {
  "success": true,
  "completed": false,
  "message": "Question skipped"
}
```

## JavaScript Modules

### QuizCore Module
- HTTP request wrapper with error handling
- UI utilities (alerts, loading states, modals)
- Storage helpers (localStorage wrapper)
- Global event handling and CSRF protection

### QuizAuth Module
- User authentication state management
- Login/logout functionality
- UI state switching between logged-in/logged-out
- Session persistence

### QuizSession Module
- Quiz taking functionality
- Question display and option selection
- Answer submission and result display
- Skip question functionality
- Progress tracking and final results

### Module Communication

```javascript
// Modules communicate through:
// 1. Shared global namespace (window.QuizCore, etc.)
// 2. Custom events (ajax-success, ajax-error)
// 3. Direct method calls

// Example: Auth module listens for login success
document.addEventListener('ajax-success', (event) => {
    const form = event.target;
    if (form.classList.contains('login-form')) {
        QuizAuth.setCurrentUser(event.detail.user);
    }
});
```

## Usage

### Taking a Quiz

1. **Login**: Enter your name on the homepage
2. **Start Quiz**: Click "Start Quiz" button
3. **Answer Questions**: Select an option and click "Submit Answer"
4. **Skip Questions**: Click "Skip Question" if unsure (0 points awarded)
5. **View Results**: See your final score and statistics

### Administrative Tasks

```bash
# Create a new quiz (extend QuizSeeder)
php artisan db:seed --class=QuizSeeder

# Check application logs
tail -f storage/logs/laravel.log

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Troubleshooting

### Common Issues

**Database Connection Error**
```bash
# Check database credentials in .env
# Ensure MySQL is running
sudo systemctl status mysql

# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

**Migration Errors**
```bash
# Reset database (WARNING: Destroys data)
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

**CSRF Token Issues**
- Ensure CSRF meta tag exists in layout
- Check browser console for JavaScript errors
- Clear browser cache and cookies

**AJAX Requests Failing**
- Check browser Network tab for error details
- Verify routes in `routes/web.php`
- Check Laravel logs in `storage/logs/laravel.log`

### Debug Mode

Enable debug mode in `.env`:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Performance Optimization

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Enable opcache in php.ini
opcache.enable=1
opcache.memory_consumption=128
```

## Contributing

### Development Workflow

1. Follow PSR-12 coding standards
2. Write unit tests for new features
3. Use meaningful commit messages
4. Update documentation for API changes

### Code Style

```bash
# Install PHP CS Fixer
composer require --dev friendsofphp/php-cs-fixer

# Fix code style
./vendor/bin/php-cs-fixer fix
```

### Testing

```bash
# Run tests
php artisan test

# Generate coverage report
php artisan test --coverage
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions:
- Check the troubleshooting section above
- Review Laravel documentation: https://laravel.com/docs
- Check application logs in `storage/logs/laravel.log`
