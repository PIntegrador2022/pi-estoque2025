<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Consulta para buscar todos os produtos com categoria
$stmt = $pdo->query("
    SELECT 
        p.id, p.nome, p.descricao, p.preco, p.quantidade, 
        c.nome AS categoria_nome, p.estoque_minimo
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Limpa qualquer saída anterior
if (ob_get_contents()) ob_clean();

// Configura o cabeçalho HTTP para download do arquivo CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment;filename="produtos.csv"');

// Abre o output para escrita
$output = fopen('php://output', 'w');

// Força UTF-8 no Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Escreve o cabeçalho do CSV
fputcsv($output, ['ID', 'Nome do Produto', 'Descrição do Produto', 'Preço (R$)', 'Quantidade em Estoque', 'Categoria', 'Estoque Mínimo']);

// Escreve os dados dos produtos
foreach ($produtos as $produto) {
    fputcsv($output, [
        $produto['id'],
        $produto['nome'],
        $produto['descricao'],
        number_format($produto['preco'], 2, ',', '.'),
        $produto['quantidade'],
        $produto['categoria_nome'] ?? 'Sem Categoria',
        $produto['estoque_minimo']
    ]);
}

fclose($output);
exit;