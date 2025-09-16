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