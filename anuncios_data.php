<?php
// Inclui o arquivo de conexão
require_once 'conexao.php';

// Função para obter os anúncios
function getAnuncios($conexao, $limite = 3) {
    $sql = "SELECT 
                id,
                titulo, 
                conteudo, 
                data_publicacao
            FROM anuncios 
            ORDER BY data_publicacao DESC 
            LIMIT $limite";
    
    $resultado = $conexao->query($sql);
    $anuncios = array();
    
    if ($resultado->num_rows > 0) {
        while ($anuncio = $resultado->fetch_assoc()) {
            $anuncios[] = $anuncio;
        }
    }
    
    return $anuncios;
}

// Obter os anúncios
$anuncios = getAnuncios($conexao);
?>
