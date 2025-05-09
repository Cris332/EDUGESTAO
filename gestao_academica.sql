-- -----------------------------------------------------
-- Database gestao_academica
-- -----------------------------------------------------
DROP DATABASE IF EXISTS `gestao_academica`;
CREATE DATABASE IF NOT EXISTS `gestao_academica` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gestao_academica`;

-- -----------------------------------------------------
-- Table `usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `tipo` ENUM('admin', 'professor', 'aluno', 'secretaria') NOT NULL,
  `status` ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
  `data_cadastro` DATE NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `cursos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cursos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` TEXT NULL,
  `carga_horaria` INT NOT NULL,
  `nivel` ENUM('fundamental', 'medio', 'tecnico') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `disciplinas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `disciplinas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` TEXT NULL,
  `carga_horaria` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `curso_disciplina`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `curso_disciplina` (
  `curso_id` INT NOT NULL,
  `disciplina_id` INT NOT NULL,
  PRIMARY KEY (`curso_id`, `disciplina_id`),
  INDEX `fk_curso_disciplina_disciplina_idx` (`disciplina_id` ASC),
  CONSTRAINT `fk_curso_disciplina_curso`
    FOREIGN KEY (`curso_id`)
    REFERENCES `cursos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_curso_disciplina_disciplina`
    FOREIGN KEY (`disciplina_id`)
    REFERENCES `disciplinas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `turmas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `turmas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `curso_id` INT NOT NULL,
  `ano` INT NOT NULL,
  `periodo` ENUM('matutino', 'vespertino', 'noturno', 'integral') NOT NULL,
  `status` ENUM('ativo', 'inativo', 'concluido') NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  INDEX `fk_turmas_curso_idx` (`curso_id` ASC),
  CONSTRAINT `fk_turmas_curso`
    FOREIGN KEY (`curso_id`)
    REFERENCES `cursos` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `professores`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `professores` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_id` INT NOT NULL,
  `formacao` VARCHAR(100) NOT NULL,
  `titulacao` ENUM('graduacao', 'especializacao', 'mestrado', 'doutorado') NOT NULL,
  `status` ENUM('ativo', 'inativo', 'afastado', 'ferias') NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  INDEX `fk_professores_usuario_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_professores_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `professor_disciplina`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `professor_disciplina` (
  `professor_id` INT NOT NULL,
  `disciplina_id` INT NOT NULL,
  `turma_id` INT NOT NULL,
  PRIMARY KEY (`professor_id`, `disciplina_id`, `turma_id`),
  INDEX `fk_professor_disciplina_disciplina_idx` (`disciplina_id` ASC),
  INDEX `fk_professor_disciplina_turma_idx` (`turma_id` ASC),
  CONSTRAINT `fk_professor_disciplina_professor`
    FOREIGN KEY (`professor_id`)
    REFERENCES `professores` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_professor_disciplina_disciplina`
    FOREIGN KEY (`disciplina_id`)
    REFERENCES `disciplinas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_professor_disciplina_turma`
    FOREIGN KEY (`turma_id`)
    REFERENCES `turmas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `alunos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `alunos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_id` INT NOT NULL,
  `matricula` VARCHAR(20) NOT NULL,
  `data_nascimento` DATE NOT NULL,
  `responsavel` VARCHAR(100) NULL,
  `telefone_responsavel` VARCHAR(20) NULL,
  `turma_id` INT NOT NULL,
  `status` ENUM('ativo', 'inativo', 'transferido', 'trancado') NOT NULL DEFAULT 'ativo',
  `data_cadastro` DATE NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `matricula_UNIQUE` (`matricula` ASC),
  INDEX `fk_alunos_usuario_idx` (`usuario_id` ASC),
  INDEX `fk_alunos_turma_idx` (`turma_id` ASC),
  CONSTRAINT `fk_alunos_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_alunos_turma`
    FOREIGN KEY (`turma_id`)
    REFERENCES `turmas` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `notas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `notas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `disciplina_id` INT NOT NULL,
  `professor_id` INT NOT NULL,
  `valor` DECIMAL(4,2) NOT NULL,
  `tipo` ENUM('prova', 'trabalho', 'projeto', 'participacao', 'recuperacao') NOT NULL,
  `bimestre` INT NOT NULL,
  `data_lancamento` DATE NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_notas_aluno_idx` (`aluno_id` ASC),
  INDEX `fk_notas_disciplina_idx` (`disciplina_id` ASC),
  INDEX `fk_notas_professor_idx` (`professor_id` ASC),
  CONSTRAINT `fk_notas_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `alunos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_notas_disciplina`
    FOREIGN KEY (`disciplina_id`)
    REFERENCES `disciplinas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_notas_professor`
    FOREIGN KEY (`professor_id`)
    REFERENCES `professores` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `frequencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `frequencia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `aluno_id` INT NOT NULL,
  `disciplina_id` INT NOT NULL,
  `data` DATE NOT NULL,
  `status` ENUM('presente', 'ausente', 'justificado') NOT NULL,
  `justificativa` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_frequencia_aluno_idx` (`aluno_id` ASC),
  INDEX `fk_frequencia_disciplina_idx` (`disciplina_id` ASC),
  CONSTRAINT `fk_frequencia_aluno`
    FOREIGN KEY (`aluno_id`)
    REFERENCES `alunos` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_frequencia_disciplina`
    FOREIGN KEY (`disciplina_id`)
    REFERENCES `disciplinas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `eventos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eventos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(100) NOT NULL,
  `descricao` TEXT NULL,
  `data_evento` DATE NOT NULL,
  `hora_inicio` TIME NULL,
  `hora_fim` TIME NULL,
  `local` VARCHAR(100) NULL,
  `tipo` ENUM('reuniao', 'prova', 'feriado', 'evento', 'outro') NOT NULL,
  `status` ENUM('agendado', 'cancelado', 'adiado', 'concluido') NOT NULL DEFAULT 'agendado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `anuncios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `anuncios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(100) NOT NULL,
  `conteudo` TEXT NOT NULL,
  `data_publicacao` DATE NOT NULL,
  `usuario_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_anuncios_usuario_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_anuncios_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `atividades`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `atividades` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('login', 'nota', 'falta', 'evento', 'anuncio', 'outro') NOT NULL,
  `descricao` TEXT NOT NULL,
  `data_registro` DATE NOT NULL,
  `hora_registro` TIME NOT NULL,
  `usuario_id` INT NULL,
  `origem` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_atividades_usuario_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_atividades_usuario`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `usuarios` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
