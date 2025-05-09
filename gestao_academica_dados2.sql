USE `gestao_academica`;

-- -----------------------------------------------------
-- Dados para a tabela `notas`
-- -----------------------------------------------------
INSERT INTO `notas` (`id`, `aluno_id`, `disciplina_id`, `professor_id`, `valor`, `tipo`, `bimestre`, `data_lancamento`) VALUES
-- Notas de Matemática (disciplina_id = 1, professor_id = 1)
(1, 1, 1, 1, 8.5, 'prova', 1, '2025-03-15'),
(2, 2, 1, 1, 7.0, 'prova', 1, '2025-03-15'),
(3, 3, 1, 1, 9.0, 'prova', 1, '2025-03-15'),
(4, 4, 1, 1, 6.5, 'prova', 1, '2025-03-15'),
(5, 5, 1, 1, 8.0, 'prova', 1, '2025-03-15'),
(6, 1, 1, 1, 7.5, 'trabalho', 1, '2025-03-25'),
(7, 2, 1, 1, 8.0, 'trabalho', 1, '2025-03-25'),
(8, 3, 1, 1, 9.5, 'trabalho', 1, '2025-03-25'),
(9, 4, 1, 1, 7.0, 'trabalho', 1, '2025-03-25'),
(10, 5, 1, 1, 8.5, 'trabalho', 1, '2025-03-25'),
-- Notas de Português (disciplina_id = 2, professor_id = 2)
(11, 1, 2, 2, 7.0, 'prova', 1, '2025-03-18'),
(12, 2, 2, 2, 8.5, 'prova', 1, '2025-03-18'),
(13, 3, 2, 2, 6.5, 'prova', 1, '2025-03-18'),
(14, 4, 2, 2, 9.0, 'prova', 1, '2025-03-18'),
(15, 5, 2, 2, 7.5, 'prova', 1, '2025-03-18'),
(16, 1, 2, 2, 8.0, 'trabalho', 1, '2025-03-28'),
(17, 2, 2, 2, 9.0, 'trabalho', 1, '2025-03-28'),
(18, 3, 2, 2, 7.0, 'trabalho', 1, '2025-03-28'),
(19, 4, 2, 2, 9.5, 'trabalho', 1, '2025-03-28'),
(20, 5, 2, 2, 8.0, 'trabalho', 1, '2025-03-28'),
-- Notas de História (disciplina_id = 3, professor_id = 3)
(21, 1, 3, 3, 9.0, 'prova', 1, '2025-03-20'),
(22, 2, 3, 3, 7.5, 'prova', 1, '2025-03-20'),
(23, 3, 3, 3, 8.0, 'prova', 1, '2025-03-20'),
(24, 4, 3, 3, 6.0, 'prova', 1, '2025-03-20'),
(25, 5, 3, 3, 9.5, 'prova', 1, '2025-03-20'),
-- Notas de Geografia (disciplina_id = 4, professor_id = 4)
(26, 1, 4, 4, 8.0, 'prova', 1, '2025-03-22'),
(27, 2, 4, 4, 6.5, 'prova', 1, '2025-03-22'),
(28, 3, 4, 4, 7.5, 'prova', 1, '2025-03-22'),
(29, 4, 4, 4, 9.0, 'prova', 1, '2025-03-22'),
(30, 5, 4, 4, 8.5, 'prova', 1, '2025-03-22'),
-- Notas de Ciências (disciplina_id = 5, professor_id = 5)
(31, 1, 5, 5, 7.5, 'prova', 1, '2025-03-25'),
(32, 2, 5, 5, 8.0, 'prova', 1, '2025-03-25'),
(33, 3, 5, 5, 9.0, 'prova', 1, '2025-03-25'),
(34, 4, 5, 5, 7.0, 'prova', 1, '2025-03-25'),
(35, 5, 5, 5, 8.5, 'prova', 1, '2025-03-25');

-- -----------------------------------------------------
-- Dados para a tabela `frequencia`
-- -----------------------------------------------------
INSERT INTO `frequencia` (`id`, `aluno_id`, `disciplina_id`, `data`, `status`, `justificativa`) VALUES
-- Janeiro
(1, 1, 1, '2025-01-10', 'presente', NULL),
(2, 2, 1, '2025-01-10', 'presente', NULL),
(3, 3, 1, '2025-01-10', 'presente', NULL),
(4, 4, 1, '2025-01-10', 'ausente', 'Consulta médica'),
(5, 5, 1, '2025-01-10', 'presente', NULL),
(6, 1, 1, '2025-01-17', 'presente', NULL),
(7, 2, 1, '2025-01-17', 'presente', NULL),
(8, 3, 1, '2025-01-17', 'ausente', NULL),
(9, 4, 1, '2025-01-17', 'presente', NULL),
(10, 5, 1, '2025-01-17', 'presente', NULL),
(11, 1, 1, '2025-01-24', 'presente', NULL),
(12, 2, 1, '2025-01-24', 'ausente', 'Doença'),
(13, 3, 1, '2025-01-24', 'presente', NULL),
(14, 4, 1, '2025-01-24', 'presente', NULL),
(15, 5, 1, '2025-01-24', 'presente', NULL),
(16, 1, 1, '2025-01-31', 'presente', NULL),
(17, 2, 1, '2025-01-31', 'presente', NULL),
(18, 3, 1, '2025-01-31', 'presente', NULL),
(19, 4, 1, '2025-01-31', 'presente', NULL),
(20, 5, 1, '2025-01-31', 'ausente', 'Consulta médica'),
-- Fevereiro
(21, 1, 1, '2025-02-07', 'presente', NULL),
(22, 2, 1, '2025-02-07', 'presente', NULL),
(23, 3, 1, '2025-02-07', 'presente', NULL),
(24, 4, 1, '2025-02-07', 'presente', NULL),
(25, 5, 1, '2025-02-07', 'presente', NULL),
(26, 1, 1, '2025-02-14', 'ausente', 'Doença'),
(27, 2, 1, '2025-02-14', 'presente', NULL),
(28, 3, 1, '2025-02-14', 'presente', NULL),
(29, 4, 1, '2025-02-14', 'presente', NULL),
(30, 5, 1, '2025-02-14', 'presente', NULL),
(31, 1, 1, '2025-02-21', 'justificado', 'Atestado médico'),
(32, 2, 1, '2025-02-21', 'presente', NULL),
(33, 3, 1, '2025-02-21', 'presente', NULL),
(34, 4, 1, '2025-02-21', 'ausente', NULL),
(35, 5, 1, '2025-02-21', 'presente', NULL),
(36, 1, 1, '2025-02-28', 'presente', NULL),
(37, 2, 1, '2025-02-28', 'presente', NULL),
(38, 3, 1, '2025-02-28', 'ausente', 'Doença'),
(39, 4, 1, '2025-02-28', 'presente', NULL),
(40, 5, 1, '2025-02-28', 'presente', NULL),
-- Março
(41, 1, 1, '2025-03-07', 'presente', NULL),
(42, 2, 1, '2025-03-07', 'presente', NULL),
(43, 3, 1, '2025-03-07', 'presente', NULL),
(44, 4, 1, '2025-03-07', 'presente', NULL),
(45, 5, 1, '2025-03-07', 'presente', NULL),
(46, 1, 1, '2025-03-14', 'presente', NULL),
(47, 2, 1, '2025-03-14', 'presente', NULL),
(48, 3, 1, '2025-03-14', 'presente', NULL),
(49, 4, 1, '2025-03-14', 'justificado', 'Consulta médica'),
(50, 5, 1, '2025-03-14', 'presente', NULL),
(51, 1, 1, '2025-03-21', 'presente', NULL),
(52, 2, 1, '2025-03-21', 'ausente', NULL),
(53, 3, 1, '2025-03-21', 'presente', NULL),
(54, 4, 1, '2025-03-21', 'presente', NULL),
(55, 5, 1, '2025-03-21', 'presente', NULL),
(56, 1, 1, '2025-03-28', 'presente', NULL),
(57, 2, 1, '2025-03-28', 'presente', NULL),
(58, 3, 1, '2025-03-28', 'presente', NULL),
(59, 4, 1, '2025-03-28', 'presente', NULL),
(60, 5, 1, '2025-03-28', 'presente', NULL),
-- Abril
(61, 1, 1, '2025-04-04', 'presente', NULL),
(62, 2, 1, '2025-04-04', 'presente', NULL),
(63, 3, 1, '2025-04-04', 'ausente', 'Doença'),
(64, 4, 1, '2025-04-04', 'presente', NULL),
(65, 5, 1, '2025-04-04', 'presente', NULL),
(66, 1, 1, '2025-04-11', 'presente', NULL),
(67, 2, 1, '2025-04-11', 'presente', NULL),
(68, 3, 1, '2025-04-11', 'justificado', 'Atestado médico'),
(69, 4, 1, '2025-04-11', 'presente', NULL),
(70, 5, 1, '2025-04-11', 'presente', NULL),
(71, 1, 1, '2025-04-18', 'presente', NULL),
(72, 2, 1, '2025-04-18', 'presente', NULL),
(73, 3, 1, '2025-04-18', 'presente', NULL),
(74, 4, 1, '2025-04-18', 'presente', NULL),
(75, 5, 1, '2025-04-18', 'ausente', NULL),
(76, 1, 1, '2025-04-25', 'presente', NULL),
(77, 2, 1, '2025-04-25', 'ausente', NULL),
(78, 3, 1, '2025-04-25', 'presente', NULL),
(79, 4, 1, '2025-04-25', 'presente', NULL),
(80, 5, 1, '2025-04-25', 'presente', NULL),
-- Maio
(81, 1, 1, '2025-05-02', 'presente', NULL),
(82, 2, 1, '2025-05-02', 'presente', NULL),
(83, 3, 1, '2025-05-02', 'presente', NULL),
(84, 4, 1, '2025-05-02', 'presente', NULL),
(85, 5, 1, '2025-05-02', 'presente', NULL);

-- -----------------------------------------------------
-- Dados para a tabela `eventos`
-- -----------------------------------------------------
INSERT INTO `eventos` (`id`, `titulo`, `descricao`, `data_evento`, `hora_inicio`, `hora_fim`, `local`, `tipo`, `status`) VALUES
(1, 'Prova de Matemática - 9º Ano', 'Avaliação bimestral de matemática', '2025-05-15', '10:00:00', '11:30:00', 'Sala 102', 'prova', 'adiado'),
(2, 'Reunião de Pais e Mestres', 'Reunião para discutir o desempenho dos alunos no primeiro bimestre', '2025-05-20', '19:00:00', '21:00:00', 'Auditório', 'reuniao', 'agendado'),
(3, 'Feriado Nacional', 'Corpus Christi', '2025-06-01', NULL, NULL, NULL, 'feriado', 'agendado'),
(4, 'Feira de Ciências', 'Apresentação de projetos científicos desenvolvidos pelos alunos', '2025-06-10', '08:00:00', '17:00:00', 'Pátio da Escola', 'evento', 'agendado'),
(5, 'Conselho de Classe', 'Reunião de professores para avaliação do desempenho das turmas', '2025-06-15', '14:00:00', '17:00:00', 'Sala dos Professores', 'reuniao', 'agendado');

-- -----------------------------------------------------
-- Dados para a tabela `anuncios`
-- -----------------------------------------------------
INSERT INTO `anuncios` (`id`, `titulo`, `conteudo`, `data_publicacao`, `usuario_id`) VALUES
(1, 'Volta às aulas após feriado', 'Informamos que as aulas retornarão normalmente na quinta-feira, 15 de maio, após o feriado de quarta-feira.', '2025-05-11', 1),
(2, 'Campanha de arrecadação de alimentos', 'Nossa campanha de arrecadação de alimentos não-perecíveis começa na próxima semana. Contamos com a participação de todos!', '2025-05-05', 7),
(3, 'Manutenção do sistema', 'O sistema ficará indisponível para manutenção no domingo, 04/05, das 00h às 06h.', '2025-05-01', 1);

-- -----------------------------------------------------
-- Dados para a tabela `atividades`
-- -----------------------------------------------------
INSERT INTO `atividades` (`id`, `tipo`, `descricao`, `data_registro`, `hora_registro`, `usuario_id`, `origem`) VALUES
(1, 'falta', 'João Silva não compareceu à aula de Matemática', '2025-05-08', '10:30:00', 2, 'mestre'),
(2, 'nota', 'Notas de Português do 8º ano foram lançadas', '2025-05-08', '09:15:00', 3, 'sistema'),
(3, 'evento', 'Feira de Ciências agendada para 10/06', '2025-05-07', '15:45:00', 7, 'coordenação'),
(4, 'login', 'Usuário admin realizou login no sistema', '2025-05-08', '08:00:00', 1, 'sistema'),
(5, 'anuncio', 'Novo anúncio publicado: Volta às aulas após feriado', '2025-05-08', '11:00:00', 1, 'sistema');
