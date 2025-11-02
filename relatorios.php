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

// Variáveis para armazenar os resultados
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : null;
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : null;
$tipo_relatorio = isset($_GET['tipo_relatorio']) ? $_GET['tipo_relatorio'] : null;
$resultados = [];

// Processar os filtros (se o formulário foi enviado)
if ($data_inicio && $data_fim && $tipo_relatorio) {
    switch ($tipo_relatorio) {
        case 'consumo':
            $stmt = $pdo->prepare("
                SELECT 
                    p.nome AS produto,
                    SUM(m.quantidade) AS total_consumido
                FROM movimentacoes m
                JOIN produtos p ON m.produto_id = p.id
                WHERE m.tipo = 'saida' AND m.data_movimentacao BETWEEN :data_inicio AND :data_fim
                GROUP BY p.nome
                ORDER BY total_consumido DESC
            ");
            $stmt->execute(['data_inicio' => $data_inicio, 'data_fim' => $data_fim]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'reposicao':
            $stmt = $pdo->prepare("
                SELECT 
                    p.nome AS produto,
                    SUM(m.quantidade) AS total_reposto
                FROM movimentacoes m
                JOIN produtos p ON m.produto_id = p.id
                WHERE m.tipo = 'entrada' AND m.data_movimentacao BETWEEN :data_inicio AND :data_fim
                GROUP BY p.nome
                ORDER BY total_reposto DESC
            ");
            $stmt->execute(['data_inicio' => $data_inicio, 'data_fim' => $data_fim]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'tendencias':
            $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(m.data_movimentacao, '%m-%Y') AS mes,
                    SUM(m.quantidade) AS total_movimentado
                FROM movimentacoes m
                WHERE m.tipo = 'saida' AND m.data_movimentacao BETWEEN :data_inicio AND :data_fim
                GROUP BY mes
                ORDER BY m.data_movimentacao ASC
            ");
            $stmt->execute(['data_inicio' => $data_inicio, 'data_fim' => $data_fim]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'valor':
            $stmt = $pdo->prepare("
                SELECT 
                    p.nome AS produto,
                    SUM(m.quantidade * p.preco) AS valor_total
                FROM movimentacoes m
                JOIN produtos p ON m.produto_id = p.id
                WHERE m.data_movimentacao BETWEEN :data_inicio AND :data_fim
                GROUP BY p.nome
                ORDER BY valor_total DESC
            ");
            $stmt->execute(['data_inicio' => $data_inicio, 'data_fim' => $data_fim]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios</title>
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

                <h2 class="mb-4">Relatórios de Movimentação</h2>

                <!-- Formulário de Filtros -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="data_inicio" class="form-label">Data Inicial <span class="text-danger">*</span></label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" required value="<?= htmlspecialchars($data_inicio ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="data_fim" class="form-label">Data Final <span class="text-danger">*</span></label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control" required value="<?= htmlspecialchars($data_fim ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="tipo_relatorio" class="form-label">Tipo de Relatório <span class="text-danger">*</span></label>
                                <select name="tipo_relatorio" id="tipo_relatorio" class="form-select" required>
                                    <option value="consumo" <?= $tipo_relatorio === 'consumo' ? 'selected' : '' ?>>Consumo (Saídas)</option>
                                    <option value="reposicao" <?= $tipo_relatorio === 'reposicao' ? 'selected' : '' ?>>Reposição (Entradas)</option>
                                    <option value="tendencias" <?= $tipo_relatorio === 'tendencias' ? 'selected' : '' ?>>Tendências Temporais</option>
                                    <option value="valor" <?= $tipo_relatorio === 'valor' ? 'selected' : '' ?>>Valor Total Movimentado</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-chart-line"></i> Gerar Relatório
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Resultados -->
                <div id="resultados">
                    <?php if (!empty($resultados)): ?>
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Resultados do Relatório
                                    <?php
                                    $titulos = [
                                        'consumo' => 'Consumo por Produto',
                                        'reposicao' => 'Reposição por Produto',
                                        'tendencias' => 'Tendências Temporais',
                                        'valor' => 'Valor Movimentado por Produto'
                                    ];
                                    echo isset($titulos[$tipo_relatorio]) ? $titulos[$tipo_relatorio] : '';
                                    ?>
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Tabela -->
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <?php if ($tipo_relatorio === 'tendencias'): ?>
                                                    <th>Mês (MM-YYYY)</th>
                                                    <th>Total Movimentado</th>
                                                <?php else: ?>
                                                    <th>Produto</th>
                                                    <th>Quantidade</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($resultados as $resultado): ?>
                                                <tr>
                                                    <?php if ($tipo_relatorio === 'tendencias'): ?>
                                                        <td><?= htmlspecialchars($resultado['mes']) ?></td>
                                                        <td><?= formatarNumeroInteiro($resultado['total_movimentado']) ?></td>
                                                    <?php elseif ($tipo_relatorio === 'valor'): ?>
                                                        <td><?= htmlspecialchars($resultado['produto']) ?></td>
                                                        <td><?= formatarValorMonetario($resultado['valor_total']) ?></td>
                                                    <?php else: ?>
                                                        <td><?= htmlspecialchars($resultado['produto']) ?></td>
                                                        <td><?= formatarNumeroInteiro($resultado['total_consumido'] ?? $resultado['total_reposto']) ?></td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Gráfico -->
                                <div class="mt-4">
                                    <canvas id="graficoRelatorio" height="120"></canvas>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const ctx = document.getElementById('graficoRelatorio').getContext('2d');
                                        const tipo = '<?= addslashes($tipo_relatorio) ?>';
                                        const labels = <?= json_encode(array_column($resultados, $tipo === 'tendencias' ? 'mes' : 'produto')) ?>;
                                        const valores = <?= json_encode(
                                            array_map(
                                                fn($r) => $tipo === 'valor' ? (float)$r['valor_total'] : 
                                                         ($tipo === 'tendencias' ? (int)$r['total_movimentado'] : 
                                                          (int)($r['total_consumido'] ?? $r['total_reposto'])),
                                                $resultados
                                            )
                                        ) ?>;

                                        // Limitar a 10 itens para evitar poluição visual
                                        const maxItens = 10;
                                        const exibirLabels = labels.length <= maxItens ? labels : labels.slice(0, maxItens);
                                        const exibirValores = labels.length <= maxItens ? valores : valores.slice(0, maxItens);

                                        let config;

                                        if (tipo === 'tendencias') {
                                            config = {
                                                type: 'line',
                                                data: {
                                                    labels: exibirLabels,
                                                    datasets: [{
                                                        label: 'Movimentação',
                                                        data: exibirValores,
                                                        borderColor: '#4e73df',
                                                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                                                        borderWidth: 2,
                                                        fill: true,
                                                        tension: 0.3
                                                    }]
                                                },
                                                options: {
                                                    responsive: true,
                                                    plugins: { legend: { display: true } },
                                                    scales: {
                                                        y: { beginAtZero: true, title: { display: true, text: 'Quantidade' } },
                                                        x: { title: { display: true, text: 'Período (MM-YYYY)' } }
                                                    }
                                                }
                                            };
                                        } else if (tipo === 'valor') {
                                            config = {
                                                type: 'doughnut',
                                                data: {
                                                    labels: exibirLabels,
                                                    datasets: [{
                                                        data: exibirValores,
                                                        backgroundColor: [
                                                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                                                            '#858796', '#9b59b6', '#3498db', '#2ecc71', '#e67e22'
                                                        ],
                                                        borderWidth: 2,
                                                        borderColor: '#fff'
                                                    }]
                                                },
                                                options: {
                                                    responsive: true,
                                                    plugins: {
                                                        legend: { position: 'right' },
                                                        tooltip: {
                                                            callbacks: {
                                                                label: (item) => `R$ ${item.raw.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`
                                                            }
                                                        }
                                                    }
                                                }
                                            };
                                        } else {
                                            config = {
                                                type: 'bar',
                                                data: {
                                                    labels: exibirLabels,
                                                    datasets: [{
                                                        label: tipo === 'consumo' ? 'Consumido' : 'Reposto',
                                                        data: exibirValores,
                                                        backgroundColor: '#4e73df',
                                                        borderColor: '#3a5ecf',
                                                        borderWidth: 1
                                                    }]
                                                },
                                                options: {
                                                    indexAxis: 'y',
                                                    responsive: true,
                                                    plugins: { legend: { display: false } },
                                                    scales: {
                                                        x: { beginAtZero: true, title: { display: true, text: 'Quantidade' } }
                                                    }
                                                }
                                            };
                                        }

                                        new Chart(ctx, config);
                                    });
                                </script>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Nenhum resultado disponível. Preencha os filtros e clique em "Gerar Relatório".
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('graficoRelatorio').getContext('2d');
    const tipo = '<?= addslashes($tipo_relatorio ?? '') ?>';
    
    // Preparar labels e valores de forma segura
    const labels = [];
    const valores = [];
    
    <?php foreach ($resultados as $r): ?>
        <?php if ($tipo_relatorio === 'tendencias'): ?>
            labels.push('<?= addslashes($r['mes']) ?>');
            valores.push(<?= (int)$r['total_movimentado'] ?>);
        <?php elseif ($tipo_relatorio === 'valor'): ?>
            labels.push('<?= addslashes($r['produto']) ?>');
            valores.push(<?= (float)$r['valor_total'] ?>);
        <?php else: ?>
            labels.push('<?= addslashes($r['produto']) ?>');
            valores.push(<?= (int)($r['total_consumido'] ?? $r['total_reposto']) ?>);
        <?php endif; ?>
    <?php endforeach; ?>

    // Limitar a 10 itens
    const maxItens = 10;
    const exibirLabels = labels.length <= maxItens ? labels : labels.slice(0, maxItens);
    const exibirValores = labels.length <= maxItens ? valores : valores.slice(0, maxItens);

    let config;

    if (tipo === 'tendencias') {
        config = {
            type: 'line',
            data: {
                labels: exibirLabels,
                datasets: [{
                    label: 'Movimentação',
                    data: exibirValores,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Quantidade' } },
                    x: { title: { display: true, text: 'Período (MM-YYYY)' } }
                }
            }
        };
    } else if (tipo === 'valor') {
        config = {
            type: 'doughnut',
            data: {
                labels: exibirLabels,
                datasets: [{
                    data: exibirValores,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#858796', '#9b59b6', '#3498db', '#2ecc71', '#e67e22'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'R$ ' + context.raw.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                }
            }
        };
    } else {
        config = {
            type: 'bar',
            data: {
                labels: exibirLabels,
                datasets: [{
                    label: tipo === 'consumo' ? 'Consumido' : 'Reposto',
                    data: exibirValores,
                    backgroundColor: '#4e73df',
                    borderColor: '#3a5ecf',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, title: { display: true, text: 'Quantidade' } }
                }
            }
        };
    }

    new Chart(ctx, config);
});
</script>
</body>

</html>