<?php

/**
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpUndefinedNamespaceInspection
 */

session_start();
require_once "conexao.php";

// Impede acesso sem login
if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit;
}

// Importa a biblioteca FPDF
require_once __DIR__ . "/fpdf/fpdf.php";

// Dados do aluno para o certificado
$alunoId = $_SESSION['aluno_id'];
$nomeAluno = $_SESSION['aluno_nome'] ?? "Aluno";
$mediaFinal = $_SESSION['media_final'] ?? 0;

// Tratamento da média se não vier por sessão (fallback)
if (!$mediaFinal) {
    // Calcula novamente em caso de acesso direto
    $stmt = $conn->prepare("
        SELECT 
            COUNT(ae.id) AS total,
            SUM(CASE WHEN ae.resultado = 1 THEN 1 ELSE 0 END) AS acertos,
            e.nivel
        FROM alunos_exercicios ae
        JOIN exercicios e ON ae.id_exercicio = e.id
        WHERE ae.id_usuario = ?
        GROUP BY e.nivel
        ORDER BY e.nivel ASC
    ");
    $stmt->bind_param("i", $alunoId);
    $stmt->execute();
    $result = $stmt->get_result();

    $soma = 0;
    $niveis = 0;

    while ($row = $result->fetch_assoc()) {
        if ($row['total'] > 0) {
            $percent = ($row['acertos'] / $row['total']) * 100;
            $soma += $percent;
            $niveis++;
        }
    }
    $mediaFinal = $niveis > 0 ? round($soma / $niveis, 2) : 0;
}

// Data atual formatada
$meses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];

$mes = $meses[(int)date("m")];
$ano = date("Y");

// =========================
//  GERAÇÃO DO CERTIFICADO
// =========================

$pdf = new FPDF("L", "mm", "A4");
/** @noinspection PhpUndefinedClassInspection */
/** @var FPDF $pdf */

$pdf->AddPage();

// Fundo claro
$pdf->SetFillColor(240, 248, 255);
$pdf->Rect(0, 0, 297, 210, "F");

// Borda decorativa
$pdf->SetDrawColor(0, 51, 102);
$pdf->SetLineWidth(2);
$pdf->Rect(5, 5, 287, 200, "D");

// Título
$pdf->SetFont("Arial", "B", 32);
$pdf->SetTextColor(0, 51, 102);
$pdf->Ln(20);
$pdf->Cell(0, 20, "CERTIFICADO DE CONCLUSAO", 0, 1, "C");

// Subtítulo
$pdf->SetFont("Arial", "", 18);
$pdf->Ln(5);
$pdf->Cell(0, 12, "Certificamos que", 0, 1, "C");

// Nome do aluno
$pdf->SetFont("Arial", "B", 26);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 18, utf8_decode($nomeAluno), 0, 1, "C");

// Texto de corpo
$pdf->Ln(4);
$pdf->SetFont("Arial", "", 16);
$pdf->Cell(0, 10, utf8_decode("Concluiu o curso de Treinamento Musical Online,"), 0, 1, "C");
$pdf->Cell(0, 10, utf8_decode("alcançando a média final de desempenho:"), 0, 1, "C");

// Nota
$pdf->SetFont("Arial", "B", 24);
$pdf->Cell(0, 16, $mediaFinal . "%", 0, 1, "C");

// Data
$pdf->Ln(4);
$pdf->SetFont("Arial", "", 16);
$pdf->Cell(0, 10, utf8_decode("Conferido em $mes de $ano"), 0, 1, "C");

// Assinaturas
$pdf->Ln(20);

// Linha da assinatura - Professor
$pdf->SetFont("Arial", "", 14);
$pdf->Cell(140, 8, "_____________________________", 0, 0, "C");
$pdf->Cell(140, 8, "_____________________________", 0, 1, "C");

$pdf->Cell(140, 8, utf8_decode("Prof. Ademir Homrich"), 0, 0, "C");
$pdf->Cell(140, 8, utf8_decode("Luciano Rodrigues - Desenvolvedor"), 0, 1, "C");

// Emissão do PDF
$pdf->Output("D", "certificado_$nomeAluno.pdf");
exit;
