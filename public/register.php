<?php
require_once '../app/controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (AuthController::register($name, $email, $password)) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro no cadastro.";
    }
}
?>

<form method="POST">
    Nome: <input type="text" name="name" required><br>
    E-mail: <input type="email" name="email" required><br>
    Senha: <input type="password" name="password" required><br>
    <button type="submit">Cadastrar</button>
</form>