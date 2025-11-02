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

// Carrega o autoloader do Composer
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Cria uma nova planilha
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Define o cabeçalho
$headers = ['ID', 'Nome', 'Descrição', 'Preço (R$)', 'Quantidade em Estoque', 'Categoria', 'Estoque Mínimo'];
$sheet->fromArray($headers, null, 'A1');

// Preenche os dados dos produtos
$row = 2;
foreach ($produtos as $produto) {
    $sheet->setCellValue('A' . $row, $produto['id']);
    $sheet->setCellValue('B' . $row, $produto['nome']);
    $sheet->setCellValue('C' . $row, $produto['descricao']);
    $sheet->setCellValue('D' . $row, number_format($produto['preco'], 2, ',', '.'));
    $sheet->setCellValue('E' . $row, $produto['quantidade']);
    $sheet->setCellValue('F' . $row, $produto['categoria_nome'] ?? 'Sem Categoria');
    $sheet->setCellValue('G' . $row, $produto['estoque_minimo']);
    $row++;
}

// Formatação do cabeçalho
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
];
$sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

// Ajusta a largura das colunas
foreach (range('A', 'G') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Alinha valores numéricos à direita
$sheet->getStyle('D2:G' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

// Limpa qualquer saída anterior
if (ob_get_contents()) ob_clean();

// Configura o cabeçalho HTTP para download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="lista-de-produtos.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;