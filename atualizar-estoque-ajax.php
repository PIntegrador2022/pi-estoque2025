<?php
session_start();
include_once 'db/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Não autorizado']);
    exit;
}

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['erro' => 'Método inválido']);
    exit;
}

$produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
$acao = $_POST['acao'] ?? '';

if (!$produto_id || !in_array($acao, ['adicionar', 'remover'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados inválidos']);
    exit;
}

try {
    // Busca a quantidade atual
    $stmt = $pdo->prepare("SELECT quantidade FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        http_response_code(404);
        echo json_encode(['erro' => 'Produto não encontrado']);
        exit;
    }

    $nova_quantidade = $produto['quantidade'];
    if ($acao === 'adicionar') {
        $nova_quantidade += 1;
    } elseif ($acao === 'remover' && $nova_quantidade > 0) {
        $nova_quantidade -= 1;
    } else {
        // Não permite quantidade negativa
        echo json_encode(['quantidade' => $produto['quantidade']]);
        exit;
    }

    // Atualiza no banco
    $stmt = $pdo->prepare("UPDATE produtos SET quantidade = ? WHERE id = ?");
    $stmt->execute([$nova_quantidade, $produto_id]);

    // Responde com a nova quantidade
    echo json_encode(['quantidade' => $nova_quantidade]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro interno']);
}