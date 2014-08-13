<?php
	require 'app_assets/app_utils.php';
	
	function run_app($params, $outdir, $connection) {
		/////////////////////////////  Temporario  ////////////////////////////////
		abort_execution('Ainda não implementado!');
		///////////////////////////////////////////////////////////////////////////
		
		$required_params = array('radius', 'size', 'interfaces');
		check_required_params($required_params, $params);
		
		$radius = $params['radius'];
		$size = $params['size'];
		$interfaces = $params['interface'];
		$xml = isset($params['xml']) ? 1 : 0;
		$pcap = isset($params['pcap']) ? 1 : 0;
	
		log_params("numero de nós: $size, raio do disco: $radius, interfaces: $interfaces, xml: $xml, pcap: $pcap", $outdir, $connection);
		exec_and_log_command("./waf --cwd=$outdir --run 'uniform_disc --number-of-nodes=$size --radius=$radius --xml=$xml --pcap=$pcap --interfaces=$interfaces'", $outdir, $connection);
	}
?>
