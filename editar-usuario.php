<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Verifica se o ID do usuário foi enviado via GET
if (!isset($_GET['id'])) {
    header("Location: listagem-usuarios.php");
    exit;
}

$id_usuario = $_GET['id'];

// Buscar os dados do usuário pelo ID
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: listagem-usuarios.php");
    exit;
}

// Processa o formulário de edição de usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $login = $_POST['login'];
    $senha = $_POST['senha'];
    $nivel_acesso = $_POST['nivel_acesso'];

    // Se a senha for fornecida, atualiza a senha; caso contrário, mantém a senha atual
    if (!empty($senha)) {
        $senha_hash = md5($senha);
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, login = ?, senha = ?, nivel_acesso = ? WHERE id = ?");
        $stmt->execute([$nome, $login, $senha_hash, $nivel_acesso, $id_usuario]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, login = ?, nivel_acesso = ? WHERE id = ?");
        $stmt->execute([$nome, $login, $nivel_acesso, $id_usuario]);
    }

    // Redireciona para a listagem de usuários após a edição
    header("Location: listagem-usuarios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
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

                <h2 class="mb-4">Editar Usuário</h2>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="login" class="form-label">Login (usuário) <span class="text-danger">*</span></label>
                                <input type="text" name="login" id="login" class="form-control" value="<?= htmlspecialchars($usuario['login']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Nova Senha (deixe em branco para manter)</label>
                                <input type="password" name="senha" id="senha" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="nivel_acesso" class="form-label">Nível de Acesso <span class="text-danger">*</span></label>
                                <select name="nivel_acesso" id="nivel_acesso" class="form-select" required>
                                    <option value="admin" <?= $usuario['nivel_acesso'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                                    <option value="usuario" <?= $usuario['nivel_acesso'] == 'usuario' ? 'selected' : '' ?>>Usuário Comum</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                            <a href="listagem-usuarios.php" class="btn btn-secondary">Cancelar</a>
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