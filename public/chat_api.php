<?php
session_start();
require_once "../config/database.php";

$conversation_id = $_GET['conversation_id'] ?? 0;
$last_id = $_GET['last_id'] ?? 0;

$sql = "SELECT * FROM messages WHERE conversation_id = ? AND id > ? ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$conversation_id, $last_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["messages" => $messages]);