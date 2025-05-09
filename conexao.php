<?php
// Configurações de conexão com o banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "gestao_academica";

// Estabelece a conexão com o banco de dados
$conexao = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica se houve erro na conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Define o charset para UTF-8
$conexao->set_charset("utf8");
?>
