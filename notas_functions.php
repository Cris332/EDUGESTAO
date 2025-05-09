<?php
/**
 * Funções para gerenciamento de notas
 */

/**
 * Obtém todas as turmas ativas
 */
function obterTurmas($conexao) {
    $sql = "SELECT t.id, t.nome, c.nome as curso, t.ano, t.periodo
            FROM turmas t
            JOIN cursos c ON t.curso_id = c.id
            WHERE t.status = 'ativo'
            ORDER BY t.nome";
    
    $resultado = $conexao->query($sql);
    
    $turmas = [];
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $turmas[] = $row;
        }
    }
    
    return $turmas;
}

/**
 * Obtém todas as disciplinas
 */
function obterDisciplinas($conexao) {
    $sql = "SELECT id, nome, carga_horaria
            FROM disciplinas
            ORDER BY nome";
    
    $resultado = $conexao->query($sql);
    
    $disciplinas = [];
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $disciplinas[] = $row;
        }
    }
    
    return $disciplinas;
}

/**
 * Obtém todos os alunos de uma turma específica
 */
function obterAlunosPorTurma($conexao, $turma_id) {
    $turma_id = (int)$turma_id;
    
    $sql = "SELECT a.id, a.matricula, u.nome, a.status
            FROM alunos a
            JOIN usuarios u ON a.usuario_id = u.id
            WHERE a.turma_id = $turma_id AND a.status = 'ativo'
            ORDER BY u.nome";
    
    $resultado = $conexao->query($sql);
    
    $alunos = [];
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $alunos[] = $row;
        }
    }
    
    return $alunos;
}

/**
 * Obtém as notas de um aluno por disciplina e bimestre
 */
function obterNotasAluno($conexao, $aluno_id, $disciplina_id = null) {
    $aluno_id = (int)$aluno_id;
    
    $where = "n.aluno_id = $aluno_id";
    if ($disciplina_id) {
        $disciplina_id = (int)$disciplina_id;
        $where .= " AND n.disciplina_id = $disciplina_id";
    }
    
    $sql = "SELECT n.id, n.aluno_id, n.disciplina_id, d.nome as disciplina, 
                   n.valor, n.tipo, n.bimestre, n.data_lancamento,
                   u.nome as professor
            FROM notas n
            JOIN disciplinas d ON n.disciplina_id = d.id
            JOIN professores p ON n.professor_id = p.id
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE $where
            ORDER BY d.nome, n.bimestre, n.tipo";
    
    $resultado = $conexao->query($sql);
    
    $notas = [];
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $disciplina = $row['disciplina_id'];
            $bimestre = $row['bimestre'];
            $tipo = $row['tipo'];
            
            if (!isset($notas[$disciplina])) {
                $notas[$disciplina] = [
                    'nome' => $row['disciplina'],
                    'bimestres' => [
                        1 => ['prova' => null, 'trabalho' => null, 'projeto' => null, 'participacao' => null, 'recuperacao' => null, 'media' => null],
                        2 => ['prova' => null, 'trabalho' => null, 'projeto' => null, 'participacao' => null, 'recuperacao' => null, 'media' => null],
                        3 => ['prova' => null, 'trabalho' => null, 'projeto' => null, 'participacao' => null, 'recuperacao' => null, 'media' => null],
                        4 => ['prova' => null, 'trabalho' => null, 'projeto' => null, 'participacao' => null, 'recuperacao' => null, 'media' => null]
                    ]
                ];
            }
            
            $notas[$disciplina]['bimestres'][$bimestre][$tipo] = [
                'id' => $row['id'],
                'valor' => $row['valor'],
                'data' => $row['data_lancamento'],
                'professor' => $row['professor']
            ];
            
            // Calcular média do bimestre (considerando apenas prova e trabalho para simplificar)
            $prova = isset($notas[$disciplina]['bimestres'][$bimestre]['prova']) ? 
                     $notas[$disciplina]['bimestres'][$bimestre]['prova']['valor'] : 0;
            
            $trabalho = isset($notas[$disciplina]['bimestres'][$bimestre]['trabalho']) ? 
                        $notas[$disciplina]['bimestres'][$bimestre]['trabalho']['valor'] : 0;
            
            $participacao = isset($notas[$disciplina]['bimestres'][$bimestre]['participacao']) ? 
                           $notas[$disciplina]['bimestres'][$bimestre]['participacao']['valor'] : 0;
            
            // Média ponderada: prova (60%), trabalho (30%), participação (10%)
            if ($prova > 0 || $trabalho > 0 || $participacao > 0) {
                $divisor = 0;
                $soma = 0;
                
                if ($prova > 0) {
                    $soma += $prova * 0.6;
                    $divisor += 0.6;
                }
                
                if ($trabalho > 0) {
                    $soma += $trabalho * 0.3;
                    $divisor += 0.3;
                }
                
                if ($participacao > 0) {
                    $soma += $participacao * 0.1;
                    $divisor += 0.1;
                }
                
                $media = $divisor > 0 ? round($soma / $divisor, 1) : 0;
                $notas[$disciplina]['bimestres'][$bimestre]['media'] = $media;
            }
        }
    }
    
    return $notas;
}

/**
 * Salva uma nota no banco de dados
 */
function salvarNota($conexao, $dados) {
    // Validação básica
    if (empty($dados['aluno_id']) || empty($dados['disciplina_id']) || 
        empty($dados['professor_id']) || empty($dados['valor']) || 
        empty($dados['tipo']) || empty($dados['bimestre'])) {
        return [
            'sucesso' => false,
            'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.',
            'tipo' => 'danger'
        ];
    }
    
    // Sanitização
    $aluno_id = (int)$dados['aluno_id'];
    $disciplina_id = (int)$dados['disciplina_id'];
    $professor_id = (int)$dados['professor_id'];
    $valor = (float)$dados['valor'];
    $tipo = $conexao->real_escape_string($dados['tipo']);
    $bimestre = (int)$dados['bimestre'];
    $data_lancamento = date('Y-m-d');
    
    // Validar valor da nota (entre 0 e 10)
    if ($valor < 0 || $valor > 10) {
        return [
            'sucesso' => false,
            'mensagem' => 'O valor da nota deve estar entre 0 e 10.',
            'tipo' => 'danger'
        ];
    }
    
    // Verificar se já existe uma nota para este aluno, disciplina, tipo e bimestre
    $sql = "SELECT id FROM notas 
            WHERE aluno_id = $aluno_id 
            AND disciplina_id = $disciplina_id 
            AND tipo = '$tipo' 
            AND bimestre = $bimestre";
    
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows > 0) {
        // Atualizar nota existente
        $nota = $resultado->fetch_assoc();
        $id = $nota['id'];
        
        $sql = "UPDATE notas 
                SET valor = $valor, professor_id = $professor_id, data_lancamento = '$data_lancamento' 
                WHERE id = $id";
        
        if ($conexao->query($sql)) {
            return [
                'sucesso' => true,
                'mensagem' => 'Nota atualizada com sucesso!',
                'tipo' => 'success',
                'id' => $id
            ];
        } else {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao atualizar nota: ' . $conexao->error,
                'tipo' => 'danger'
            ];
        }
    } else {
        // Inserir nova nota
        $sql = "INSERT INTO notas (aluno_id, disciplina_id, professor_id, valor, tipo, bimestre, data_lancamento) 
                VALUES ($aluno_id, $disciplina_id, $professor_id, $valor, '$tipo', $bimestre, '$data_lancamento')";
        
        if ($conexao->query($sql)) {
            return [
                'sucesso' => true,
                'mensagem' => 'Nota cadastrada com sucesso!',
                'tipo' => 'success',
                'id' => $conexao->insert_id
            ];
        } else {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao cadastrar nota: ' . $conexao->error,
                'tipo' => 'danger'
            ];
        }
    }
}

/**
 * Obtém a média final de um aluno em uma disciplina
 */
function calcularMediaFinal($conexao, $aluno_id, $disciplina_id) {
    $aluno_id = (int)$aluno_id;
    $disciplina_id = (int)$disciplina_id;
    
    $notas = obterNotasAluno($conexao, $aluno_id, $disciplina_id);
    
    if (empty($notas) || !isset($notas[$disciplina_id])) {
        return 0;
    }
    
    $bimestres = $notas[$disciplina_id]['bimestres'];
    $soma = 0;
    $count = 0;
    
    foreach ($bimestres as $bimestre) {
        if ($bimestre['media'] !== null) {
            $soma += $bimestre['media'];
            $count++;
        }
    }
    
    return $count > 0 ? round($soma / $count, 1) : 0;
}

/**
 * Obtém o professor de uma disciplina para uma turma
 */
function obterProfessorDisciplina($conexao, $disciplina_id, $turma_id) {
    $disciplina_id = (int)$disciplina_id;
    $turma_id = (int)$turma_id;
    
    $sql = "SELECT p.id
            FROM professores p
            JOIN professor_disciplina pd ON p.id = pd.professor_id
            JOIN turmas t ON pd.curso_id = t.curso_id
            WHERE pd.disciplina_id = $disciplina_id AND t.id = $turma_id
            LIMIT 1";
    
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        return $row['id'];
    }
    
    // Se não encontrar, retorna o primeiro professor cadastrado (para simplificar)
    $sql = "SELECT id FROM professores LIMIT 1";
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        return $row['id'];
    }
    
    return 1; // Valor padrão
}

/**
 * Obtém o boletim completo de um aluno
 */
function obterBoletimAluno($conexao, $aluno_id) {
    $aluno_id = (int)$aluno_id;
    
    // Obter dados do aluno
    $sql = "SELECT a.id, a.matricula, u.nome, t.nome as turma, c.nome as curso
            FROM alunos a
            JOIN usuarios u ON a.usuario_id = u.id
            JOIN turmas t ON a.turma_id = t.id
            JOIN cursos c ON t.curso_id = c.id
            WHERE a.id = $aluno_id";
    
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows === 0) {
        return null;
    }
    
    $aluno = $resultado->fetch_assoc();
    
    // Obter notas do aluno
    $notas = obterNotasAluno($conexao, $aluno_id);
    
    // Calcular médias finais
    foreach ($notas as $disciplina_id => &$disciplina) {
        $soma_medias = 0;
        $count = 0;
        
        foreach ($disciplina['bimestres'] as $bimestre) {
            if ($bimestre['media'] !== null) {
                $soma_medias += $bimestre['media'];
                $count++;
            }
        }
        
        $disciplina['media_final'] = $count > 0 ? round($soma_medias / $count, 1) : 0;
        $disciplina['situacao'] = $disciplina['media_final'] >= 6 ? 'Aprovado' : 'Reprovado';
    }
    
    return [
        'aluno' => $aluno,
        'disciplinas' => $notas
    ];
}
