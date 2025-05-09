<?php
// Inclui o arquivo de conexão
require_once 'conexao.php';

// Função para obter o total de alunos
function getTotalAlunos($conexao) {
    $sql = "SELECT COUNT(*) as total FROM alunos WHERE status = 'ativo'";
    $resultado = $conexao->query($sql);
    $dados = $resultado->fetch_assoc();
    return $dados['total'];
}

// Função para obter o total de professores
function getTotalProfessores($conexao) {
    $sql = "SELECT COUNT(*) as total FROM professores WHERE status = 'ativo'";
    $resultado = $conexao->query($sql);
    $dados = $resultado->fetch_assoc();
    return $dados['total'];
}

// Função para obter o total de turmas
function getTotalTurmas($conexao) {
    $sql = "SELECT COUNT(*) as total FROM turmas WHERE status = 'ativo'";
    $resultado = $conexao->query($sql);
    $dados = $resultado->fetch_assoc();
    return $dados['total'];
}

// Função para obter o total de cursos
function getTotalCursos($conexao) {
    $sql = "SELECT COUNT(*) as total FROM cursos";
    $resultado = $conexao->query($sql);
    $dados = $resultado->fetch_assoc();
    return $dados['total'];
}

// Função para obter a variação percentual de alunos no último mês
function getVariacaoAlunos($conexao) {
    $sql = "SELECT 
                (SELECT COUNT(*) FROM alunos WHERE status = 'ativo' AND 
                 MONTH(data_cadastro) = MONTH(CURRENT_DATE) AND 
                 YEAR(data_cadastro) = YEAR(CURRENT_DATE)) as atual,
                (SELECT COUNT(*) FROM alunos WHERE status = 'ativo' AND 
                 MONTH(data_cadastro) = MONTH(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)) AND 
                 YEAR(data_cadastro) = YEAR(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH))) as anterior";
    
    $resultado = $conexao->query($sql);
    $dados = $resultado->fetch_assoc();
    
    if ($dados['anterior'] > 0) {
        $variacao = (($dados['atual'] - $dados['anterior']) / $dados['anterior']) * 100;
        return number_format($variacao, 1);
    } else {
        return 0;
    }
}

// Obter os dados
$totalAlunos = getTotalAlunos($conexao);
$totalProfessores = getTotalProfessores($conexao);
$totalTurmas = getTotalTurmas($conexao);
$totalCursos = getTotalCursos($conexao);
$variacaoAlunos = getVariacaoAlunos($conexao);
?>
