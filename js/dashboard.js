// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebar = document.getElementById('sidebar-wrapper');
    
    // Toggle sidebar for mobile
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sidebar-visible');
        });
    }
    
    // Close sidebar when clicking on overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            document.body.classList.remove('sidebar-visible');
        });
    }
    
    // Close sidebar when clicking on a menu item on mobile
    const menuItems = document.querySelectorAll('#sidebar-wrapper .list-group-item');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                document.body.classList.remove('sidebar-visible');
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            document.body.classList.remove('sidebar-visible');
        }
        
        // Resize charts if they exist
        if (window.attendanceChart) {
            window.attendanceChart.resize();
        }
        if (window.turmasChart) {
            window.turmasChart.resize();
        }
        if (window.disciplinasChart) {
            window.disciplinasChart.resize();
        }
    });

    // Initialize charts
    initAttendanceChart();
    initTurmasChart();
    initDisciplinasChart();

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Show welcome notification (only on desktop)
    if (window.innerWidth >= 768) {
        setTimeout(function() {
            Swal.fire({
                title: 'Bem-vindo ao EduGestão!',
                text: 'Você tem 3 notificações não lidas',
                icon: 'info',
                confirmButtonText: 'Entendi',
                confirmButtonColor: '#4e73df'
            });
        }, 1000);
    }
});

// Initialize the attendance chart with data from PHP
function initAttendanceChart() {
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        // Get screen width for responsive adjustments
        const screenWidth = window.innerWidth;
        
        // Adjust chart options based on screen size
        const legendPosition = screenWidth < 768 ? 'top' : 'bottom';
        const legendPadding = screenWidth < 768 ? 10 : 20;
        const barPercentage = screenWidth < 576 ? 0.8 : 0.6;
        
        // Get data from PHP (these variables will be set in the HTML)
        const meses = window.chartData.meses;
        const presenca = window.chartData.presenca;
        const ausencia = window.chartData.ausencia;
        const justificado = window.chartData.justificado;
        
        // Create chart instance and store it globally for resize access
        window.attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [
                    {
                        label: 'Presença',
                        data: presenca,
                        backgroundColor: '#4e73df',
                        borderWidth: 0,
                        borderRadius: screenWidth < 576 ? 2 : 4,
                        barPercentage: barPercentage,
                    },
                    {
                        label: 'Ausência',
                        data: ausencia,
                        backgroundColor: '#e74a3b',
                        borderWidth: 0,
                        borderRadius: screenWidth < 576 ? 2 : 4,
                        barPercentage: barPercentage,
                    },
                    {
                        label: 'Justificado',
                        data: justificado,
                        backgroundColor: '#f6c23e',
                        borderWidth: 0,
                        borderRadius: screenWidth < 576 ? 2 : 4,
                        barPercentage: barPercentage,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: legendPosition,
                        align: screenWidth < 576 ? 'start' : 'center',
                        labels: {
                            usePointStyle: true,
                            padding: legendPadding,
                            boxWidth: screenWidth < 576 ? 8 : 10,
                            font: {
                                size: screenWidth < 576 ? 10 : 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        titleFont: {
                            size: screenWidth < 576 ? 12 : 14
                        },
                        bodyFont: {
                            size: screenWidth < 576 ? 11 : 13
                        },
                        padding: screenWidth < 576 ? 6 : 10
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: screenWidth < 576 ? 10 : 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: screenWidth < 576 ? 50 : 25,
                            font: {
                                size: screenWidth < 576 ? 10 : 12
                            },
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            borderDash: [2],
                            drawBorder: false
                        }
                    }
                }
            }
        });
    }
}

// Initialize the turmas chart with data from PHP
function initTurmasChart() {
    const ctx = document.getElementById('turmasChart');
    if (ctx) {
        // Get screen width for responsive adjustments
        const screenWidth = window.innerWidth;
        
        // Get data from PHP
        const turmas = window.chartData.turmas;
        const medias = window.chartData.medias;
        
        // Definir cores para destacar as melhores turmas
        // Ouro, prata e bronze para as três melhores turmas
        const backgroundColors = [
            'rgba(255, 215, 0, 0.8)',  // Ouro para a 1ª colocada
            'rgba(192, 192, 192, 0.8)', // Prata para a 2ª colocada
            'rgba(205, 127, 50, 0.8)'   // Bronze para a 3ª colocada
        ];
        
        // Verificar se temos dados para exibir
        if (turmas.length === 0 || medias.length === 0) {
            // Se não houver dados, exibir mensagem no console
            console.error('Não há dados de desempenho por turma para exibir');
            // Exibir uma mensagem no gráfico
            const noDataText = document.createElement('div');
            noDataText.style.position = 'absolute';
            noDataText.style.top = '50%';
            noDataText.style.left = '50%';
            noDataText.style.transform = 'translate(-50%, -50%)';
            noDataText.style.textAlign = 'center';
            noDataText.innerHTML = '<p>Não há dados suficientes</p>';
            ctx.parentNode.appendChild(noDataText);
            return;
        }
        
        // Garantir que temos exatamente 3 cores mesmo se tivermos menos de 3 turmas
        while (backgroundColors.length < 3) {
            backgroundColors.push('rgba(200, 200, 200, 0.5)');
        }
        
        // Create chart instance
        window.turmasChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: turmas,
                datasets: [{
                    label: 'Média de Notas',
                    data: medias,
                    backgroundColor: backgroundColors,
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Sempre usar barras horizontais para melhor visualização
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Média: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: screenWidth < 576 ? 10 : 12
                            }
                        }
                    },
                    y: {
                        grid: {
                            borderDash: [2],
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: screenWidth < 576 ? 10 : 12
                            }
                        }
                    }
                }
            }
        });
    }
}

// Initialize the disciplinas chart with data from PHP
function initDisciplinasChart() {
    const ctx = document.getElementById('disciplinasChart');
    if (ctx) {
        // Get screen width for responsive adjustments
        const screenWidth = window.innerWidth;
        
        // Get data from PHP
        const disciplinas = window.chartData.disciplinas;
        const mediasDisciplinas = window.chartData.mediasDisciplinas;
        
        // Create chart instance
        window.disciplinasChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: disciplinas,
                datasets: [{
                    data: mediasDisciplinas,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: screenWidth < 576 ? 10 : 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Média: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });
    }
}
