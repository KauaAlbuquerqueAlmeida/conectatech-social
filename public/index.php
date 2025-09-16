<?php
session_start();
require_once '../app/controllers/ApiController.php';

// Usuário logado (simulação)
$_SESSION['user_id'] = 1;
?>

<h1>Feed ConectaTech</h1>
<div id="feed"></div>

<script>
// Função de Long Polling
function fetchFeed() {
    fetch('feed_api.php')
    .then(response => response.json())
    .then(posts => {
        const feedDiv = document.getElementById('feed');
        feedDiv.innerHTML = '';

        posts.forEach(post => {
            let postDiv = document.createElement('div');
            postDiv.style.border = '1px solid #ccc';
            postDiv.style.padding = '10px';
            postDiv.style.margin = '10px 0';

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
                <div style="margin-top:10px; padding-left:20px;">
                    ${commentHTML}
                    <input type="text" id="comment_${post.id}" placeholder="Escreva um comentário">
                    <button onclick="commentPost(${post.id})">Comentar</button>
                </div>
            `;
            feedDiv.appendChild(postDiv);
        });
    })
    .finally(() => {
        setTimeout(fetchFeed, 3000); // Atualiza a cada 3 segundos
    });
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
</script>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>ConectaTech Social</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        header { background: #007bff; color: #fff; padding: 15px; text-align: center; }
        #new-post { background: #fff; padding: 15px; margin: 15px auto; max-width: 600px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        #feed { max-width: 600px; margin: 0 auto; }
        .post { background: #fff; padding: 15px; margin: 10px 0; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .post strong { font-size: 1.1em; }
        .post small { color: #555; }
        .comments { margin-top: 10px; padding-left: 15px; border-left: 2px solid #eee; }
        .comments p { margin: 5px 0; }
        input[type=text] { width: calc(100% - 80px); padding: 5px; }
        button { padding: 5px 10px; margin-left: 5px; cursor: pointer; }
    </style>
</head>
<body>

<header>
    <h1>ConectaTech Social</h1>
</header>

<div id="new-post">
    <h3>Criar novo post</h3>
    <textarea id="post-content" placeholder="O que você quer compartilhar?" rows="3" style="width:100%; padding:5px;"></textarea>
    <button onclick="createPost()">Publicar</button>
</div>

<div id="feed"></div>

<script>
// Long Polling
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

// Funções de interação
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

// Criar novo post
function createPost() {
    const content = document.getElementById('post-content').value;
    if(!content.trim()) return;

    fetch('create_post_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `content=${encodeURIComponent(content)}`
    }).then(() => {
        document.getElementById('post-content').value = '';
    });
}
</script>

</body>
</html>