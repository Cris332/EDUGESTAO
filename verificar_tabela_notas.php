<?php
// Incluir arquivo de conexão
require_once 'conexao.php';

// Verificar se a tabela notas existe
$check_table = $conexao->query("SHOW TABLES LIKE 'notas'");
if ($check_table->num_rows == 0) {
    echo "<h3>A tabela 'notas' não existe no banco de dados.</h3>";
    
    // Criar a tabela notas
    $sql_create = "CREATE TABLE IF NOT EXISTS `notas` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `aluno_id` int(11) NOT NULL,
        `disciplina_id` int(11) NOT NULL,
        `professor_id` int(11) NOT NULL,
        `valor` decimal(4,2) NOT NULL,
        `tipo` enum('prova','trabalho','participacao','recuperacao','projeto') NOT NULL,
        `bimestre` int(11) NOT NULL,
        `data_lancamento` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `aluno_id` (`aluno_id`),
        KEY `disciplina_id` (`disciplina_id`),
        KEY `professor_id` (`professor_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    if ($conexao->query($sql_create)) {
        echo "<p>Tabela 'notas' criada com sucesso!</p>";
    } else {
        echo "<p>Erro ao criar tabela 'notas': " . $conexao->error . "</p>";
    }
} else {
    echo "<h3>A tabela 'notas' já existe no banco de dados.</h3>";
    
    // Verificar a estrutura da tabela
    $check_structure = $conexao->query("DESCRIBE notas");
    echo "<h4>Estrutura da tabela 'notas':</h4>";
    echo "<table border='1'><tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";
    
    while ($row = $check_structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Verificar se há registros na tabela
    $check_records = $conexao->query("SELECT COUNT(*) as total FROM notas");
    $total = $check_records->fetch_assoc()['total'];
    echo "<p>Total de registros na tabela 'notas': " . $total . "</p>";
    
    if ($total == 0) {
        echo "<p>A tabela está vazia. Deseja inserir alguns registros de exemplo?</p>";
        echo "<a href='?inserir_exemplos=1'>Inserir exemplos</a>";
    }
}

// Inserir registros de exemplo se solicitado
if (isset($_GET['inserir_exemplos']) && $_GET['inserir_exemplos'] == 1) {
    // Verificar se existem alunos e disciplinas
    $check_alunos = $conexao->query("SELECT id FROM alunos LIMIT 1");
    $check_disciplinas = $conexao->query("SELECT id FROM disciplinas LIMIT 1");
    
    if ($check_alunos->num_rows == 0 || $check_disciplinas->num_rows == 0) {
        echo "<p>É necessário ter alunos e disciplinas cadastrados para inserir exemplos de notas.</p>";
    } else {
        // Obter IDs de alunos e disciplinas
        $alunos = [];
        $result_alunos = $conexao->query("SELECT id FROM alunos LIMIT 5");
        while ($row = $result_alunos->fetch_assoc()) {
            $alunos[] = $row['id'];
        }
        
        $disciplinas = [];
        $result_disciplinas = $conexao->query("SELECT id FROM disciplinas");
        while ($row = $result_disciplinas->fetch_assoc()) {
            $disciplinas[] = $row['id'];
        }
        
        // Inserir notas de exemplo
        $tipos = ['prova', 'trabalho', 'participacao'];
        $inserted = 0;
        
        foreach ($alunos as $aluno_id) {
            foreach ($disciplinas as $disciplina_id) {
                for ($bimestre = 1; $bimestre <= 4; $bimestre++) {
                    foreach ($tipos as $tipo) {
                        $valor = rand(50, 100) / 10; // Valor entre 5.0 e 10.0
                        
                        $sql = "INSERT INTO notas (aluno_id, disciplina_id, professor_id, valor, tipo, bimestre, data_lancamento) 
                                VALUES ($aluno_id, $disciplina_id, 1, $valor, '$tipo', $bimestre, NOW())";
                        
                        if ($conexao->query($sql)) {
                            $inserted++;
                        }
                    }
                }
            }
        }
        
        echo "<p>Foram inseridos $inserted registros de exemplo na tabela 'notas'.</p>";
    }
}

// Voltar para a página de notas
echo "<p><a href='notas.php'>Voltar para a página de notas</a></p>";
?>
