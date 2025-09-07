<footer class="app-footer">
    <div class="footer-content">
        <p>&copy; {{ date('Y') }} Laravel MVC AJAX Quiz. Built with Laravel {{ app()->version() }}</p>
        <p class="footer-tech">PHP {{ phpversion() }} | MySQL | AJAX | Strict MVC Architecture</p>
    </div>
</footer>

<style>
.app-footer {
    margin-top: 50px;
    padding: 20px 0;
    text-align: center;
    color: rgba(255, 255, 255, 0.8);
}

.footer-content p {
    margin: 5px 0;
    font-size: 0.9rem;
}

.footer-tech {
    opacity: 0.7;
    font-size: 0.8rem;
}
</style>