<?php
// Inclui o arquivo com o sistema de segurança
//include("seguranca.php"); Original
include("seguranca3.php");
include_once("connecta1.php");

protegePagina(true);

if ($ssh_con->logged()) {
	$url = $_SERVER['HTTP_HOST'];            // Get the server
	$url .= rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // Get the current directory
	$url .= "/"; 
	header("Location: $url");
}
 

// Verifica se um formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Salva duas variáveis com o que foi digitado no formulário
	// Detalhe: faz uma verificação com isset() pra saber se o campo foi preenchido
	$usuario = (isset($_POST['usuario'])) ? $_POST['usuario'] : '';
	$senha = (isset($_POST['senha'])) ? $_POST['senha'] : '';
	
	/*$myPass = base64_encode($senha);
	$myUser = $usuario;*/

	$_SESSION['usuarioLogin'] = $_SESSION['usuarioNome'] = $usuario;
	$_SESSION['usuarioSenha'] = $senha;
	

	
	
	$ssh_con->set_user($usuario);
	$ssh_con->set_passwd($senha);

	
	$ssh_con->set_host($_SG['host_ssh']);
	$ssh_con->set_port($_SG['port_ssh']);

	
	//utiliza a funcao da classe ssh_conecta
	if ($ssh_con->login()) {
		validaUsuario3($usuario, $senha);
		$url = $_SERVER['HTTP_HOST'];            // Get the server
		$url .= rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // Get the current directory
		$url .= "/cenapad.php";
		header("Location: $url");
	} else {
		expulsaVisitante1();
	}
}

?>
