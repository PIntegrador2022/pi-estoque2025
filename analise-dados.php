<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise de Dados</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

                <h2 class="mb-4">Análise de Dados com Aprendizado de Máquina</h2>

                <!-- Cards de Tendência -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Demanda Crescente</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">+22% nas últimas 4 semanas</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-arrow-trend-up fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Demanda Estável</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Variação < ±5%</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Demanda Decrescente</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">-18% nas últimas 4 semanas</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-arrow-trend-down fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Tendência -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tendência de Demanda por Produto (Últimos 60 dias)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoTendencia" height="100"></canvas>
                    </div>
                </div>

                <!-- Top Produtos por Previsão -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top 5 Produtos com Maior Previsão de Saída (Próximos 7 dias)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produto</th>
                                        <th>Previsão (unidades)</th>
                                        <th>Tendência</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>iPhone 15</td>
                                        <td>42</td>
                                        <td><span class="badge bg-success">Crescente</span></td>
                                    </tr>
                                    <tr>
                                        <td>AirPods Pro</td>
                                        <td>38</td>
                                        <td><span class="badge bg-success">Crescente</span></td>
                                    </tr>
                                    <tr>
                                        <td>MacBook Air M2</td>
                                        <td>25</td>
                                        <td><span class="badge bg-warning text-dark">Estável</span></td>
                                    </tr>
                                    <tr>
                                        <td>Carregador USB-C Rápido</td>
                                        <td>22</td>
                                        <td><span class="badge bg-danger">Decrescente</span></td>
                                    </tr>
                                    <tr>
                                        <td>Fone Sony WH-1000XM5</td>
                                        <td>18</td>
                                        <td><span class="badge bg-success">Crescente</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Nota explicativa -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Nota:</strong> Esta análise é gerada por um modelo de aprendizado de máquina que processa o histórico de saídas dos últimos 90 dias.
                    Os dados exibidos são simulados para demonstração acadêmica.
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>

    <!-- Gráfico de Tendência Estático -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('graficoTendencia').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [
                    {
                        label: 'iPhone 15 (Crescente)',
                        data: [20, 22, 25, 28, 30, 33, 35, 38, 40, 42, 45, 48],
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Carregador USB-C (Decrescente)',
                        data: [50, 48, 45, 42, 40, 38, 35, 32, 30, 28, 25, 22],
                        borderColor: '#e74a3b',
                        backgroundColor: 'rgba(231, 74, 59, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'MacBook Air M2 (Estável)',
                        data: [15, 16, 15, 17, 16, 15, 16, 17, 16, 15, 16, 17],
                        borderColor: '#f6c23e',
                        backgroundColor: 'rgba(246, 194, 58, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: false, title: { display: true, text: 'Unidades Vendidas' } },
                    x: { title: { display: true, text: 'Mês' } }
                }
            }
        });
    });
    </script>
</body>

</html>