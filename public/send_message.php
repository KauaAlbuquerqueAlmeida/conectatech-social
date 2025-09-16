<!-- Nomes: Kauã de Albuquerque Almeida, Matheus Villar e Miguel Borges -->
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/database.php'; // garante caminho correto

$user_id = $_SESSION['user_id'] ?? null;
$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
$message = trim($_POST['message'] ?? '');

if (!$user_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

if ($conversation_id <= 0 || $message === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros inválidos. conversation_id e message são obrigatórios.']);
    exit;
}

try {
    // Verifica se a conversa existe
    $stmt = $pdo->prepare("SELECT id FROM conversations WHERE id = ?");
    $stmt->execute([$conversation_id]);
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        http_response_code(400);
        echo json_encode(['error' => 'Conversa não encontrada. Crie a conversa antes de enviar mensagens.']);
        exit;
    }

    // Insere a mensagem
    $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$conversation_id, $user_id, $message]);

    echo json_encode(['success' => true, 'message_id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    // Loga o erro no log do servidor para não expor detalhes ao usuário
    error_log("Erro send_message.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno ao salvar a mensagem. Verifique logs.']);
}