<?php

	//error_reporting(E_ALL ^ E_STRICT);
	error_reporting(0);
	include_once("http_response_code.php");
	include_once ("seguranca3.php"); // Inclui o arquivo com o sistema de segurança
	protegePagina(); // Chama a função que protege a página
	
	include_once 'infra_handler.php';
	$nome_user=$_SESSION['usuarioNome'];//variavel que contem o login do usuario logado
	
	try {
		$allocated_infra = infra_controller::get_allocated_infrastructure($nome_user);
	} catch (Exception $e) {
		print $e->getMessage()."\n";
		http_response_code(500);
		die;
	}	
	
	//header("Content-type: text/xml");
	echo '<?xml version="1.0"?>';
	echo '<jobs>';
	$jobs = array();
	foreach( $allocated_infra as $infra ) {
		if (!$infra->is_ready()) continue;
		$tmp = $infra->get_jobs();
		foreach ($tmp as $job) {
			echo get_class($infra)." / ".get_class($job).PHP_EOL;
			$job->set_infra($infra);
		}
		$jobs = array_merge($jobs, $tmp);
	}
	
	function comp($job1, $job2) {
		$t1 = strtotime($job1->get_start_date());
		$t2 = strtotime($job2->get_start_date());
		return $t2 - $t1;
	}
	
	usort($jobs, "comp");
	
	foreach ($jobs as $job) {
		echo "<job>";			
		
		$dataInicio = $job->get_start_date();
		echo "<startDate>$dataInicio</startDate>";
		
		$runn = ($job->is_running()) ? 'true' : 'false';
		echo "<isrunning>". $runn ."</isrunning>";			

		$app = $job->get_app();
		echo "<application>$app</application>";
		
		$description = infra_controller::job_to_description($job->get_infra(), $job);
		echo "<description>$description</description>";			
		
		$params = $job->get_params();
		echo "<params>$params</params>";						
		
		echo "</job>";
	}		
	echo '</jobs>';
?>

