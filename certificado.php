<?php
session_start();
require_once "conexao.php";

// Segurança — impede acesso sem login
if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit;
}

// Carrega tFPDF (UTF-8 completo)
require_once __DIR__ . "/tfpdf/tfpdf.php";

$alunoId    = $_SESSION['aluno_id'];
$nomeAluno  = $_SESSION['aluno_nome'] ?? "Aluno";
$mediaFinal = $_SESSION['media_final'] ?? null;

// Recalcula a média caso não esteja na sessão
if (!$mediaFinal) {
    $stmt = $conn->prepare("
        SELECT 
            e.nivel,
            COUNT(ae.id) AS total,
            SUM(CASE WHEN ae.resultado = 1 THEN 1 ELSE 0 END) AS acertos
        FROM alunos_exercicios ae
        JOIN exercicios e ON ae.id_exercicios = e.id
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


// =============================
//  DATA EM PT-BR (SEM DEPRECADO)
// =============================
$formatter = new IntlDateFormatter(
    'pt_BR',
    IntlDateFormatter::LONG,
    IntlDateFormatter::NONE,
    'America/Sao_Paulo',
    IntlDateFormatter::GREGORIAN,
    "MMMM"
);

$mes = ucfirst($formatter->format(new DateTime())); // Ex: Março
$ano = date("Y");

// =============================
//     GERAR CERTIFICADO
// =============================
$pdf = new tFPDF("L", "mm", "A4");
$pdf->AddPage();

// Registrar fonte UTF-8
$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
$pdf->AddFont('DejaVu', 'B', 'DejaVuSans-Bold.ttf', true);

$pdf->SetFont("DejaVu", "", 16);

// Fundo suave
$pdf->SetFillColor(240, 248, 255);
$pdf->Rect(0, 0, 297, 210, "F");

// Moldura
$pdf->SetDrawColor(0, 51, 102);
$pdf->SetLineWidth(2);
$pdf->Rect(5, 5, 287, 200);

// Título
$pdf->Ln(20);
$pdf->SetFont("DejaVu", "B", 32);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 20, "CERTIFICADO DE CONCLUSÃO", 0, 1, "C");

// Subtítulo
$pdf->SetFont("DejaVu", "", 20);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(4);
$pdf->Cell(0, 12, "Certificamos que", 0, 1, "C");

// Nome do aluno
$pdf->SetFont("DejaVu", "B", 28);
$pdf->Cell(0, 16, $nomeAluno, 0, 1, "C");

// Texto de conclusão
$pdf->Ln(3);
$pdf->SetFont("DejaVu", "", 18);
$pdf->MultiCell(
    0,
    10,
    "Concluiu o curso de Treinamento Musical Online no instrumento Violão, " .
        "alcançando a média final de desempenho:",
    0,
    "C"
);

// Nota
$pdf->Ln(3);
$pdf->SetFont("DejaVu", "B", 26);
$pdf->Cell(0, 14, $mediaFinal . "%", 0, 1, "C");

// Data
$pdf->Ln(6);
$pdf->SetFont("DejaVu", "", 18);
$pdf->Cell(0, 10, "Conferido em $mes de $ano", 0, 1, "C");

// Assinaturas
$pdf->Ln(20);

// Linhas
$pdf->SetFont("DejaVu", "", 14);
$pdf->Cell(140, 8, "_____________________________", 0, 0, "C");
$pdf->Cell(140, 8, "_____________________________", 0, 1, "C");

// Nomes
$pdf->Cell(140, 8, "Prof. Ademir Homrich - Município de Canoas/RS", 0, 0, "C");
$pdf->Cell(140, 8, "Luciano Moraes Rodrigues - Desenvolvedor", 0, 1, "C");

// Saída do PDF
$pdf->Output("D", "certificado_" . str_replace(" ", "_", $nomeAluno) . ".pdf");
exit;
