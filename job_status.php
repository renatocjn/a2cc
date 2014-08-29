<?php

	error_reporting(E_ALL ^ E_STRICT);
	
	include_once ("seguranca3.php"); // Inclui o arquivo com o sistema de segurança
	protegePagina(); // Chama a função que protege a página
	
	include_once 'infra_handler.php';
	$nome_user=$_SESSION['usuarioNome'];//variavel que contem o login do usuario logado
	
	$allocated_infra = infra_controller::get_allocated_infrastructure($nome_user);
	
	//header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
	echo '<jobs>';
	
	foreach( $allocated_infra as $infra )	{
		if (!$infra->is_ready()) continue;
		$jobs = $infra->get_jobs();
		foreach ($jobs as $job) {
			echo '\t<job>';			
			
			$dataInicio = $job->get_start_date();
			echo "\t\t<startDate> $dataInicio </startDate>";
			
			$runn = ($job->is_running()) ? 'true' : 'false';
			echo "\t\t<isrunning> ". $runn . " </isrunning>";			

			$app = $job->get_app();
			echo "\t\t<application> $app </application>";
			
			$description = infra_controller::job_to_description($infra, $job);
			echo "\t\t<description> $description </description>";			
			
			$params = $job->get_params();
			echo "\t\t<params> $params </params>";						
			
			echo '\t</job>';
		}
	}		
	echo '</jobs>';
?>

