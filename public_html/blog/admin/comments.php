<?php
require_once '../config/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit('Доступ запрещён');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delStmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $delStmt->execute([$_POST['delete_id']]);
    header('Location: comments.php');
    exit;
}

$stmt = $pdo->query("SELECT comments.*, users.username, posts.title as post_title 
                     FROM comments 
                     JOIN users ON comments.user_id = users.id 
                     JOIN posts ON comments.post_id = posts.id 
                     ORDER BY comments.created_at DESC");
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление комментариями</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../templates/header.php'; ?>
<div class="container">
    <h2>Комментарии (спам можно удалить)</h2>
    <table class="admin-table">
        <tr><th>ID</th><th>Автор</th><th>Пост</th><th>Текст</th><th>Дата</th><th>Действие</th></tr>
        <?php foreach ($comments as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['username']) ?></td>
            <td><?= htmlspecialchars($c['post_title']) ?></td>
            <td><?= nl2br(htmlspecialchars(mb_substr($c['content'],0,80))) ?></td>
            <td><?= date('d.m.Y H:i', strtotime($c['created_at'])) ?></td>
            <td>
                <form method="post" style="display:inline">
                    <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
                    <button type="submit" onclick="return confirm('Удалить?')">Удалить</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include '../templates/footer.php'; ?>
</body>
</html>