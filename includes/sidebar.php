<?php
// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$nivel_acesso = $_SESSION['nivel_acesso'];
?>

<aside class="sidebar d-flex flex-column flex-shrink-0 p-3" style="width: 250px; background-color: #2c3e50; color: white;">
    <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4 fw-bold">Estoque</span>
    </a>
    <hr style="border-color: #34495e;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-white" style="color: white !important;">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>

        <!-- Gerenciamento de Produtos -->
        <li class="nav-item">
            <a href="#submenu-produtos" class="nav-link text-white collapsed" data-bs-toggle="collapse" role="button">
                <i class="fas fa-boxes me-2"></i> Produtos
            </a>
            <ul class="nav flex-column collapse" id="submenu-produtos">
                <li><a href="cadastro-produto.php" class="nav-link text-white ps-4">Cadastrar</a></li>
                <li><a href="listagem-produtos.php" class="nav-link text-white ps-4">Listar</a></li>
                <li><a href="entrada-produto.php" class="nav-link text-white ps-4">Entrada</a></li>
                <li><a href="saida-produto.php" class="nav-link text-white ps-4">Saída</a></li>
                <li><a href="contagem.php" class="nav-link text-white ps-4">Contagem Rápida</a></li>
                <li><a href="relatorios.php" class="nav-link text-white ps-4">Relatórios</a></li>
            </ul>
        </li>

        <!-- Categorias -->
        <li class="nav-item">
            <a href="#submenu-categorias" class="nav-link text-white collapsed" data-bs-toggle="collapse" role="button">
                <i class="fas fa-tags me-2"></i> Categorias
            </a>
            <ul class="nav flex-column collapse" id="submenu-categorias">
                <li><a href="listagem-categorias.php" class="nav-link text-white ps-4">Listar</a></li>
                <li><a href="cadastro-categoria.php" class="nav-link text-white ps-4">Cadastrar</a></li>
            </ul>
        </li>

        <!-- Gerenciamento de Usuários (apenas admin) -->
        <?php if ($nivel_acesso == 'admin'): ?>
            <li class="nav-item">
                <a href="#submenu-usuarios" class="nav-link text-white collapsed" data-bs-toggle="collapse" role="button">
                    <i class="fas fa-users me-2"></i> Usuários
                </a>
                <ul class="nav flex-column collapse" id="submenu-usuarios">
                    <li><a href="cadastro-usuario.php" class="nav-link text-white ps-4">Cadastrar</a></li>
                    <li><a href="listagem-usuarios.php" class="nav-link text-white ps-4">Listar</a></li>
                </ul>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a href="analise-dados.php" class="nav-link text-white">
                <i class="fas fa-chart-line me-2"></i> Análise de Dados
            </a>
        </li>
        <li class="nav-item mt-auto">
            <a href="logout.php" class="nav-link text-white">
                <i class="fas fa-sign-out-alt me-2"></i> Sair
            </a>
        </li>
    </ul>
</aside>