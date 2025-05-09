-- Script para adicionar novos campos à tabela 'alunos'
USE `gestao_academica`;

-- Verificar se a coluna 'cpf_aluno' já existe
SET @existe_cpf_aluno = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = 'gestao_academica' 
                     AND TABLE_NAME = 'alunos' 
                     AND COLUMN_NAME = 'cpf_aluno');

-- Adicionar a coluna 'cpf_aluno' se não existir
SET @sql_cpf_aluno = IF(@existe_cpf_aluno = 0, 
                'ALTER TABLE `alunos` ADD COLUMN `cpf_aluno` VARCHAR(14) NULL AFTER `data_nascimento`', 
                'SELECT "Coluna cpf_aluno já existe"');
PREPARE stmt_cpf_aluno FROM @sql_cpf_aluno;
EXECUTE stmt_cpf_aluno;
DEALLOCATE PREPARE stmt_cpf_aluno;

-- Verificar se a coluna 'sexo' já existe
SET @existe_sexo = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = 'gestao_academica' 
                     AND TABLE_NAME = 'alunos' 
                     AND COLUMN_NAME = 'sexo');

-- Adicionar a coluna 'sexo' se não existir
SET @sql_sexo = IF(@existe_sexo = 0, 
                'ALTER TABLE `alunos` ADD COLUMN `sexo` VARCHAR(50) NULL AFTER `cpf_aluno`', 
                'SELECT "Coluna sexo já existe"');
PREPARE stmt_sexo FROM @sql_sexo;
EXECUTE stmt_sexo;
DEALLOCATE PREPARE stmt_sexo;

-- Verificar se a coluna 'genero_personalizado' já existe
SET @existe_genero = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = 'gestao_academica' 
                     AND TABLE_NAME = 'alunos' 
                     AND COLUMN_NAME = 'genero_personalizado');

-- Adicionar a coluna 'genero_personalizado' se não existir
SET @sql_genero = IF(@existe_genero = 0, 
                'ALTER TABLE `alunos` ADD COLUMN `genero_personalizado` VARCHAR(100) NULL AFTER `sexo`', 
                'SELECT "Coluna genero_personalizado já existe"');
PREPARE stmt_genero FROM @sql_genero;
EXECUTE stmt_genero;
DEALLOCATE PREPARE stmt_genero;

-- Verificar se a coluna 'cpf_responsavel' já existe
SET @existe_cpf = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = 'gestao_academica' 
                     AND TABLE_NAME = 'alunos' 
                     AND COLUMN_NAME = 'cpf_responsavel');

-- Adicionar a coluna 'cpf_responsavel' se não existir
SET @sql_cpf = IF(@existe_cpf = 0, 
                'ALTER TABLE `alunos` ADD COLUMN `cpf_responsavel` VARCHAR(14) NULL AFTER `telefone_responsavel`', 
                'SELECT "Coluna cpf_responsavel já existe"');
PREPARE stmt_cpf FROM @sql_cpf;
EXECUTE stmt_cpf;
DEALLOCATE PREPARE stmt_cpf;

-- Verificar se a coluna 'email_responsavel' já existe
SET @existe_email = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = 'gestao_academica' 
                     AND TABLE_NAME = 'alunos' 
                     AND COLUMN_NAME = 'email_responsavel');

-- Adicionar a coluna 'email_responsavel' se não existir
SET @sql_email = IF(@existe_email = 0, 
                'ALTER TABLE `alunos` ADD COLUMN `email_responsavel` VARCHAR(255) NULL AFTER `cpf_responsavel`', 
                'SELECT "Coluna email_responsavel já existe"');
PREPARE stmt_email FROM @sql_email;
EXECUTE stmt_email;
DEALLOCATE PREPARE stmt_email;

-- Verificar se a coluna 'inscricao' já existe
SET @existe_inscricao = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = 'gestao_academica' 
                     AND TABLE_NAME = 'alunos' 
                     AND COLUMN_NAME = 'inscricao');

-- Adicionar a coluna 'inscricao' se não existir
SET @sql_inscricao = IF(@existe_inscricao = 0, 
                'ALTER TABLE `alunos` ADD COLUMN `inscricao` VARCHAR(20) NULL AFTER `matricula`', 
                'SELECT "Coluna inscricao já existe"');
PREPARE stmt_inscricao FROM @sql_inscricao;
EXECUTE stmt_inscricao;
DEALLOCATE PREPARE stmt_inscricao;
