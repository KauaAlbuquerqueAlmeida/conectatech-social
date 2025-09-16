<!-- Nomes: Kauã de Albuquerque Almeida, Matheus Villar e Miguel Borges -->
<?php
session_start();
require_once '../app/controllers/ApiController.php';

$user_id = $_SESSION['user_id'] ?? null;
$post_id = $_POST['post_id'] ?? null;

if($user_id && $post_id){
    ApiController::addLike($user_id, $post_id);
}