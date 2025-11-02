<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$nivel_acesso = $_SESSION['nivel_acesso'];

// Inicializa a variável de busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Consulta os produtos com base na busca
if ($busca) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id OR nome LIKE :nome");
    $stmt->bindValue(':id', $busca, PDO::PARAM_INT);
    $stmt->bindValue(':nome', '%' . $busca . '%', PDO::PARAM_STR);
    $stmt->execute();
} else {
    $stmt = $pdo->query("SELECT * FROM produtos");
}
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variável para armazenar a mensagem (não usada no fluxo AJAX, mas mantida para compatibilidade)
$mensagem = '';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrada de Produtos</title>
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

                <h2 class="mb-4">Entrada de Produtos</h2>

                <!-- Formulário de Busca -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-10">
                                <label for="busca" class="form-label">Buscar por nome ou código</label>
                                <input type="text" name="busca" id="busca" class="form-control" value="<?= htmlspecialchars($busca) ?>" placeholder="Digite o nome ou código do produto">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Buscar</button>
                            </div>
                        </form>
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
                                        <th>Quantidade Atual</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos as $produto): ?>
                                        <tr>
                                            <td><?= $produto['id'] ?></td>
                                            <td><?= htmlspecialchars($produto['nome']) ?></td>
                                            <td><?= htmlspecialchars($produto['descricao']) ?></td>
                                            <td><span class="qtd-atual"><?= $produto['quantidade'] ?></span></td>
                                            <td>
                                                <!-- Botão + rápido -->
                                                <button type="button" class="btn btn-success btn-sm me-2"
                                                        onclick="atualizarEstoque(<?= $produto['id'] ?>, 'adicionar', this)">
                                                    <i class="fas fa-plus"></i>
                                                </button>

                                                <!-- Formulário de entrada completa -->
                                                <form class="d-inline" id="form-entrada-<?= $produto['id'] ?>" 
                                                      onsubmit="registrarEntrada(<?= $produto['id'] ?>); return false;">
                                                    <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                                                    <input type="number" name="quantidade_adicional" class="form-control form-control-sm d-inline" style="width:80px;" placeholder="Qtd" min="1" required>
                                                    <input type="date" name="data_movimentacao" class="form-control form-control-sm d-inline" style="width:120px;" value="<?= date('Y-m-d') ?>" required>
                                                    <button type="submit" class="btn btn-primary btn-sm mt-1">
                                                        Registrar
                                                    </button>
                                                </form>
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
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($produto['nome']) ?></h6>
                                        <small class="text-muted">
                                            Código: <?= $produto['id'] ?><br>
                                            Quantidade: <span class="qtd-atual"><?= $produto['quantidade'] ?></span>
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm"
                                            onclick="atualizarEstoque(<?= $produto['id'] ?>, 'adicionar', this)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <form class="mt-2" id="form-entrada-<?= $produto['id'] ?>" 
                                      onsubmit="registrarEntrada(<?= $produto['id'] ?>); return false;">
                                    <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                                    <div class="input-group mb-2">
                                        <input type="number" name="quantidade_adicional" class="form-control" placeholder="Quantidade" min="1" required>
                                        <input type="date" name="data_movimentacao" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                        <button class="btn btn-primary" type="submit">Registrar</button>
                                    </div>
                                </form>
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

    <!-- Scripts de Atualização em Tempo Real -->
    <script>
    function atualizarEstoque(produtoId, acao, botao) {
        botao.disabled = true;
        const iconeOriginal = botao.innerHTML;
        botao.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch('atualizar-estoque-ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `produto_id=${produtoId}&acao=${acao}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert('Erro: ' + data.erro);
            } else {
                const container = botao.closest('tr, .list-group-item');
                if (container) {
                    container.querySelectorAll('.qtd-atual').forEach(el => el.textContent = data.quantidade);
                }
            }
        })
        .catch(() => alert('Erro de conexão'))
        .finally(() => {
            botao.disabled = false;
            botao.innerHTML = iconeOriginal;
        });
    }

    function registrarEntrada(produtoId) {
        const form = document.getElementById('form-entrada-' + produtoId);
        const quantidadeInput = form.querySelector('[name="quantidade_adicional"]');
        const dataInput = form.querySelector('[name="data_movimentacao"]');

        const quantidade = parseInt(quantidadeInput.value);
        const data = dataInput.value;

        if (isNaN(quantidade) || quantidade <= 0) {
            alert('Quantidade inválida');
            return;
        }
        if (!data) {
            alert('Selecione uma data');
            return;
        }

        fetch('registrar-entrada-ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `produto_id=${produtoId}&quantidade_adicional=${quantidade}&data_movimentacao=${encodeURIComponent(data)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert('Erro: ' + data.erro);
            } else {
                // Atualiza a quantidade na tela
                const container = form.closest('tr, .list-group-item');
                if (container) {
                    container.querySelectorAll('.qtd-atual').forEach(el => el.textContent = data.nova_quantidade);
                }
                // Limpa o campo de quantidade
                quantidadeInput.value = '';
                // Feedback opcional
                alert('Entrada registrada com sucesso!');
            }
        })
        .catch(() => alert('Erro de conexão'));
    }
    </script>
</body>

</html>