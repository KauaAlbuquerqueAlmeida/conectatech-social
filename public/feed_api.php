<!-- Nomes: Kauã de Albuquerque Almeida, Matheus Villar e Miguel Borges -->
<?php
require "config/database.php";
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        die("Erro: usuário não está logado.");
    }

    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = $_POST['message'] ?? null;

    if (!$receiver_id || !$message) {
        die("Erro: receiver_id ou message não enviados.");
    }

    try {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$sender_id, $receiver_id, $message]);
        echo "Mensagem salva com sucesso!";
    } catch (PDOException $e) {
        die("Erro ao salvar mensagem: " . $e->getMessage());
    }
}