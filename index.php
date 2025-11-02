<?php
session_start();
include_once 'db/connect.php';

// Processa o formulário de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = md5($_POST['senha']);

    // Verifica se o login e senha estão corretos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE login = ? AND senha = ?");
    $stmt->execute([$login, $senha]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Inicia a sessão e redireciona para o dashboard
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];
        header("Location: dashboard.php");
        exit;
    } else {
        // Define uma variável de erro para exibir a mensagem
        $erro = "Login ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Seu CSS personalizado (opcional, para ajustes finos) -->
    <link rel="stylesheet" href="css/login.css">
    <style>
  .bg-custom-dark {
    background-color: #34495e !important;
  }
</style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header text-center py-4 bg-custom-dark text-white">
                        <h4 class="mb-0"><i class="fas fa-boxes me-2"></i>Sistema de Estoque</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="login" class="form-label">Login</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="login" id="login" class="form-control" placeholder="Digite seu login" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Digite sua senha" required>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn bg-custom-dark btn-lg text-light">
                                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted small">
                        © <?= date('Y') ?> Sistema de Estoque
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>