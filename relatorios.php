<?php
// Incluir arquivos necessários
require_once 'conexao.php';
require_once 'relatorios_functions.php';

// Inicializar variáveis
$tipo_relatorio = isset($_GET['tipo']) ? $_GET['tipo'] : 'alunos';
$filtros = [];

// Processar filtros
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Filtros para relatório de alunos
    if (isset($_POST['turma_id']) && !empty($_POST['turma_id'])) {
        $filtros['turma_id'] = (int)$_POST['turma_id'];
    }
    
    if (isset($_POST['curso_id']) && !empty($_POST['curso_id'])) {
        $filtros['curso_id'] = (int)$_POST['curso_id'];
    }
    
    if (isset($_POST['status']) && !empty($_POST['status'])) {
        $filtros['status'] = $_POST['status'];
    }
    
    if (isset($_POST['nivel']) && !empty($_POST['nivel'])) {
        $filtros['nivel'] = $_POST['nivel'];
    }
    
    // Filtros para relatório de notas
    if (isset($_POST['disciplina_id']) && !empty($_POST['disciplina_id'])) {
        $filtros['disciplina_id'] = (int)$_POST['disciplina_id'];
    }
    
    if (isset($_POST['periodo']) && !empty($_POST['periodo'])) {
        $filtros['periodo'] = $_POST['periodo'];
    }
    
    if (isset($_POST['ano_letivo']) && !empty($_POST['ano_letivo'])) {
        $filtros['ano_letivo'] = (int)$_POST['ano_letivo'];
    }
    
    if (isset($_POST['nota_min']) && $_POST['nota_min'] !== '') {
        $filtros['nota_min'] = (float)$_POST['nota_min'];
    }
    
    if (isset($_POST['nota_max']) && $_POST['nota_max'] !== '') {
        $filtros['nota_max'] = (float)$_POST['nota_max'];
    }
    
    // Filtros para relatório de cursos
    if (isset($_POST['carga_horaria_min']) && !empty($_POST['carga_horaria_min'])) {
        $filtros['carga_horaria_min'] = (int)$_POST['carga_horaria_min'];
    }
    
    if (isset($_POST['carga_horaria_max']) && !empty($_POST['carga_horaria_max'])) {
        $filtros['carga_horaria_max'] = (int)$_POST['carga_horaria_max'];
    }
}

// Obter dados para o relatório selecionado
$dados_relatorio = [];
$titulo_relatorio = '';

switch ($tipo_relatorio) {
    case 'alunos':
        $dados_relatorio = obterDadosRelatorioAlunos($conexao, $filtros);
        $titulo_relatorio = 'Relatório de Alunos';
        break;
    case 'cursos':
        $dados_relatorio = obterDadosRelatorioCursos($conexao, $filtros);
        $titulo_relatorio = 'Relatório de Cursos';
        break;
    case 'notas':
        $dados_relatorio = obterDadosRelatorioNotas($conexao, $filtros);
        $titulo_relatorio = 'Relatório de Notas';
        break;
    default:
        $dados_relatorio = obterDadosRelatorioAlunos($conexao, $filtros);
        $titulo_relatorio = 'Relatório de Alunos';
        break;
}

// Obter dados para filtros
$turmas = obterTurmasParaFiltro($conexao);
$cursos = obterCursosParaFiltro($conexao);
$disciplinas = obterDisciplinasParaFiltro($conexao);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_relatorio; ?> - Sistema de Gestão Acadêmica</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
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
        .export-buttons {
            margin-bottom: 20px;
        }
        .export-buttons .btn {
            margin-right: 10px;
        }
        .table-container {
            overflow-x: auto;
        }
        .filters-card {
            margin-bottom: 20px;
        }
        .report-type-nav {
            margin-bottom: 20px;
        }
        .report-type-nav .nav-link {
            border-radius: 5px;
            margin-right: 5px;
        }
        .report-type-nav .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
        }
        .print-only {
            display: none;
        }
        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .print-header p {
            font-size: 14px;
            color: #666;
        }
        .btn-export {
            transition: all 0.3s;
        }
        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Layout similar ao dashboard -->
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-white border-end no-print" id="sidebar-wrapper">
            <div class="sidebar-heading d-flex align-items-center p-3">
                <i class="fas fa-school text-primary me-2"></i>
                <span class="fs-4 fw-bold">EduGestão</span>
            </div>
             <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="estudante.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-graduate"></i> Estudantes
                </a>
                <a href="notas.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-chalkboard-teacher"></i> Notas
                </a>
                <a href="cursos.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-book"></i> Cursos
                </a>
                
                <a href="relatorios.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-book"></i> Relatórios
                </a>
            </div>
        </div>
        
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom no-print">
                <div class="container-fluid">
                    <!-- Hamburger menu for mobile -->
                    <button class="navbar-toggler" id="sidebarToggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <!-- Page title -->
                    <div class="d-flex flex-column">
                        <span class="navbar-brand mb-0 h1">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>
                            <?php echo $titulo_relatorio; ?>
                        </span>
                        <span class="text-muted small">Visualize e exporte relatórios do sistema</span>
                    </div>
                    
                    <div class="ms-auto d-flex align-items-center">
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
            
            <!-- Cabeçalho para impressão -->
            <div class="print-only print-header">
                <h1>Sistema de Gestão Acadêmica - <?php echo $titulo_relatorio; ?></h1>
                <p>Data de geração: <?php echo date('d/m/Y H:i:s'); ?></p>
            </div>
            
            <!-- Page content -->
            <div class="container-fluid p-4">
                <!-- Tipos de relatório -->
                <div class="report-type-nav no-print">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($tipo_relatorio === 'alunos') ? 'active' : ''; ?>" href="relatorios.php?tipo=alunos">
                                <i class="fas fa-user-graduate me-1"></i> Alunos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($tipo_relatorio === 'cursos') ? 'active' : ''; ?>" href="relatorios.php?tipo=cursos">
                                <i class="fas fa-book me-1"></i> Cursos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($tipo_relatorio === 'notas') ? 'active' : ''; ?>" href="relatorios.php?tipo=notas">
                                <i class="fas fa-chart-line me-1"></i> Notas
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Filtros -->
                <div class="card filters-card no-print">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-filter me-2"></i> Filtros
                    </div>
                    <div class="card-body">
                        <form method="POST" action="relatorios.php?tipo=<?php echo $tipo_relatorio; ?>" id="filtrosForm">
                            <?php if ($tipo_relatorio === 'alunos'): ?>
                            <!-- Filtros para relatório de alunos -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="curso_id" class="form-label">Curso</label>
                                    <select class="form-select" id="curso_id" name="curso_id">
                                        <option value="">Todos os cursos</option>
                                        <?php foreach ($cursos as $curso): ?>
                                        <option value="<?php echo $curso['id']; ?>" <?php echo (isset($filtros['curso_id']) && $filtros['curso_id'] == $curso['id']) ? 'selected' : ''; ?>>
                                            <?php echo $curso['nome']; ?> (<?php echo ucfirst($curso['nivel']); ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="turma_id" class="form-label">Turma</label>
                                    <select class="form-select" id="turma_id" name="turma_id">
                                        <option value="">Todas as turmas</option>
                                        <?php foreach ($turmas as $turma): ?>
                                        <option value="<?php echo $turma['id']; ?>" <?php echo (isset($filtros['turma_id']) && $filtros['turma_id'] == $turma['id']) ? 'selected' : ''; ?>>
                                            <?php echo $turma['nome']; ?> - <?php echo $turma['curso']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Todos os status</option>
                                        <option value="ativo" <?php echo (isset($filtros['status']) && $filtros['status'] === 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="inativo" <?php echo (isset($filtros['status']) && $filtros['status'] === 'inativo') ? 'selected' : ''; ?>>Inativo</option>
                                        <option value="transferido" <?php echo (isset($filtros['status']) && $filtros['status'] === 'transferido') ? 'selected' : ''; ?>>Transferido</option>
                                        <option value="trancado" <?php echo (isset($filtros['status']) && $filtros['status'] === 'trancado') ? 'selected' : ''; ?>>Trancado</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="nivel" class="form-label">Nível</label>
                                    <select class="form-select" id="nivel" name="nivel">
                                        <option value="">Todos os níveis</option>
                                        <option value="fundamental" <?php echo (isset($filtros['nivel']) && $filtros['nivel'] === 'fundamental') ? 'selected' : ''; ?>>Fundamental</option>
                                        <option value="medio" <?php echo (isset($filtros['nivel']) && $filtros['nivel'] === 'medio') ? 'selected' : ''; ?>>Médio</option>
                                        <option value="tecnico" <?php echo (isset($filtros['nivel']) && $filtros['nivel'] === 'tecnico') ? 'selected' : ''; ?>>Técnico</option>
                                    </select>
                                </div>
                            </div>
                            <?php elseif ($tipo_relatorio === 'cursos'): ?>
                            <!-- Filtros para relatório de cursos -->
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nivel" class="form-label">Nível</label>
                                    <select class="form-select" id="nivel" name="nivel">
                                        <option value="">Todos os níveis</option>
                                        <option value="fundamental" <?php echo (isset($filtros['nivel']) && $filtros['nivel'] === 'fundamental') ? 'selected' : ''; ?>>Fundamental</option>
                                        <option value="medio" <?php echo (isset($filtros['nivel']) && $filtros['nivel'] === 'medio') ? 'selected' : ''; ?>>Médio</option>
                                        <option value="tecnico" <?php echo (isset($filtros['nivel']) && $filtros['nivel'] === 'tecnico') ? 'selected' : ''; ?>>Técnico</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="carga_horaria_min" class="form-label">Carga Horária Mínima</label>
                                    <input type="number" class="form-control" id="carga_horaria_min" name="carga_horaria_min" value="<?php echo isset($filtros['carga_horaria_min']) ? $filtros['carga_horaria_min'] : ''; ?>" min="0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="carga_horaria_max" class="form-label">Carga Horária Máxima</label>
                                    <input type="number" class="form-control" id="carga_horaria_max" name="carga_horaria_max" value="<?php echo isset($filtros['carga_horaria_max']) ? $filtros['carga_horaria_max'] : ''; ?>" min="0">
                                </div>
                            </div>
                            <?php elseif ($tipo_relatorio === 'notas'): ?>
                            <!-- Filtros para relatório de notas -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="curso_id" class="form-label">Curso</label>
                                    <select class="form-select" id="curso_id" name="curso_id">
                                        <option value="">Todos os cursos</option>
                                        <?php foreach ($cursos as $curso): ?>
                                        <option value="<?php echo $curso['id']; ?>" <?php echo (isset($filtros['curso_id']) && $filtros['curso_id'] == $curso['id']) ? 'selected' : ''; ?>>
                                            <?php echo $curso['nome']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="turma_id" class="form-label">Turma</label>
                                    <select class="form-select" id="turma_id" name="turma_id">
                                        <option value="">Todas as turmas</option>
                                        <?php foreach ($turmas as $turma): ?>
                                        <option value="<?php echo $turma['id']; ?>" <?php echo (isset($filtros['turma_id']) && $filtros['turma_id'] == $turma['id']) ? 'selected' : ''; ?>>
                                            <?php echo $turma['nome']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="disciplina_id" class="form-label">Disciplina</label>
                                    <select class="form-select" id="disciplina_id" name="disciplina_id">
                                        <option value="">Todas as disciplinas</option>
                                        <?php foreach ($disciplinas as $disciplina): ?>
                                        <option value="<?php echo $disciplina['id']; ?>" <?php echo (isset($filtros['disciplina_id']) && $filtros['disciplina_id'] == $disciplina['id']) ? 'selected' : ''; ?>>
                                            <?php echo $disciplina['nome']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="periodo" class="form-label">Período</label>
                                    <select class="form-select" id="periodo" name="periodo">
                                        <option value="">Todos os períodos</option>
                                        <option value="1" <?php echo (isset($filtros['periodo']) && $filtros['periodo'] === '1') ? 'selected' : ''; ?>>1º Bimestre</option>
                                        <option value="2" <?php echo (isset($filtros['periodo']) && $filtros['periodo'] === '2') ? 'selected' : ''; ?>>2º Bimestre</option>
                                        <option value="3" <?php echo (isset($filtros['periodo']) && $filtros['periodo'] === '3') ? 'selected' : ''; ?>>3º Bimestre</option>
                                        <option value="4" <?php echo (isset($filtros['periodo']) && $filtros['periodo'] === '4') ? 'selected' : ''; ?>>4º Bimestre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="ano_letivo" class="form-label">Ano Letivo</label>
                                    <select class="form-select" id="ano_letivo" name="ano_letivo">
                                        <option value="">Todos os anos</option>
                                        <?php for ($ano = date('Y'); $ano >= date('Y') - 5; $ano--): ?>
                                        <option value="<?php echo $ano; ?>" <?php echo (isset($filtros['ano_letivo']) && $filtros['ano_letivo'] == $ano) ? 'selected' : ''; ?>>
                                            <?php echo $ano; ?>
                                        </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nota_min" class="form-label">Nota Mínima</label>
                                    <input type="number" class="form-control" id="nota_min" name="nota_min" value="<?php echo isset($filtros['nota_min']) ? $filtros['nota_min'] : ''; ?>" min="0" max="10" step="0.1">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nota_max" class="form-label">Nota Máxima</label>
                                    <input type="number" class="form-control" id="nota_max" name="nota_max" value="<?php echo isset($filtros['nota_max']) ? $filtros['nota_max'] : ''; ?>" min="0" max="10" step="0.1">
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i> Aplicar Filtros
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Botões de exportação -->
                <div class="export-buttons no-print">
                    <button type="button" class="btn btn-success btn-export" id="exportExcel">
                        <i class="fas fa-file-excel me-2"></i> Exportar para Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-export" id="exportPDF">
                        <i class="fas fa-file-pdf me-2"></i> Exportar para PDF
                    </button>
                    <button type="button" class="btn btn-info btn-export" id="printReport">
                        <i class="fas fa-print me-2"></i> Imprimir
                    </button>
                </div>
                
                <!-- Tabela de dados -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><?php echo $titulo_relatorio; ?></h5>
                    </div>
                    <div class="card-body table-container">
                        <?php if (empty($dados_relatorio)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Nenhum dado encontrado para os filtros selecionados.
                        </div>
                        <?php else: ?>
                        <table class="table table-striped table-hover" id="tabelaRelatorio">
                            <thead>
                                <?php if ($tipo_relatorio === 'alunos'): ?>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nome</th>
                                    <th>Turma</th>
                                    <th>Curso</th>
                                    <th>Nível</th>
                                    <th>Responsável</th>
                                    <th>Telefone</th>
                                    <th>Status</th>
                                    <th>Data Cadastro</th>
                                </tr>
                                <?php elseif ($tipo_relatorio === 'cursos'): ?>
                                <tr>
                                    <th>Nome</th>
                                    <th>Nível</th>
                                    <th>Carga Horária</th>
                                    <th>Total Turmas</th>
                                    <th>Total Alunos</th>
                                    <th>Descrição</th>
                                </tr>
                                <?php elseif ($tipo_relatorio === 'notas'): ?>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Aluno</th>
                                    <th>Turma</th>
                                    <th>Curso</th>
                                    <th>Disciplina</th>
                                    <th>Nota</th>
                                    <th>Período</th>
                                    <th>Ano Letivo</th>
                                </tr>
                                <?php endif; ?>
                            </thead>
                            <tbody>
                                <?php if ($tipo_relatorio === 'alunos'): ?>
                                    <?php foreach ($dados_relatorio as $aluno): ?>
                                    <tr>
                                        <td><?php echo $aluno['matricula']; ?></td>
                                        <td><?php echo $aluno['nome']; ?></td>
                                        <td><?php echo $aluno['turma'] ?? 'N/A'; ?></td>
                                        <td><?php echo $aluno['curso'] ?? 'N/A'; ?></td>
                                        <td><?php echo ucfirst($aluno['nivel'] ?? 'N/A'); ?></td>
                                        <td><?php echo $aluno['responsavel'] ?? 'N/A'; ?></td>
                                        <td><?php echo $aluno['telefone_responsavel'] ?? 'N/A'; ?></td>
                                        <td>
                                            <?php 
                                            $status_class = '';
                                            switch ($aluno['status']) {
                                                case 'ativo':
                                                    $status_class = 'bg-success';
                                                    break;
                                                case 'inativo':
                                                    $status_class = 'bg-secondary';
                                                    break;
                                                case 'transferido':
                                                    $status_class = 'bg-info';
                                                    break;
                                                case 'trancado':
                                                    $status_class = 'bg-warning';
                                                    break;
                                                default:
                                                    $status_class = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($aluno['status']); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($aluno['data_cadastro'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php elseif ($tipo_relatorio === 'cursos'): ?>
                                    <?php foreach ($dados_relatorio as $curso): ?>
                                    <tr>
                                        <td><?php echo $curso['nome']; ?></td>
                                        <td><?php echo ucfirst($curso['nivel']); ?></td>
                                        <td><?php echo $curso['carga_horaria']; ?> horas</td>
                                        <td><?php echo $curso['total_turmas']; ?></td>
                                        <td><?php echo $curso['total_alunos']; ?></td>
                                        <td><?php echo $curso['descricao'] ?? 'N/A'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php elseif ($tipo_relatorio === 'notas'): ?>
                                    <?php foreach ($dados_relatorio as $nota): ?>
                                    <tr>
                                        <td><?php echo $nota['matricula']; ?></td>
                                        <td><?php echo $nota['aluno_nome']; ?></td>
                                        <td><?php echo $nota['turma'] ?? 'N/A'; ?></td>
                                        <td><?php echo $nota['curso'] ?? 'N/A'; ?></td>
                                        <td><?php echo $nota['disciplina'] ?? 'N/A'; ?></td>
                                        <td>
                                            <?php 
                                            $nota_valor = (float)$nota['nota'];
                                            $nota_class = '';
                                            if ($nota_valor >= 7) {
                                                $nota_class = 'text-success fw-bold';
                                            } elseif ($nota_valor >= 5) {
                                                $nota_class = 'text-warning fw-bold';
                                            } else {
                                                $nota_class = 'text-danger fw-bold';
                                            }
                                            ?>
                                            <span class="<?php echo $nota_class; ?>"><?php echo number_format($nota_valor, 1, ',', '.'); ?></span>
                                        </td>
                                        <td><?php echo $nota['periodo'] . 'º Bimestre'; ?></td>
                                        <td><?php echo $nota['ano_letivo']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- SheetJS (xlsx) -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Inicializar elementos da interface
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTables
            const table = $('#tabelaRelatorio').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                },
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
            });
            
            // Animar itens do menu
            const menuItems = document.querySelectorAll('.list-group-item');
            menuItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, 100 * index);
            });
            
            // Toggle para o sidebar em dispositivos móveis
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.getElementById('wrapper').classList.toggle('toggled');
                });
            }
            
            // Exportar para Excel
            document.getElementById('exportExcel').addEventListener('click', function() {
                exportToExcel();
            });
            
            // Exportar para PDF
            document.getElementById('exportPDF').addEventListener('click', function() {
                exportToPDF();
            });
            
            // Imprimir relatório
            document.getElementById('printReport').addEventListener('click', function() {
                window.print();
            });
        });
        
        // Função para exportar para Excel
        function exportToExcel() {
            const tipoRelatorio = '<?php echo $tipo_relatorio; ?>';
            const tituloRelatorio = '<?php echo $titulo_relatorio; ?>';
            const dataAtual = new Date().toLocaleDateString('pt-BR');
            const nomeArquivo = `${tituloRelatorio}_${dataAtual}.xlsx`;
            
            // Obter dados da tabela
            const table = document.getElementById('tabelaRelatorio');
            if (!table || table.rows.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sem dados',
                    text: 'Não há dados para exportar.'
                });
                return;
            }
            
            // Criar workbook
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(table);
            
            // Adicionar worksheet ao workbook
            XLSX.utils.book_append_sheet(wb, ws, tituloRelatorio);
            
            // Exportar para arquivo
            XLSX.writeFile(wb, nomeArquivo);
            
            // Mostrar mensagem de sucesso
            Swal.fire({
                icon: 'success',
                title: 'Exportação concluída',
                text: `O relatório foi exportado para Excel com sucesso!`,
                timer: 2000,
                timerProgressBar: true
            });
        }
        
        // Função para exportar para PDF
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const tipoRelatorio = '<?php echo $tipo_relatorio; ?>';
            const tituloRelatorio = '<?php echo $titulo_relatorio; ?>';
            const dataAtual = new Date().toLocaleDateString('pt-BR');
            
            // Criar nova instância do jsPDF
            const doc = new jsPDF('l', 'mm', 'a4');
            
            // Adicionar título e data
            doc.setFontSize(18);
            doc.text(tituloRelatorio, 14, 22);
            doc.setFontSize(10);
            doc.text(`Data de geração: ${dataAtual}`, 14, 30);
            
            // Obter cabeçalhos e dados da tabela
            const table = document.getElementById('tabelaRelatorio');
            if (!table || table.rows.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sem dados',
                    text: 'Não há dados para exportar.'
                });
                return;
            }
            
            // Usar autoTable para criar a tabela no PDF
            doc.autoTable({
                html: '#tabelaRelatorio',
                startY: 35,
                theme: 'grid',
                headStyles: {
                    fillColor: [13, 110, 253],
                    textColor: 255,
                    fontStyle: 'bold'
                },
                alternateRowStyles: {
                    fillColor: [240, 240, 240]
                },
                margin: { top: 35 },
                styles: {
                    overflow: 'linebreak',
                    cellWidth: 'auto',
                    fontSize: 9
                }
            });
            
            // Salvar o PDF
            doc.save(`${tituloRelatorio}_${dataAtual}.pdf`);
            
            // Mostrar mensagem de sucesso
            Swal.fire({
                icon: 'success',
                title: 'Exportação concluída',
                text: `O relatório foi exportado para PDF com sucesso!`,
                timer: 2000,
                timerProgressBar: true
            });
        }
    </script>
</body>
</html>
