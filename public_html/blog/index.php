<?php
require_once 'config/db.php';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Общее количество постов
$countStmt = $pdo->query("SELECT COUNT(*) FROM posts");
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

// Запрос постов с автором
$sql = "SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная - Блог</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'templates/header.php'; ?>
<div class="container">
    <h1>Все статьи</h1>
    <?php foreach ($posts as $post): ?>
        <div class="post-card">
            <?php if ($post['image']): ?>
                <img src="<?= htmlspecialchars($post['image']) ?>" alt="Изображение" class="post-img">
            <?php endif; ?>
            <h2><a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
            <div class="post-meta">Автор: <?= htmlspecialchars($post['username']) ?> | Дата: <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></div>
            <p><?= htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 200)) ?>...</p>
        </div>
    <?php endforeach; ?>

    <!-- Пагинация -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?>">&laquo; Предыдущая</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" <?= $i == $page ? 'class="active"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>">Следующая &raquo;</a>
        <?php endif; ?>
    </div>
</div>
<?php include 'templates/footer.php'; ?>
</body>
</html>