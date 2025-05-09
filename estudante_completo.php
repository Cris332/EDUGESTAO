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
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
        }
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #4e73df;
            color: #4e73df;
            font-weight: 600;
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
                            <a class="nav-link dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://via.placeholder.com/40" class="rounded-circle" width="32" height="32" alt="Usuário">
                                <span class="d-none d-lg-inline-block ms-1">Administrador</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Perfil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configurações</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Sair</a></li>
                            </ul>
                        </div>
                        <div class="ms-3">
                            <a href="#" class="position-relative text-dark">
                                <i class="fas fa-bell fa-lg"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </a>
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
                                                    <img src="<?php echo !empty($e['foto']) ? 'uploads/estudantes/' . $e['foto'] : 'https://via.placeholder.com/40?text=Foto'; ?>" 
                                                         class="rounded-circle" 
                                                         width="40" 
                                                         height="40" 
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
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#excluirModal" data-id="<?php echo $e['id']; ?>" data-nome="<?php echo htmlspecialchars($e['nome']); ?>" data-bs-toggle="tooltip" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <a href="#" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Visualizar Detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
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
                                <form action="estudante.php" method="POST" enctype="multipart/form-data" class="row g-3">
                                    <input type="hidden" name="acao" value="cadastrar">
                                    
                                    <!-- Dados básicos -->
                                    <div class="col-md-6">
                                        <label for="nome" class="form-label">Nome Completo*</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="data_nascimento" class="form-label">Data de Nascimento*</label>
                                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="turma_id" class="form-label">Turma*</label>
                                        <select class="form-select" id="turma_id" name="turma_id" required>
                                            <option value="">Selecione...</option>
                                            <?php foreach ($turmas as $turma): ?>
                                            <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- Dados de matrícula -->
                                    <div class="col-md-4">
                                        <label for="matricula" class="form-label">Número de Matrícula*</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="matricula" name="matricula" required>
                                            <button class="btn btn-outline-secondary" type="button" id="gerarMatricula">
                                                <i class="fas fa-sync-alt"></i> Gerar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="inscricao" class="form-label">Número de Inscrição</label>
                                        <input type="text" class="form-control" id="inscricao" name="inscricao">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="foto" class="form-label">Foto do Estudante</label>
                                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                        <div class="mt-2 text-center">
                                            <img src="https://via.placeholder.com/150?text=Foto" class="foto-preview" alt="Preview da foto">
                                        </div>
                                    </div>
                                    
                                    <!-- Dados do responsável -->
                                    <div class="col-md-6">
                                        <label for="responsavel" class="form-label">Nome do Responsável*</label>
                                        <input type="text" class="form-control" id="responsavel" name="responsavel" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefone_responsavel" class="form-label">Telefone do Responsável*</label>
                                        <input type="text" class="form-control" id="telefone_responsavel" name="telefone_responsavel" required>
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
                                <form action="estudante.php" method="POST" enctype="multipart/form-data" class="row g-3">
                                    <input type="hidden" name="acao" value="editar">
                                    <input type="hidden" name="id" value="<?php echo $estudante['id']; ?>">
                                    
                                    <!-- Dados básicos -->
                                    <div class="col-md-6">
                                        <label for="nome" class="form-label">Nome Completo*</label>
                                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($estudante['nome']); ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="data_nascimento" class="form-label">Data de Nascimento*</label>
                                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?php echo $estudante['data_nascimento']; ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="turma_id" class="form-label">Turma*</label>
                                        <select class="form-select" id="turma_id" name="turma_id" required>
                                            <option value="">Selecione...</option>
                                            <?php foreach ($turmas as $turma): ?>
                                            <option value="<?php echo $turma['id']; ?>" <?php echo $turma['id'] == $estudante['turma_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($turma['nome']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- Dados de matrícula -->
                                    <div class="col-md-4">
                                        <label for="matricula" class="form-label">Número de Matrícula*</label>
                                        <input type="text" class="form-control" id="matricula" name="matricula" value="<?php echo htmlspecialchars($estudante['matricula']); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="inscricao" class="form-label">Número de Inscrição</label>
                                        <input type="text" class="form-control" id="inscricao" name="inscricao" value="<?php echo htmlspecialchars($estudante['inscricao']); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="foto" class="form-label">Alterar Foto</label>
                                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                        <div class="form-text">Deixe em branco para manter a foto atual</div>
                                    </div>
                                    
                                    <!-- Dados do responsável -->
                                    <div class="col-md-6">
                                        <label for="responsavel" class="form-label">Nome do Responsável*</label>
                                        <input type="text" class="form-control" id="responsavel" name="responsavel" value="<?php echo htmlspecialchars($estudante['responsavel']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefone_responsavel" class="form-label">Telefone do Responsável*</label>
                                        <input type="text" class="form-control" id="telefone_responsavel" name="telefone_responsavel" value="<?php echo htmlspecialchars($estudante['telefone_responsavel']); ?>" required>
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
</body>
</html>
