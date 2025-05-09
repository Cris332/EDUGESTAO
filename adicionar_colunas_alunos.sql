-- Script para adicionar as colunas 'foto' e 'inscricao' à tabela 'alunos'
USE `gestao_academica`;

-- Verificar se a coluna 'foto' já existe
SET @existe_foto = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = 'gestao_academica' 
                    AND TABLE_NAME = 'alunos' 
                    AND COLUMN_NAME = 'foto');

-- Adicionar a coluna 'foto' se não existir
SET @sql_foto = IF(@existe_foto = 0, 
                'ALTER TABLE `alunos` ADD COLUMN `foto` VARCHAR(255) NULL AFTER `data_cadastro`', 
                'SELECT "Coluna foto já existe"');
PREPARE stmt_foto FROM @sql_foto;
EXECUTE stmt_foto;
DEALLOCATE PREPARE stmt_foto;

-- Verificar se a coluna 'inscricao' já existe
SET @existe_inscricao = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                         WHERE TABLE_SCHEMA = 'gestao_academica' 
                         AND TABLE_NAME = 'alunos' 
                         AND COLUMN_NAME = 'inscricao');

-- Adicionar a coluna 'inscricao' se não existir
SET @sql_inscricao = IF(@existe_inscricao = 0, 
                     'ALTER TABLE `alunos` ADD COLUMN `inscricao` VARCHAR(50) NULL AFTER `foto`', 
                     'SELECT "Coluna inscricao já existe"');
PREPARE stmt_inscricao FROM @sql_inscricao;
EXECUTE stmt_inscricao;
DEALLOCATE PREPARE stmt_inscricao;
