<?php
include("../seguranca3.php"); // Inclui o arquivo com o sistema de segurança
protegePagina();
//initialize the session
session_start();
session_destroy();

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles

  //Estava dando erro aqui!
  session_unregister('nome');
  session_unregister('tipo');
  session_unregister('nivel');
  
  	//Elimina os dados da sessão
	unset($_SESSION['usuarioNome']);
	unset($_SESSION['usuarioLogin']);
	unset($_SESSION['usuarioSenha']);
	 
  
  //session_unregister('perfil_geral');*/
		

  $logoutGoTo = "../login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}


?>
