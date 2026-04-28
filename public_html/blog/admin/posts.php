<?php
require_once '../config/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Доступ запрещён');
}
$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление постами</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../templates/header.php'; ?>
<div class="container">
    <h2>Управление постами</h2>
    <a href="post_edit.php" class="btn">+ Добавить пост</a>
    <table class="admin-table">
        <tr><th>ID</th><th>Заголовок</th><th>Автор</th><th>Дата</th><th>Действия</th></tr>
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= $post['id'] ?></td>
            <td><?= htmlspecialchars($post['title']) ?></td>
            <td><?= htmlspecialchars($post['username']) ?></td>
            <td><?= date('d.m.Y', strtotime($post['created_at'])) ?></td>
            <td>
                <a href="post_edit.php?id=<?= $post['id'] ?>">Редактировать</a>
                <a href="post_delete.php?id=<?= $post['id'] ?>" onclick="return confirm('Удалить пост и все комментарии?')">Удалить</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include '../templates/footer.php'; ?>
</body>
</html>