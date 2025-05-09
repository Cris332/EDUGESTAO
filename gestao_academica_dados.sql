USE `gestao_academica`;

-- -----------------------------------------------------
-- Dados para a tabela `usuarios`
-- -----------------------------------------------------
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `status`, `data_cadastro`) VALUES
(1, 'Admin Sistema', 'admin@edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'admin', 'ativo', '2024-01-01'),
(2, 'Maria Silva', 'maria.silva@edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'professor', 'ativo', '2024-01-10'),
(3, 'João Santos', 'joao.santos@edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'professor', 'ativo', '2024-01-15'),
(4, 'Ana Oliveira', 'ana.oliveira@edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'professor', 'ativo', '2024-01-20'),
(5, 'Carlos Pereira', 'carlos.pereira@edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'professor', 'ativo', '2024-02-01'),
(6, 'Juliana Costa', 'juliana.costa@edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'professor', 'ativo', '2024-02-10'),
(7, 'Pedro Almeida', 'pedro.almeida@edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'secretaria', 'ativo', '2024-02-15'),
(8, 'Fernanda Lima', 'fernanda.lima@edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'secretaria', 'ativo', '2024-02-20'),
(9, 'João Silva', 'joao.silva@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-01'),
(10, 'Maria Santos', 'maria.santos@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-01'),
(11, 'Pedro Oliveira', 'pedro.oliveira@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-02'),
(12, 'Ana Costa', 'ana.costa@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-02'),
(13, 'Lucas Pereira', 'lucas.pereira@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-03'),
(14, 'Julia Almeida', 'julia.almeida@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-03'),
(15, 'Mateus Lima', 'mateus.lima@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-04'),
(16, 'Gabriela Sousa', 'gabriela.sousa@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-04'),
(17, 'Rafael Ferreira', 'rafael.ferreira@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-05'),
(18, 'Camila Rodrigues', 'camila.rodrigues@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-05'),
(19, 'Bruno Martins', 'bruno.martins@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-06'),
(20, 'Larissa Gomes', 'larissa.gomes@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-06'),
(21, 'Thiago Cardoso', 'thiago.cardoso@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-07'),
(22, 'Isabela Ribeiro', 'isabela.ribeiro@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-07'),
(23, 'Gustavo Alves', 'gustavo.alves@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-08'),
(24, 'Mariana Castro', 'mariana.castro@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-08'),
(25, 'Felipe Nunes', 'felipe.nunes@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-09'),
(26, 'Carolina Mendes', 'carolina.mendes@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-09'),
(27, 'Leonardo Barros', 'leonardo.barros@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-10'),
(28, 'Amanda Dias', 'amanda.dias@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-10'),
(29, 'Vinícius Rocha', 'vinicius.rocha@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-11'),
(30, 'Beatriz Campos', 'beatriz.campos@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-03-11'),
(31, 'Henrique Lopes', 'henrique.lopes@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-04-01'),
(32, 'Natália Araújo', 'natalia.araujo@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-04-01'),
(33, 'Diego Moreira', 'diego.moreira@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-04-02'),
(34, 'Bianca Teixeira', 'bianca.teixeira@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-04-02'),
(35, 'Rodrigo Correia', 'rodrigo.correia@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-04-03'),
(36, 'Letícia Azevedo', 'leticia.azevedo@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-04-03'),
(37, 'Marcelo Cunha', 'marcelo.cunha@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-04-04'),
(38, 'Fernanda Pinto', 'fernanda.pinto@aluno.edugestao.com', '$2y$10$abcdefghijklmnopqrstuuWzAC6QKO1vQ5OL5XZxV0hQ1Iq9ABCDE', 'aluno', 'ativo', '2024-04-04');

-- -----------------------------------------------------
-- Dados para a tabela `cursos`
-- -----------------------------------------------------
INSERT INTO `cursos` (`id`, `nome`, `descricao`, `carga_horaria`, `nivel`) VALUES
(1, 'Ensino Fundamental - Anos Finais', 'Ensino Fundamental do 6º ao 9º ano', 800, 'fundamental'),
(2, 'Ensino Médio', 'Ensino Médio Regular', 1000, 'medio'),
(3, 'Técnico em Informática', 'Curso técnico integrado ao ensino médio', 1200, 'tecnico'),
(4, 'Técnico em Administração', 'Curso técnico integrado ao ensino médio', 1100, 'tecnico'),
(5, 'Técnico em Enfermagem', 'Curso técnico integrado ao ensino médio', 1300, 'tecnico');

-- -----------------------------------------------------
-- Dados para a tabela `disciplinas`
-- -----------------------------------------------------
INSERT INTO `disciplinas` (`id`, `nome`, `descricao`, `carga_horaria`) VALUES
(1, 'Matemática', 'Disciplina de matemática básica e avançada', 160),
(2, 'Português', 'Língua portuguesa e literatura', 160),
(3, 'História', 'História geral e do Brasil', 80),
(4, 'Geografia', 'Geografia geral e do Brasil', 80),
(5, 'Ciências', 'Ciências naturais', 80),
(6, 'Física', 'Física básica e avançada', 120),
(7, 'Química', 'Química básica e avançada', 120),
(8, 'Biologia', 'Biologia celular e geral', 120),
(9, 'Educação Física', 'Atividades físicas e esportes', 80),
(10, 'Inglês', 'Língua inglesa', 80),
(11, 'Artes', 'Expressão artística e história da arte', 40),
(12, 'Filosofia', 'Introdução à filosofia', 40),
(13, 'Sociologia', 'Introdução à sociologia', 40),
(14, 'Programação', 'Lógica e linguagens de programação', 120),
(15, 'Banco de Dados', 'Modelagem e implementação de bancos de dados', 80),
(16, 'Redes de Computadores', 'Fundamentos de redes e protocolos', 80),
(17, 'Gestão Empresarial', 'Princípios de administração e gestão', 80),
(18, 'Contabilidade', 'Noções básicas de contabilidade', 80),
(19, 'Marketing', 'Fundamentos de marketing', 60),
(20, 'Anatomia', 'Anatomia humana', 120),
(21, 'Fisiologia', 'Fisiologia humana', 120),
(22, 'Primeiros Socorros', 'Técnicas de primeiros socorros', 60);

-- -----------------------------------------------------
-- Dados para a tabela `curso_disciplina`
-- -----------------------------------------------------
INSERT INTO `curso_disciplina` (`curso_id`, `disciplina_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 9), (1, 10), (1, 11),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 6), (2, 7), (2, 8), (2, 9), (2, 10), (2, 11), (2, 12), (2, 13),
(3, 1), (3, 2), (3, 6), (3, 14), (3, 15), (3, 16),
(4, 1), (4, 2), (4, 17), (4, 18), (4, 19),
(5, 1), (5, 2), (5, 8), (5, 20), (5, 21), (5, 22);

-- -----------------------------------------------------
-- Dados para a tabela `turmas`
-- -----------------------------------------------------
INSERT INTO `turmas` (`id`, `nome`, `curso_id`, `ano`, `periodo`, `status`) VALUES
(1, '6º Ano A', 1, 2025, 'matutino', 'ativo'),
(2, '7º Ano A', 1, 2025, 'matutino', 'ativo'),
(3, '8º Ano A', 1, 2025, 'matutino', 'ativo'),
(4, '9º Ano A', 1, 2025, 'matutino', 'ativo'),
(5, '1º Ano A - EM', 2, 2025, 'matutino', 'ativo'),
(6, '2º Ano A - EM', 2, 2025, 'matutino', 'ativo'),
(7, '3º Ano A - EM', 2, 2025, 'matutino', 'ativo'),
(8, '1º Ano A - Informática', 3, 2025, 'integral', 'ativo'),
(9, '2º Ano A - Informática', 3, 2025, 'integral', 'ativo'),
(10, '3º Ano A - Informática', 3, 2025, 'integral', 'ativo'),
(11, '1º Ano A - Administração', 4, 2025, 'vespertino', 'ativo'),
(12, '2º Ano A - Administração', 4, 2025, 'vespertino', 'ativo'),
(13, '3º Ano A - Administração', 4, 2025, 'vespertino', 'ativo'),
(14, '1º Ano A - Enfermagem', 5, 2025, 'noturno', 'ativo'),
(15, '2º Ano A - Enfermagem', 5, 2025, 'noturno', 'ativo'),
(16, '3º Ano A - Enfermagem', 5, 2025, 'noturno', 'ativo');

-- -----------------------------------------------------
-- Dados para a tabela `professores`
-- -----------------------------------------------------
INSERT INTO `professores` (`id`, `usuario_id`, `formacao`, `titulacao`, `status`) VALUES
(1, 2, 'Licenciatura em Matemática', 'mestrado', 'ativo'),
(2, 3, 'Licenciatura em Letras', 'mestrado', 'ativo'),
(3, 4, 'Licenciatura em História', 'doutorado', 'ativo'),
(4, 5, 'Licenciatura em Geografia', 'especializacao', 'ativo'),
(5, 6, 'Licenciatura em Ciências Biológicas', 'doutorado', 'ativo');

-- -----------------------------------------------------
-- Dados para a tabela `professor_disciplina`
-- -----------------------------------------------------
INSERT INTO `professor_disciplina` (`professor_id`, `disciplina_id`, `turma_id`) VALUES
(1, 1, 1), (1, 1, 2), (1, 1, 3), (1, 1, 4), (1, 1, 5), (1, 1, 6), (1, 1, 7),
(2, 2, 1), (2, 2, 2), (2, 2, 3), (2, 2, 4), (2, 2, 5), (2, 2, 6), (2, 2, 7),
(3, 3, 1), (3, 3, 2), (3, 3, 3), (3, 3, 4), (3, 3, 5), (3, 3, 6), (3, 3, 7),
(4, 4, 1), (4, 4, 2), (4, 4, 3), (4, 4, 4), (4, 4, 5), (4, 4, 6), (4, 4, 7),
(5, 5, 1), (5, 5, 2), (5, 5, 3), (5, 5, 4), (5, 8, 5), (5, 8, 6), (5, 8, 7);

-- -----------------------------------------------------
-- Dados para a tabela `alunos`
-- -----------------------------------------------------
INSERT INTO `alunos` (`id`, `usuario_id`, `matricula`, `data_nascimento`, `responsavel`, `telefone_responsavel`, `turma_id`, `status`, `data_cadastro`) VALUES
(1, 9, '202401001', '2013-05-15', 'Roberto Silva', '(11) 98765-4321', 1, 'ativo', '2024-03-01'),
(2, 10, '202401002', '2013-06-20', 'Carla Santos', '(11) 98765-4322', 1, 'ativo', '2024-03-01'),
(3, 11, '202401003', '2013-07-10', 'Marcos Oliveira', '(11) 98765-4323', 1, 'ativo', '2024-03-02'),
(4, 12, '202401004', '2013-08-05', 'Patrícia Costa', '(11) 98765-4324', 1, 'ativo', '2024-03-02'),
(5, 13, '202401005', '2013-09-12', 'Ricardo Pereira', '(11) 98765-4325', 1, 'ativo', '2024-03-03'),
(6, 14, '202402001', '2012-04-25', 'Sônia Almeida', '(11) 98765-4326', 2, 'ativo', '2024-03-03'),
(7, 15, '202402002', '2012-05-18', 'Eduardo Lima', '(11) 98765-4327', 2, 'ativo', '2024-03-04'),
(8, 16, '202402003', '2012-06-30', 'Cristina Sousa', '(11) 98765-4328', 2, 'ativo', '2024-03-04'),
(9, 17, '202402004', '2012-07-22', 'Paulo Ferreira', '(11) 98765-4329', 2, 'ativo', '2024-03-05'),
(10, 18, '202402005', '2012-08-14', 'Luciana Rodrigues', '(11) 98765-4330', 2, 'ativo', '2024-03-05'),
(11, 19, '202403001', '2011-03-05', 'Antônio Martins', '(11) 98765-4331', 3, 'ativo', '2024-03-06'),
(12, 20, '202403002', '2011-04-17', 'Sandra Gomes', '(11) 98765-4332', 3, 'ativo', '2024-03-06'),
(13, 21, '202403003', '2011-05-28', 'José Cardoso', '(11) 98765-4333', 3, 'ativo', '2024-03-07'),
(14, 22, '202403004', '2011-06-09', 'Márcia Ribeiro', '(11) 98765-4334', 3, 'ativo', '2024-03-07'),
(15, 23, '202403005', '2011-07-11', 'Luiz Alves', '(11) 98765-4335', 3, 'ativo', '2024-03-08'),
(16, 24, '202404001', '2010-02-14', 'Renata Castro', '(11) 98765-4336', 4, 'ativo', '2024-03-08'),
(17, 25, '202404002', '2010-03-23', 'Alberto Nunes', '(11) 98765-4337', 4, 'ativo', '2024-03-09'),
(18, 26, '202404003', '2010-04-19', 'Vanessa Mendes', '(11) 98765-4338', 4, 'ativo', '2024-03-09'),
(19, 27, '202404004', '2010-05-27', 'Cláudio Barros', '(11) 98765-4339', 4, 'ativo', '2024-03-10'),
(20, 28, '202404005', '2010-06-16', 'Elaine Dias', '(11) 98765-4340', 4, 'ativo', '2024-03-10'),
(21, 29, '202405001', '2009-01-08', 'Rogério Rocha', '(11) 98765-4341', 5, 'ativo', '2024-03-11'),
(22, 30, '202405002', '2009-02-19', 'Denise Campos', '(11) 98765-4342', 5, 'ativo', '2024-03-11'),
(23, 31, '202405003', '2009-03-29', 'Fábio Lopes', '(11) 98765-4343', 5, 'ativo', '2024-04-01'),
(24, 32, '202405004', '2009-04-07', 'Simone Araújo', '(11) 98765-4344', 5, 'ativo', '2024-04-01'),
(25, 33, '202405005', '2009-05-15', 'André Moreira', '(11) 98765-4345', 5, 'ativo', '2024-04-02'),
(26, 34, '202406001', '2008-01-26', 'Mônica Teixeira', '(11) 98765-4346', 6, 'ativo', '2024-04-02'),
(27, 35, '202406002', '2008-02-04', 'Sérgio Correia', '(11) 98765-4347', 6, 'ativo', '2024-04-03'),
(28, 36, '202406003', '2008-03-13', 'Tatiana Azevedo', '(11) 98765-4348', 6, 'ativo', '2024-04-03'),
(29, 37, '202406004', '2008-04-21', 'Gilberto Cunha', '(11) 98765-4349', 6, 'ativo', '2024-04-04'),
(30, 38, '202406005', '2008-05-30', 'Regina Pinto', '(11) 98765-4350', 6, 'ativo', '2024-04-04');
