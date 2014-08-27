<?php
	include("seguranca3.php"); // Inclui o arquivo com o sistema de segurança
	protegePagina();
	
	$nome_user=$_SESSION['usuarioNome'];
	
	//Elimina os dados da sessão
	unset($_SESSION['usuarioNome']);
	unset($_SESSION['usuarioLogin']);
	unset($_SESSION['usuarioSenha']);
	 
	//Encerra a sessão
	$lang = $_SESSION['lang'];
	session_destroy();
	http_redirect($lang.'/login.php');
?>
