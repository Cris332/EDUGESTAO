USE `gestao_academica`;

-- Adicionando notas para mais turmas para garantir que tenhamos dados suficientes para o gráfico
-- Notas para alunos do 7º Ano (turma_id = 2)
INSERT INTO `notas` (`aluno_id`, `disciplina_id`, `professor_id`, `valor`, `tipo`, `bimestre`, `data_lancamento`) VALUES
(6, 1, 1, 9.5, 'prova', 1, '2025-03-15'),
(7, 1, 1, 8.7, 'prova', 1, '2025-03-15'),
(8, 1, 1, 9.2, 'prova', 1, '2025-03-15'),
(9, 1, 1, 8.9, 'prova', 1, '2025-03-15'),
(10, 1, 1, 9.0, 'prova', 1, '2025-03-15'),
(6, 2, 2, 9.0, 'prova', 1, '2025-03-18'),
(7, 2, 2, 8.5, 'prova', 1, '2025-03-18'),
(8, 2, 2, 9.3, 'prova', 1, '2025-03-18'),
(9, 2, 2, 8.8, 'prova', 1, '2025-03-18'),
(10, 2, 2, 9.1, 'prova', 1, '2025-03-18');

-- Notas para alunos do 8º Ano (turma_id = 3)
INSERT INTO `notas` (`aluno_id`, `disciplina_id`, `professor_id`, `valor`, `tipo`, `bimestre`, `data_lancamento`) VALUES
(11, 1, 1, 7.8, 'prova', 1, '2025-03-15'),
(12, 1, 1, 8.2, 'prova', 1, '2025-03-15'),
(13, 1, 1, 7.5, 'prova', 1, '2025-03-15'),
(14, 1, 1, 8.0, 'prova', 1, '2025-03-15'),
(15, 1, 1, 7.9, 'prova', 1, '2025-03-15'),
(11, 2, 2, 7.6, 'prova', 1, '2025-03-18'),
(12, 2, 2, 8.1, 'prova', 1, '2025-03-18'),
(13, 2, 2, 7.4, 'prova', 1, '2025-03-18'),
(14, 2, 2, 8.3, 'prova', 1, '2025-03-18'),
(15, 2, 2, 7.7, 'prova', 1, '2025-03-18');

-- Notas para alunos do 1º Ano A - EM (turma_id = 5)
INSERT INTO `notas` (`aluno_id`, `disciplina_id`, `professor_id`, `valor`, `tipo`, `bimestre`, `data_lancamento`) VALUES
(21, 1, 1, 8.8, 'prova', 1, '2025-03-15'),
(22, 1, 1, 8.4, 'prova', 1, '2025-03-15'),
(23, 1, 1, 8.6, 'prova', 1, '2025-03-15'),
(24, 1, 1, 8.9, 'prova', 1, '2025-03-15'),
(25, 1, 1, 8.7, 'prova', 1, '2025-03-15'),
(21, 2, 2, 8.5, 'prova', 1, '2025-03-18'),
(22, 2, 2, 8.3, 'prova', 1, '2025-03-18'),
(23, 2, 2, 8.7, 'prova', 1, '2025-03-18'),
(24, 2, 2, 8.8, 'prova', 1, '2025-03-18'),
(25, 2, 2, 8.6, 'prova', 1, '2025-03-18');
