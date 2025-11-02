<?php
session_start();
include_once 'db/connect.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Não autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['erro' => 'Método inválido']);
    exit;
}

$produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
$quantidade_retirada = filter_input(INPUT_POST, 'quantidade_retirada', FILTER_VALIDATE_INT);
$data_movimentacao = $_POST['data_movimentacao'] ?? '';

if (!$produto_id || !$quantidade_retirada || $quantidade_retirada <= 0 || !strtotime($data_movimentacao)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados inválidos']);
    exit;
}

try {
    // Verifica estoque suficiente
    $stmt = $pdo->prepare("SELECT quantidade FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);
    $estoque_atual = (int)$stmt->fetchColumn();

    if ($estoque_atual < $quantidade_retirada) {
        http_response_code(400);
        echo json_encode(['erro' => 'Quantidade insuficiente em estoque']);
        exit;
    }

    // Atualiza estoque
    $nova_quantidade = $estoque_atual - $quantidade_retirada;
    $stmt = $pdo->prepare("UPDATE produtos SET quantidade = ? WHERE id = ?");
    $stmt->execute([$nova_quantidade, $produto_id]);

    // Registra movimentação
    $stmt = $pdo->prepare("INSERT INTO movimentacoes (produto_id, tipo, quantidade, data_movimentacao) VALUES (?, 'saida', ?, ?)");
    $stmt->execute([$produto_id, $quantidade_retirada, $data_movimentacao]);

    echo json_encode(['nova_quantidade' => $nova_quantidade]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao registrar saída']);
}