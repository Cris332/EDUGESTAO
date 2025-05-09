// Função para aplicar máscara de CPF
function maskCPF(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/^(\d{3})(\d)/, '$1.$2');
    value = value.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
    value = value.replace(/\.(\d{3})(\d)/, '.$1-$2');
    input.value = value;
}

// Função para aplicar máscara de telefone
function maskPhone(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 10) {
        value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
    } else if (value.length > 6) {
        value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
    } else if (value.length > 2) {
        value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
    } else if (value.length > 0) {
        value = value.replace(/^(\d{0,2})/, '($1');
    }
    input.value = value;
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables apenas se ainda não estiver inicializado
    if (document.getElementById('tabelaEstudantes') && $.fn.DataTable) {
        // Verificar se a tabela já foi inicializada
        if (!$.fn.DataTable.isDataTable('#tabelaEstudantes')) {
            $('#tabelaEstudantes').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json',
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    search: "Pesquisar:",
                    info: "Mostrando _START_ até _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 até 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros no total)",
                    paginate: {
                        first: "Primeiro",
                        last: "Último",
                        next: "Próximo",
                        previous: "Anterior"
                    }
                },
                responsive: true,
                // Remover a segunda barra de pesquisa
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
            console.log('DataTable inicializado pelo estudante.js');
        } else {
            console.log('DataTable já inicializado, pulando inicialização em estudante.js');
        }
    }
    
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Configurar o modal de exclusão
    var excluirModal = document.getElementById('excluirModal')
    if (excluirModal) {
        excluirModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var id = button.getAttribute('data-id')
            var nome = button.getAttribute('data-nome')
            
            document.getElementById('idEstudante').value = id
            document.getElementById('nomeEstudante').textContent = nome
        })
    }
    
    // Configurar o modal de detalhes
    var detalhesModal = document.getElementById('detalhesModal')
    if (detalhesModal) {
        detalhesModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget
            var id = button.getAttribute('data-id')
            var nome = button.getAttribute('data-nome')
            var matricula = button.getAttribute('data-matricula')
            var turma = button.getAttribute('data-turma')
            var nascimento = button.getAttribute('data-nascimento')
            var responsavel = button.getAttribute('data-responsavel')
            var telefone = button.getAttribute('data-telefone')
            var status = button.getAttribute('data-status')
            var cadastro = button.getAttribute('data-cadastro')
            var foto = button.getAttribute('data-foto')
            
            // Preencher os dados no modal
            document.getElementById('detalheNome').textContent = nome
            document.getElementById('detalheMatricula').textContent = matricula
            document.getElementById('detalheTurma').textContent = turma
            document.getElementById('detalheNascimento').textContent = nascimento
            document.getElementById('detalheResponsavel').textContent = responsavel
            document.getElementById('detalheTelefone').textContent = telefone
            document.getElementById('detalheCadastro').textContent = cadastro
            document.getElementById('detalheFoto').src = foto
            
            // Configurar o status com a cor apropriada
            var statusElement = document.getElementById('detalheStatus')
            statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1)
            
            if (status === 'ativo') {
                statusElement.classList.add('bg-success')
                statusElement.classList.remove('bg-danger', 'bg-warning')
            } else if (status === 'inativo') {
                statusElement.classList.add('bg-danger')
                statusElement.classList.remove('bg-success', 'bg-warning')
            } else {
                statusElement.classList.add('bg-warning')
                statusElement.classList.remove('bg-success', 'bg-danger')
            }
            
            // Configurar o botão de editar
            document.getElementById('btnEditarDetalhes').href = '?aba=editar&id=' + id
        })
    }
    
    // Configurar o botão de gerar matrícula
    var btnGerarMatricula = document.getElementById('gerarMatricula')
    if (btnGerarMatricula) {
        btnGerarMatricula.addEventListener('click', function() {
            var turmaId = document.getElementById('turma_id').value
            if (!turmaId) {
                Swal.fire({
                    title: 'Atenção!',
                    text: 'Selecione uma turma primeiro para gerar a matrícula.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                })
                return
            }
            
            // Gerar matrícula com base na turma e data atual
            var ano = new Date().getFullYear()
            var cursoId = turmaId.toString().padStart(2, '0')
            var random = Math.floor(Math.random() * 1000).toString().padStart(3, '0')
            var matricula = ano.toString() + cursoId + random
            
            document.getElementById('matricula').value = matricula
        })
    }
    
    // Configurar o botão de gerar inscrição
    var btnGerarInscricao = document.getElementById('gerarInscricao')
    if (btnGerarInscricao) {
        btnGerarInscricao.addEventListener('click', function() {
            // Gerar inscrição com base na data atual e um número aleatório
            var data = new Date()
            var ano = data.getFullYear()
            var mes = (data.getMonth() + 1).toString().padStart(2, '0')
            var random = Math.floor(Math.random() * 10000).toString().padStart(4, '0')
            var inscricao = 'INS-' + ano + mes + '-' + random
            
            document.getElementById('inscricao').value = inscricao
        })
    }
    
    // Configurar o campo de sexo para mostrar/esconder o campo de gênero personalizado
    var selectSexo = document.getElementById('sexo')
    var divGeneroPersonalizado = document.getElementById('genero_personalizado_container')
    var inputGeneroPersonalizado = document.getElementById('genero_personalizado')
    
    if (selectSexo && divGeneroPersonalizado && inputGeneroPersonalizado) {
        selectSexo.addEventListener('change', function() {
            if (this.value === 'Personalizado') {
                divGeneroPersonalizado.style.display = 'block'
                inputGeneroPersonalizado.setAttribute('required', 'required')
            } else {
                divGeneroPersonalizado.style.display = 'none'
                inputGeneroPersonalizado.removeAttribute('required')
                inputGeneroPersonalizado.value = ''
            }
        })
    }
    
    // Preview da foto ao selecionar arquivo
    var inputFoto = document.getElementById('foto')
    if (inputFoto) {
        inputFoto.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader()
                reader.onload = function(e) {
                    var fotoPreview = document.querySelector('.foto-preview')
                    if (fotoPreview) {
                        fotoPreview.src = e.target.result
                    }
                }
                reader.readAsDataURL(this.files[0])
            }
        })
    }
    
    // Aplicar máscaras para CPF e telefone
    var inputCPF = document.getElementById('cpf_responsavel')
    if (inputCPF) {
        inputCPF.addEventListener('input', function() {
            maskCPF(this)
        })
    }
    
    var inputTelefone = document.getElementById('telefone_responsavel')
    if (inputTelefone) {
        inputTelefone.addEventListener('input', function() {
            maskPhone(this)
        })
    }
    
    // Validar formulário
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            // Verificar se o gênero personalizado está preenchido quando necessário
            var selectSexo = form.querySelector('#sexo')
            var inputGenero = form.querySelector('#genero_personalizado')
            
            if (selectSexo && selectSexo.value === 'Personalizado' && inputGenero) {
                if (!inputGenero.value.trim()) {
                    inputGenero.setCustomValidity('Por favor, especifique o gênero.')
                } else {
                    inputGenero.setCustomValidity('')
                }
            }
            
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                
                // Mostrar mensagem de erro
                Swal.fire({
                    title: 'Atenção!',
                    text: 'Por favor, preencha todos os campos obrigatórios corretamente.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                })
            }
            
            form.classList.add('was-validated')
        }, false)
    })
    
    // Configurar o sidebar toggle para mobile
    var sidebarToggle = document.getElementById('sidebarToggle')
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sb-sidenav-toggled')
        })
    }
});
