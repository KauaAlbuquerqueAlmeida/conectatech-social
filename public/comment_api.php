<!-- Nomes: Kauã de Albuquerque Almeida, Matheus Villar e Miguel Borges -->
<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../app/controllers/ApiController.php';

$user_id = $_SESSION['user_id'] ?? null;
$post_id = $_POST['post_id'] ?? null;
$comment = $_POST['comment'] ?? null;

if($user_id && $post_id && $comment){
    ApiController::addComment($user_id, $post_id, $comment);
}