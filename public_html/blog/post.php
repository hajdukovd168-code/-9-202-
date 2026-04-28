<?php
require_once 'config/db.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Получаем пост
$stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if (!$post) {
    die('Пост не найден');
}

// Получаем комментарии
$commentStmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY created_at ASC");
$commentStmt->execute([$post_id]);
$comments = $commentStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'templates/header.php'; ?>
<div class="container">
    <article class="post-full">
        <?php if ($post['image']): ?>
            <img src="<?= htmlspecialchars($post['image']) ?>" class="post-img-full">
        <?php endif; ?>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-meta">Автор: <?= htmlspecialchars($post['username']) ?> | Дата: <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></div>
        <div class="post-content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>
    </article>

    <section class="comments">
        <h3>Комментарии (<?= count($comments) ?>)</h3>
        <div id="comments-list">
            <?php foreach ($comments as $comment): ?>
                <div class="comment" data-id="<?= $comment['id'] ?>">
                    <strong><?= htmlspecialchars($comment['username']) ?></strong> <small><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></small>
                    <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <button class="delete-comment" data-id="<?= $comment['id'] ?>">Удалить</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="add-comment">
                <h4>Оставить комментарий</h4>
                <textarea id="comment-text" rows="3" placeholder="Ваш комментарий..."></textarea>
                <button id="submit-comment">Отправить</button>
                <div id="comment-message"></div>
            </div>
        <?php else: ?>
            <p>Чтобы оставить комментарий, <a href="login.php">войдите</a>.</p>
        <?php endif; ?>
    </section>
</div>
<?php include 'templates/footer.php'; ?>
<script>
    const postId = <?= $post_id ?>; const isAdmin = <?= isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'true' : 'false' ?>;
</script>
<script>
    const postId = <?= $post_id ?>;
</script>
</body>
</html>