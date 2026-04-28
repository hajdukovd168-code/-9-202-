<?php
require_once '../config/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit('Доступ запрещён');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) $id = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $imagePath = $post['image'] ?? '';

    // Загрузка файла
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
        $imagePath = 'uploads/' . $filename;
    }

    if ($id) {
        $sql = "UPDATE posts SET title=?, content=?, image=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $imagePath, $id]);
    } else {
        $sql = "INSERT INTO posts (title, content, image, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $imagePath, $_SESSION['user_id']]);
    }
    header('Location: posts.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Редактирование' : 'Новый пост' ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../templates/header.php'; ?>
<div class="container">
    <h2><?= $id ? 'Редактировать пост' : 'Добавить пост' ?></h2>
    <form method="post" enctype="multipart/form-data">
        <label>Заголовок</label>
        <input type="text" name="title" value="<?= htmlspecialchars($post['title'] ?? '') ?>" required>
        <label>Текст</label>
        <textarea name="content" rows="10" required><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
        <label>Изображение</label>
        <?php if ($post && $post['image']): ?>
            <img src="../<?= htmlspecialchars($post['image']) ?>" width="100"><br>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
        <button type="submit">Сохранить</button>
    </form>
</div>
<?php include '../templates/footer.php'; ?>
</body>
</html>