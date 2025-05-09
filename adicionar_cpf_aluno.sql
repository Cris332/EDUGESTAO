-- Script para adicionar o campo cpf_aluno à tabela 'alunos'
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
