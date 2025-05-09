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
        
        // Resize chart if it exists
        if (window.attendanceChart) {
            window.attendanceChart.resize();
        }
    });

    // Initialize attendance chart with responsive options
    initAttendanceChart();

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


// Initialize the attendance chart
function initAttendanceChart() {
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        // Get screen width for responsive adjustments
        const screenWidth = window.innerWidth;
        
        // Adjust chart options based on screen size
        const legendPosition = screenWidth < 768 ? 'top' : 'bottom';
        const legendPadding = screenWidth < 768 ? 10 : 20;
        const barPercentage = screenWidth < 576 ? 0.8 : 0.6;
        
        // Create chart instance and store it globally for resize access
        window.attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai'],
                datasets: [
                    {
                        label: 'Presença',
                        data: [85, 82, 80, 87, 83],
                        backgroundColor: '#4e73df',
                        borderWidth: 0,
                        borderRadius: screenWidth < 576 ? 2 : 4,
                        barPercentage: barPercentage,
                    },
                    {
                        label: 'Ausência',
                        data: [10, 12, 15, 8, 12],
                        backgroundColor: '#e74a3b',
                        borderWidth: 0,
                        borderRadius: screenWidth < 576 ? 2 : 4,
                        barPercentage: barPercentage,
                    },
                    {
                        label: 'Justificado',
                        data: [5, 6, 5, 5, 5],
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

// Function to handle notification clicks
function handleNotification(id) {
    Swal.fire({
        title: 'Notificação',
        text: `Você clicou na notificação #${id}`,
        icon: 'info',
        confirmButtonText: 'Fechar',
        confirmButtonColor: '#4e73df'
    });
}

// Function to handle event details
function showEventDetails(eventId) {
    Swal.fire({
        title: 'Detalhes do Evento',
        html: `
            <div class="text-start">
                <p><strong>ID:</strong> ${eventId}</p>
                <p><strong>Data:</strong> 15/05/2025</p>
                <p><strong>Local:</strong> Sala 102</p>
                <p><strong>Descrição:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl eget ultricies tincidunt.</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Fechar',
        confirmButtonColor: '#4e73df'
    });
}

// Function to show confirmation dialogs
function confirmAction(action, callback) {
    Swal.fire({
        title: 'Tem certeza?',
        text: `Você está prestes a ${action}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#e74a3b',
        confirmButtonText: 'Sim',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}
