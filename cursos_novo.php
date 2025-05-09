<?php
// Incluir arquivos necessários
require_once 'conexao.php';
require_once 'cursos_functions.php';

// Inicializar variáveis
$mensagem = '';
$tipo_mensagem = '';

// Obter lista de cursos
$cursos = obterCursos($conexao);

// Processar formulário de inscrição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'inscrever_aluno') {
        // Processar inscrição de aluno
        $resultado = inscreverAlunoEmCurso(
            $conexao, 
            (int)$_POST['aluno_id'], 
            (int)$_POST['curso_id'], 
            (int)$_POST['turma_id']
        );
        
        $mensagem = $resultado['mensagem'];
        $tipo_mensagem = $resultado['tipo'];
    } elseif ($_POST['acao'] === 'cadastrar_curso') {
        // Processar cadastro de curso
        $resultado = cadastrarCurso($conexao, $_POST);
        
        $mensagem = $resultado['mensagem'];
        $tipo_mensagem = $resultado['tipo'];
        
        // Atualizar lista de cursos
        if ($resultado['sucesso']) {
            $cursos = obterCursos($conexao);
        }
    } elseif ($_POST['acao'] === 'cadastrar_turma') {
        // Processar cadastro de turma
        $resultado = cadastrarTurma($conexao, $_POST);
        
        $mensagem = $resultado['mensagem'];
        $tipo_mensagem = $resultado['tipo'];
    }
}

// Processar requisições AJAX
if (isset($_GET['acao'])) {
    header('Content-Type: application/json');
    
    if ($_GET['acao'] === 'buscar_alunos' && isset($_GET['termo'])) {
        $alunos = buscarAlunos($conexao, $_GET['termo']);
        echo json_encode(['sucesso' => true, 'alunos' => $alunos]);
        exit;
    } elseif ($_GET['acao'] === 'obter_turmas' && isset($_GET['curso_id'])) {
        $turmas = obterTurmasPorCurso($conexao, $_GET['curso_id']);
        echo json_encode(['sucesso' => true, 'turmas' => $turmas]);
        exit;
    } elseif ($_GET['acao'] === 'verificar_inscricao' && isset($_GET['aluno_id']) && isset($_GET['curso_id'])) {
        $inscrito = verificarInscricaoExistente($conexao, $_GET['aluno_id'], $_GET['curso_id']);
        echo json_encode(['sucesso' => true, 'inscrito' => $inscrito]);
        exit;
    } elseif ($_GET['acao'] === 'obter_detalhes_curso' && isset($_GET['curso_id'])) {
        $curso = obterDetalhesCurso($conexao, $_GET['curso_id']);
        $alunos = obterAlunosPorCurso($conexao, $_GET['curso_id']);
        echo json_encode(['sucesso' => true, 'curso' => $curso, 'alunos' => $alunos]);
        exit;
    } elseif ($_GET['acao'] === 'atualizar_inscricao' && isset($_POST['aluno_id']) && isset($_POST['turma_id'])) {
        $status = isset($_POST['status']) ? $_POST['status'] : null;
        $resultado = atualizarInscricaoAluno($conexao, $_POST['aluno_id'], $_POST['turma_id'], $status);
        echo json_encode($resultado);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Cursos - Sistema de Gestão Acadêmica</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        .list-group-item {
            border: none;
            padding: 0.8rem 1.25rem;
            transition: all 0.3s;
            opacity: 0;
            transform: translateX(-20px);
        }
        .list-group-item.active {
            background-color: #0d6efd;
            color: white;
            border-radius: 5px;
        }
        .list-group-item:hover:not(.active) {
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .menu-hover {
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
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
        .curso-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .curso-card .card-body {
            flex: 1;
        }
        .curso-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .search-results {
            position: absolute;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            background: white;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: none;
        }
        .search-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .search-item:hover {
            background-color: #f8f9fa;
        }
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 0.2em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
            vertical-align: middle;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .loading-spinner.show {
            opacity: 1;
        }
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
        .nivel-badge {
            position: absolute;
            top: 10px;
            left: 10px;
        }
    </style>
</head>
<body>
    <!-- Layout similar ao dashboard -->
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-white border-end" id="sidebar-wrapper">
            <div class="sidebar-heading d-flex align-items-center p-3">
                <i class="fas fa-school text-primary me-2"></i>
                <span class="fs-4 fw-bold">EduGestão</span>
            </div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="estudante.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-graduate"></i> Estudantes
                </a>
                <a href="notas.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-chart-line"></i> Notas
                </a>
                <a href="cursos.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-book"></i> Cursos
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-chalkboard-teacher"></i> Professores
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-door-open"></i> Turmas
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-chart-bar"></i> Relatórios
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-cog"></i> Configurações
                </a>
            </div>
        </div>
        
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <!-- Hamburger menu for mobile -->
                    <button class="navbar-toggler" id="sidebarToggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <!-- Page title -->
                    <div class="d-flex flex-column">
                        <span class="navbar-brand mb-0 h1">
                            <i class="fas fa-book me-2 text-primary"></i>
                            Gestão de Cursos
                        </span>
                        <span class="text-muted small">Gerenciamento de cursos e inscrições</span>
                    </div>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <div class="input-group me-3 d-none d-lg-flex">
                            <input type="text" class="form-control" placeholder="Pesquisar...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="position-relative">
                                    <img src="https://via.placeholder.com/40" class="rounded-circle border border-2 border-primary" width="38" height="38" alt="Usuário">
                                    <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1 d-lg-none" style="width: 10px; height: 10px;"></span>
                                </div>
                                <div class="d-none d-lg-flex flex-column ms-2">
                                    <span class="fw-bold">Administrador</span>
                                    <small class="text-muted">Admin</small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                                <li class="dropdown-header text-center">
                                    <img src="https://via.placeholder.com/40" class="rounded-circle mb-2" width="60" height="60" alt="Usuário">
                                    <h6 class="mb-0">Administrador</h6>
                                    <small class="text-muted">admin@edugestao.com</small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2 text-primary"></i> Meu Perfil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2 text-secondary"></i> Configurações</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-question-circle me-2 text-info"></i> Ajuda</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2 text-danger"></i> Sair</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Page content -->
            <div class="container-fluid p-4">
                <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php endif; ?>
                
                <!-- Ações -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Cursos Disponíveis</h4>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inscricaoModal">
                            <i class="fas fa-user-plus me-2"></i> Inscrever Aluno
                        </button>
                        <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#novoCursoModal">
                            <i class="fas fa-plus me-2"></i> Novo Curso
                        </button>
                    </div>
                </div>
                
                <!-- Lista de Cursos -->
                <div class="row">
                    <?php if (empty($cursos)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Nenhum curso cadastrado. Clique em "Novo Curso" para adicionar.
                        </div>
                    </div>
                    <?php else: ?>
                        <?php foreach ($cursos as $curso): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card curso-card animate__animated animate__fadeIn">
                                <?php 
                                $nivel_class = '';
                                $nivel_text = '';
                                 
                                switch ($curso['nivel']) {
                                    case 'fundamental':
                                        $nivel_class = 'bg-info';
                                        $nivel_text = 'Fundamental';
                                        break;
                                    case 'medio':
                                        $nivel_class = 'bg-primary';
                                        $nivel_text = 'Médio';
                                        break;
                                    case 'tecnico':
                                        $nivel_class = 'bg-success';
                                        $nivel_text = 'Técnico';
                                        break;
                                    default:
                                        $nivel_class = 'bg-secondary';
                                        $nivel_text = 'Outro';
                                }
                                ?>
                                <span class="badge <?php echo $nivel_class; ?> nivel-badge"><?php echo $nivel_text; ?></span>
                                <span class="badge bg-primary curso-badge"><?php echo $curso['total_alunos']; ?> alunos</span>
                                 
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><?php echo $curso['nome']; ?></h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo !empty($curso['descricao']) ? $curso['descricao'] : 'Sem descrição disponível.'; ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-clock me-1"></i> <?php echo $curso['carga_horaria']; ?> horas
                                            </span>
                                            <span class="badge bg-light text-dark ms-2">
                                                <i class="fas fa-users me-1"></i> <?php echo $curso['total_turmas']; ?> turmas
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white d-flex justify-content-between">
                                    <button type="button" class="btn btn-sm btn-outline-primary inscricao-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#inscricaoModal" 
                                            data-curso-id="<?php echo $curso['id']; ?>" 
                                            data-curso-nome="<?php echo $curso['nome']; ?>">
                                        <i class="fas fa-user-plus me-1"></i> Inscrever Aluno
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detalhesModal" 
                                            data-curso-id="<?php echo $curso['id']; ?>">
                                        <i class="fas fa-info-circle me-1"></i> Detalhes
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Inscrição de Aluno -->
    <div class="modal fade" id="inscricaoModal" tabindex="-1" aria-labelledby="inscricaoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="inscricaoModalLabel">Inscrever Aluno em Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="inscricaoForm" method="POST" action="cursos_novo.php">
                        <input type="hidden" name="acao" value="inscrever_aluno">
                        <input type="hidden" name="aluno_id" id="aluno_id">
                        <input type="hidden" name="curso_id" id="curso_id">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="busca_aluno" class="form-label">Buscar Aluno</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="busca_aluno" placeholder="Digite o nome ou matrícula">
                                    <button class="btn btn-outline-secondary" type="button" id="buscarAlunoBtn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="search-results" id="resultadosBusca"></div>
                                <div class="form-text">Digite pelo menos 3 caracteres para iniciar a busca.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="curso_select" class="form-label">Curso</label>
                                <select class="form-select" id="curso_select" name="curso_select" required>
                                    <option value="">Selecione um curso</option>
                                    <?php foreach ($cursos as $curso): ?>
                                    <option value="<?php echo $curso['id']; ?>"><?php echo $curso['nome']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="turma_id" class="form-label">Turma</label>
                                <select class="form-select" id="turma_id" name="turma_id" required disabled>
                                    <option value="">Selecione um curso primeiro</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Dados do Aluno -->
                        <div class="card mb-3 d-none" id="dadosAlunoCard">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Dados do Aluno</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nome:</strong> <span id="aluno_nome"></span></p>
                                        <p><strong>Matrícula:</strong> <span id="aluno_matricula"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Turma Atual:</strong> <span id="aluno_turma"></span></p>
                                        <p><strong>Status:</strong> <span id="aluno_status"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning d-none" id="alunoJaInscritoAlert">
                            <i class="fas fa-exclamation-triangle me-2"></i> Este aluno já está inscrito neste curso.
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="inscricaoSubmitBtn" disabled>
                                <i class="fas fa-user-plus me-2"></i> Inscrever Aluno
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Novo Curso -->
    <div class="modal fade" id="novoCursoModal" tabindex="-1" aria-labelledby="novoCursoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="novoCursoModalLabel">Cadastrar Novo Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="novoCursoForm" method="POST" action="cursos_novo.php">
                        <input type="hidden" name="acao" value="cadastrar_curso">
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Curso</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="carga_horaria" class="form-label">Carga Horária (horas)</label>
                                <input type="number" class="form-control" id="carga_horaria" name="carga_horaria" min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nivel" class="form-label">Nível</label>
                                <select class="form-select" id="nivel" name="nivel" required>
                                    <option value="">Selecione</option>
                                    <option value="fundamental">Fundamental</option>
                                    <option value="medio">Médio</option>
                                    <option value="tecnico">Técnico</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i> Cadastrar Curso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Edição de Inscrição -->
    <div class="modal fade" id="editarInscricaoModal" tabindex="-1" aria-labelledby="editarInscricaoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editarInscricaoModalLabel">Editar Inscrição do Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="editarInscricaoForm">
                        <input type="hidden" id="edit_aluno_id" name="aluno_id">
                        <input type="hidden" id="edit_curso_id" name="curso_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Aluno</label>
                            <input type="text" class="form-control" id="edit_aluno_nome" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="edit_aluno_matricula" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_turma_id" class="form-label">Turma</label>
                            <select class="form-select" id="edit_turma_id" name="turma_id" required>
                                <!-- Preenchido via JavaScript -->
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status">
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                                <option value="transferido">Transferido</option>
                                <option value="trancado">Trancado</option>
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Detalhes do Curso -->
    <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="detalhesModalLabel">Detalhes do Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div id="detalhesCursoContent">
                        <!-- Preenchido via JavaScript -->
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2">Carregando detalhes do curso...</p>
                        </div>
                    </div>
                    
                    <!-- Lista de Alunos -->
                    <div id="listaAlunosContent" class="mt-4 d-none">
                        <h5 class="border-bottom pb-2"><i class="fas fa-user-graduate me-2"></i> Alunos Matriculados</h5>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tabelaAlunos">
                                <thead>
                                    <tr>
                                        <th>Matrícula</th>
                                        <th>Nome</th>
                                        <th>Turma</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="corpoTabelaAlunos">
                                    <!-- Preenchido via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div id="semAlunosMsg" class="alert alert-info d-none">
                            <i class="fas fa-info-circle me-2"></i> Nenhum aluno matriculado neste curso.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                animation: true,
                delay: {show: 100, hide: 100}
            });
        });
        
        // Configurar o sidebar toggle
        var sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
                
                // Animar o ícone do toggle com rotação
                var icon = this.querySelector('i');
                icon.style.transition = 'transform 0.3s ease';
                
                if (document.body.classList.contains('sb-sidenav-toggled')) {
                    icon.classList.replace('fa-bars', 'fa-times');
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    icon.classList.replace('fa-times', 'fa-bars');
                    icon.style.transform = 'rotate(0deg)';
                }
            });
        }
        
        // Adicionar animações aos itens do menu
        var menuItems = document.querySelectorAll('.list-group-item');
        menuItems.forEach(function(item, index) {
            // Adicionar delay na animação inicial para efeito cascata
            setTimeout(function() {
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, index * 50);
            
            item.addEventListener('mouseenter', function() {
                // Adicionar classe para efeito de hover
                this.classList.add('menu-hover');
            });
            
            item.addEventListener('mouseleave', function() {
                // Remover classe para efeito de hover
                this.classList.remove('menu-hover');
            });
        });
        
        // Busca de alunos
        var buscaAluno = document.getElementById('busca_aluno');
        var resultadosBusca = document.getElementById('resultadosBusca');
        var buscarAlunoBtn = document.getElementById('buscarAlunoBtn');
        var alunoId = document.getElementById('aluno_id');
        var dadosAlunoCard = document.getElementById('dadosAlunoCard');
        var alunoNome = document.getElementById('aluno_nome');
        var alunoMatricula = document.getElementById('aluno_matricula');
        var alunoTurma = document.getElementById('aluno_turma');
        var alunoStatus = document.getElementById('aluno_status');
        var inscricaoSubmitBtn = document.getElementById('inscricaoSubmitBtn');
        var alunoJaInscritoAlert = document.getElementById('alunoJaInscritoAlert');
        
        // Função para buscar alunos
        function buscarAlunos() {
            var termo = buscaAluno.value.trim();
            
            if (termo.length < 3) {
                resultadosBusca.style.display = 'none';
                return;
            }
            
            // Mostrar spinner de carregamento
            buscarAlunoBtn.innerHTML = '<span class="loading-spinner show"></span>';
            
            fetch('cursos_novo.php?acao=buscar_alunos&termo=' + encodeURIComponent(termo))
                .then(response => response.json())
                .then(data => {
                    // Esconder spinner
                    buscarAlunoBtn.innerHTML = '<i class="fas fa-search"></i>';
                    
                    if (data.sucesso && data.alunos.length > 0) {
                        // Limpar resultados anteriores
                        resultadosBusca.innerHTML = '';
                        
                        // Adicionar novos resultados
                        data.alunos.forEach(function(aluno) {
                            var item = document.createElement('div');
                            item.className = 'search-item';
                            item.innerHTML = '<strong>' + aluno.nome + '</strong><br>' +
                                            '<small>Matrícula: ' + aluno.matricula + '</small>';
                             
                            item.addEventListener('click', function() {
                                // Preencher dados do aluno
                                alunoId.value = aluno.id;
                                buscaAluno.value = aluno.nome;
                                alunoNome.textContent = aluno.nome;
                                alunoMatricula.textContent = aluno.matricula;
                                alunoTurma.textContent = aluno.turma || 'Não atribuído';
                                alunoStatus.textContent = aluno.status || 'Ativo';
                                 
                                // Mostrar card de dados
                                dadosAlunoCard.classList.remove('d-none');
                                 
                                // Esconder resultados
                                resultadosBusca.style.display = 'none';
                                 
                                // Verificar se o aluno já está inscrito no curso selecionado
                                verificarInscricao();
                                 
                                // Habilitar botão de inscrição se curso e turma estiverem selecionados
                                verificarFormularioCompleto();
                            });
                             
                            resultadosBusca.appendChild(item);
                        });
                        
                        // Mostrar resultados
                        resultadosBusca.style.display = 'block';
                    } else {
                        resultadosBusca.innerHTML = '<div class="search-item">Nenhum aluno encontrado</div>';
                        resultadosBusca.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar alunos:', error);
                    buscarAlunoBtn.innerHTML = '<i class="fas fa-search"></i>';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Ocorreu um erro ao buscar alunos. Tente novamente.'
                    });
                });
        }
        
        // Evento de clique no botão de busca
        if (buscarAlunoBtn) {
            buscarAlunoBtn.addEventListener('click', buscarAlunos);
        }
        
        // Evento de digitação no campo de busca
        if (buscaAluno) {
            buscaAluno.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    buscarAlunos();
                } else if (e.key === 'Escape') {
                    resultadosBusca.style.display = 'none';
                } else if (this.value.trim().length >= 3) {
                    buscarAlunos();
                } else {
                    resultadosBusca.style.display = 'none';
                }
            });
            
            // Fechar resultados ao clicar fora
            document.addEventListener('click', function(e) {
                if (!buscaAluno.contains(e.target) && !resultadosBusca.contains(e.target)) {
                    resultadosBusca.style.display = 'none';
                }
            });
        }
        
        // Carregar turmas ao selecionar um curso
        var cursoSelect = document.getElementById('curso_select');
        var turmaSelect = document.getElementById('turma_id');
        
        if (cursoSelect) {
            cursoSelect.addEventListener('change', function() {
                var cursoId = this.value;
                document.getElementById('curso_id').value = cursoId;
                
                if (cursoId) {
                    // Habilitar select de turmas
                    turmaSelect.disabled = false;
                    turmaSelect.innerHTML = '<option value="">Carregando turmas...</option>';
                    
                    // Carregar turmas do curso
                    fetch('cursos_novo.php?acao=obter_turmas&curso_id=' + cursoId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.sucesso) {
                                turmaSelect.innerHTML = '<option value="">Selecione uma turma</option>';
                                 
                                if (data.turmas.length > 0) {
                                    data.turmas.forEach(function(turma) {
                                        var option = document.createElement('option');
                                        option.value = turma.id;
                                        option.textContent = turma.nome + ' (' + turma.periodo + ') - ' + turma.ano;
                                        turmaSelect.appendChild(option);
                                    });
                                } else {
                                    turmaSelect.innerHTML = '<option value="">Nenhuma turma disponível</option>';
                                }
                            } else {
                                turmaSelect.innerHTML = '<option value="">Erro ao carregar turmas</option>';
                            }
                             
                            // Verificar se o aluno já está inscrito no curso
                            verificarInscricao();
                             
                            // Verificar se o formulário está completo
                            verificarFormularioCompleto();
                        })
                        .catch(error => {
                            console.error('Erro ao carregar turmas:', error);
                            turmaSelect.innerHTML = '<option value="">Erro ao carregar turmas</option>';
                        });
                } else {
                    // Desabilitar select de turmas
                    turmaSelect.disabled = true;
                    turmaSelect.innerHTML = '<option value="">Selecione um curso primeiro</option>';
                    
                    // Verificar se o formulário está completo
                    verificarFormularioCompleto();
                }
            });
        }
        
        // Verificar se o aluno já está inscrito no curso
        function verificarInscricao() {
            var alunoIdValue = alunoId.value;
            var cursoIdValue = document.getElementById('curso_id').value;
            
            if (alunoIdValue && cursoIdValue) {
                fetch('cursos_novo.php?acao=verificar_inscricao&aluno_id=' + alunoIdValue + '&curso_id=' + cursoIdValue)
                    .then(response => response.json())
                    .then(data => {
                        if (data.sucesso) {
                            if (data.inscrito) {
                                // Mostrar alerta de aluno já inscrito
                                alunoJaInscritoAlert.classList.remove('d-none');
                                inscricaoSubmitBtn.disabled = true;
                            } else {
                                // Esconder alerta de aluno já inscrito
                                alunoJaInscritoAlert.classList.add('d-none');
                                // Verificar se o formulário está completo
                                verificarFormularioCompleto();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao verificar inscrição:', error);
                    });
            }
        }
        
        // Verificar se o formulário está completo
        function verificarFormularioCompleto() {
            var alunoIdValue = alunoId.value;
            var cursoIdValue = document.getElementById('curso_id').value;
            var turmaIdValue = turmaSelect.value;
            
            if (alunoIdValue && cursoIdValue && turmaIdValue && alunoJaInscritoAlert.classList.contains('d-none')) {
                inscricaoSubmitBtn.disabled = false;
            } else {
                inscricaoSubmitBtn.disabled = true;
            }
        }
        
        // Evento de mudança no select de turma
        if (turmaSelect) {
            turmaSelect.addEventListener('change', verificarFormularioCompleto);
        }
        
        // Carregar detalhes do curso no modal de detalhes
        var detalhesModal = document.getElementById('detalhesModal');
        if (detalhesModal) {
            detalhesModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var cursoId = button.getAttribute('data-curso-id');
                var detalhesCursoContent = document.getElementById('detalhesCursoContent');
                var listaAlunosContent = document.getElementById('listaAlunosContent');
                var corpoTabelaAlunos = document.getElementById('corpoTabelaAlunos');
                var semAlunosMsg = document.getElementById('semAlunosMsg');
                
                // Mostrar spinner de carregamento
                detalhesCursoContent.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div><p class="mt-2">Carregando detalhes do curso...</p></div>';
                listaAlunosContent.classList.add('d-none');
                
                // Carregar detalhes do curso
                fetch('cursos_novo.php?acao=obter_detalhes_curso&curso_id=' + cursoId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.sucesso) {
                            var curso = data.curso;
                            var alunos = data.alunos;
                            
                            // Atualizar título do modal
                            var modalTitle = detalhesModal.querySelector('.modal-title');
                            if (modalTitle) {
                                modalTitle.textContent = 'Detalhes do Curso: ' + curso.nome;
                            }
                            
                            // Preencher detalhes do curso
                            var nivelTexto = '';
                            switch (curso.nivel) {
                                case 'fundamental':
                                    nivelTexto = 'Fundamental';
                                    break;
                                case 'medio':
                                    nivelTexto = 'Médio';
                                    break;
                                case 'tecnico':
                                    nivelTexto = 'Técnico';
                                    break;
                                default:
                                    nivelTexto = 'Outro';
                            }
                            
                            var html = '<div class="card">';
                            html += '<div class="card-header bg-info text-white"><h5 class="mb-0">Informações Gerais</h5></div>';
                            html += '<div class="card-body">';
                            html += '<div class="row">';
                            html += '<div class="col-md-6">';
                            html += '<p><strong>Nome:</strong> ' + curso.nome + '</p>';
                            html += '<p><strong>Nível:</strong> ' + nivelTexto + '</p>';
                            html += '<p><strong>Carga Horária:</strong> ' + curso.carga_horaria + ' horas</p>';
                            html += '</div>';
                            html += '<div class="col-md-6">';
                            html += '<p><strong>Total de Turmas:</strong> ' + curso.turmas.length + '</p>';
                            html += '<p><strong>Total de Alunos:</strong> ' + curso.total_alunos + '</p>';
                            html += '</div>';
                            html += '</div>';
                            
                            if (curso.descricao) {
                                html += '<div class="mt-3">';
                                html += '<h6>Descrição:</h6>';
                                html += '<p>' + curso.descricao + '</p>';
                                html += '</div>';
                            }
                            
                            html += '</div>';
                            html += '</div>';
                            
                            // Adicionar seção de turmas
                            if (curso.turmas.length > 0) {
                                html += '<div class="card mt-3">';
                                html += '<div class="card-header bg-primary text-white"><h5 class="mb-0">Turmas</h5></div>';
                                html += '<div class="card-body">';
                                html += '<div class="row">';
                                
                                curso.turmas.forEach(function(turma) {
                                    html += '<div class="col-md-6 mb-3">';
                                    html += '<div class="card h-100">';
                                    html += '<div class="card-body">';
                                    html += '<h6 class="card-title">' + turma.nome + '</h6>';
                                    html += '<p class="card-text"><small><strong>Período:</strong> ' + turma.periodo + '</small></p>';
                                    html += '<p class="card-text"><small><strong>Ano:</strong> ' + turma.ano + '</small></p>';
                                    html += '<p class="card-text"><small><strong>Alunos:</strong> ' + turma.total_alunos + '</small></p>';
                                    html += '<p class="card-text"><small><strong>Status:</strong> ' + turma.status + '</small></p>';
                                    html += '</div>';
                                    html += '</div>';
                                    html += '</div>';
                                });
                                
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                            }
                            
                            detalhesCursoContent.innerHTML = html;
                            
                            // Preencher lista de alunos
                            if (alunos.length > 0) {
                                corpoTabelaAlunos.innerHTML = '';
                                
                                alunos.forEach(function(aluno) {
                                    var tr = document.createElement('tr');
                                    
                                    // Status badge
                                    var statusBadge = '';
                                    switch (aluno.status) {
                                        case 'ativo':
                                            statusBadge = '<span class="badge bg-success">Ativo</span>';
                                            break;
                                        case 'inativo':
                                            statusBadge = '<span class="badge bg-secondary">Inativo</span>';
                                            break;
                                        case 'transferido':
                                            statusBadge = '<span class="badge bg-info">Transferido</span>';
                                            break;
                                        case 'trancado':
                                            statusBadge = '<span class="badge bg-warning">Trancado</span>';
                                            break;
                                        default:
                                            statusBadge = '<span class="badge bg-secondary">' + aluno.status + '</span>';
                                    }
                                    
                                    tr.innerHTML = '<td>' + aluno.matricula + '</td>' +
                                                  '<td>' + aluno.nome + '</td>' +
                                                  '<td>' + aluno.turma + ' (' + aluno.periodo + ')</td>' +
                                                  '<td>' + statusBadge + '</td>' +
                                                  '<td>' +
                                                      '<button type="button" class="btn btn-sm btn-warning editar-inscricao-btn" ' +
                                                              'data-bs-toggle="modal" ' +
                                                              'data-bs-target="#editarInscricaoModal" ' +
                                                              'data-aluno-id="' + aluno.id + '" ' +
                                                              'data-aluno-nome="' + aluno.nome + '" ' +
                                                              'data-aluno-matricula="' + aluno.matricula + '" ' +
                                                              'data-turma-id="' + aluno.turma_id + '" ' +
                                                              'data-status="' + aluno.status + '" ' +
                                                              'data-curso-id="' + cursoId + '">' +
                                                          '<i class="fas fa-edit"></i>' +
                                                      '</button>' +
                                                  '</td>';
                                    
                                    corpoTabelaAlunos.appendChild(tr);
                                });
                                
                                semAlunosMsg.classList.add('d-none');
                                listaAlunosContent.classList.remove('d-none');
                                
                                // Inicializar DataTable
                                if ($.fn.DataTable.isDataTable('#tabelaAlunos')) {
                                    $('#tabelaAlunos').DataTable().destroy();
                                }
                                
                                $('#tabelaAlunos').DataTable({
                                    language: {
                                        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                                    },
                                    responsive: true,
                                    pageLength: 10,
                                    lengthMenu: [5, 10, 25, 50],
                                    order: [[1, 'asc']]
                                });
                            } else {
                                corpoTabelaAlunos.innerHTML = '';
                                semAlunosMsg.classList.remove('d-none');
                                listaAlunosContent.classList.remove('d-none');
                            }
                        } else {
                            detalhesCursoContent.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Erro ao carregar detalhes do curso.</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao carregar detalhes do curso:', error);
                        detalhesCursoContent.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> Erro ao carregar detalhes do curso.</div>';
                    });
            });
        }
        
        // Configurar modal de edição de inscrição
        var editarInscricaoModal = document.getElementById('editarInscricaoModal');
        if (editarInscricaoModal) {
            editarInscricaoModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var alunoId = button.getAttribute('data-aluno-id');
                var alunoNome = button.getAttribute('data-aluno-nome');
                var alunoMatricula = button.getAttribute('data-aluno-matricula');
                var turmaId = button.getAttribute('data-turma-id');
                var status = button.getAttribute('data-status');
                var cursoId = button.getAttribute('data-curso-id');
                
                // Preencher campos do formulário
                document.getElementById('edit_aluno_id').value = alunoId;
                document.getElementById('edit_curso_id').value = cursoId;
                document.getElementById('edit_aluno_nome').value = alunoNome;
                document.getElementById('edit_aluno_matricula').value = alunoMatricula;
                document.getElementById('edit_status').value = status;
                
                // Carregar turmas do curso
                var editTurmaSelect = document.getElementById('edit_turma_id');
                editTurmaSelect.innerHTML = '<option value="">Carregando turmas...</option>';
                
                fetch('cursos_novo.php?acao=obter_turmas&curso_id=' + cursoId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.sucesso) {
                            editTurmaSelect.innerHTML = '';
                            
                            if (data.turmas.length > 0) {
                                data.turmas.forEach(function(turma) {
                                    var option = document.createElement('option');
                                    option.value = turma.id;
                                    option.textContent = turma.nome + ' (' + turma.periodo + ') - ' + turma.ano;
                                    option.selected = (turma.id == turmaId);
                                    editTurmaSelect.appendChild(option);
                                });
                            } else {
                                editTurmaSelect.innerHTML = '<option value="">Nenhuma turma disponível</option>';
                            }
                        } else {
                            editTurmaSelect.innerHTML = '<option value="">Erro ao carregar turmas</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao carregar turmas:', error);
                        editTurmaSelect.innerHTML = '<option value="">Erro ao carregar turmas</option>';
                    });
            });
            
            // Processar formulário de edição de inscrição
            var editarInscricaoForm = document.getElementById('editarInscricaoForm');
            if (editarInscricaoForm) {
                editarInscricaoForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    var formData = new FormData(this);
                    var submitBtn = this.querySelector('button[type="submit"]');
                    var originalText = submitBtn.innerHTML;
                    
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processando...';
                    submitBtn.disabled = true;
                    
                    fetch('cursos_novo.php?acao=atualizar_inscricao', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        
                        if (data.sucesso) {
                            // Fechar modal
                            var modal = bootstrap.Modal.getInstance(editarInscricaoModal);
                            modal.hide();
                            
                            // Mostrar mensagem de sucesso
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: data.mensagem,
                                timer: 3000,
                                timerProgressBar: true
                            }).then(() => {
                                // Recarregar a página para atualizar os dados
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: data.mensagem
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao atualizar inscrição:', error);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Ocorreu um erro ao processar a solicitação. Tente novamente.'
                        });
                    });
                });
            }
        }
        
        // Preencher dados do curso ao abrir modal de inscrição
        var inscricaoModal = document.getElementById('inscricaoModal');
        if (inscricaoModal) {
            inscricaoModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                
                if (button && button.classList.contains('inscricao-btn')) {
                    var cursoId = button.getAttribute('data-curso-id');
                    var cursoNome = button.getAttribute('data-curso-nome');
                    
                    // Selecionar curso no select
                    if (cursoSelect) {
                        cursoSelect.value = cursoId;
                        cursoSelect.dispatchEvent(new Event('change'));
                    }
                    
                    // Atualizar título do modal
                    var modalTitle = inscricaoModal.querySelector('.modal-title');
                    if (modalTitle) {
                        modalTitle.textContent = 'Inscrever Aluno no Curso: ' + cursoNome;
                    }
                } else {
                    // Limpar formulário
                    if (document.getElementById('inscricaoForm')) {
                        document.getElementById('inscricaoForm').reset();
                    }
                    
                    // Esconder card de dados do aluno
                    if (dadosAlunoCard) {
                        dadosAlunoCard.classList.add('d-none');
                    }
                    
                    // Esconder alerta de aluno já inscrito
                    if (alunoJaInscritoAlert) {
                        alunoJaInscritoAlert.classList.add('d-none');
                    }
                    
                    // Desabilitar botão de inscrição
                    if (inscricaoSubmitBtn) {
                        inscricaoSubmitBtn.disabled = true;
                    }
                    
                    // Desabilitar select de turmas
                    if (turmaSelect) {
                        turmaSelect.disabled = true;
                        turmaSelect.innerHTML = '<option value="">Selecione um curso primeiro</option>';
                    }
                }
            });
        }
        
        // Feedback visual ao enviar formulário
        var forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    var originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processando...';
                    submitBtn.disabled = true;
                    
                    // Restaurar botão após 5 segundos (caso ocorra algum erro)
                    setTimeout(function() {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 5000);
                }
            });
        });
        
        // Inicializar DataTables (se houver tabelas)
        var tables = document.querySelectorAll('.datatable');
        tables.forEach(function(table) {
            new DataTable(table, {
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                },
                responsive: true
            });
        });
        
        // Animação para cards
        var cards = document.querySelectorAll('.card');
        cards.forEach(function(card, index) {
            setTimeout(function() {
                card.classList.add('animate__fadeIn');
            }, index * 100);
        });
    });
    </script>
</body>
</html>
