<?php
/**
 * Funções para gerenciamento de cursos
 */

/**
 * Obtém todos os cursos disponíveis
 */
function obterCursos($conexao) {
    $sql = "SELECT c.id, c.nome, c.descricao, c.carga_horaria, c.nivel, 
                   COUNT(DISTINCT t.id) as total_turmas,
                   COUNT(DISTINCT a.id) as total_alunos
            FROM cursos c
            LEFT JOIN turmas t ON c.id = t.curso_id
            LEFT JOIN alunos a ON t.id = a.turma_id
            GROUP BY c.id
            ORDER BY c.nome";
    
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
 * Obtém um curso específico pelo ID
 */
function obterCursoPorId($conexao, $curso_id) {
    $curso_id = (int)$curso_id;
    
    $sql = "SELECT c.*, COUNT(DISTINCT t.id) as total_turmas
            FROM cursos c
            LEFT JOIN turmas t ON c.id = t.curso_id
            WHERE c.id = $curso_id
            GROUP BY c.id";
    
    $resultado = $conexao->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        return $resultado->fetch_assoc();
    }
    
    return null;
}

/**
 * Obtém as turmas de um curso específico
 */
function obterTurmasPorCurso($conexao, $curso_id) {
    $curso_id = (int)$curso_id;
    
    $sql = "SELECT t.id, t.nome, t.ano, t.periodo, t.status, 
                   COUNT(a.id) as total_alunos
            FROM turmas t
            LEFT JOIN alunos a ON t.id = a.turma_id
            WHERE t.curso_id = $curso_id
            GROUP BY t.id
            ORDER BY t.ano DESC, t.nome";
    
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
 * Obtém os alunos de um curso específico
 */
function obterAlunosPorCurso($conexao, $curso_id) {
    $curso_id = (int)$curso_id;
    
    // Verificar se a tabela usuarios existe e se há relação com alunos
    $check_usuarios = $conexao->query("SHOW TABLES LIKE 'usuarios'");
    $has_usuarios = $check_usuarios->num_rows > 0;
    
    // Verificar se a coluna nome existe diretamente na tabela alunos
    $check_nome = $conexao->query("SHOW COLUMNS FROM alunos LIKE 'nome'");
    $has_nome_alunos = $check_nome->num_rows > 0;
    
    if ($has_usuarios) {
        // Buscar usando a relação com a tabela usuarios
        $sql = "SELECT a.id, a.matricula, u.nome, a.turma_id, t.nome as turma, 
                       a.responsavel, a.telefone_responsavel, a.status, t.periodo, t.ano
                FROM alunos a
                JOIN usuarios u ON a.usuario_id = u.id
                JOIN turmas t ON a.turma_id = t.id
                WHERE t.curso_id = $curso_id
                ORDER BY u.nome";
    } elseif ($has_nome_alunos) {
        // Buscar usando o nome diretamente da tabela alunos
        $sql = "SELECT a.id, a.matricula, a.nome, a.turma_id, t.nome as turma, 
                       a.responsavel, a.telefone_responsavel, a.status, t.periodo, t.ano
                FROM alunos a
                JOIN turmas t ON a.turma_id = t.id
                WHERE t.curso_id = $curso_id
                ORDER BY a.nome";
    } else {
        // Buscar apenas por matrícula
        $sql = "SELECT a.id, a.matricula, CONCAT('Aluno ', a.id) as nome, a.turma_id, 
                       t.nome as turma, a.responsavel, a.telefone_responsavel, a.status, t.periodo, t.ano
                FROM alunos a
                JOIN turmas t ON a.turma_id = t.id
                WHERE t.curso_id = $curso_id
                ORDER BY a.id";
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
 * Obtém detalhes de um curso específico, incluindo suas turmas
 */
function obterDetalhesCurso($conexao, $curso_id) {
    $curso_id = (int)$curso_id;
    
    // Obter dados do curso
    $curso = obterCursoPorId($conexao, $curso_id);
    
    if (!$curso) {
        return null;
    }
    
    // Obter turmas do curso
    $curso['turmas'] = obterTurmasPorCurso($conexao, $curso_id);
    
    // Obter total de alunos
    $sql = "SELECT COUNT(a.id) as total_alunos
            FROM alunos a
            JOIN turmas t ON a.turma_id = t.id
            WHERE t.curso_id = $curso_id";
    
    $resultado = $conexao->query($sql);
    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $curso['total_alunos'] = $row['total_alunos'];
    } else {
        $curso['total_alunos'] = 0;
    }
    
    return $curso;
}

/**
 * Busca alunos pelo nome ou matrícula
 */
function buscarAlunos($conexao, $termo) {
    $termo = $conexao->real_escape_string($termo);
    
    // Verificar se a tabela usuarios existe e se há relação com alunos
    $check_usuarios = $conexao->query("SHOW TABLES LIKE 'usuarios'");
    $has_usuarios = $check_usuarios->num_rows > 0;
    
    // Verificar se a coluna nome existe diretamente na tabela alunos
    $check_nome = $conexao->query("SHOW COLUMNS FROM alunos LIKE 'nome'");
    $has_nome_alunos = $check_nome->num_rows > 0;
    
    if ($has_usuarios) {
        // Buscar usando a relação com a tabela usuarios
        $sql = "SELECT a.id, a.matricula, u.nome, a.turma_id, t.nome as turma, c.nome as curso, 
                       a.responsavel, a.telefone_responsavel, a.status
                FROM alunos a
                JOIN usuarios u ON a.usuario_id = u.id
                LEFT JOIN turmas t ON a.turma_id = t.id
                LEFT JOIN cursos c ON t.curso_id = c.id
                WHERE u.nome LIKE '%$termo%' OR a.matricula LIKE '%$termo%'
                ORDER BY u.nome
                LIMIT 10";
    } elseif ($has_nome_alunos) {
        // Buscar usando o nome diretamente da tabela alunos
        $sql = "SELECT a.id, a.matricula, a.nome, a.turma_id, t.nome as turma, c.nome as curso, 
                       a.responsavel, a.telefone_responsavel, a.status
                FROM alunos a
                LEFT JOIN turmas t ON a.turma_id = t.id
                LEFT JOIN cursos c ON t.curso_id = c.id
                WHERE a.nome LIKE '%$termo%' OR a.matricula LIKE '%$termo%'
                ORDER BY a.nome
                LIMIT 10";
    } else {
        // Buscar apenas por matrícula
        $sql = "SELECT a.id, a.matricula, CONCAT('Aluno ', a.id) as nome, a.turma_id, 
                       t.nome as turma, c.nome as curso, a.responsavel, a.telefone_responsavel, a.status
                FROM alunos a
                LEFT JOIN turmas t ON a.turma_id = t.id
                LEFT JOIN cursos c ON t.curso_id = c.id
                WHERE a.matricula LIKE '%$termo%'
                ORDER BY a.id
                LIMIT 10";
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
 * Verifica se já existe uma inscrição para o aluno no curso
 */
function verificarInscricaoExistente($conexao, $aluno_id, $curso_id) {
    $aluno_id = (int)$aluno_id;
    $curso_id = (int)$curso_id;
    
    // Verificar se o aluno já está em uma turma deste curso
    $sql = "SELECT a.id
            FROM alunos a
            JOIN turmas t ON a.turma_id = t.id
            WHERE a.id = $aluno_id AND t.curso_id = $curso_id";
    
    $resultado = $conexao->query($sql);
    
    return ($resultado && $resultado->num_rows > 0);
}

/**
 * Inscreve um aluno em um curso (atribuindo-o a uma turma)
 */
function inscreverAlunoEmCurso($conexao, $aluno_id, $curso_id, $turma_id) {
    $aluno_id = (int)$aluno_id;
    $curso_id = (int)$curso_id;
    $turma_id = (int)$turma_id;
    
    // Verificar se o aluno existe
    $check_aluno = $conexao->query("SELECT id FROM alunos WHERE id = $aluno_id");
    if ($check_aluno->num_rows == 0) {
        return [
            'sucesso' => false,
            'mensagem' => 'Aluno não encontrado.',
            'tipo' => 'danger'
        ];
    }
    
    // Verificar se a turma pertence ao curso
    $check_turma = $conexao->query("SELECT id FROM turmas WHERE id = $turma_id AND curso_id = $curso_id");
    if ($check_turma->num_rows == 0) {
        return [
            'sucesso' => false,
            'mensagem' => 'Turma não pertence ao curso selecionado.',
            'tipo' => 'danger'
        ];
    }
    
    // Verificar se o aluno já está inscrito neste curso
    if (verificarInscricaoExistente($conexao, $aluno_id, $curso_id)) {
        return [
            'sucesso' => false,
            'mensagem' => 'Aluno já está inscrito neste curso.',
            'tipo' => 'warning'
        ];
    }
    
    // Atualizar a turma do aluno
    $sql = "UPDATE alunos SET turma_id = $turma_id WHERE id = $aluno_id";
    
    if ($conexao->query($sql)) {
        return [
            'sucesso' => true,
            'mensagem' => 'Aluno inscrito com sucesso no curso!',
            'tipo' => 'success'
        ];
    } else {
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao inscrever aluno: ' . $conexao->error,
            'tipo' => 'danger'
        ];
    }
}

/**
 * Atualiza a inscrição de um aluno em um curso (mudando a turma)
 */
function atualizarInscricaoAluno($conexao, $aluno_id, $turma_id, $status = null) {
    $aluno_id = (int)$aluno_id;
    $turma_id = (int)$turma_id;
    
    // Verificar se o aluno existe
    $check_aluno = $conexao->query("SELECT id, status FROM alunos WHERE id = $aluno_id");
    if ($check_aluno->num_rows == 0) {
        return [
            'sucesso' => false,
            'mensagem' => 'Aluno não encontrado.',
            'tipo' => 'danger'
        ];
    }
    
    // Verificar se a turma existe
    $check_turma = $conexao->query("SELECT id FROM turmas WHERE id = $turma_id");
    if ($check_turma->num_rows == 0) {
        return [
            'sucesso' => false,
            'mensagem' => 'Turma não encontrada.',
            'tipo' => 'danger'
        ];
    }
    
    // Preparar a consulta SQL
    $sql = "UPDATE alunos SET turma_id = $turma_id";
    
    // Adicionar status se fornecido
    if ($status) {
        $status = $conexao->real_escape_string($status);
        $sql .= ", status = '$status'";
    }
    
    $sql .= " WHERE id = $aluno_id";
    
    if ($conexao->query($sql)) {
        return [
            'sucesso' => true,
            'mensagem' => 'Inscrição do aluno atualizada com sucesso!',
            'tipo' => 'success'
        ];
    } else {
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao atualizar inscrição: ' . $conexao->error,
            'tipo' => 'danger'
        ];
    }
}

/**
 * Cadastra um novo curso
 */
function cadastrarCurso($conexao, $dados) {
    // Validação básica
    if (empty($dados['nome']) || empty($dados['carga_horaria']) || empty($dados['nivel'])) {
        return [
            'sucesso' => false,
            'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.',
            'tipo' => 'danger'
        ];
    }
    
    // Sanitização
    $nome = $conexao->real_escape_string($dados['nome']);
    $descricao = $conexao->real_escape_string($dados['descricao'] ?? '');
    $carga_horaria = (int)$dados['carga_horaria'];
    $nivel = $conexao->real_escape_string($dados['nivel']);
    
    // Inserir novo curso
    $sql = "INSERT INTO cursos (nome, descricao, carga_horaria, nivel) 
            VALUES ('$nome', '$descricao', $carga_horaria, '$nivel')";
    
    if ($conexao->query($sql)) {
        return [
            'sucesso' => true,
            'mensagem' => 'Curso cadastrado com sucesso!',
            'tipo' => 'success',
            'id' => $conexao->insert_id
        ];
    } else {
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao cadastrar curso: ' . $conexao->error,
            'tipo' => 'danger'
        ];
    }
}

/**
 * Cadastra uma nova turma para um curso
 */
function cadastrarTurma($conexao, $dados) {
    // Validação básica
    if (empty($dados['nome']) || empty($dados['curso_id']) || 
        empty($dados['ano']) || empty($dados['periodo'])) {
        return [
            'sucesso' => false,
            'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.',
            'tipo' => 'danger'
        ];
    }
    
    // Sanitização
    $nome = $conexao->real_escape_string($dados['nome']);
    $curso_id = (int)$dados['curso_id'];
    $ano = (int)$dados['ano'];
    $periodo = $conexao->real_escape_string($dados['periodo']);
    $status = $conexao->real_escape_string($dados['status'] ?? 'ativo');
    
    // Verificar se o curso existe
    $check_curso = $conexao->query("SELECT id FROM cursos WHERE id = $curso_id");
    if ($check_curso->num_rows == 0) {
        return [
            'sucesso' => false,
            'mensagem' => 'Curso não encontrado.',
            'tipo' => 'danger'
        ];
    }
    
    // Inserir nova turma
    $sql = "INSERT INTO turmas (nome, curso_id, ano, periodo, status) 
            VALUES ('$nome', $curso_id, $ano, '$periodo', '$status')";
    
    if ($conexao->query($sql)) {
        return [
            'sucesso' => true,
            'mensagem' => 'Turma cadastrada com sucesso!',
            'tipo' => 'success',
            'id' => $conexao->insert_id
        ];
    } else {
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao cadastrar turma: ' . $conexao->error,
            'tipo' => 'danger'
        ];
    }
}
?>
