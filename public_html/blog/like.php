<?php
require_once 'config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Не авторизован']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = (int)$data['post_id'];
$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$user_id, $post_id]);
$existing = $stmt->fetch();

if ($existing) {
 
    $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    $liked = false;
} else {
    
    $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $post_id]);
    $liked = true;
}


$stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$stmt->execute([$post_id]);
$count = $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'count' => (int)$count
]);