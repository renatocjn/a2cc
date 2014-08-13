<?php

	include_once 'infra_handler.php';
	include_once 'decision_module.php';
	
	session_start();
	$_SESSION['usuarioLogin'] = $SESSION['usuarioNome'] = 'rneto2';
	$_SESSION['usuarioSenha'] = 'senhapass';
	
	$app = 'ns3/mesh/grid';
	$params = array('step'=>'100', 'interface'=>'2');
//	$sizes = array(3=>3, 5=>10, 10=>10, 15=>10, 20=>10);
	$sizes = array(3=>3, 5=>10, 10=>10, 15=>10);
	$num_runs = 10;
	$EOL = "\n";

	foreach ($sizes as $size_x => $size_y) {
		$p = $params;
		$p['gradeX'] = $size_x;
		$p['gradeY'] = $size_y;
		
		print "$size_x x $size_y".$EOL;
		$runs_time = array();
		for ($i=0; $i<$num_runs; $i++) {
			$t1 = time();
			print 'run '.($i+1).' of '.$num_runs.$EOL;
			submit_job($app, $p);
			$tmp = time() - $t1;
			print 'totalRunTime '.$tmp.$EOL;
			$runs_time[] = $tmp; 
			
			$allocated_infra = infra_controller::get_allocated_infrastructure($_SESSION['usuarioLogin']);
			foreach( $allocated_infra as $infra )	{
				if (!$infra->is_ready()) continue;
				$jobs = $infra->get_jobs();
				foreach ($jobs as $job) $job->dispose();
				$infra->dispose();
			}
		}
	}
?>