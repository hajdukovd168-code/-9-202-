<?php
require_once 'config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = (int)$data['post_id'];
$content = trim($data['content']);

if (empty($content)) {
    echo json_encode(['success' => false, 'error' => 'Комментарий не может быть пустым']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO comments (content, user_id, post_id) VALUES (?, ?, ?)");
$result = $stmt->execute([$content, $_SESSION['user_id'], $post_id]);
if ($result) {
    $comment_id = $pdo->lastInsertId();
    // Получаем имя пользователя для ответа
    $userStmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $username = $userStmt->fetchColumn();
    echo json_encode([
        'success' => true,
        'comment' => [
            'id' => $comment_id,
            'username' => $username,
            'content' => $content,
            'created_at' => date('d.m.Y H:i:s')
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка БД']);
}