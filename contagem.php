<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$nivel_acesso = $_SESSION['nivel_acesso'];

// Processa a atualização da quantidade via GET (botões + e -)
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['acao']) && isset($_GET['id'])) {
    $produto_id = $_GET['id'];
    $acao = $_GET['acao']; // 'adicionar' ou 'remover'

    // Busca a quantidade atual do produto
    $stmt = $pdo->prepare("SELECT quantidade FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        $nova_quantidade = $produto['quantidade'];
        if ($acao == 'adicionar') {
            $nova_quantidade += 1;
        } elseif ($acao == 'remover' && $nova_quantidade > 0) {
            $nova_quantidade -= 1;
        }

        // Atualiza a quantidade no banco de dados
        $stmt = $pdo->prepare("UPDATE produtos SET quantidade = ? WHERE id = ?");
        $stmt->execute([$nova_quantidade, $produto_id]);

        // Redireciona para evitar reenvio do formulário ao recarregar a página
        header("Location: contagem.php");
        exit;
    }
}

// Buscar todos os produtos
$stmt = $pdo->query("SELECT * FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contagem de Produtos</title>
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

                <h2 class="mb-4">Contagem de Produtos</h2>

                <!-- Tabela de Produtos (Desktop) -->
                <div class="card shadow mb-4 d-none d-md-block">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ajuste Rápido de Estoque</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Quantidade Atual</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $produto): ?>
                                        <tr class="<?= $produto['quantidade'] <= ($produto['estoque_minimo'] ?? 10) ? 'table-warning' : '' ?>">
                                            <td><?= htmlspecialchars($produto['nome']) ?></td>
                                            <td><?= $produto['quantidade'] ?></td>
                                            <td>
                                                <a href="?acao=adicionar&id=<?= $produto['id'] ?>" class="btn btn-success btn-sm me-1">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <a href="?acao=remover&id=<?= $produto['id'] ?>" class="btn btn-danger btn-sm"
                                                    <?= $produto['quantidade'] <= 0 ? 'disabled' : '' ?>>
                                                    <i class="fas fa-minus"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Lista para Mobile -->
                <div class="d-md-none">
                    <div class="list-group">
                        <?php foreach ($produtos as $produto): ?>
                            <div class="list-group-item <?= $produto['quantidade'] <= ($produto['estoque_minimo'] ?? 10) ? 'bg-warning bg-opacity-25' : '' ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($produto['nome']) ?></h6>
                                        <small class="text-muted">Qtd: <?= $produto['quantidade'] ?></small>
                                    </div>
                                    <div>
                                        <a href="?acao=adicionar&id=<?= $produto['id'] ?>" class="btn btn-success btn-sm me-1">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <a href="?acao=remover&id=<?= $produto['id'] ?>" class="btn btn-danger btn-sm"
                                            <?= $produto['quantidade'] <= 0 ? 'disabled' : '' ?>>
                                            <i class="fas fa-minus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Atenção:</strong> Esta ação ajusta o estoque diretamente. Para manter um histórico completo, use as opções <em>Entrada</em> ou <em>Saída</em> de produtos.
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>

</html>