<!-- Nomes: Kauã de Albuquerque Almeida, Matheus Villar e Miguel Borges -->
<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Simulação de login
$_SESSION['user_id'] = 1;  // usuário logado
$conversation_id = 1;      // conversa ativa
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="../css/enviarmensagem.css">
   
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">💬 Chat</div>
        <div id="chat-box" class="chat-box"></div>
        <div class="chat-input">
            <input type="text" id="message" placeholder="Digite sua mensagem...">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>

    <script>
    const conversationId = <?= $conversation_id ?>;
    const userId = <?= $_SESSION['user_id'] ?>;

    // Função de long polling para buscar mensagens
    function fetchMessages(lastId = 0) {
        fetch('chat_api.php?conversation_id=' + conversationId + '&last_id=' + lastId)
        .then(res => res.json())
        .then(data => {
            let chatBox = document.getElementById("chat-box");
            data.messages.forEach(m => {
                let div = document.createElement("div");
                div.classList.add("message");
                div.classList.add(m.sender_id == userId ? "sent" : "received");
                div.textContent = m.message;
                chatBox.appendChild(div);
                chatBox.scrollTop = chatBox.scrollHeight;
                lastId = m.id;
            });
            setTimeout(() => fetchMessages(lastId), 2000);
        })
        .catch(() => setTimeout(() => fetchMessages(lastId), 2000));
    }

    fetchMessages();

    // Enviar mensagem
    function sendMessage() {
        let input = document.getElementById("message");
        let msg = input.value;
        if(msg.trim() === "") return;

        fetch("send_message.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "conversation_id=" + conversationId + "&message=" + encodeURIComponent(msg)
        }).then(() => {
            input.value = "";
        });
    }
    </script>
</body>
</html>