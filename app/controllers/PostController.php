<?php
require_once '../config/database.php';

class PostController {

    // Obter posts com paginação
    public static function getPosts($page = 1, $perPage = 5) {
        global $pdo;
        $offset = ($page - 1) * $perPage;
        $stmt = $pdo->prepare(
            "SELECT p.id, p.content, p.image, p.created_at, u.name, u.profile_image,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS total_likes,
            (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS total_comments
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
            LIMIT :offset, :perPage"
        );
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Adicionar like
    public static function addLike($user_id, $post_id) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $post_id]);
    }

    // Adicionar comentário
    public static function addComment($user_id, $post_id, $comment) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, $post_id, $comment]);
    }

    // Obter comentários de um post
    public static function getComments($post_id) {
        global $pdo;
        $stmt = $pdo->prepare(
            "SELECT c.comment, c.created_at, u.name 
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at ASC"
        );
        $stmt->execute([$post_id]);
        return $stmt->fetchAll();
    }
}
?>