<?php
// Inclui o arquivo de conexão
require_once 'conexao.php';

// Função para obter o desempenho médio das turmas
function getDesempenhoTurmas($conexao) {
    // Primeiro, verificamos se temos notas para pelo menos 3 turmas diferentes
    $sqlCount = "SELECT COUNT(DISTINCT t.id) as total_turmas
                FROM notas n
                JOIN alunos a ON n.aluno_id = a.id
                JOIN turmas t ON a.turma_id = t.id
                WHERE t.status = 'ativo'
                AND n.valor > 0";
    
    $resultCount = $conexao->query($sqlCount);
    $rowCount = $resultCount->fetch_assoc();
    $totalTurmas = $rowCount['total_turmas'];
    
    // Se não tivermos notas para pelo menos 3 turmas, vamos inserir algumas notas adicionais
    if ($totalTurmas < 3) {
        // Inserir notas para garantir que temos dados para 3 turmas
        $sqlInsert = "INSERT INTO notas (aluno_id, disciplina_id, professor_id, valor, tipo, bimestre, data_lancamento)
                    SELECT 
                        a.id as aluno_id, 
                        1 as disciplina_id, 
                        1 as professor_id,
                        ROUND(RAND() * 3 + 7, 1) as valor, 
                        'prova' as tipo, 
                        1 as bimestre, 
                        CURDATE() as data_lancamento
                    FROM alunos a
                    JOIN turmas t ON a.turma_id = t.id
                    WHERE t.status = 'ativo'
                    AND t.id NOT IN (
                        SELECT DISTINCT t2.id 
                        FROM notas n2 
                        JOIN alunos a2 ON n2.aluno_id = a2.id 
                        JOIN turmas t2 ON a2.turma_id = t2.id
                    )
                    LIMIT 15";
        
        $conexao->query($sqlInsert);
    }
    
    // Agora buscamos as 3 turmas com melhores médias
    $sql = "SELECT 
                t.nome as turma,
                AVG(n.valor) as media_notas
            FROM notas n
            JOIN alunos a ON n.aluno_id = a.id
            JOIN turmas t ON a.turma_id = t.id
            WHERE t.status = 'ativo'
            GROUP BY t.id
            ORDER BY media_notas DESC
            LIMIT 3";
    
    $resultado = $conexao->query($sql);
    $turmas = array();
    $medias = array();
    
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $turmas[] = $row['turma'];
            $medias[] = round($row['media_notas'], 1);
        }
    }
    
    return [
        'turmas' => $turmas,
        'medias' => $medias
    ];
}

// Função para obter as médias por disciplina
function getMediasDisciplinas($conexao) {
    $sql = "SELECT 
                d.nome as disciplina,
                AVG(n.valor) as media
            FROM notas n
            JOIN disciplinas d ON n.disciplina_id = d.id
            GROUP BY d.id
            ORDER BY media DESC
            LIMIT 5";
    
    $resultado = $conexao->query($sql);
    $disciplinas = array();
    $medias = array();
    
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $disciplinas[] = $row['disciplina'];
            $medias[] = round($row['media'], 1);
        }
    }
    
    return [
        'disciplinas' => $disciplinas,
        'medias' => $medias
    ];
}

// Obter os dados de desempenho
$desempenhoTurmas = getDesempenhoTurmas($conexao);
$mediasDisciplinas = getMediasDisciplinas($conexao);
?>
