<?php
	include("seguranca3.php"); // Inclui o arquivo com o sistema de segurança
	protegePagina();
	
	$nome_user=$_SESSION['usuarioNome'];
	
	//Elimina os dados da sessão
	unset($_SESSION['usuarioNome']);
	unset($_SESSION['usuarioLogin']);
	unset($_SESSION['usuarioSenha']);
	 
	//Encerra a sessão
	session_destroy();
	$url = $_SERVER['HTTP_HOST'];            // Get the server
	$url .= rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // Get the current directory
	$url .= '/login.php'; 
	header("Location: $url");
?>
