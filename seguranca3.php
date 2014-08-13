<? 
/**
* Sistema de segurança com acesso restrito a nuvem
*
* Usado para restringir o acesso de certas páginas do seu site
*
* @author Thiago Belem <contato@thiagobelem.net>
* @link http://thiagobelem.net/
*
* @version 1.0
* @package SistemaSeguranca
*/

include_once('connecta1.php'); //LDAP

if (isset($do_sleep)) {
	usleep(500000);
}
 
//  Configurações do Script
// ==============================
$_SG['conectaServidor'] = false;    // Abre uma conexão com o servidor MySQL?
$_SG['abreSessao'] = true;         // Inicia a sessão com um session_start()?
 
$_SG['caseSensitive'] = false;     // Usar case-sensitive? Onde 'thiago' é diferente de 'THIAGO'
 
$_SG['validaSempre'] = false;       // Deseja validar o usuário e a senha a cada carregamento de página?
// Evita que, ao mudar os dados do usuário no banco de dado o mesmo contiue logado.

// Informação do servidor de ssh
$_SG['host_ssh'] = '200.19.191.252';
//$_SG['port_ssh'] = '33000';

//$_SG['host_ssh'] = '192.168.1.36';
$_SG['port_ssh'] = '22';


$ssh_con = new ssh_conecta();
// Informação do servidor de ssh, FIM
 
// ======================================
//   ~ Não edite a partir deste ponto ~
// ======================================

// Verifica se precisa fazer a conexão com o MySQL
if ($_SG['conectaServidor'] == true) {
	$_SG['link'] = mysql_connect($_SG['servidor'], $_SG['usuario'], $_SG['senha'])
		or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
	mysql_select_db($_SG['banco'], $_SG['link'])
		or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");
}
 
// Verifica se precisa iniciar a sessão
if ($_SG['abreSessao'] == true) {
	session_start();

	if (isset($_SESSION['to_login']) and $_SESSION['to_login']) {
		unset($_SESSION['to_login']);
		//session_unregister($_SESSION['to_login']); //session_unregister é deprecado!!
		$url = $_SERVER['HTTP_HOST'];            // Get the server
		$url .= rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // Get the current directory
		$url .= $_SG['paginaLogin']; 
		header("Location: ".$url);
	}
}

function validaUsuario2($usuario, $senha, $idUsuario) {
//	global $_SG;

	$_SESSION['usuarioID'] = $idUsuario;
	$_SESSION['usuarioNome'] = $usuario;
	// Verifica a opção se sempre validar o login
//	if ($_SG['validaSempre'] == true) {
		// Definimos dois valores na sessão com os dados do login
		$_SESSION['usuarioLogin'] = $usuario;
		$_SESSION['usuarioSenha'] = $senha;
//	}

//fim da funcao teste 
	return true;
}

function validaUsuario3($usuario, $senha, $path = '') {

	$_SESSION['usuarioID'] = 1;
	$_SESSION['usuarioNome'] = $usuario;
	$_SESSION['usuarioLogin'] = $usuario;
	$_SESSION['usuarioSenha'] = $senha;
	$_SESSION['usuarioPath'] = $path;

	return true;
}
 
/**
* Função que protege uma página
*/
function protegePagina($tela_login = false) {
	global $_SG;
	global $ssh_con;
	global $protegido;
 
	if (!isset($_SESSION['usuarioID']) OR !isset($_SESSION['usuarioNome'])) {
		// Não há usuário logado, manda pra página de login
		expulsaVisitante($tela_login);
	} else if (!isset($protegido)) {
		// Há usuário logado, verifica se precisa validar o login novamente
		$ssh_con->set_user($_SESSION['usuarioLogin']);
		$ssh_con->set_passwd($_SESSION['usuarioSenha']);

		$ssh_con->set_host($_SG['host_ssh']);
		$ssh_con->set_port($_SG['port_ssh']);

		$ssh_con->cd($_SESSION['usuarioPath']);

		$protegido = true;

		if (!$ssh_con->login()) {
			expulsaVisitante($tela_login);
		}
	}
}

include_once('connecta1.php');
//Marcus: 11092013 - Função para validar login e senha
function expulsaVisitante1() 
{

	global $_SG;

	// Remove as variáveis da sessão (caso elas existam)
	unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);
	echo '<script type="text/javascript">alert("LOGIN ou SENHA incorreto!");</script>';
	
	$url = $_SERVER['HTTP_HOST'];            // Get the server
	$url .= rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // Get the current directory
	$url .= $_SG['paginaLogin']; 
	header("Location: $url");
	//echo '<script type="text/javascript">window.location.href="http://localhost/a2cON/login.php"</script>';
	return true;
}

/*
* Função para expulsar um visitante
*/
function expulsaVisitante($tela_login = false) {
	global $_SG;
	global $ssh_con;

	if (!$tela_login) {
		$login = $_SESSION['usuarioLogin'];
		$senha = $_SESSION['usuarioSenha'];
		// Remove as variáveis da sessão (caso elas existam)
		unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);

		$url = $_SERVER['HTTP_HOST'];            // Get the server
		$url .= rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // Get the current directory
		$url .= $_SG['paginaLogin']; 
		header("Location: $url");
		//Manda para tela de login
		//$_SESSION['to_login'] = true;
		//echo "document.location.reload(true)";
	}
}
?>
