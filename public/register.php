<!-- Nomes: Kauã de Albuquerque Almeida, Matheus Villar e Miguel Borges -->
<?php

require_once '../app/controllers/AuthController.php';

$mensagem = ""; // Variável para mostrar dentro da div

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (AuthController::register($name, $email, $password)) {
        header("Refresh: 1; url=index.php");
        $mensagem = "<p class='sucesso'>✅ Cadastro realizado com sucesso! Redirecionando...</p>";
    } else {
        $mensagem = "<p class='erro'>❌ Erro no cadastro. Tente novamente.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #8e44ad, #007bff);
            font-family: Arial, sans-serif;
        }

        /* Corrigido para usar a classe certa */
        .register-container {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.6);
            width: 300px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #fff;
        }

        label {
            display: block;
            text-align: left;
            color: #fff;
            margin: 10px 0 5px;
            font-size: 14px;
        }

        /* Corrigido seletor de inputs */
        input[type="text"], input[type="email"], input[type="password"] {
            width: 94%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            outline: none;
            font-size: 14px;
        }

        input::placeholder {
            color: #ddd;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        .mensagem {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .sucesso {
            color: #4CAF50;
            font-weight: bold;
        }

        .erro {
            color: #ff4f4f;
            font-weight: bold;
        }

        .register-link, .login-link {
            margin-top: 10px;
            display: block;
            color: #ddd;
            font-size: 14px;
            text-decoration: none;
        }

        .register-link:hover, .login-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        .register-container img {
            width: 150px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>

</head>
<body>
    <div class="register-container">
        <img src="../assets/imagens/Conectatechpng.png" alt="">
        <h2>Cadastro</h2>

        <?php if (!empty($mensagem)) echo "<div class='mensagem'>$mensagem</div>"; ?>

        <form method="POST">
            <label for="name">Nome</label>
            <input type="text" name="name" id="name" placeholder="Digite seu nome" required>

            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" placeholder="Digite seu e-mail" required>

            <label for="password">Senha</label>
            <input type="password" name="password" id="password" placeholder="Digite sua senha" required>

            <button type="submit">Cadastrar</button>
        </form>

        <a href="login.php" class="login-link">Já tem uma conta? Faça login</a>
    </div>
</body>
</html>
