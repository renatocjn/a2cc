<?php 
	include_once 'infra_handler.php';
	include_once 'http_response_code.php';
		
	function submit_job($application, $params) {
		
		$user = $_SESSION['usuarioLogin'];
		/*if($user == 'rneto') {
			$h = opennebula_handler::allocate_new_handler();
		}else if($user == 'rneto2') {
			$h = cluster_handler::allocate_new_handler();
		} else {
			$h = rand(0,100) < 25 ? opennebula_handler::allocate_new_handler() : cluster_handler::allocate_new_handler(); 
		}*/
//		$h = opennebula_handler::allocate_new_handler();
		$h = cluster_handler::allocate_new_handler();
		if (!$h) {
			 throw new Exception("Não pode ser alocado novos recursos");
		}
		$r = $h->start_job($application, $params);
		if (!$r) {
			 throw new Exception("Job não pode ser iniciado");
		}
	}
	
?>