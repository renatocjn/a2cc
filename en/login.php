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
			<form id="form1" name="form1" class="validate formLogin" method="post" onsubmit="return sem_branco(['campo_login', 'campo_senha'])" action="../valida3.php">
				<table>
					<h4> Access to the application portals </h4>
					<tr>
						<?php
							if (isset($_SESSION['notice']) ) {
								print '<div class="notice"> '.$_SESSION['notice'].' </div>';
								unset($_SESSION['notice']);
							}
						?>
					</tr>
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
			<p> The <abbr title="Centro Nacional de Processamento de Alto Desempenho, National Center of High Performance Processing">CENAPAD</abbr> of the <abbr title="UFC">Federal University of Ceará</abbr>, subjected to the <abbr title="Pró-reitoria de planejamento">Planning directory</abbr>, has the objective of providing High Performance Processing (HPP) Services for the demand of universities, research institutes and other public and private institutions. </p>
			<p> CENAPAD is part of the <abbr title="Sistema Nacional de Processamento de Alto Desempenho, National System of High performance processing">SINAPAD</abbr>, a network of geographically distributed HPP providers with the objective of scientific and technological improvements for the country. </p>
			<p> These portals have the objective of providing easy access to the <a href="http://www.cenapad.ufc.br/quem-somos/recursos"> computational resources available at CENAPAD - UFC. </a> Regular access to these resource require special training to directly access them. </p>
			<p> To connect and run applications on the cluster, users need training on Linux shell and SLURM commands. To use a cloud, users need to request virtual machines and learn Linux shell. With this portal, any user can use the resources through this simple web interface  where the portals decide which is the better architecture to run each application and executes the required commands to run the desired application. </p>
			<p> Further Information and requests for credentials must be requested via e-mail: <a href="mailto:suporte@cenapad.ufc.br"> suporte@cenapad.ufc.br </a> </p>  
		</div>
		<?php include 'navbar.php'; ?>
		
		<?php include 'rodape.php';?>

	</div>

	

</body>

</html>
