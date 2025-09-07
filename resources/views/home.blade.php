<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel MVC AJAX Quiz</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .login-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .user-info {
            display: none;
        }
        
        .login-form {
            display: flex;
            gap: 15px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .form-group input {
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 16px;
            min-width: 200px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
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
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .quiz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .quiz-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            cursor: pointer;
        }
        
        .quiz-card:hover {
            transform: translateY(-5px);
        }
        
        .quiz-card h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        
        .quiz-card p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .quiz-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 15px;
        }
        
        .stats-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-item h4 {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-item p {
            color: #666;
            font-size: 0.9rem;
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
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .login-form {
                flex-direction: column;
            }
            
            .form-group input {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧠 Laravel MVC AJAX Quiz</h1>
            <p>Test your PHP knowledge with our interactive quiz system</p>
        </div>

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Login Section -->
        <div class="login-section">
            <div id="login-form">
                <h2>Welcome! Please enter your name to start</h2>
                <form class="login-form" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <input type="text" id="user-name" placeholder="Enter your name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Start Quiz</button>
                </form>
            </div>
            
            <div id="user-info" class="user-info">
                <h2>Welcome back, <span id="current-user-name"></span>!</h2>
                <button type="button" class="btn btn-secondary" onclick="handleLogout()">Logout</button>
            </div>
        </div>

        <!-- Quiz Section -->
        <div id="quiz-section" style="display: none;">
            <div class="quiz-grid">
                @foreach($quizzes as $quiz)
                <div class="quiz-card" onclick="startQuiz({{ $quiz->id }})">
                    <h3>{{ $quiz->title }}</h3>
                    <p>{{ $quiz->description }}</p>
                    <div class="quiz-meta">
                        <span>📝 {{ $quiz->questions_count }} Questions</span>
                        @if($quiz->time_limit)
                        <span>⏱️ {{ intval($quiz->time_limit / 60) }} min</span>
                        @endif
                    </div>
                    <button class="btn btn-primary" style="width: 100%;">Take Quiz</button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Statistics Section -->
        @if($statistics && $statistics->total_users > 0)
        <div class="stats-section">
            <h2>📊 Platform Statistics</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <h4>{{ $statistics->total_users }}</h4>
                    <p>Total Users</p>
                </div>
                <div class="stat-item">
                    <h4>{{ $statistics->total_attempts }}</h4>
                    <p>Quiz Attempts</p>
                </div>
                <div class="stat-item">
                    <h4>{{ number_format($statistics->avg_score ?? 0, 1) }}%</h4>
                    <p>Average Score</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="{{ asset('js/quiz.js') }}"></script>
    <script>
        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            checkCurrentUser();
        });

        async function checkCurrentUser() {
            try {
                const response = await fetch('/api/user');
                const data = await response.json();
                
                if (data.user) {
                    showUserInfo(data.user);
                } else {
                    showLoginForm();
                }
            } catch (error) {
                console.error('Error checking user:', error);
                showLoginForm();
            }
        }

        async function handleLogin(event) {
            event.preventDefault();
            const name = document.getElementById('user-name').value.trim();
            
            if (!name) return;

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name })
                });

                const data = await response.json();
                
                if (data.success) {
                    showUserInfo(data.user);
                }
            } catch (error) {
                console.error('Login error:', error);
                alert('Login failed. Please try again.');
            }
        }

        async function handleLogout() {
            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                showLoginForm();
            } catch (error) {
                console.error('Logout error:', error);
            }
        }

        function showUserInfo(user) {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('user-info').style.display = 'block';
            document.getElementById('quiz-section').style.display = 'block';
            document.getElementById('current-user-name').textContent = user.name;
        }

        function showLoginForm() {
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('user-info').style.display = 'none';
            document.getElementById('quiz-section').style.display = 'none';
            document.getElementById('user-name').value = '';
        }

        function startQuiz(quizId) {
            window.location.href = `/quiz/${quizId}`;
        }
    </script>
</body>
</html>