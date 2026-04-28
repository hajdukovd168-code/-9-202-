<?php


require_once 'config/db.php';

$limit = 5;                     
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;


$totalStmt = $pdo->query("SELECT COUNT(*) FROM posts");
$totalPosts = $totalStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);


$sql = "SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);


$likesData = [];
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    $postIds = array_column($posts, 'id');
    if (!empty($postIds)) {
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));
        $likeSql = "SELECT post_id FROM likes WHERE user_id = ? AND post_id IN ($placeholders)";
        $likeStmt = $pdo->prepare($likeSql);
        $params = array_merge([$userId], $postIds);
        $likeStmt->execute($params);
        $likedPosts = $likeStmt->fetchAll(PDO::FETCH_COLUMN);
        $likesData = array_flip($likedPosts); // для быстрого поиска
    }
}


$likesCount = [];
foreach ($posts as $post) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
    $countStmt->execute([$post['id']]);
    $likesCount[$post['id']] = (int)$countStmt->fetchColumn();
}
?>

<?php include 'templates/header.php'; ?>

<div class="container">
    <h1>Все статьи</h1>
    <link rel="stylesheet" href="css/style.css">

    <?php if (empty($posts)): ?>
        <p>Пока нет ни одной статьи. <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin/post_edit.php">Добавить первый пост</a>
        <?php endif; ?></p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                
                <?php if (!empty($post['image'])): ?>
                    <img src="<?= htmlspecialchars($post['image']) ?>" alt="Изображение к посту" class="post-img">
                <?php endif; ?>

                <h2>
                    <a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
                </h2>

                <div class="post-meta">
                    Автор: <?= htmlspecialchars($post['username']) ?> |
                    Дата: <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
                </div>

                >
                <p class="post-preview">
                    <?php
                    $plainText = strip_tags($post['content']);
                    $preview = mb_substr($plainText, 0, 200, 'UTF-8');
                    echo htmlspecialchars($preview) . (mb_strlen($plainText) > 200 ? '…' : '');
                    ?>
                </p>

                
                <div class="like-section" data-post-id="<?= $post['id'] ?>">
                    <?php
                    $userLiked = isset($likesData[$post['id']]);
                    $likeCount = $likesCount[$post['id']];
                    ?>
                    <button class="like-btn <?= $userLiked ? 'liked' : '' ?>" data-id="<?= $post['id'] ?>">
                        ❤️ <span class="like-count"><?= $likeCount ?></span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>

        
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>">&laquo; Предыдущая</a>
                <?php endif; ?>

                <?php
                
                $range = 2; 
                for ($i = 1; $i <= $totalPages; $i++):
                    if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)):
                ?>
                        <a href="?page=<?= $i ?>" <?= $i == $page ? 'class="active"' : '' ?>><?= $i ?></a>
                <?php
                    elseif ($i == $page - $range - 1 || $i == $page + $range + 1):
                        echo '<span class="dots">...</span>';
                    endif;
                endfor;
                ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>">Следующая &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>