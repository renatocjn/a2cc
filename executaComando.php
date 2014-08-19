<?php
	include("./defaults.php");
	include_once ("./seguranca3.php"); // Inclui o arquivo com o sistema de segurança
	include_once ("./http_response_code.php");
	include_once ("./decision_module.php");
	include_once 'infra_handler.php';
	
	protegePagina(); // Chama a função que protege a página
	$nome_user=$_SESSION['usuarioNome'];//variavel que contem o login do usuario logado
	$data_atual = date('Y-m-d'); //Data do sistema

	if( isset($_POST['application']) ) {
		$app_script = 'applications/'.$_POST['application'].'.php';
		if (!file_exists($app_script)) {
			print 'Aplicação não localizada, talvez ainda não esteja implementada';
			http_response_code(500);
			die;
		}
		$app = $_POST['application'];

		unset($_POST['application']);
		unset($_POST['cpu_count']);
		$params = array_merge($_POST, $_FILES);
		try {
			submit_job($app, $params);
		} catch (Exception $e) {
			print $e->getMessage()."\n";
			http_response_code(500);
		}
	}
	
	else if(isset($_GET["excluir"])) {
		$description = $_GET["excluir"];
		$job = infra_controller::job_from_description($description);
		$job->dispose();
		infra_controller::dispose_vm($description);
	}
	
	else if (isset($_GET["rmAll"]))	{
		$allocated_infra = infra_controller::get_allocated_infrastructure($nome_user);
		foreach( $allocated_infra as $infra )	{
			if (!$infra->is_ready()) continue;
			$jobs = $infra->get_jobs();
			foreach ($jobs as $job) {
				$job->dispose();
			}
			$infra->dispose();
		}
	}
	
	else if (isset($_GET["down"])) {
		$description = $_GET["down"];
		$job = infra_controller::job_from_description($description);
		$job->download_all_files();
	}
	
	else {
		print "Erro Desconhecido\nGET:\n";
		print_r($_GET);
		print "POST:\n";
		print_r($_POST);
		print "FILES:\n";
		print_r($_FILES);
		http_response_code(500);
	}
?>
