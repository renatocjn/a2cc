<?php
	include("./defaults.php");
	include_once ("./seguranca3.php"); // Inclui o arquivo com o sistema de segurança
	protegePagina(); // Chama a função que protege a página
	$nome_user=$_SESSION['usuarioNome'];//variavel que contem o login do usuario logado
	$data_atual = date('Y-m-d'); //Data do sistema
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php print_title(); ?>
		<link rel="stylesheet" href="./css/styles.css" type="text/css" media="all">
		<link rel="shortcut icon" href="./css/images/cenapad.png" type="image/gif" />
	</head>
	<body>
		<div id="header"></div>
			<?php include_once('navbar.php'); ?>
		<div id="midsection2">
			<?php 
				echo  "Bem vindo, ".$nome_user.".";
				echo "<br>";
				$retorno = explode("\n", $ssh_con->command("sreport cluster AccountUtilizationByUser user={$nome_user} start=2011-01-08 end={$data_atual}"));
				$words = preg_split("/\s+/", trim($retorno[6]));
				echo "Você já executou ".$words[3]." CPU minutos no <em>padufc</em>.";
				echo "<br>";				
			?>
		</div>
		<div id="footer">Contato: contato@cenapad.ufc.br | Suporte: suporte@cenapad.ufc.br</div>
	</body>
</html>
