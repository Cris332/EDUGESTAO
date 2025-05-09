<?php
/**
 * Funções para gerenciamento de estudantes
 */

/**
 * Obtém a lista de todas as turmas ativas
 */
function obterTurmas($conexao) {
    $sql = "SELECT id, nome, curso_id, periodo FROM turmas WHERE status = 'ativo' ORDER BY nome";
    $resultado = $conexao->query($sql);
    
    $turmas = array();
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $turmas[] = $row;
        }
    }
    
    return $turmas;
}

/**
 * Pesquisa estudantes com base em um termo de pesquisa
 */
function pesquisarEstudantes($conexao, $termo = '') {
    $sql = "SELECT a.id, a.matricula, a.data_nascimento, a.responsavel, a.telefone_responsavel, 
                   a.status, a.data_cadastro, a.foto, u.nome, t.nome as turma_nome
            FROM alunos a
            JOIN usuarios u ON a.usuario_id = u.id
            JOIN turmas t ON a.turma_id = t.id
            WHERE 1=1";
    
    if (!empty($termo)) {
        $termo = $conexao->real_escape_string("%$termo%");
        $sql .= " AND (u.nome LIKE '$termo' OR a.matricula LIKE '$termo')";
    }
    
    $sql .= " ORDER BY u.nome LIMIT 50";
    
    $resultado = $conexao->query($sql);
    
    $estudantes = array();
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $estudantes[] = $row;
        }
    }
    
    return $estudantes;
}

/**
 * Obtém os dados de um estudante específico pelo ID
 */
function obterEstudantePorId($conexao, $id) {
    $id = (int)$id;
    $sql = "SELECT a.id, a.usuario_id, a.matricula, a.inscricao, a.data_nascimento, a.cpf_aluno,
                   a.sexo, a.genero_personalizado, a.responsavel, a.telefone_responsavel, 
                   a.cpf_responsavel, a.email_responsavel, a.turma_id, a.status, a.data_cadastro, a.foto, 
                   u.nome, u.email, t.nome as turma
            FROM alunos a
            JOIN usuarios u ON a.usuario_id = u.id
            JOIN turmas t ON a.turma_id = t.id
            WHERE a.id = $id";
    
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows > 0) {
        return $resultado->fetch_assoc();
    }
    
    return null;
}

/**
 * Gera um número de matrícula único
 */
function gerarMatricula($conexao, $turma_id) {
    // Obtém o ano atual
    $ano = date('Y');
    
    // Obtém o código do curso baseado na turma
    $sql = "SELECT curso_id FROM turmas WHERE id = $turma_id";
    $resultado = $conexao->query($sql);
    $curso_id = 1; // Valor padrão
    
    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $curso_id = $row['curso_id'];
    }
    
    // Conta quantos alunos já existem para este curso e ano
    $sql = "SELECT COUNT(*) as total 
            FROM alunos a 
            JOIN turmas t ON a.turma_id = t.id 
            WHERE t.curso_id = $curso_id 
            AND YEAR(a.data_cadastro) = $ano";
    
    $resultado = $conexao->query($sql);
    $row = $resultado->fetch_assoc();
    $contador = $row['total'] + 1;
    
    // Formata o número de matrícula: ANO + CURSO_ID + SEQUENCIAL
    $matricula = $ano . str_pad($curso_id, 2, '0', STR_PAD_LEFT) . str_pad($contador, 3, '0', STR_PAD_LEFT);
    
    return $matricula;
}

/**
 * Cadastra um novo estudante
 */
function cadastrarEstudante($conexao, $dados, $arquivos) {
    // Validação básica
    if (empty($dados['nome']) || empty($dados['data_nascimento']) || 
        empty($dados['responsavel']) || empty($dados['telefone_responsavel']) || 
        empty($dados['turma_id']) || empty($dados['sexo']) || 
        empty($dados['inscricao']) || empty($dados['cpf_responsavel']) || 
        empty($dados['email_responsavel']) || empty($dados['cpf_aluno'])) {
        return [
            'sucesso' => false,
            'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.',
            'tipo' => 'danger'
        ];
    }
    
    // Validação adicional para gênero personalizado
    if ($dados['sexo'] === 'Personalizado' && empty($dados['genero_personalizado'])) {
        return [
            'sucesso' => false,
            'mensagem' => 'Por favor, especifique o gênero personalizado.',
            'tipo' => 'danger'
        ];
    }
    
    // Sanitização
    $nome = $conexao->real_escape_string($dados['nome']);
    $email = $conexao->real_escape_string($dados['email'] ?? $nome . '@aluno.edugestao.com');
    $data_nascimento = $conexao->real_escape_string($dados['data_nascimento']);
    $cpf_aluno = $conexao->real_escape_string($dados['cpf_aluno']);
    $sexo = $conexao->real_escape_string($dados['sexo']);
    $genero_personalizado = $sexo === 'Personalizado' ? $conexao->real_escape_string($dados['genero_personalizado']) : null;
    $responsavel = $conexao->real_escape_string($dados['responsavel']);
    $telefone_responsavel = $conexao->real_escape_string($dados['telefone_responsavel']);
    $cpf_responsavel = $conexao->real_escape_string($dados['cpf_responsavel']);
    $email_responsavel = $conexao->real_escape_string($dados['email_responsavel']);
    $turma_id = (int)$dados['turma_id'];
    $inscricao = $conexao->real_escape_string($dados['inscricao']);
    $status = $conexao->real_escape_string($dados['status'] ?? 'ativo');
    
    // Gerar matrícula se não fornecida
    $matricula = !empty($dados['matricula']) ? 
                 $conexao->real_escape_string($dados['matricula']) : 
                 gerarMatricula($conexao, $turma_id);
    
    // Verificar se a matrícula já existe
    $sql = "SELECT id FROM alunos WHERE matricula = '$matricula'";
    $resultado = $conexao->query($sql);
    if ($resultado->num_rows > 0) {
        return [
            'sucesso' => false,
            'mensagem' => 'A matrícula informada já está em uso.',
            'tipo' => 'danger'
        ];
    }
    
    // Iniciar transação
    $conexao->begin_transaction();
    
    try {
        // Criar usuário
        $senha_hash = password_hash('123456', PASSWORD_DEFAULT); // Senha padrão inicial
        $sql = "INSERT INTO usuarios (nome, email, senha, tipo, status, data_cadastro) 
                VALUES ('$nome', '$email', '$senha_hash', 'aluno', '$status', NOW())";
        
        if (!$conexao->query($sql)) {
            throw new Exception("Erro ao cadastrar usuário: " . $conexao->error);
        }
        
        $usuario_id = $conexao->insert_id;
        
        // Processar upload de foto
        $foto = '';
        if (isset($arquivos['foto']) && $arquivos['foto']['error'] === UPLOAD_ERR_OK) {
            $foto = processarUploadFoto($arquivos['foto'], $usuario_id);
            if (!$foto) {
                throw new Exception("Erro ao processar upload da foto.");
            }
        }
        
        // Cadastrar aluno
        $sql = "INSERT INTO alunos (usuario_id, matricula, inscricao, data_nascimento, cpf_aluno, sexo, genero_personalizado,
                            responsavel, telefone_responsavel, cpf_responsavel, email_responsavel,
                            turma_id, status, data_cadastro, foto) 
				VALUES ($usuario_id, '$matricula', '$inscricao', '$data_nascimento', '$cpf_aluno', '$sexo', 
					" . ($genero_personalizado ? "'$genero_personalizado'" : "NULL") . ", 
					'$responsavel', '$telefone_responsavel', '$cpf_responsavel', '$email_responsavel',
					$turma_id, '$status', NOW(), '$foto')";
        
        if (!$conexao->query($sql)) {
            throw new Exception("Erro ao cadastrar aluno: " . $conexao->error);
        }
        
        // Commit da transação
        $conexao->commit();
        
        return [
            'sucesso' => true,
            'mensagem' => 'Estudante cadastrado com sucesso! A senha inicial é 123456.',
            'tipo' => 'success'
        ];
        
    } catch (Exception $e) {
        // Rollback em caso de erro
        $conexao->rollback();
        
        return [
            'sucesso' => false,
            'mensagem' => $e->getMessage(),
            'tipo' => 'danger'
        ];
    }
}

/**
 * Edita os dados de um estudante existente
 */
function editarEstudante($conexao, $dados, $arquivos) {
    // Validação básica
    if (empty($dados['id']) || empty($dados['nome']) || 
        empty($dados['data_nascimento']) || empty($dados['responsavel']) || 
        empty($dados['telefone_responsavel']) || empty($dados['turma_id']) ||
        empty($dados['sexo']) || empty($dados['cpf_responsavel']) || 
        empty($dados['email_responsavel']) || empty($dados['inscricao']) ||
        empty($dados['cpf_aluno'])) {
        return [
            'sucesso' => false,
            'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.',
            'tipo' => 'danger'
        ];
    }
    
    // Sanitização
    $id = (int)$dados['id'];
    $usuario_id = (int)$dados['usuario_id'];
    $nome = $conexao->real_escape_string($dados['nome']);
    $email = $conexao->real_escape_string($dados['email'] ?? $nome . '@aluno.edugestao.com');
    $data_nascimento = $conexao->real_escape_string($dados['data_nascimento']);
    $cpf_aluno = $conexao->real_escape_string($dados['cpf_aluno']);
    $sexo = $conexao->real_escape_string($dados['sexo']);
    $genero_personalizado = ($sexo === 'Personalizado') ? $conexao->real_escape_string($dados['genero_personalizado']) : null;
    $responsavel = $conexao->real_escape_string($dados['responsavel']);
    $telefone_responsavel = $conexao->real_escape_string($dados['telefone_responsavel']);
    $cpf_responsavel = $conexao->real_escape_string($dados['cpf_responsavel']);
    $email_responsavel = $conexao->real_escape_string($dados['email_responsavel']);
    $turma_id = (int)$dados['turma_id'];
    $matricula = $conexao->real_escape_string($dados['matricula']);
    $inscricao = $conexao->real_escape_string($dados['inscricao']);
    $status = $conexao->real_escape_string($dados['status'] ?? 'ativo');
    
    // Verificar se a matrícula já existe (exceto para o próprio estudante)
    $sql = "SELECT id FROM alunos WHERE matricula = '$matricula' AND id != $id";
    $resultado = $conexao->query($sql);
    if ($resultado->num_rows > 0) {
        return [
            'sucesso' => false,
            'mensagem' => 'A matrícula informada já está em uso por outro estudante.',
            'tipo' => 'danger'
        ];
    }
    
    // Iniciar transação
    $conexao->begin_transaction();
    
    try {
        // Atualizar usuário
        $sql = "UPDATE usuarios 
                SET nome = '$nome', email = '$email', status = '$status' 
                WHERE id = $usuario_id";
        
        if (!$conexao->query($sql)) {
            throw new Exception("Erro ao atualizar usuário: " . $conexao->error);
        }
        
        // Processar upload de foto
        $set_foto = "";
        if (isset($arquivos['foto']) && $arquivos['foto']['error'] === UPLOAD_ERR_OK) {
            $foto = processarUploadFoto($arquivos['foto'], $usuario_id);
            if (!$foto) {
                throw new Exception("Erro ao processar upload da foto.");
            }
            $set_foto = ", foto = '$foto'";
        }
        
        // Atualizar aluno
        $sql = "UPDATE alunos 
                SET matricula = '$matricula', inscricao = '$inscricao', data_nascimento = '$data_nascimento', 
                    cpf_aluno = '$cpf_aluno', sexo = '$sexo', genero_personalizado = " . ($genero_personalizado ? "'$genero_personalizado'" : "NULL") . ", 
                    responsavel = '$responsavel', telefone_responsavel = '$telefone_responsavel', 
                    cpf_responsavel = '$cpf_responsavel', email_responsavel = '$email_responsavel', 
                    turma_id = $turma_id, status = '$status'$set_foto
                WHERE id = $id";
        
        if (!$conexao->query($sql)) {
            throw new Exception("Erro ao atualizar aluno: " . $conexao->error);
        }
        
        // Commit da transação
        $conexao->commit();
        
        return [
            'sucesso' => true,
            'mensagem' => 'Dados do estudante atualizados com sucesso!',
            'tipo' => 'success'
        ];
        
    } catch (Exception $e) {
        // Rollback em caso de erro
        $conexao->rollback();
        
        return [
            'sucesso' => false,
            'mensagem' => $e->getMessage(),
            'tipo' => 'danger'
        ];
    }
}

/**
 * Exclui um estudante
 */
function excluirEstudante($conexao, $id) {
    $id = (int)$id;
    
    // Obter o ID do usuário associado ao aluno
    $sql = "SELECT usuario_id FROM alunos WHERE id = $id";
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows === 0) {
        return [
            'sucesso' => false,
            'mensagem' => 'Estudante não encontrado.',
            'tipo' => 'danger'
        ];
    }
    
    $row = $resultado->fetch_assoc();
    $usuario_id = $row['usuario_id'];
    
    // Iniciar transação
    $conexao->begin_transaction();
    
    try {
        // Excluir aluno
        $sql = "DELETE FROM alunos WHERE id = $id";
        if (!$conexao->query($sql)) {
            throw new Exception("Erro ao excluir estudante: " . $conexao->error);
        }
        
        // Excluir usuário
        $sql = "DELETE FROM usuarios WHERE id = $usuario_id";
        if (!$conexao->query($sql)) {
            throw new Exception("Erro ao excluir usuário: " . $conexao->error);
        }
        
        // Commit da transação
        $conexao->commit();
        
        return [
            'sucesso' => true,
            'mensagem' => 'Estudante excluído com sucesso!',
            'tipo' => 'success'
        ];
        
    } catch (Exception $e) {
        // Rollback em caso de erro
        $conexao->rollback();
        
        return [
            'sucesso' => false,
            'mensagem' => $e->getMessage(),
            'tipo' => 'danger'
        ];
    }
}

/**
 * Processa o upload de uma foto de perfil
 */
function processarUploadFoto($arquivo, $usuario_id) {
    // Verificar se o diretório de uploads existe
    $diretorio = 'uploads/estudantes/';
    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }
    
    // Verificar tipo de arquivo
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($arquivo['type'], $tipos_permitidos)) {
        return false;
    }
    
    // Verificar tamanho (máximo 2MB)
    if ($arquivo['size'] > 2 * 1024 * 1024) {
        return false;
    }
    
    // Gerar nome único para o arquivo
    $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
    $nome_arquivo = 'estudante_' . $usuario_id . '_' . time() . '.' . $extensao;
    $caminho_completo = $diretorio . $nome_arquivo;
    
    // Mover o arquivo para o diretório de uploads
    if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
        return $nome_arquivo;
    }
    
    return false;
}
