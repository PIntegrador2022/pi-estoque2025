<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$nivel_acesso = $_SESSION['nivel_acesso'];

// Funções de formatação
function formatarValorMonetario($valor)
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function formatarNumeroInteiro($numero)
{
    return number_format($numero, 0, ',', '.');
}

// Inicializa as variáveis de busca e filtro
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$filtro_estoque = isset($_GET['filtro_estoque']) ? $_GET['filtro_estoque'] : 'todos'; // Padrão: "todos"

// Consulta os produtos com base na busca e no filtro
if ($busca) {
    // Filtra por nome, ID ou categoria
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE (p.id = :id OR p.nome LIKE :nome OR c.nome LIKE :categoria)
        AND (:filtro_estoque = 'todos' OR p.quantidade <= p.estoque_minimo)
    ");
    $stmt->bindValue(':id', $busca, PDO::PARAM_INT);
    $stmt->bindValue(':nome', '%' . $busca . '%', PDO::PARAM_STR);
    $stmt->bindValue(':categoria', '%' . $busca . '%', PDO::PARAM_STR);
    $stmt->bindValue(':filtro_estoque', $filtro_estoque, PDO::PARAM_STR);
    $stmt->execute();
} else {
    // Se não houver busca, lista os produtos com base no filtro
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE :filtro_estoque = 'todos' OR p.quantidade <= p.estoque_minimo
    ");
    $stmt->bindValue(':filtro_estoque', $filtro_estoque, PDO::PARAM_STR);
    $stmt->execute();
}
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Produtos</title>
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

                <h2 class="mb-4">Listagem de Produtos</h2>

                <!-- Formulário de Busca e Filtro -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label for="busca" class="form-label">Buscar por nome, código ou categoria</label>
                                <input type="text" name="busca" id="busca" class="form-control" value="<?= htmlspecialchars($busca) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="filtro_estoque" class="form-label">Filtrar por Estoque</label>
                                <select name="filtro_estoque" id="filtro_estoque" class="form-select">
                                    <option value="todos" <?= $filtro_estoque === 'todos' ? 'selected' : '' ?>>Todos os Produtos</option>
                                    <option value="baixo" <?= $filtro_estoque === 'baixo' ? 'selected' : '' ?>>Estoque Baixo</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Aplicar</button>
                            </div>
                        </form>
                        <div class="mt-3">
                            <a href="exportar-produtos.php" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Exportar para Excel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Produtos (Desktop) -->
                <div class="card shadow mb-4 d-none d-md-block">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Produtos</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Categoria</th>
                                        <th>Quantidade</th>
                                        <th>Estoque Mínimo</th>
                                        <th>Preço</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $produto): ?>
                                        <tr class="<?= $produto['quantidade'] <= $produto['estoque_minimo'] ? 'table-warning' : '' ?>">
                                            <td><?= $produto['id'] ?></td>
                                            <td><?= htmlspecialchars($produto['nome']) ?></td>
                                            <td><?= htmlspecialchars($produto['descricao']) ?></td>
                                            <td><?= htmlspecialchars($produto['categoria_nome'] ?? 'Sem Categoria') ?></td>
                                            <td><?= formatarNumeroInteiro($produto['quantidade']) ?></td>
                                            <td><?= formatarNumeroInteiro($produto['estoque_minimo']) ?></td>
                                            <td><?= formatarValorMonetario($produto['preco']) ?></td>
                                            <td>
                                                <a href="editar-produto.php?id=<?= $produto['id'] ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="excluir-produto.php?id=<?= $produto['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                                    <i class="fas fa-trash"></i>
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
                            <div class="list-group-item <?= $produto['quantidade'] <= $produto['estoque_minimo'] ? 'bg-warning bg-opacity-25' : '' ?>">
                                <h6 class="mb-1"><?= htmlspecialchars($produto['nome']) ?></h6>
                                <small class="text-muted">
                                    Código: <?= $produto['id'] ?><br>
                                    Categoria: <?= htmlspecialchars($produto['categoria_nome'] ?? 'Sem Categoria') ?><br>
                                    Quantidade: <?= formatarNumeroInteiro($produto['quantidade']) ?> / <?= formatarNumeroInteiro($produto['estoque_minimo']) ?><br>
                                    Preço: <?= formatarValorMonetario($produto['preco']) ?>
                                </small>
                                <div class="mt-2">
                                    <a href="editar-produto.php?id=<?= $produto['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="excluir-produto.php?id=<?= $produto['id'] ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Tem certeza?')">
                                        Excluir
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
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