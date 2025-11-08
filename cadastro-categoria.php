<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$nivel_acesso = $_SESSION['nivel_acesso'];

// Processa o formulário de cadastro de categoria
$mensagem = ''; // Variável para armazenar a mensagem
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);

    if (empty($nome)) {
        $mensagem = "<p style='color:red;'>O nome da categoria é obrigatório.</p>";
    } else {
        // Insere a categoria no banco de dados
        $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
        $stmt->execute([$nome]);

        $mensagem = "<p style='color:green;'>Categoria cadastrada com sucesso!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Categoria</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Seu CSS personalizado -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include_once 'includes/sidebar.php'; ?>

            <!-- Conteúdo Principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Cabeçalho -->
                <header class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <img src="https://bluefocus.com.br/sites/default/files/styles/medium/public/estoque.png?itok=1yVi8VcO" alt="Logo" width="50">
                    </div>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="fw-bold"><?= htmlspecialchars($_SESSION['nome']) ?></span>
                        </a>
                        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="editar-perfil.php">Editar Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                        </ul>
                    </div>
                </header>

                <!-- Mensagem de feedback -->
                <?php if (!empty($mensagem)): ?>
                    <div class="alert <?= strpos($mensagem, 'sucesso') !== false ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                        <?= $mensagem ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <h2 class="mb-4">Cadastro de Categoria</h2>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" required>
                                <div class="mb-2" style="color: d6d1d1;">Ex: Smartphone, notebook, acessórios, etc.</div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cadastrar Categoria
                            </button>
                            <a href="listagem-categorias.php" class="btn btn-secondary">Cancelar</a>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>