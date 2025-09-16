<?php
session_start();
require_once "../config/database.php"; // Caminho correto para o seu arquivo de conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql); // Usando $pdo de database.php
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php");
            exit();
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #8e44ad, #007bff); /* Roxo -> Azul */
            font-family: Arial, sans-serif;
        }

        .login-container {
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

        input[type="email"], input[type="password"] {
            width: 100%;
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

        .erro {
            color: #ff4f4f;
            margin-bottom: 10px;
        }

        .register-link {
            margin-top: 15px;
            display: block;
            color: #ddd;
            font-size: 14px;
            text-decoration: none;
        }

        .register-link:hover {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($erro)) echo "<p class='erro'>$erro</p>"; ?>

        <form method="POST" action="">
            <label>Email:</label>
            <input type="email" name="email" placeholder="Digite seu email" required>

            <label>Senha:</label>
            <input type="password" name="password" placeholder="Digite sua senha" required>

            <button type="submit">Entrar</button>
        </form>

        <a href="register.php" class="register-link">Não tem conta? Cadastre-se</a>
    </div>

</body>
</html>