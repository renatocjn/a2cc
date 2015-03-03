<html lang="pt-br">
<head>
<?php
	chdir('..');
    session_start();
    $_SESSION['lang'] = 'pt';
    $titulo_pag = "Acesso ao Sistema";
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
					<h4> Acesso aos portais de aplicação </h4>
					<tr>
						<?php
							if (isset($_SESSION['notice']) ) {
								print '<div class="notice"> '.$_SESSION['notice'].' </div>';
								unset($_SESSION['notice']);
							}
						?>
					</tr>
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
			<p> O <b>Centro Nacional de Processamento de Alto Desempenho (CENAPAD)</b> da Universidade Federal do Ceará (UFC), ligado à Pró-Reitoria de Planejamento, tem por objetivo prestar serviços de Processamento de Alto Desempenho (PAD) sob demanda às universidades, institutos de pesquisa e outras instituições publicas e privadas. </p>
			<p> Ele é integrante do consórcio SINAPAD (Sistema Nacional de Processamento de Alto Desempenho), uma rede de centros geograficamente espalhados pelo Brasil com o intuito de fornecer os recursos de PAD necessário para o desenvolvimento científico e tecnológico do país.  </p>
			<p> Estes portais tem como objetivo facilitar o acesso aos <a href="http://www.cenapad.ufc.br/quem-somos/recursos"> recursos disponíveis no CENAPAD - UFC </a> devido à necessidade de treinamento especifico para tratar diretamente com eles.</p>
			<p> Para tratar com o cluster o usuário precisa de treinamento com Linux shell e comandos SLURM. Para tratar com a cloud o	usuário precisa requisitar máquinas virtuais e aprender Linux shell. Com a utilização deste portal o usuário pode utilizar estes recursos através desta interface web simples onde o portal determina em qual infraestrutura ele executará a aplicação e executa os comandos necessários. </p>
			<p> Requisições de credenciais, dúvidas e mais informações podem ser enviadas via e-mail: <a href="mailto:suporte@cenapad.ufc.br"> suporte@cenapad.ufc.br </a>  </p>
		</div>
		
		<?php include 'navbar.php'; ?>
		<?php include 'rodape.php';?>

	</div>

	

</body>

</html>
