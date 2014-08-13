<?php
	require 'app_assets/app_utils.php';
	
	function run_app($params, $outdir, $connection) {	
		$required_params = array('gradeX', 'gradeY', 'step', 'interface');
		check_required_params($required_params, $params);
		
		$gradeX = $params['gradeX'];
		$gradeY = $params['gradeY'];
		$step = $params['step'];
		$interfaces = $params['interface'];
		$xml = isset($params['xml']) ? 1 : 0;
		$pcap = isset($params['pcap']) ? 1 : 0;
		
		$r = array();
		$r['params_description'] = "x size: $gradeX, y size: $gradeY, step: $step, xml: $xml, pcap: $pcap, interfaces: $interfaces";
		$r['cmd'] = "./waf --cwd=$outdir --run 'mesh --x-size=$gradeX --y-size=$gradeY --step=$step --xml=$xml --pcap=$pcap --interfaces=$interfaces'";
		$r['cmd_dir'] = 'ns-allinone-3.19/ns-3.19/';
		
		return $r;
	}
?>