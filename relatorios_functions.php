<?php
/**
 * Funções para geração de relatórios
 */

/**
 * Obtém dados para relatório de alunos
 */
function obterDadosRelatorioAlunos($conexao, $filtros = []) {
    // Construir a consulta base
    $sql = "SELECT a.id, a.matricula, ";
    
    // Verificar se a tabela usuarios existe e se há relação com alunos
    $check_usuarios = $conexao->query("SHOW TABLES LIKE 'usuarios'");
    $has_usuarios = $check_usuarios->num_rows > 0;
    
    // Verificar se a coluna nome existe diretamente na tabela alunos
    $check_nome = $conexao->query("SHOW COLUMNS FROM alunos LIKE 'nome'");
    $has_nome_alunos = $check_nome->num_rows > 0;
    
    if ($has_usuarios) {
        $sql .= "u.nome, ";
    } elseif ($has_nome_alunos) {
        $sql .= "a.nome, ";
    } else {
        $sql .= "CONCAT('Aluno ', a.id) as nome, ";
    }
    
    $sql .= "t.nome as turma, c.nome as curso, c.nivel, 
             a.responsavel, a.telefone_responsavel, a.status, 
             a.data_cadastro
             FROM alunos a ";
    
    if ($has_usuarios) {
        $sql .= "JOIN usuarios u ON a.usuario_id = u.id ";
    }
    
    $sql .= "LEFT JOIN turmas t ON a.turma_id = t.id
             LEFT JOIN cursos c ON t.curso_id = c.id ";
    
    // Adicionar condições de filtro
    $where = [];
    
    if (!empty($filtros['turma_id'])) {
        $turma_id = (int)$filtros['turma_id'];
        $where[] = "a.turma_id = $turma_id";
    }
    
    if (!empty($filtros['curso_id'])) {
        $curso_id = (int)$filtros['curso_id'];
        $where[] = "t.curso_id = $curso_id";
    }
    
    if (!empty($filtros['status'])) {
        $status = $conexao->real_escape_string($filtros['status']);
        $where[] = "a.status = '$status'";
    }
    
    if (!empty($filtros['nivel'])) {
        $nivel = $conexao->real_escape_string($filtros['nivel']);
        $where[] = "c.nivel = '$nivel'";
    }
    
    // Adicionar cláusula WHERE se houver condições
    if (!empty($where)) {
        $sql .= "WHERE " . implode(" AND ", $where);
    }
    
    // Ordenação
    $sql .= " ORDER BY ";
    if ($has_usuarios) {
        $sql .= "u.nome";
    } elseif ($has_nome_alunos) {
        $sql .= "a.nome";
    } else {
        $sql .= "a.id";
    }
    
    $resultado = $conexao->query($sql);
    
    $alunos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $alunos[] = $row;
        }
    }
    
    return $alunos;
}

/**
 * Obtém dados para relatório de cursos
 */
function obterDadosRelatorioCursos($conexao, $filtros = []) {
    // Consulta base
    $sql = "SELECT c.id, c.nome, c.descricao, c.carga_horaria, c.nivel,
                   COUNT(DISTINCT t.id) as total_turmas,
                   COUNT(DISTINCT a.id) as total_alunos
            FROM cursos c
            LEFT JOIN turmas t ON c.id = t.curso_id
            LEFT JOIN alunos a ON t.id = a.turma_id ";
    
    // Adicionar condições de filtro
    $where = [];
    
    if (!empty($filtros['nivel'])) {
        $nivel = $conexao->real_escape_string($filtros['nivel']);
        $where[] = "c.nivel = '$nivel'";
    }
    
    if (!empty($filtros['carga_horaria_min'])) {
        $carga_min = (int)$filtros['carga_horaria_min'];
        $where[] = "c.carga_horaria >= $carga_min";
    }
    
    if (!empty($filtros['carga_horaria_max'])) {
        $carga_max = (int)$filtros['carga_horaria_max'];
        $where[] = "c.carga_horaria <= $carga_max";
    }
    
    // Adicionar cláusula WHERE se houver condições
    if (!empty($where)) {
        $sql .= "WHERE " . implode(" AND ", $where);
    }
    
    // Agrupar e ordenar
    $sql .= " GROUP BY c.id ORDER BY c.nome";
    
    $resultado = $conexao->query($sql);
    
    $cursos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $cursos[] = $row;
        }
    }
    
    return $cursos;
}

/**
 * Obtém dados para relatório de notas
 */
function obterDadosRelatorioNotas($conexao, $filtros = []) {
    // Verificar se a tabela notas existe
    $check_notas = $conexao->query("SHOW TABLES LIKE 'notas'");
    if ($check_notas->num_rows == 0) {
        return [];
    }
    
    // Verificar se a tabela usuarios existe e se há relação com alunos
    $check_usuarios = $conexao->query("SHOW TABLES LIKE 'usuarios'");
    $has_usuarios = $check_usuarios->num_rows > 0;
    
    // Verificar se a coluna nome existe diretamente na tabela alunos
    $check_nome = $conexao->query("SHOW COLUMNS FROM alunos LIKE 'nome'");
    $has_nome_alunos = $check_nome->num_rows > 0;
    
    // Construir a consulta base
    $sql = "SELECT n.id, a.matricula, ";
    
    if ($has_usuarios) {
        $sql .= "u.nome as aluno_nome, ";
    } elseif ($has_nome_alunos) {
        $sql .= "a.nome as aluno_nome, ";
    } else {
        $sql .= "CONCAT('Aluno ', a.id) as aluno_nome, ";
    }
    
    $sql .= "t.nome as turma, c.nome as curso, d.nome as disciplina,
    
             n.valor, n.bimestre, DATE_FORMAT(n.data_lancamento, '%Y') as ano_letivo
             FROM notas n
             JOIN alunos a ON n.aluno_id = a.id ";
    
    if ($has_usuarios) {
        $sql .= "JOIN usuarios u ON a.usuario_id = u.id ";
    }
    
    $sql .= "LEFT JOIN turmas t ON a.turma_id = t.id
             LEFT JOIN cursos c ON t.curso_id = c.id
             LEFT JOIN disciplinas d ON n.disciplina_id = d.id ";
    
    // Adicionar condições de filtro
    $where = [];
    
    if (!empty($filtros['turma_id'])) {
        $turma_id = (int)$filtros['turma_id'];
        $where[] = "a.turma_id = $turma_id";
    }
    
    if (!empty($filtros['curso_id'])) {
        $curso_id = (int)$filtros['curso_id'];
        $where[] = "t.curso_id = $curso_id";
    }
    
    if (!empty($filtros['disciplina_id'])) {
        $disciplina_id = (int)$filtros['disciplina_id'];
        $where[] = "n.disciplina_id = $disciplina_id";
    }
    
    if (!empty($filtros['periodo'])) {
        $periodo = (int)$filtros['periodo'];
        $where[] = "n.bimestre = $periodo";
    }
    
    if (!empty($filtros['ano_letivo'])) {
        $ano_letivo = (int)$filtros['ano_letivo'];
        $where[] = "YEAR(n.data_lancamento) = $ano_letivo";
    }
    
    if (isset($filtros['nota_min'])) {
        $nota_min = (float)$filtros['nota_min'];
        $where[] = "n.valor >= $nota_min";
    }
    
    if (isset($filtros['nota_max'])) {
        $nota_max = (float)$filtros['nota_max'];
        $where[] = "n.valor <= $nota_max";
    }
    
    // Adicionar cláusula WHERE se houver condições
    if (!empty($where)) {
        $sql .= "WHERE " . implode(" AND ", $where);
    }
    
    // Ordenação
    $sql .= " ORDER BY ";
    if ($has_usuarios) {
        $sql .= "u.nome, ";
    } elseif ($has_nome_alunos) {
        $sql .= "a.nome, ";
    } else {
        $sql .= "a.id, ";
    }
    $sql .= "d.nome, n.bimestre";
    
    $resultado = $conexao->query($sql);
    
    $notas = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $notas[] = $row;
        }
    }
    
    return $notas;
}

/**
 * Obtém todas as turmas para filtros
 */
function obterTurmasParaFiltro($conexao) {
    $sql = "SELECT t.id, t.nome, c.nome as curso, t.ano, t.periodo
            FROM turmas t
            LEFT JOIN cursos c ON t.curso_id = c.id
            WHERE t.status = 'ativo'
            ORDER BY c.nome, t.nome";
    
    $resultado = $conexao->query($sql);
    
    $turmas = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $turmas[] = $row;
        }
    }
    
    return $turmas;
}

/**
 * Obtém todos os cursos para filtros
 */
function obterCursosParaFiltro($conexao) {
    $sql = "SELECT id, nome, nivel
            FROM cursos
            ORDER BY nome";
    
    $resultado = $conexao->query($sql);
    
    $cursos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $cursos[] = $row;
        }
    }
    
    return $cursos;
}

/**
 * Obtém todas as disciplinas para filtros
 */
function obterDisciplinasParaFiltro($conexao) {
    // Verificar se a tabela disciplinas existe
    $check_disciplinas = $conexao->query("SHOW TABLES LIKE 'disciplinas'");
    if ($check_disciplinas->num_rows == 0) {
        return [];
    }
    
    $sql = "SELECT id, nome, carga_horaria
            FROM disciplinas
            ORDER BY nome";
    
    $resultado = $conexao->query($sql);
    
    $disciplinas = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $disciplinas[] = $row;
        }
    }
    
    return $disciplinas;
}
?>
