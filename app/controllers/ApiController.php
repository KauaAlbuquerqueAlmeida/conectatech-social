<?php
require_once '../config/database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ApiController {

    // Retorna posts com curtidas e comentários
    public static function getPostsJSON() {
        global $pdo;
        $query = "SELECT p.id, p.content, p.image, p.created_at, u.name,
                  (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS total_likes,
                  (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', u2.name, 'comment', c.comment, 'created_at', c.created_at))
                   FROM comments c
                   JOIN users u2 ON c.user_id = u2.id
                   WHERE c.post_id = p.id) AS comments
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  ORDER BY p.created_at DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($posts);
    }

    public static function addLike($user_id, $post_id) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT IGNORE INTO likes (user_id, post_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $post_id]);
    }

    public static function addComment($user_id, $post_id, $comment) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, $post_id, $comment]);
    }
}
?>