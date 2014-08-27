<html lang="en">
<head>
<?php
	chdir('..');
    session_start();
    $_SESSION['lang'] = 'en';
    $titulo_pag = "Access to the system";
	include 'head.php';
	
	$do_sleep=true;
	include("./defaults.php");
	include_once("seguranca3.php");
	protegePagina(true);
	
	if ($ssh_con->logged()) {
		http_redirect('cenapad.php');
	}

?>
</head>
<body>
	<div class="container_16">
		
		<?php
			include 'banner.php';
		?>
		
		<div class="conteudo grid_14">		
		
		<?php
			if (isset($_SESSION['notice']) ) {
				print '<div class="notice"> '.$_SESSION['notice'].' </div>';
				unset($_SESSION['notice']);
			}
		?>
			<form id="form1" name="form1" class="validate formLogin" method="post" onsubmit="return sem_branco(['campo_login', 'campo_senha'])" action="../valida3.php">
				<table>
					<tr>
						<td>User Name</td>
						<td><input type="text" class="" id="usuario" name="usuario" /></td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type="password" class="" id="senha" name="senha" /></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" value="Login" class="btn btnPrimary " name="enviar" id="enviar" />
							<input type="reset" value="Cancel" class="btn btnSecundary" />
						</td>
					</tr>
				</table>
			</form>
		</div>
		<?php include 'navbar.php'; ?>
		
		<?php include 'rodape.php';?>

	</div>

	

</body>

</html>
