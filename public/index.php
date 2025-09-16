<?php
session_start();
require_once '../app/controllers/ApiController.php';

// Checa se usuário está logado
$loggedIn = isset($_SESSION['users']);
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
        }
        header {
            background-color: #1f1f1f;
            padding: 15px;
            text-align: center;
            font-size: 1.8em;
            font-weight: bold;
            color: #00d8ff;
        }
        #feed {
            max-width: 600px;
            margin: 20px auto;
        }
        .post {
            background-color: #1e1e1e;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        .post strong { color: #00d8ff; font-size: 1.1em; }
        .post small { color: #aaa; }
        .comments {
            margin-top: 10px;
            padding-left: 15px;
            border-left: 2px solid #333;
        }
        .comments p { margin: 5px 0; }
        input[type=text], textarea {
            width: calc(100% - 100px);
            padding: 8px;
            border-radius: 5px;
            border: none;
            background-color: #2a2a2a;
            color: #f1f1f1;
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
            margin: 20px auto;
            border-radius: 8px;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        #show-post-btn {
            display: block;
            margin: 20px auto;
            max-width: 600px;
            text-align: center;
        }
    </style>
</head>
<body>

<header>
    ConectaTech Social
    <h1>Login</h1>
    <?php if($loggedIn): ?>
        <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! <a href="logout.php" style="color:#00d8ff;">Sair</a></p>
    <?php else: ?>
        <p><a href="login.php" style="color:#00d8ff;">
        Login</a> | <a href="register.php" style="color:#00d8ff;">Registrar</a></p>
    <?php endif; ?>

</header>

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

<script>
// Função para mostrar/ocultar a caixa de post
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

            postDiv.innerHTML = `
                <strong>${post.name}</strong> <small>(${post.created_at})</small>
                <p>${post.content}</p>
                <p>Curtidas: ${post.total_likes} | Comentários: ${comments.length}</p>
                <button onclick="likePost(${post.id})">Curtir</button>
                <div class="comments">
                    ${commentHTML}
                    <input type="text" id="comment_${post.id}" placeholder="Escreva um comentário">
                    <button onclick="commentPost(${post.id})">Comentar</button>
                </div>
            `;
            feedDiv.appendChild(postDiv);
        });
    })
    .finally(() => setTimeout(fetchFeed, 3000));
}

fetchFeed();

// Likes
function likePost(postId) {
    fetch('like_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `post_id=${postId}`
    });
}

// Comentários
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

// Criar post
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
