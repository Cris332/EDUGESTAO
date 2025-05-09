<?php
// Inclui o arquivo de conexão
require_once 'conexao.php';

// Função para obter as atividades recentes
function getAtividadesRecentes($conexao, $limite = 3) {
    $sql = "SELECT 
                a.id,
                a.tipo,
                a.descricao,
                a.data_registro,
                a.hora_registro,
                a.origem,
                u.nome as usuario
            FROM atividades a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            ORDER BY a.data_registro DESC, a.hora_registro DESC
            LIMIT $limite";
    
    $resultado = $conexao->query($sql);
    $atividades = array();
    
    if ($resultado->num_rows > 0) {
        while ($atividade = $resultado->fetch_assoc()) {
            $atividades[] = $atividade;
        }
    }
    
    return $atividades;
}

// Obter as atividades recentes
$atividadesRecentes = getAtividadesRecentes($conexao);
?>
