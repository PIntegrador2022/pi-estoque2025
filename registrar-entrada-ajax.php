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
$quantidade_adicional = filter_input(INPUT_POST, 'quantidade_adicional', FILTER_VALIDATE_INT);
$data_movimentacao = $_POST['data_movimentacao'] ?? '';

if (!$produto_id || !$quantidade_adicional || $quantidade_adicional <= 0 || !strtotime($data_movimentacao)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados inválidos']);
    exit;
}

try {
    // Atualiza estoque
    $stmt = $pdo->prepare("UPDATE produtos SET quantidade = quantidade + ? WHERE id = ?");
    $stmt->execute([$quantidade_adicional, $produto_id]);

    // Registra movimentação
    $stmt = $pdo->prepare("INSERT INTO movimentacoes (produto_id, tipo, quantidade, data_movimentacao) VALUES (?, 'entrada', ?, ?)");
    $stmt->execute([$produto_id, $quantidade_adicional, $data_movimentacao]);

    // Busca nova quantidade
    $stmt = $pdo->prepare("SELECT quantidade FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);
    $nova_quantidade = $stmt->fetchColumn();

    echo json_encode(['nova_quantidade' => (int)$nova_quantidade]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao registrar entrada']);
}