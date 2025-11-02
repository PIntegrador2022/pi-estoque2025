<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Garante que o nome do usuário está definido na sessão
if (!isset($_SESSION['nome'])) {
    // Busca o nome do usuário no banco de dados, caso não esteja na sessão
    $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario_logado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_logado) {
        $_SESSION['nome'] = $usuario_logado['nome']; // Salva o nome na sessão
    } else {
        // Redireciona para a página de login se o usuário não for encontrado
        session_destroy();
        header("Location: index.php");
        exit;
    }
}

$nivel_acesso = $_SESSION['nivel_acesso'];

// Buscar o total de usuários
$stmt = $pdo->query("SELECT COUNT(*) AS total_usuarios FROM usuarios");
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total_usuarios'];

// Buscar o total de produtos
$stmt = $pdo->query("SELECT COUNT(*) AS total_produtos FROM produtos");
$total_produtos = $stmt->fetch(PDO::FETCH_ASSOC)['total_produtos'];

// Buscar o valor total dos produtos
$stmt = $pdo->query("SELECT SUM(preco * quantidade) AS valor_total_produtos FROM produtos");
$valor_total_produtos = $stmt->fetch(PDO::FETCH_ASSOC)['valor_total_produtos'];
$valor_total_produtos = number_format($valor_total_produtos, 2, ',', '.');

// Consulta para produtos com estoque baixo
$stmt = $pdo->query("SELECT * FROM produtos WHERE quantidade <= estoque_minimo");
$produtos_baixo_estoque = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_baixo_estoque = count($produtos_baixo_estoque);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Seu CSS personalizado (mantém seu style.css) -->
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
                    <!-- Botão de menu (móvel) -->
                    <button class="btn btn-outline-dark d-md-none me-2" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <img src="https://bluefocus.com.br/sites/default/files/styles/medium/public/estoque.png?itok=1yVi8VcO" alt="Logo" width="50">
                    </div>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="fw-bold"><?= htmlspecialchars($_SESSION['nome']) ?></span>
                        </a>
                        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="editar-perfil.php">Editar Perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                        </ul>
                    </div>
                </header>

                <h2 class="mb-4">Painel de Controle</h2>

                <!-- Cards do Dashboard -->
                <div class="row g-4">
                    <!-- Card: Total de Usuários -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Usuários</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_usuarios ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Total de Produtos -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total de Produtos</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_produtos ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Valor Total -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Valor Total dos Produtos</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?= $valor_total_produtos ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Estoque Baixo -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Estoque Baixo</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php if ($total_baixo_estoque > 0): ?>
                                                <?= $total_baixo_estoque ?> produto(s)
                                            <?php else: ?>
                                                Nenhum
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista detalhada de produtos com estoque baixo (opcional, abaixo dos cards) -->
                <?php if ($total_baixo_estoque > 0): ?>
                    <div class="row mt-4">
                        <div class="col">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Produtos com Estoque Baixo</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <?php foreach ($produtos_baixo_estoque as $produto): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= htmlspecialchars($produto['nome']) ?>
                                                <span class="badge bg-warning text-dark">
                                                    <?= $produto['quantidade'] ?> / <?= $produto['estoque_minimo'] ?>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS (opcional, mas necessário para dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>

</html>