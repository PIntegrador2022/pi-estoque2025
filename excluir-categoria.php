<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$nivel_acesso = $_SESSION['nivel_acesso'];

// Verifica se o ID da categoria foi fornecido via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: listagem-categorias.php");
    exit;
}

$categoria_id = $_GET['id'];

// Busca os dados da categoria no banco de dados
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$categoria_id]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    echo "<p style='color:red;'>Categoria não encontrada.</p>";
    exit;
}

// Verifica se a categoria está associada a algum produto
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_produtos FROM produtos WHERE categoria_id = ?");
$stmt->execute([$categoria_id]);
$total_produtos = $stmt->fetch(PDO::FETCH_ASSOC)['total_produtos'];

if ($total_produtos > 0) {
    echo "<p style='color:red;'>Não é possível excluir esta categoria porque ela está associada a $total_produtos produto(s).</p>";
    echo "<a href='listagem-categorias.php'>Voltar</a>";
    exit;
}

// Exclui a categoria do banco de dados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->execute([$categoria_id]);

    header("Location: listagem-categorias.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Categoria</title>
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

                <h2 class="mb-4">Excluir Categoria</h2>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <?php if ($total_produtos > 0): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Não é possível excluir esta categoria!</strong><br>
                                Ela está associada a <strong><?= $total_produtos ?> produto(s)</strong>.
                            </div>
                            <a href="listagem-categorias.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar para a Listagem
                            </a>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Você está prestes a excluir a categoria <strong><?= htmlspecialchars($categoria['nome']) ?></strong>.<br>
                                Esta ação <strong>não pode ser desfeita</strong>.
                            </div>

                            <form method="POST" class="d-inline">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Confirmar Exclusão
                                </button>
                                <a href="listagem-categorias.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </form>
                        <?php endif; ?>
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