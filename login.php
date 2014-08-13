<html lang="pt-br">
<head>
<?php
    session_start();
    $titulo_pag = "Acesso ao Sistema";
	include 'head.php';
	
	$do_sleep=true;
	include("./defaults.php");
	include_once("seguranca3.php");
	protegePagina(true);
	
	if ($ssh_con->logged()) {
		$url = $_SERVER['HTTP_HOST'];            // Get the server
		$url .= rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // Get the current directory
		$url .= '/cenapad.php'; 
		header("Location: $url");
	}
	
	//if(!(preg_match('/Chrome/', $_SERVER['HTTP_USER_AGENT']))){
		//echo "<script type='text/javascript'>
		//alert('Para melhor visualização do sistema, favor utilizar o Google Chrome.');
		//</script>";
	//}

?>
</head>
<body>
	<div class="container_16">


		<?php
			include 'banner.php';
			include 'menu_principal.php';
		?>
		<div class="conteudo grid_14">
			<form id="form1" name="form1" class="validate formLogin" method="post" onsubmit="return sem_branco(['campo_login', 'campo_senha'])" action="valida3.php">
				<table>
					<tr>
						<td>Nome</td>
						<td><input type="text" class="" id="usuario" name="usuario" /></td>
					</tr>
					<tr>
						<td>Senha</td>
						<td><input type="password" class="" id="senha" name="senha" /></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" value="Entrar" class="btn btnPrimary " name="enviar" id="enviar" />
							<input type="reset" value="Cancelar" class="btn btnSecundary" />
						</td>
					</tr>
				</table>
			</form>
		</div>
		<?php include 'rodape.php';?>

	</div>

	

</body>

</html>
