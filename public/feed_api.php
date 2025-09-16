<?php
session_start();
require_once '../app/controllers/ApiController.php';

// Simulação de usuário logado
$_SESSION['user_id'] = 1;
?>

<h1>Feed ConectaTech (Long Polling)</h1>
<div id="feed"></div>

<script>
// Função para Long Polling
function fetchFeed() {
    fetch('feed_api.php')
    .then(response => response.json())
    .then(posts => {
        let feedDiv = document.getElementById('feed');
        feedDiv.innerHTML = '';

        posts.forEach(post => {
            let postDiv = document.createElement('div');
            postDiv.style.border = '1px solid #ccc';
            postDiv.style.padding = '10px';
            postDiv.style.margin = '10px 0';

            let html = `<strong>${post.name}</strong> <small>(${post.created_at})</small>
                        <p>${post.content}</p>
                        <p>Curtidas: ${post.total_likes} | Comentários: ${post.comments ? JSON.parse(post.comments).length : 0}</p>
                        <button onclick="likePost(${post.id})">Curtir</button>
                        <div style="margin-top:10px; padding-left:20px;">`;

            if(post.comments){
                JSON.parse(post.comments).forEach(c => {
                    html += `<p><strong>${c.name}:</strong> ${c.comment}</p>`;
                });
            }

            html += `<input type="text" id="comment_${post.id}" placeholder="Escreva um comentário">
                     <button onclick="commentPost(${post.id})">Comentar</button>
                     </div>`;

            postDiv.innerHTML = html;
            feedDiv.appendChild(postDiv);
        });
    })
    .finally(() => {
        // Chamar novamente após 3 segundos
        setTimeout(fetchFeed, 3000);
    });
}

fetchFeed();

// Funções de interação
function likePost(postId){
    fetch('like_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `post_id=${postId}`
    });
}

function commentPost(postId){
    let input = document.getElementById(`comment_${postId}`);
    let comment = input.value;
    if(comment.trim() === '') return;

    fetch('comment_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `post_id=${postId}&comment=${encodeURIComponent(comment)}`
    });
    input.value = '';
}
</script>