<!-- Nomes: Kauã de Albuquerque Almeida, Matheus Villar e Miguel Borges -->
<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../config/database.php";

$conversation_id = $_GET['conversation_id'] ?? 0;
$last_id = $_GET['last_id'] ?? 0;

$sql = "SELECT * FROM messages WHERE conversation_id = ? AND id > ? ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$conversation_id, $last_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["messages" => $messages]);