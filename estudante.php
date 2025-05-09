<?php
// Incluir arquivos necessários
require_once 'conexao.php';
require_once 'estudante_functions.php';

// Inicializar variáveis
$aba_ativa = isset($_GET['aba']) ? $_GET['aba'] : 'listar';
$mensagem = '';
$tipo_mensagem = '';

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'cadastrar':
                // Processar cadastro de estudante
                $resultado = cadastrarEstudante($conexao, $_POST, $_FILES);
                $mensagem = $resultado['mensagem'];
                $tipo_mensagem = $resultado['tipo'];
                break;
                
            case 'editar':
                // Processar edição de estudante
                $resultado = editarEstudante($conexao, $_POST, $_FILES);
                $mensagem = $resultado['mensagem'];
                $tipo_mensagem = $resultado['tipo'];
                break;
                
            case 'excluir':
                // Processar exclusão de estudante
                $resultado = excluirEstudante($conexao, $_POST['id']);
                $mensagem = $resultado['mensagem'];
                $tipo_mensagem = $resultado['tipo'];
                break;
        }
    }
}

// Buscar dados para a página
$turmas = obterTurmas($conexao);
$termo_pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';
$estudantes = pesquisarEstudantes($conexao, $termo_pesquisa);

// Obter estudante específico para edição
$estudante = null;
if ($aba_ativa === 'editar' && isset($_GET['id'])) {
    $estudante = obterEstudantePorId($conexao, $_GET['id']);
    if (!$estudante) {
        $mensagem = 'Estudante não encontrado.';
        $tipo_mensagem = 'danger';
        $aba_ativa = 'listar';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Estudantes - EduGestão</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos gerais */
        .foto-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e9ecef;
        }
        .card-estudante {
            transition: all 0.3s ease;
        }
        .card-estudante:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .nav-link.active {
            border-bottom: 3px solid #4e73df;
            color: #4e73df;
            font-weight: 600;
        }
        
        /* Estilos do sidebar e animações */
        #sidebar-wrapper {
            min-height: 100vh;
            width: 250px;
            transition: margin 0.25s ease-out;
            z-index: 1;
        }
        
        #page-content-wrapper {
            min-width: 100vw;
            transition: margin-left 0.25s ease-out;
        }
        
        .sb-sidenav-toggled #sidebar-wrapper {
            margin-left: -250px;
        }
        
        .sb-sidenav-toggled #page-content-wrapper {
            margin-left: 0;
        }
        
        /* Animações para itens do menu */
        .list-group-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            padding-left: 1.25rem;
        }
        
        .list-group-item:hover {
            background-color: #f8f9fa;
            border-left: 3px solid #4e73df;
            padding-left: 1.5rem;
        }
        
        .list-group-item.active {
            background-color: rgba(78, 115, 223, 0.1);
            color: #4e73df;
            border-left: 3px solid #4e73df;
            font-weight: 600;
        }
        
        .list-group-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            transition: transform 0.2s ease;
        }
        
        .list-group-item:hover i {
            transform: scale(1.2);
        }
        
        /* Animação para dropdown do usuário */
        .dropdown-menu {
            animation: fadeIn 0.2s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsividade */
        @media (min-width: 992px) {
            #sidebar-wrapper {
                margin-left: 0;
            }
            
            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }
            
            .sb-sidenav-toggled #sidebar-wrapper {
                margin-left: -250px;
            }
        }
        
        /* Efeito de hover personalizado */
        .menu-hover {
            background-color: #f0f7ff;
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
                <a href="estudante.php" class="list-group-item list-group-item-action active">
                    <i class="fas fa-user-graduate"></i> Estudantes
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-chalkboard-teacher"></i> Professores
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-book"></i> Cursos
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <i class="fas fa-door-open"></i> Salas
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
                            <i class="fas fa-user-graduate me-2 text-primary"></i>
                            Gestão de Estudantes
                        </span>
                        <span class="text-muted small">Cadastro e manutenção de estudantes</span>
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
                        <div class="ms-3">
                            <div class="dropdown">
                                <a href="#" class="text-dark" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="position-relative d-inline-block">
                                        <i class="fas fa-bell fa-lg"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; transform: translate(-50%, -50%);">
                                            3
                                        </span>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm p-0" aria-labelledby="notificationDropdown" style="width: 320px;">
                                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                        <h6 class="mb-0 fw-bold">Notificações</h6>
                                        <a href="#" class="text-decoration-none small">Marcar todas como lidas</a>
                                    </div>
                                    <div class="notification-list" style="max-height: 300px; overflow-y: auto;">
                                        <a href="#" class="dropdown-item p-3 border-bottom d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-primary text-white rounded-circle p-2">
                                                    <i class="fas fa-user-plus"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <p class="mb-0">Novo estudante cadastrado</p>
                                                <small class="text-muted">Há 5 minutos</small>
                                            </div>
                                        </a>
                                        <a href="#" class="dropdown-item p-3 border-bottom d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-success text-white rounded-circle p-2">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <p class="mb-0">Notas atualizadas com sucesso</p>
                                                <small class="text-muted">Há 2 horas</small>
                                            </div>
                                        </a>
                                        <a href="#" class="dropdown-item p-3 border-bottom d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-warning text-white rounded-circle p-2">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <p class="mb-0">Lembrete: Reunião de professores</p>
                                                <small class="text-muted">Amanhã às 14:00</small>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="p-2 text-center border-top">
                                        <a href="#" class="text-decoration-none">Ver todas as notificações</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Conteúdo da página -->
            <div class="container-fluid p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Gestão de Estudantes</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Estudantes</li>
                        </ol>
                    </nav>
                </div>
                
                <!-- Mensagem de feedback -->
                <?php if (!empty($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php endif; ?>
                
                <!-- Abas de navegação -->
                <ul class="nav nav-tabs mb-4" id="estudantesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?php echo $aba_ativa === 'listar' ? 'active' : ''; ?>" 
                           id="listar-tab" 
                           href="?aba=listar" 
                           role="tab">
                            <i class="fas fa-list me-2"></i>Listar Estudantes
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?php echo $aba_ativa === 'cadastrar' ? 'active' : ''; ?>" 
                           id="cadastrar-tab" 
                           href="?aba=cadastrar" 
                           role="tab">
                            <i class="fas fa-user-plus me-2"></i>Cadastrar Estudante
                        </a>
                    </li>
                    <?php if ($aba_ativa === 'editar'): ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" 
                           id="editar-tab" 
                           href="#" 
                           role="tab">
                            <i class="fas fa-user-edit me-2"></i>Editar Estudante
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Conteúdo das abas -->
                <div class="tab-content" id="estudantesTabsContent">
                    <!-- Aba de Listagem -->
                    <?php if ($aba_ativa === 'listar'): ?>
                    <div class="tab-pane fade show active" id="listar" role="tabpanel" aria-labelledby="listar-tab">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="mb-0 text-primary">Lista de Estudantes</h5>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="" method="GET" class="d-flex">
                                            <input type="hidden" name="aba" value="listar">
                                            <input type="text" name="pesquisa" class="form-control me-2" placeholder="Pesquisar por nome ou matrícula" value="<?php echo htmlspecialchars($termo_pesquisa); ?>">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (empty($estudantes)): ?>
                                <div class="alert alert-info">
                                    Nenhum estudante encontrado. <a href="?aba=cadastrar" class="alert-link">Cadastre um novo estudante</a>.
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tabelaEstudantes">
                                        <thead>
                                            <tr>
                                                <th>Foto</th>
                                                <th>Nome</th>
                                                <th>Matrícula</th>
                                                <th>Turma</th>
                                                <th>Data de Nascimento</th>
                                                <th>Responsável</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($estudantes as $e): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo !empty($e['foto']) ? 'uploads/estudantes/' . htmlspecialchars($e['foto']) : 'https://via.placeholder.com/40?text=Foto'; ?>" 
                                                         class="rounded-circle" 
                                                         width="40" 
                                                         height="40" 
                                                         style="object-fit: cover;"
                                                         alt="Foto de <?php echo htmlspecialchars($e['nome']); ?>">
                                                </td>
                                                <td><?php echo htmlspecialchars($e['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($e['matricula']); ?></td>
                                                <td><?php echo htmlspecialchars($e['turma_nome']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($e['data_nascimento'])); ?></td>
                                                <td><?php echo htmlspecialchars($e['responsavel']); ?></td>
                                                <td>
                                                    <a href="?aba=editar&id=<?php echo $e['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#excluirModal" data-id="<?php echo $e['id']; ?>" data-nome="<?php echo htmlspecialchars($e['nome']); ?>" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detalhesModal" 
                                                        data-id="<?php echo $e['id']; ?>" 
                                                        data-nome="<?php echo htmlspecialchars($e['nome']); ?>" 
                                                        data-matricula="<?php echo htmlspecialchars($e['matricula']); ?>" 
                                                        data-turma="<?php echo htmlspecialchars($e['turma_nome']); ?>" 
                                                        data-nascimento="<?php echo date('d/m/Y', strtotime($e['data_nascimento'])); ?>" 
                                                        data-responsavel="<?php echo htmlspecialchars($e['responsavel']); ?>" 
                                                        data-telefone="<?php echo htmlspecialchars($e['telefone_responsavel']); ?>" 
                                                        data-status="<?php echo htmlspecialchars($e['status']); ?>" 
                                                        data-cadastro="<?php echo date('d/m/Y', strtotime($e['data_cadastro'])); ?>" 
                                                        data-foto="<?php echo !empty($e['foto']) ? 'uploads/estudantes/' . htmlspecialchars($e['foto']) : 'https://via.placeholder.com/150?text=Foto'; ?>" 
                                                        title="Visualizar Detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Aba de Cadastro -->
                    <?php if ($aba_ativa === 'cadastrar'): ?>
                    <div class="tab-pane fade show active" id="cadastrar" role="tabpanel" aria-labelledby="cadastrar-tab">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 text-primary">Cadastrar Novo Estudante</h5>
                            </div>
                            <div class="card-body">
                                <form action="estudante.php" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
                                    <input type="hidden" name="acao" value="cadastrar">
                                    
                                    <!-- Card de Dados Pessoais -->
                                    <div class="col-12">
                                        <div class="card mb-3 border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h5 class="mb-0"><i class="fas fa-user me-2"></i> Dados Pessoais do Estudante</h5>
                                            </div>
                                            <div class="card-body row g-3">
                                                <!-- Nome e Data de Nascimento -->
                                                <div class="col-md-8">
                                                    <label for="nome" class="form-label">Nome Completo*</label>
                                                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome completo" required>
                                                    <div class="invalid-feedback">Por favor, informe o nome completo.</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="data_nascimento" class="form-label">Data de Nascimento*</label>
                                                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                                                    <div class="invalid-feedback">Por favor, informe a data de nascimento.</div>
                                                </div>
                                                
                                                <!-- CPF do Aluno -->
                                                <div class="col-md-4">
                                                    <label for="cpf_aluno" class="form-label">CPF do Aluno*</label>
                                                    <input type="text" class="form-control" id="cpf_aluno" name="cpf_aluno" placeholder="000.000.000-00" required>
                                                    <div class="invalid-feedback">Por favor, informe o CPF do aluno.</div>
                                                </div>
                                                
                                                <!-- Sexo e Gênero Personalizado -->
                                                <div class="col-md-4">
                                                    <label for="sexo" class="form-label">Sexo*</label>
                                                    <select class="form-select" id="sexo" name="sexo" required>
                                                        <option value="" selected disabled>Selecione...</option>
                                                        <option value="Menino">Menino</option>
                                                        <option value="Menina">Menina</option>
                                                        <option value="Personalizado">Personalizado</option>
                                                    </select>
                                                    <div class="invalid-feedback">Por favor, selecione uma opção.</div>
                                                </div>
                                                <div class="col-md-4" id="genero_personalizado_container" style="display: none;">
                                                    <label for="genero_personalizado" class="form-label">Gênero Personalizado*</label>
                                                    <input type="text" class="form-control" id="genero_personalizado" name="genero_personalizado" placeholder="Especifique o gênero">
                                                    <div class="invalid-feedback">Por favor, especifique o gênero.</div>
                                                </div>
                                                
                                                <!-- Turma -->
                                                <div class="col-md-6">
                                                    <label for="turma_id" class="form-label">Turma*</label>
                                                    <select class="form-select" id="turma_id" name="turma_id" required>
                                                        <option value="" selected disabled>Selecione a turma...</option>
                                                        <?php foreach ($turmas as $turma): ?>
                                                        <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="invalid-feedback">Por favor, selecione uma turma.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card de Matrícula e Inscrição -->
                                    <div class="col-md-6">
                                        <div class="card mb-3 border-info">
                                            <div class="card-header bg-info text-white">
                                                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i> Matrícula e Inscrição</h5>
                                            </div>
                                            <div class="card-body row g-3">
                                                <!-- Matrícula -->
                                                <div class="col-12">
                                                    <label for="matricula" class="form-label">Número de Matrícula*</label>
                                                    <div class="input-group has-validation">
                                                        <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Número de matrícula" required>
                                                        <button class="btn btn-outline-primary" type="button" id="gerarMatricula" data-bs-toggle="tooltip" title="Gerar matrícula automaticamente">
                                                            <i class="fas fa-sync-alt"></i> Gerar
                                                        </button>
                                                        <div class="invalid-feedback">Por favor, informe o número de matrícula.</div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Inscrição -->
                                                <div class="col-12">
                                                    <label for="inscricao" class="form-label">Número de Inscrição*</label>
                                                    <div class="input-group has-validation">
                                                        <input type="text" class="form-control" id="inscricao" name="inscricao" placeholder="Número de inscrição" required>
                                                        <button class="btn btn-outline-primary" type="button" id="gerarInscricao" data-bs-toggle="tooltip" title="Gerar inscrição automaticamente">
                                                            <i class="fas fa-sync-alt"></i> Gerar
                                                        </button>
                                                        <div class="invalid-feedback">Por favor, informe o número de inscrição.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card de Foto -->
                                    <div class="col-md-6">
                                        <div class="card mb-3 border-success">
                                            <div class="card-header bg-success text-white">
                                                <h5 class="mb-0"><i class="fas fa-camera me-2"></i> Foto do Estudante</h5>
                                            </div>
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <img src="https://via.placeholder.com/200?text=Foto" class="foto-preview img-thumbnail rounded-circle mb-3" alt="Pré-visualização da foto" style="width: 200px; height: 200px; object-fit: cover;">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="foto" class="form-label d-block text-start">Selecione uma foto*</label>
                                                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                                                    <div class="invalid-feedback">Por favor, selecione uma foto.</div>
                                                    <div class="form-text text-start">Formatos aceitos: JPG, PNG ou GIF. Tamanho máximo: 2MB.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card de Dados do Responsável -->
                                    <div class="col-12">
                                        <div class="card mb-3 border-warning">
                                            <div class="card-header bg-warning text-dark">
                                                <h5 class="mb-0"><i class="fas fa-users me-2"></i> Dados do Responsável</h5>
                                            </div>
                                            <div class="card-body row g-3">
                                                <!-- Nome do Responsável -->
                                                <div class="col-md-6">
                                                    <label for="responsavel" class="form-label">Nome do Responsável*</label>
                                                    <input type="text" class="form-control" id="responsavel" name="responsavel" placeholder="Nome completo do responsável" required>
                                                    <div class="invalid-feedback">Por favor, informe o nome do responsável.</div>
                                                </div>
                                                
                                                <!-- Documento do Responsável -->
                                                <div class="col-md-6">
                                                    <label for="cpf_responsavel" class="form-label">Documento do Responsável (CPF/RG)*</label>
                                                    <input type="text" class="form-control" id="cpf_responsavel" name="cpf_responsavel" placeholder="000.000.000-00 ou 00.000.000-0" required>
                                                    <div class="invalid-feedback">Por favor, informe o documento do responsável.</div>
                                                </div>
                                                
                                                <!-- Telefone do Responsável -->
                                                <div class="col-md-6">
                                                    <label for="telefone_responsavel" class="form-label">Telefone do Responsável*</label>
                                                    <input type="text" class="form-control" id="telefone_responsavel" name="telefone_responsavel" placeholder="(00) 00000-0000" required>
                                                    <div class="invalid-feedback">Por favor, informe o telefone do responsável.</div>
                                                </div>
                                                
                                                <!-- E-mail do Responsável -->
                                                <div class="col-md-6">
                                                    <label for="email_responsavel" class="form-label">E-mail do Responsável*</label>
                                                    <input type="email" class="form-control" id="email_responsavel" name="email_responsavel" placeholder="email@exemplo.com" required>
                                                    <div class="invalid-feedback">Por favor, informe um e-mail válido.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <hr>
                                        <div class="d-flex justify-content-end">
                                            <button type="reset" class="btn btn-secondary me-2">Limpar</button>
                                            <button type="submit" class="btn btn-primary">Cadastrar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Aba de Edição -->
                    <?php if ($aba_ativa === 'editar' && $estudante): ?>
                    <div class="tab-pane fade show active" id="editar" role="tabpanel" aria-labelledby="editar-tab">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 text-primary">Editar Estudante</h5>
                            </div>
                            <div class="card-body">
                                <form action="estudante.php" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
                                    <input type="hidden" name="acao" value="editar">
                                    <input type="hidden" name="id" value="<?php echo $estudante['id']; ?>">
                                    
                                    <!-- Card de Dados Pessoais -->
                                    <div class="col-12">
                                        <div class="card mb-3 border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h5 class="mb-0"><i class="fas fa-user me-2"></i> Dados Pessoais do Estudante</h5>
                                            </div>
                                            <div class="card-body row g-3">
                                                <!-- Nome e Data de Nascimento -->
                                                <div class="col-md-8">
                                                    <label for="nome" class="form-label">Nome Completo*</label>
                                                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome completo" value="<?php echo htmlspecialchars($estudante['nome']); ?>" required>
                                                    <div class="invalid-feedback">Por favor, informe o nome completo.</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="data_nascimento" class="form-label">Data de Nascimento*</label>
                                                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?php echo $estudante['data_nascimento']; ?>" required>
                                                    <div class="invalid-feedback">Por favor, informe a data de nascimento.</div>
                                                </div>
                                                
                                                <!-- CPF do Aluno -->
                                                <div class="col-md-4">
                                                    <label for="cpf_aluno" class="form-label">CPF do Aluno*</label>
                                                    <input type="text" class="form-control" id="cpf_aluno" name="cpf_aluno" placeholder="000.000.000-00" value="<?php echo htmlspecialchars(isset($estudante['cpf_aluno']) ? $estudante['cpf_aluno'] : ''); ?>" required>
                                                    <div class="invalid-feedback">Por favor, informe o CPF do aluno.</div>
                                                </div>
                                                
                                                <!-- Sexo e Gênero Personalizado -->
                                                <div class="col-md-4">
                                                    <label for="sexo" class="form-label">Sexo*</label>
                                                    <select class="form-select" id="sexo" name="sexo" required>
                                                        <option value="" disabled>Selecione...</option>
                                                        <option value="Menino" <?php echo (isset($estudante['sexo']) && $estudante['sexo'] == 'Menino') ? 'selected' : ''; ?>>Menino</option>
                                                        <option value="Menina" <?php echo (isset($estudante['sexo']) && $estudante['sexo'] == 'Menina') ? 'selected' : ''; ?>>Menina</option>
                                                        <option value="Personalizado" <?php echo (isset($estudante['sexo']) && $estudante['sexo'] == 'Personalizado') ? 'selected' : ''; ?>>Personalizado</option>
                                                    </select>
                                                    <div class="invalid-feedback">Por favor, selecione uma opção.</div>
                                                </div>
                                                <div class="col-md-4" id="genero_personalizado_container" style="display: <?php echo (isset($estudante['sexo']) && $estudante['sexo'] == 'Personalizado') ? 'block' : 'none'; ?>">
                                                    <label for="genero_personalizado" class="form-label">Gênero Personalizado*</label>
                                                    <input type="text" class="form-control" id="genero_personalizado" name="genero_personalizado" placeholder="Especifique o gênero" value="<?php echo htmlspecialchars(isset($estudante['genero_personalizado']) ? $estudante['genero_personalizado'] : ''); ?>">
                                                    <div class="invalid-feedback">Por favor, especifique o gênero.</div>
                                                </div>
                                                
                                                <!-- Turma -->
                                                <div class="col-md-6">
                                                    <label for="turma_id" class="form-label">Turma*</label>
                                                    <select class="form-select" id="turma_id" name="turma_id" required>
                                                        <option value="" disabled>Selecione a turma...</option>
                                                        <?php foreach ($turmas as $turma): ?>
                                                        <option value="<?php echo $turma['id']; ?>" <?php echo $turma['id'] == $estudante['turma_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($turma['nome']); ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="invalid-feedback">Por favor, selecione uma turma.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card de Matrícula e Inscrição -->
                                    <div class="col-md-6">
                                        <div class="card mb-3 border-info">
                                            <div class="card-header bg-info text-white">
                                                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i> Matrícula e Inscrição</h5>
                                            </div>
                                            <div class="card-body row g-3">
                                                <!-- Matrícula -->
                                                <div class="col-12">
                                                    <label for="matricula" class="form-label">Número de Matrícula*</label>
                                                    <div class="input-group has-validation">
                                                        <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Número de matrícula" value="<?php echo htmlspecialchars($estudante['matricula']); ?>" required>
                                                        <button class="btn btn-outline-primary" type="button" id="gerarMatricula" data-bs-toggle="tooltip" title="Gerar matrícula automaticamente">
                                                            <i class="fas fa-sync-alt"></i> Gerar
                                                        </button>
                                                        <div class="invalid-feedback">Por favor, informe o número de matrícula.</div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Inscrição -->
                                                <div class="col-12">
                                                    <label for="inscricao" class="form-label">Número de Inscrição*</label>
                                                    <div class="input-group has-validation">
                                                        <input type="text" class="form-control" id="inscricao" name="inscricao" placeholder="Número de inscrição" value="<?php echo htmlspecialchars(isset($estudante['inscricao']) ? $estudante['inscricao'] : ''); ?>" required>
                                                        <button class="btn btn-outline-primary" type="button" id="gerarInscricao" data-bs-toggle="tooltip" title="Gerar inscrição automaticamente">
                                                            <i class="fas fa-sync-alt"></i> Gerar
                                                        </button>
                                                        <div class="invalid-feedback">Por favor, informe o número de inscrição.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card de Foto -->
                                    <div class="col-md-6">
                                        <div class="card mb-3 border-success">
                                            <div class="card-header bg-success text-white">
                                                <h5 class="mb-0"><i class="fas fa-camera me-2"></i> Foto do Estudante</h5>
                                            </div>
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <?php if (!empty($estudante['foto'])): ?>
                                                    <img src="uploads/estudantes/<?php echo htmlspecialchars($estudante['foto']); ?>" 
                                                         class="foto-preview img-thumbnail rounded-circle mb-3" 
                                                         alt="Foto atual do estudante" 
                                                         style="width: 200px; height: 200px; object-fit: cover;">
                                                    <?php else: ?>
                                                    <img src="https://via.placeholder.com/200?text=Foto" 
                                                         class="foto-preview img-thumbnail rounded-circle mb-3" 
                                                         alt="Pré-visualização da foto" 
                                                         style="width: 200px; height: 200px; object-fit: cover;">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="foto" class="form-label d-block text-start">Alterar foto</label>
                                                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                                    <div class="form-text text-start">Deixe em branco para manter a foto atual. Formatos aceitos: JPG, PNG ou GIF. Tamanho máximo: 2MB.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card de Dados do Responsável -->
                                    <div class="col-12">
                                        <div class="card mb-3 border-warning">
                                            <div class="card-header bg-warning text-dark">
                                                <h5 class="mb-0"><i class="fas fa-users me-2"></i> Dados do Responsável</h5>
                                            </div>
                                            <div class="card-body row g-3">
                                                <!-- Nome do Responsável -->
                                                <div class="col-md-6">
                                                    <label for="responsavel" class="form-label">Nome do Responsável*</label>
                                                    <input type="text" class="form-control" id="responsavel" name="responsavel" placeholder="Nome completo do responsável" value="<?php echo htmlspecialchars($estudante['responsavel']); ?>" required>
                                                    <div class="invalid-feedback">Por favor, informe o nome do responsável.</div>
                                                </div>
                                                
                                                <!-- Documento do Responsável -->
                                                <div class="col-md-6">
                                                    <label for="cpf_responsavel" class="form-label">Documento do Responsável (CPF/RG)*</label>
                                                    <input type="text" class="form-control" id="cpf_responsavel" name="cpf_responsavel" placeholder="000.000.000-00 ou 00.000.000-0" value="<?php echo htmlspecialchars(isset($estudante['cpf_responsavel']) ? $estudante['cpf_responsavel'] : ''); ?>" required>
                                                    <div class="invalid-feedback">Por favor, informe o documento do responsável.</div>
                                                </div>
                                                
                                                <!-- Telefone do Responsável -->
                                                <div class="col-md-6">
                                                    <label for="telefone_responsavel" class="form-label">Telefone do Responsável*</label>
                                                    <input type="text" class="form-control" id="telefone_responsavel" name="telefone_responsavel" placeholder="(00) 00000-0000" value="<?php echo htmlspecialchars($estudante['telefone_responsavel']); ?>" required>
                                                    <div class="invalid-feedback">Por favor, informe o telefone do responsável.</div>
                                                </div>
                                                
                                                <!-- E-mail do Responsável -->
                                                <div class="col-md-6">
                                                    <label for="email_responsavel" class="form-label">E-mail do Responsável*</label>
                                                    <input type="email" class="form-control" id="email_responsavel" name="email_responsavel" placeholder="email@exemplo.com" value="<?php echo htmlspecialchars(isset($estudante['email_responsavel']) ? $estudante['email_responsavel'] : ''); ?>" required>
                                                    <div class="invalid-feedback">Por favor, informe um e-mail válido.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <hr>
                                        <div class="d-flex justify-content-end">
                                            <a href="?aba=listar" class="btn btn-secondary me-2">Cancelar</a>
                                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Modal de Exclusão -->
                <div class="modal fade" id="excluirModal" tabindex="-1" aria-labelledby="excluirModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="excluirModalLabel">Confirmar Exclusão</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                <p>Tem certeza que deseja excluir o estudante <strong id="nomeEstudante"></strong>?</p>
                                <p class="text-danger">Esta ação não pode ser desfeita.</p>
                            </div>
                            <div class="modal-footer">
                                <form action="estudante.php" method="POST">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" id="idEstudante">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal de Detalhes do Estudante -->
                <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title" id="detalhesModalLabel">Detalhes do Estudante</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 text-center mb-3">
                                        <img id="detalheFoto" src="https://via.placeholder.com/150?text=Foto" class="img-fluid rounded-circle border" alt="Foto do Estudante" style="width: 150px; height: 150px; object-fit: cover;">
                                    </div>
                                    <div class="col-md-8">
                                        <h4 id="detalheNome" class="mb-3 text-primary"></h4>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <p><strong>Matrícula:</strong> <span id="detalheMatricula"></span></p>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <p><strong>Turma:</strong> <span id="detalheTurma"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-2">
                                        <p><strong>Data de Nascimento:</strong> <span id="detalheNascimento"></span></p>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <p><strong>Status:</strong> <span id="detalheStatus" class="badge"></span></p>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <p><strong>Responsável:</strong> <span id="detalheResponsavel"></span></p>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <p><strong>Telefone:</strong> <span id="detalheTelefone"></span></p>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <p><strong>Data de Cadastro:</strong> <span id="detalheCadastro"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="#" id="btnEditarDetalhes" class="btn btn-primary"><i class="fas fa-edit me-1"></i> Editar</a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script src="js/estudante.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar se a tabela já foi inicializada pelo DataTables
        if ($.fn.DataTable && document.getElementById('tabelaEstudantes')) {
            // Verificar se a tabela já foi inicializada
            if (!$.fn.DataTable.isDataTable('#tabelaEstudantes')) {
                // Somente inicializa se ainda não tiver sido inicializada
                $('#tabelaEstudantes').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                    },
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>t<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i><"d-flex"p>>',
                    initComplete: function() {
                        // Adicionar classe para estilização personalizada
                        $('.dataTables_filter input').addClass('form-control form-control-sm ms-2');
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        
                        // Adicionar animação de fade-in na tabela
                        $('.dataTables_wrapper').css('opacity', '0');
                        $('.dataTables_wrapper').animate({opacity: 1}, 500);
                    }
                });
            } else {
                // Se já estiver inicializada, apenas aplicar as animações
                $('.dataTables_filter input').addClass('form-control form-control-sm ms-2');
                $('.dataTables_length select').addClass('form-select form-select-sm');
                
                // Adicionar animação de fade-in na tabela já inicializada
                $('.dataTables_wrapper').css('opacity', '0');
                $('.dataTables_wrapper').animate({opacity: 1}, 500);
            }
        }
        
        // Inicializar tooltips com animação
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                animation: true,
                delay: {show: 100, hide: 100}
            });
        });
        
        // Configurar o modal de exclusão com animações
        var excluirModal = document.getElementById('excluirModal')
        if (excluirModal) {
            excluirModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget
                var id = button.getAttribute('data-id')
                var nome = button.getAttribute('data-nome')
                
                document.getElementById('idEstudante').value = id
                document.getElementById('nomeEstudante').textContent = nome
                
                // Adicionar animação de shake ao aviso de exclusão
                setTimeout(function() {
                    document.querySelector('.text-danger').classList.add('animate__animated', 'animate__headShake');
                }, 300);
            });
            
            // Adicionar animação de fade ao fechar o modal
            excluirModal.addEventListener('hide.bs.modal', function() {
                var modalDialog = this.querySelector('.modal-dialog');
                modalDialog.classList.add('animate__animated', 'animate__fadeOutDown');
                
                setTimeout(function() {
                    modalDialog.classList.remove('animate__animated', 'animate__fadeOutDown');
                }, 500);
            });
        }
        
        // Configurar o botão de gerar matrícula com animação
        var btnGerarMatricula = document.getElementById('gerarMatricula')
        if (btnGerarMatricula) {
            btnGerarMatricula.addEventListener('click', function() {
                var turmaId = document.getElementById('turma_id').value
                if (!turmaId) {
                    Swal.fire({
                        title: 'Atenção!',
                        text: 'Selecione uma turma primeiro para gerar a matrícula.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    })
                    return
                }
                
                // Gerar matrícula com base na turma e data atual
                var ano = new Date().getFullYear()
                var cursoId = turmaId.toString().padStart(2, '0')
                var random = Math.floor(Math.random() * 1000).toString().padStart(3, '0')
                var matricula = ano.toString() + cursoId + random
                
                // Animar o campo de matrícula ao preencher
                var matriculaInput = document.getElementById('matricula');
                matriculaInput.value = matricula;
                matriculaInput.classList.add('bg-light');
                setTimeout(function() {
                    matriculaInput.classList.remove('bg-light');
                }, 500);
            })
        }
        
        // Preview da foto ao selecionar arquivo com animação
        var inputFoto = document.getElementById('foto')
        if (inputFoto) {
            inputFoto.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader()
                    reader.onload = function(e) {
                        var fotoPreview = document.querySelector('.foto-preview')
                        if (fotoPreview) {
                            // Adicionar animação de fade ao trocar a imagem
                            fotoPreview.style.opacity = '0';
                            fotoPreview.src = e.target.result;
                            $(fotoPreview).animate({opacity: 1}, 300);
                        }
                    }
                    reader.readAsDataURL(this.files[0])
                }
            })
        }
        
        // Configurar o sidebar toggle para desktop e mobile com animações aprimoradas
        var sidebarToggle = document.getElementById('sidebarToggle')
        var wrapper = document.getElementById('wrapper')
        
        if (sidebarToggle) {
            // Verificar estado salvo no localStorage
            if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
                document.body.classList.add('sb-sidenav-toggled')
            }
            
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault()
                document.body.classList.toggle('sb-sidenav-toggled')
                
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
                
                // Salvar estado no localStorage
                localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'))
            })
        }
        
        // Adicionar animações aprimoradas aos itens do menu
        var menuItems = document.querySelectorAll('.list-group-item')
        menuItems.forEach(function(item, index) {
            // Adicionar delay na animação inicial para efeito cascata
            setTimeout(function() {
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, index * 50);
            
            item.addEventListener('mouseenter', function() {
                // Adicionar classe para efeito de hover
                this.classList.add('menu-hover');
                
                // Animar o ícone
                var icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = 'scale(1.2)';
                }
            });
            
            item.addEventListener('mouseleave', function() {
                // Remover classe ao sair
                this.classList.remove('menu-hover');
                
                // Restaurar o ícone
                var icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = 'scale(1)';
                }
            });
        });
        
        // Estilizar os itens do menu inicialmente (para animação de entrada)
        menuItems.forEach(function(item) {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            item.style.transition = 'all 0.3s ease';
        });
        
        // Fechar sidebar automaticamente em telas pequenas após clicar em um item
        if (window.innerWidth < 992) {
            menuItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    document.body.classList.add('sb-sidenav-toggled');
                })
            })
        }
        
        // Animação para os dropdowns
        var dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(function(dropdown) {
            dropdown.addEventListener('show.bs.dropdown', function() {
                var menu = this.querySelector('.dropdown-menu');
                menu.classList.add('animate__animated', 'animate__fadeIn');
                menu.style.animationDuration = '0.3s';
            });
            
            dropdown.addEventListener('hide.bs.dropdown', function() {
                var menu = this.querySelector('.dropdown-menu');
                menu.classList.add('animate__animated', 'animate__fadeOut');
                menu.style.animationDuration = '0.2s';
            });
        });
        
        // Animação para notificações
        var notificationItems = document.querySelectorAll('.notification-list .dropdown-item');
        notificationItems.forEach(function(item, index) {
            item.style.opacity = '0';
            item.style.transform = 'translateY(10px)';
            item.style.transition = 'all 0.3s ease';
            
            // Adicionar delay para efeito cascata
            setTimeout(function() {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 100 + (index * 50));
        });
    });
    </script>
    
    <!-- Adicionar Animate.css para animações adicionais -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</body>
</html>
