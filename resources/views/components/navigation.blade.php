<nav class="navbar">
    <div class="nav-container">
        <a href="{{ route('home') }}" class="nav-brand">
            <h1>🧠 Laravel Quiz</h1>
        </a>
        
        <div class="nav-links">
            <span id="nav-user-info" style="display: none;">
                Welcome, <span id="nav-user-name"></span>!
            </span>
        </div>
    </div>
</nav>

<style>
.navbar {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    margin-bottom: 30px;
    border-radius: 10px;
}

.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
}

.nav-brand h1 {
    color: white;
    font-size: 1.8rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    text-decoration: none;
}

.nav-links {
    color: white;
    opacity: 0.9;
}

@media (max-width: 768px) {
    .nav-container {
        padding: 10px 20px;
    }
    
    .nav-brand h1 {
        font-size: 1.4rem;
    }
}
</style>