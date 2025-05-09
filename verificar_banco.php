<?php
// Incluir arquivo de conexão
require_once 'conexao.php';

// Função para verificar se uma tabela existe
function tabelaExiste($conexao, $tabela) {
    $resultado = $conexao->query("SHOW TABLES LIKE '$tabela'");
    return $resultado->num_rows > 0;
}

// Função para verificar se uma coluna existe em uma tabela
function colunaExiste($conexao, $tabela, $coluna) {
    $resultado = $conexao->query("SHOW COLUMNS FROM `$tabela` LIKE '$coluna'");
    return $resultado->num_rows > 0;
}

// Função para criar a tabela de alunos se não existir
function criarTabelaAlunos($conexao) {
    $sql = "CREATE TABLE IF NOT EXISTS `alunos` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `nome` VARCHAR(100) NOT NULL,
        `matricula` VARCHAR(20) NOT NULL,
        `data_nascimento` DATE NULL,
        `responsavel` VARCHAR(100) NULL,
        `telefone_responsavel` VARCHAR(20) NULL,
        `turma_id` INT NOT NULL,
        `status` ENUM('ativo', 'inativo', 'transferido', 'trancado') NOT NULL DEFAULT 'ativo',
        `data_cadastro` DATE NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE INDEX `matricula_UNIQUE` (`matricula` ASC)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    if ($conexao->query($sql)) {
        echo "<p class='text-success'>Tabela 'alunos' criada com sucesso!</p>";
    } else {
        echo "<p class='text-danger'>Erro ao criar tabela 'alunos': " . $conexao->error . "</p>";
    }
}

// Função para criar a tabela de turmas se não existir
function criarTabelaTurmas($conexao) {
    $sql = "CREATE TABLE IF NOT EXISTS `turmas` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `nome` VARCHAR(50) NOT NULL,
        `curso_id` INT NOT NULL,
        `ano` INT NOT NULL,
        `periodo` VARCHAR(20) NOT NULL,
        `status` ENUM('ativo', 'inativo', 'concluido') NOT NULL DEFAULT 'ativo',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    if ($conexao->query($sql)) {
        echo "<p class='text-success'>Tabela 'turmas' criada com sucesso!</p>";
    } else {
        echo "<p class='text-danger'>Erro ao criar tabela 'turmas': " . $conexao->error . "</p>";
    }
}

// Função para criar a tabela de disciplinas se não existir
function criarTabelaDisciplinas($conexao) {
    $sql = "CREATE TABLE IF NOT EXISTS `disciplinas` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `nome` VARCHAR(100) NOT NULL,
        `carga_horaria` INT NOT NULL,
        `descricao` TEXT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    if ($conexao->query($sql)) {
        echo "<p class='text-success'>Tabela 'disciplinas' criada com sucesso!</p>";
    } else {
        echo "<p class='text-danger'>Erro ao criar tabela 'disciplinas': " . $conexao->error . "</p>";
    }
}

// Função para criar a tabela de notas se não existir
function criarTabelaNotas($conexao) {
    $sql = "CREATE TABLE IF NOT EXISTS `notas` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `aluno_id` INT NOT NULL,
        `disciplina_id` INT NOT NULL,
        `professor_id` INT NOT NULL,
        `valor` DECIMAL(4,2) NOT NULL,
        `tipo` ENUM('prova','trabalho','participacao','recuperacao','projeto') NOT NULL,
        `bimestre` INT NOT NULL,
        `data_lancamento` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        INDEX `aluno_id` (`aluno_id`),
        INDEX `disciplina_id` (`disciplina_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    if ($conexao->query($sql)) {
        echo "<p class='text-success'>Tabela 'notas' criada com sucesso!</p>";
    } else {
        echo "<p class='text-danger'>Erro ao criar tabela 'notas': " . $conexao->error . "</p>";
    }
}

// Função para criar a tabela de cursos se não existir
function criarTabelaCursos($conexao) {
    $sql = "CREATE TABLE IF NOT EXISTS `cursos` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `nome` VARCHAR(100) NOT NULL,
        `descricao` TEXT NULL,
        `duracao` INT NOT NULL,
        `status` ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    if ($conexao->query($sql)) {
        echo "<p class='text-success'>Tabela 'cursos' criada com sucesso!</p>";
    } else {
        echo "<p class='text-danger'>Erro ao criar tabela 'cursos': " . $conexao->error . "</p>";
    }
}

// Função para adicionar dados de exemplo
function adicionarDadosExemplo($conexao) {
    // Verificar se já existem dados
    $check_cursos = $conexao->query("SELECT COUNT(*) as total FROM cursos");
    $total_cursos = $check_cursos->fetch_assoc()['total'];
    
    if ($total_cursos == 0) {
        // Adicionar cursos
        $sql_cursos = "INSERT INTO `cursos` (`nome`, `descricao`, `duracao`, `status`) VALUES
            ('Ensino Fundamental', 'Ensino Fundamental Completo', 9, 'ativo'),
            ('Ensino Médio', 'Ensino Médio Completo', 3, 'ativo'),
            ('Técnico em Informática', 'Curso Técnico em Informática', 2, 'ativo');";
        
        if ($conexao->query($sql_cursos)) {
            echo "<p class='text-success'>Cursos de exemplo adicionados com sucesso!</p>";
        } else {
            echo "<p class='text-danger'>Erro ao adicionar cursos: " . $conexao->error . "</p>";
        }
    }
    
    // Verificar se já existem turmas
    $check_turmas = $conexao->query("SELECT COUNT(*) as total FROM turmas");
    $total_turmas = $check_turmas->fetch_assoc()['total'];
    
    if ($total_turmas == 0) {
        // Adicionar turmas
        $sql_turmas = "INSERT INTO `turmas` (`nome`, `curso_id`, `ano`, `periodo`, `status`) VALUES
            ('1º Ano A', 2, 2025, 'Matutino', 'ativo'),
            ('2º Ano A', 2, 2025, 'Matutino', 'ativo'),
            ('3º Ano A', 2, 2025, 'Matutino', 'ativo'),
            ('Turma 1 - Informática', 3, 2025, 'Noturno', 'ativo');";
        
        if ($conexao->query($sql_turmas)) {
            echo "<p class='text-success'>Turmas de exemplo adicionadas com sucesso!</p>";
        } else {
            echo "<p class='text-danger'>Erro ao adicionar turmas: " . $conexao->error . "</p>";
        }
    }
    
    // Verificar se já existem disciplinas
    $check_disciplinas = $conexao->query("SELECT COUNT(*) as total FROM disciplinas");
    $total_disciplinas = $check_disciplinas->fetch_assoc()['total'];
    
    if ($total_disciplinas == 0) {
        // Adicionar disciplinas
        $sql_disciplinas = "INSERT INTO `disciplinas` (`nome`, `carga_horaria`, `descricao`) VALUES
            ('Matemática', 80, 'Matemática básica e avançada'),
            ('Português', 80, 'Língua portuguesa e literatura'),
            ('História', 60, 'História geral e do Brasil'),
            ('Geografia', 60, 'Geografia geral e do Brasil'),
            ('Física', 60, 'Física básica e avançada'),
            ('Química', 60, 'Química básica e avançada'),
            ('Biologia', 60, 'Biologia geral'),
            ('Inglês', 40, 'Língua inglesa'),
            ('Educação Física', 40, 'Atividades físicas e esportes'),
            ('Programação', 80, 'Lógica de programação e desenvolvimento de software');";
        
        if ($conexao->query($sql_disciplinas)) {
            echo "<p class='text-success'>Disciplinas de exemplo adicionadas com sucesso!</p>";
        } else {
            echo "<p class='text-danger'>Erro ao adicionar disciplinas: " . $conexao->error . "</p>";
        }
    }
    
    // Verificar se já existem alunos
    $check_alunos = $conexao->query("SELECT COUNT(*) as total FROM alunos");
    $total_alunos = $check_alunos->fetch_assoc()['total'];
    
    if ($total_alunos == 0) {
        // Adicionar alunos
        $sql_alunos = "INSERT INTO `alunos` (`nome`, `matricula`, `data_nascimento`, `responsavel`, `telefone_responsavel`, `turma_id`, `status`, `data_cadastro`) VALUES
            ('João Silva', '2025001', '2008-05-15', 'Maria Silva', '(11) 99999-1111', 1, 'ativo', '2025-01-15'),
            ('Ana Oliveira', '2025002', '2008-08-20', 'Pedro Oliveira', '(11) 99999-2222', 1, 'ativo', '2025-01-15'),
            ('Carlos Santos', '2025003', '2008-03-10', 'Sandra Santos', '(11) 99999-3333', 1, 'ativo', '2025-01-15'),
            ('Mariana Costa', '2025004', '2008-11-25', 'José Costa', '(11) 99999-4444', 1, 'ativo', '2025-01-15'),
            ('Pedro Souza', '2025005', '2008-07-30', 'Lucia Souza', '(11) 99999-5555', 1, 'ativo', '2025-01-15'),
            ('Juliana Lima', '2025006', '2007-04-12', 'Roberto Lima', '(11) 99999-6666', 2, 'ativo', '2025-01-15'),
            ('Lucas Ferreira', '2025007', '2007-09-18', 'Carla Ferreira', '(11) 99999-7777', 2, 'ativo', '2025-01-15'),
            ('Beatriz Almeida', '2025008', '2007-01-22', 'Marcos Almeida', '(11) 99999-8888', 2, 'ativo', '2025-01-15'),
            ('Gabriel Martins', '2025009', '2007-06-05', 'Fernanda Martins', '(11) 99999-9999', 2, 'ativo', '2025-01-15'),
            ('Isabela Rodrigues', '2025010', '2007-12-15', 'Paulo Rodrigues', '(11) 98888-1111', 2, 'ativo', '2025-01-15'),
            ('Rafael Gomes', '2025011', '2006-02-28', 'Cristina Gomes', '(11) 98888-2222', 3, 'ativo', '2025-01-15'),
            ('Camila Pereira', '2025012', '2006-10-08', 'Antônio Pereira', '(11) 98888-3333', 3, 'ativo', '2025-01-15'),
            ('Matheus Barbosa', '2025013', '2006-07-17', 'Aline Barbosa', '(11) 98888-4444', 3, 'ativo', '2025-01-15'),
            ('Laura Cardoso', '2025014', '2006-03-24', 'Ricardo Cardoso', '(11) 98888-5555', 3, 'ativo', '2025-01-15'),
            ('Gustavo Ribeiro', '2025015', '2006-11-11', 'Patrícia Ribeiro', '(11) 98888-6666', 3, 'ativo', '2025-01-15'),
            ('Thiago Mendes', '2025016', '2005-05-20', 'Luciana Mendes', '(11) 98888-7777', 4, 'ativo', '2025-01-15'),
            ('Fernanda Castro', '2025017', '2005-08-14', 'Eduardo Castro', '(11) 98888-8888', 4, 'ativo', '2025-01-15'),
            ('Bruno Carvalho', '2025018', '2005-01-30', 'Márcia Carvalho', '(11) 98888-9999', 4, 'ativo', '2025-01-15'),
            ('Amanda Nunes', '2025019', '2005-09-05', 'Sérgio Nunes', '(11) 97777-1111', 4, 'ativo', '2025-01-15'),
            ('Leonardo Dias', '2025020', '2005-04-18', 'Vanessa Dias', '(11) 97777-2222', 4, 'ativo', '2025-01-15');";
        
        if ($conexao->query($sql_alunos)) {
            echo "<p class='text-success'>Alunos de exemplo adicionados com sucesso!</p>";
        } else {
            echo "<p class='text-danger'>Erro ao adicionar alunos: " . $conexao->error . "</p>";
        }
    }
    
    // Verificar se já existem notas
    $check_notas = $conexao->query("SELECT COUNT(*) as total FROM notas");
    $total_notas = $check_notas->fetch_assoc()['total'];
    
    if ($total_notas == 0) {
        // Gerar notas aleatórias para os alunos
        $alunos_result = $conexao->query("SELECT id FROM alunos LIMIT 20");
        $alunos = [];
        while ($row = $alunos_result->fetch_assoc()) {
            $alunos[] = $row['id'];
        }
        
        $disciplinas_result = $conexao->query("SELECT id FROM disciplinas LIMIT 10");
        $disciplinas = [];
        while ($row = $disciplinas_result->fetch_assoc()) {
            $disciplinas[] = $row['id'];
        }
        
        $tipos = ['prova', 'trabalho', 'participacao'];
        $valores_sql = [];
        
        foreach ($alunos as $aluno_id) {
            foreach ($disciplinas as $disciplina_id) {
                for ($bimestre = 1; $bimestre <= 4; $bimestre++) {
                    foreach ($tipos as $tipo) {
                        $valor = rand(50, 100) / 10; // Valor entre 5.0 e 10.0
                        $valores_sql[] = "($aluno_id, $disciplina_id, 1, $valor, '$tipo', $bimestre, NOW())";
                    }
                }
            }
        }
        
        if (!empty($valores_sql)) {
            $sql_notas = "INSERT INTO `notas` (`aluno_id`, `disciplina_id`, `professor_id`, `valor`, `tipo`, `bimestre`, `data_lancamento`) VALUES " . implode(", ", $valores_sql);
            
            if ($conexao->query($sql_notas)) {
                echo "<p class='text-success'>Notas de exemplo adicionadas com sucesso!</p>";
            } else {
                echo "<p class='text-danger'>Erro ao adicionar notas: " . $conexao->error . "</p>";
            }
        }
    }
}

// Verificar e corrigir a estrutura do banco de dados
function verificarECorrigirBancoDados($conexao) {
    // Verificar tabelas principais
    $tabelas = ['cursos', 'turmas', 'disciplinas', 'alunos', 'notas'];
    $tabelas_faltantes = [];
    
    foreach ($tabelas as $tabela) {
        if (!tabelaExiste($conexao, $tabela)) {
            $tabelas_faltantes[] = $tabela;
        }
    }
    
    // Criar tabelas faltantes
    if (in_array('cursos', $tabelas_faltantes)) {
        criarTabelaCursos($conexao);
    }
    
    if (in_array('turmas', $tabelas_faltantes)) {
        criarTabelaTurmas($conexao);
    }
    
    if (in_array('disciplinas', $tabelas_faltantes)) {
        criarTabelaDisciplinas($conexao);
    }
    
    if (in_array('alunos', $tabelas_faltantes)) {
        criarTabelaAlunos($conexao);
    }
    
    if (in_array('notas', $tabelas_faltantes)) {
        criarTabelaNotas($conexao);
    }
    
    // Verificar colunas necessárias na tabela alunos
    if (tabelaExiste($conexao, 'alunos')) {
        if (!colunaExiste($conexao, 'alunos', 'nome') && colunaExiste($conexao, 'alunos', 'usuario_id')) {
            // Adicionar coluna nome se não existir mas existir usuario_id
            $sql = "ALTER TABLE `alunos` ADD COLUMN `nome` VARCHAR(100) NULL AFTER `usuario_id`";
            if ($conexao->query($sql)) {
                echo "<p class='text-success'>Coluna 'nome' adicionada à tabela 'alunos'.</p>";
                
                // Atualizar nomes dos alunos a partir da tabela usuarios
                if (tabelaExiste($conexao, 'usuarios')) {
                    $sql_update = "UPDATE `alunos` a JOIN `usuarios` u ON a.usuario_id = u.id SET a.nome = u.nome WHERE a.nome IS NULL";
                    if ($conexao->query($sql_update)) {
                        echo "<p class='text-success'>Nomes dos alunos atualizados com sucesso!</p>";
                    } else {
                        echo "<p class='text-danger'>Erro ao atualizar nomes dos alunos: " . $conexao->error . "</p>";
                    }
                }
            } else {
                echo "<p class='text-danger'>Erro ao adicionar coluna 'nome': " . $conexao->error . "</p>";
            }
        }
    }
    
    // Adicionar dados de exemplo se necessário
    adicionarDadosExemplo($conexao);
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação do Banco de Dados - Sistema de Gestão Acadêmica</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-database me-2"></i> Verificação do Banco de Dados</h3>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-4">Resultados da Verificação</h4>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Esta ferramenta verifica e corrige automaticamente a estrutura do banco de dados para o Sistema de Gestão Acadêmica.
                        </div>
                        
                        <div class="results">
                            <?php verificarECorrigirBancoDados($conexao); ?>
                        </div>
                        
                        <div class="mt-4">
                            <a href="notas.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i> Voltar para a Página de Notas
                            </a>
                            <a href="verificar_banco.php?refresh=1" class="btn btn-success ms-2">
                                <i class="fas fa-sync-alt me-2"></i> Atualizar Verificação
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
