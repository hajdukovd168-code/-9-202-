<header>
    <div class="logo"><a href="index.php">Мой блог</a></div>
    <nav>
        <ul class="nav-links">
            <li><a href="index.php">Главная</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/posts.php">Админ-панель</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Выйти (<?= htmlspecialchars($_SESSION['username'] ?? '') ?>)</a></li>
            <?php else: ?>
                <li><a href="login.php">Войти</a></li>
                <li><a href="register.php">Регистрация</a></li>
            <?php endif; ?>
        </ul>
        <div class="burger">☰</div>
    </nav>
    <script>
document.querySelector('.burger').addEventListener('click', function() {
    document.querySelector('.nav-links').classList.toggle('active');
});
</script>
</header>