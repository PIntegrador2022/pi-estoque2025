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

$id_produto = (int)$_GET['id'];

// Primeiro: exclui todas as movimentações associadas ao produto
$stmt = $pdo->prepare("DELETE FROM movimentacoes WHERE produto_id = ?");
$stmt->execute([$id_produto]);

// Depois: exclui o produto
$stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
$stmt->execute([$id_produto]);

// Redireciona para a listagem de produtos (mais lógico que o dashboard)
header("Location: listagem-produtos.php?msg=produto_excluido");
exit;
?>