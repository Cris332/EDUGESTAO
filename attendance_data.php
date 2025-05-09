<?php
// Inclui o arquivo de conexão
require_once 'conexao.php';

// Função para obter dados de presença dos últimos 5 meses
function getDadosPresenca($conexao) {
    $meses = array();
    $presenca = array();
    $ausencia = array();
    $justificado = array();
    
    // Obtém os últimos 5 meses
    for ($i = 4; $i >= 0; $i--) {
        $mes = date('m', strtotime("-$i month"));
        $ano = date('Y', strtotime("-$i month"));
        $nomeMes = date('M', strtotime("-$i month"));
        
        $meses[] = ucfirst($nomeMes);
        
        // Consulta para presença
        $sqlPresenca = "SELECT COUNT(*) as total FROM frequencia 
                        WHERE MONTH(data) = '$mes' 
                        AND YEAR(data) = '$ano' 
                        AND status = 'presente'";
        
        // Consulta para ausência
        $sqlAusencia = "SELECT COUNT(*) as total FROM frequencia 
                       WHERE MONTH(data) = '$mes' 
                       AND YEAR(data) = '$ano' 
                       AND status = 'ausente'";
        
        // Consulta para justificado
        $sqlJustificado = "SELECT COUNT(*) as total FROM frequencia 
                          WHERE MONTH(data) = '$mes' 
                          AND YEAR(data) = '$ano' 
                          AND status = 'justificado'";
        
        $resultPresenca = $conexao->query($sqlPresenca);
        $resultAusencia = $conexao->query($sqlAusencia);
        $resultJustificado = $conexao->query($sqlJustificado);
        
        $dadosPresenca = $resultPresenca->fetch_assoc();
        $dadosAusencia = $resultAusencia->fetch_assoc();
        $dadosJustificado = $resultJustificado->fetch_assoc();
        
        $total = $dadosPresenca['total'] + $dadosAusencia['total'] + $dadosJustificado['total'];
        
        if ($total > 0) {
            $presenca[] = round(($dadosPresenca['total'] / $total) * 100);
            $ausencia[] = round(($dadosAusencia['total'] / $total) * 100);
            $justificado[] = round(($dadosJustificado['total'] / $total) * 100);
        } else {
            $presenca[] = 0;
            $ausencia[] = 0;
            $justificado[] = 0;
        }
    }
    
    return [
        'meses' => $meses,
        'presenca' => $presenca,
        'ausencia' => $ausencia,
        'justificado' => $justificado
    ];
}

// Obter os dados de presença
$dadosPresenca = getDadosPresenca($conexao);
?>
