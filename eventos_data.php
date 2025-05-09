<?php
// Inclui o arquivo de conexão
require_once 'conexao.php';

// Função para obter os próximos eventos
function getProximosEventos($conexao, $limite = 3) {
    $sql = "SELECT 
                id,
                titulo, 
                data_evento, 
                hora_inicio, 
                hora_fim, 
                local, 
                tipo,
                status
            FROM eventos 
            WHERE data_evento >= CURDATE() 
            ORDER BY data_evento ASC 
            LIMIT $limite";
    
    $resultado = $conexao->query($sql);
    $eventos = array();
    
    if ($resultado->num_rows > 0) {
        while ($evento = $resultado->fetch_assoc()) {
            $eventos[] = $evento;
        }
    }
    
    return $eventos;
}

// Obter os próximos eventos
$proximosEventos = getProximosEventos($conexao);
?>
