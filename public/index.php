<?php
session_start();
require_once '../app/controllers/ApiController.php';

$loggedIn = isset($_SESSION['users']);
$email = $loggedIn ? $_SESSION['email'] : null;
$name = $loggedIn ? $_SESSION['name'] : null;

$friends = [];

if ($loggedIn) {
    try {
        $host = 'localhost';
        $dbname = 'nome_do_banco'; // troque pelo seu
        $user = 'root';            // seu usuário MySQL
        $pass = '';                // sua senha MySQL
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT u.name, u.email 
                               FROM friends f
                               JOIN users u ON f.friend_email = u.email
                               WHERE f.user_email = :email");
        $stmt->execute(['email' => $email]);
        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Erro na conexão com o banco: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>ConectaTech Social</title>
<style>
    body {
        font-family: 'Helvetica', Arial, sans-serif;
        background-color: #121212;
        color: #f1f1f1;
        margin: 0;
        padding: 0;
        display: flex;
    }

    header {
        background-color: #1f1f1f;
        padding: 15px;
        text-align: center;
        font-weight: bold;
        flex-basis: 100%;
    }

    header img {
        height: 50px;
    }

    .sidebar {
        width: 220px;
        background-color: #1f1f1f;
        padding: 15px;
        height: 100vh;
        box-sizing: border-box;
    }

    .sidebar h3 {
        margin-top: 0;
        color: #00d8ff;
    }

    .sidebar img {
        width: 50px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .sidebar p, .sidebar a {
        color: #ddd;
        text-decoration: none;
        margin: 5px 0;
        display: block;
    }

    .sidebar a:hover { color: #00d8ff; }

    #main {
        flex-grow: 1;
        padding: 20px;
        max-width: 800px;
    }

    #feed { margin-top: 20px; }

    .post {
        background-color: #1e1e1e;
        padding: 15px;
        margin: 10px 0;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }

    .post strong { color: #00d8ff; font-size: 1.1em; }
    .post small { color: #aaa; }

    .comments { margin-top: 10px; padding-left: 15px; border-left: 2px solid #333; }
    .comments p { margin: 5px 0; }

    input[type=text], textarea {
        width: calc(100% - 100px);
        padding: 8px;
        border-radius: 5px;
        border: none;
        background-color: #2a2a2a;
        color: #f1f1f1;
        margin-bottom: 5px;
    }

    button {
        padding: 8px 12px;
        margin-left: 5px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        background-color: #00d8ff;
        color: #121212;
        font-weight: bold;
    }

    #new-post {
        display: none;
        background-color: #1e1e1e;
        padding: 15px;
        margin: 20px 0;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }

    #show-post-btn { text-align: center; margin-bottom: 20px; }

</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="../assets/imagens/Conectatechpequenopng.png" alt="ConectaTech Logo">
    <h3>Perfil</h3>
    <?php if($loggedIn): ?>
        <p>Nome: <?php echo htmlspecialchars($user_name); ?></p>
        <p>E-mail: <?php echo htmlspecialchars($user_email); ?></p>
        <a href="logout.php">Sair</a>
    <?php else: ?>
        <p><a href="login.php">Login</a></p>
        <p><a href="register.php">Registrar</a></p>
    <?php endif; ?>

    <h3>Amigos</h3>
    <?php if($loggedIn): ?>
        <?php if(count($friends) > 0): ?>
            <?php foreach($friends as $f): ?>
                <p><?php echo htmlspecialchars($f['name']); ?> (<?php echo htmlspecialchars($f['email']); ?>)</p>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhum amigo adicionado ainda.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Faça login para ver seus amigos</p>
    <?php endif; ?>
</div>

<!-- Conteúdo principal -->
<div id="main">

<?php if($loggedIn): ?>
    <div id="show-post-btn">
        <button onclick="toggleNewPost()">Criar Novo Post</button>
    </div>

    <div id="new-post">
        <h3>Criar Post</h3>
        <textarea id="post-content" placeholder="O que você quer compartilhar?" rows="3"></textarea>
        <button onclick="createPost()">Publicar</button>
    </div>
<?php else: ?>
    <div style="text-align:center; margin:20px;">
        <p>Faça <a href="login.php" style="color:#00d8ff;">login</a> para publicar e interagir.</p>
    </div>
<?php endif; ?>

<div id="feed"></div>
</div>

<script>
// Mostrar/ocultar nova postagem
function toggleNewPost() {
    const box = document.getElementById('new-post');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

// Long Polling do feed
function fetchFeed() {
    fetch('feed_api.php')
    .then(response => response.json())
    .then(posts => {
        const feedDiv = document.getElementById('feed');
        feedDiv.innerHTML = '';

        posts.forEach(post => {
            let postDiv = document.createElement('div');
            postDiv.classList.add('post');

            let comments = post.comments ? JSON.parse(post.comments) : [];
            let commentHTML = '';
            comments.forEach(c => {
                commentHTML += `<p><strong>${c.name}:</strong> ${c.comment}</p>`;
            });

            let interactionHTML = '';
            <?php if($loggedIn): ?>
                interactionHTML = `
                    <button onclick="likePost(${post.id})">Curtir</button>
                    <div class="comments">
                        ${commentHTML}
                        <input type="text" id="comment_${post.id}" placeholder="Escreva um comentário">
                        <button onclick="commentPost(${post.id})">Comentar</button>
                    </div>
                `;
            <?php else: ?>
                interactionHTML = `<p>Faça login para curtir e comentar.</p>`;
            <?php endif; ?>

            postDiv.innerHTML = `
                <strong>${post.name}</strong> <small>(${post.created_at})</small>
                <p>${post.content}</p>
                <p>Curtidas: ${post.total_likes} | Comentários: ${comments.length}</p>
                ${interactionHTML}
            `;
            feedDiv.appendChild(postDiv);
        });
    })
    .finally(() => setTimeout(fetchFeed, 3000));
}

fetchFeed();

function likePost(postId) {
    fetch('like_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `post_id=${postId}`
    });
}

function commentPost(postId) {
    const input = document.getElementById(`comment_${postId}`);
    const comment = input.value;
    if(!comment.trim()) return;

    fetch('comment_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `post_id=${postId}&comment=${encodeURIComponent(comment)}`
    });
    input.value = '';
}

function createPost() {
    const content = document.getElementById('post-content').value;
    if(!content.trim()) return;

    fetch('create_post_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `content=${encodeURIComponent(content)}`
    }).then(() => {
        document.getElementById('post-content').value = '';
        document.getElementById('new-post').style.display = 'none';
    });
}
</script>

</body>
</html>

