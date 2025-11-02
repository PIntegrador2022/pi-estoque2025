<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Verifica se o ID do produto foi enviado via GET
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id_produto = $_GET['id'];

// Buscar os dados do produto pelo ID
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id_produto]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: dashboard.php");
    exit;
}

// Processa o formulário de edição de produto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'] ?? '';
    $quantidade = (int)$_POST['quantidade'];
    $preco = (float)$_POST['preco'];
    $estoque_minimo = (int)$_POST['estoque_minimo'];
    $categoria_id = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;

    // Atualiza os dados do produto no banco de dados
    $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, descricao = ?, quantidade = ?, preco = ?, estoque_minimo = ?, categoria_id = ? WHERE id = ?");
    $stmt->execute([$nome, $descricao, $quantidade, $preco, $estoque_minimo, $categoria_id, $id_produto]);

    // Redireciona para a listagem de produtos após a edição
    header("Location: listagem-produtos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

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
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php">Sair</a></li>
        </ul>
    </div>
</header>

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
                            <span class="fw-bold">Olá, <?= htmlspecialchars($_SESSION['nome']) ?></span>
                        </a>
                        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="editar-perfil.php">Editar Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                        </ul>
                    </div>
                </header>

                <h2 class="mb-4">Editar Produto</h2>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea name="descricao" id="descricao" class="form-control" rows="3"><?= htmlspecialchars($produto['descricao']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="quantidade" class="form-label">Quantidade <span class="text-danger">*</span></label>
                                    <input type="number" name="quantidade" id="quantidade" class="form-control" value="<?= $produto['quantidade'] ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="preco" class="form-label">Preço (R$) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="preco" id="preco" class="form-control" value="<?= $produto['preco'] ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="estoque_minimo" class="form-label">Estoque Mínimo <span class="text-danger">*</span></label>
                                    <input type="number" name="estoque_minimo" id="estoque_minimo" class="form-control" value="<?= $produto['estoque_minimo'] ?? 10 ?>" min="1" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="categoria_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                    <select name="categoria_id" id="categoria_id" class="form-select" required>
                                        <option value="">Selecione uma categoria</option>
                                        <?php
                                        $stmt = $pdo->query("SELECT * FROM categorias");
                                        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id'] ?>" <?= ($categoria['id'] == ($produto['categoria_id'] ?? '')) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            <a href="listagem-produtos.php" class="btn btn-secondary">Cancelar</a>
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