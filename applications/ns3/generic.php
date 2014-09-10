<?php
	require 'app_assets/app_utils.php';
	
	function run_app($params, $outdir, $connection) {	
		
		$file_path = $_FILES['scriptFile']['tmp_name'];
		$file_name = $_FILES['scriptFile']['name'];
		$name = pathinfo($file_name);
		
		$connection->send_file($file_path, 'ns-allinone-3.20/ns-3.20/scratch/'.$name['basename']);
	
		$r = array();
		$r['params_description'] = $params['param_str'];
		$r['cmd'] = "./waf --cwd=$outdir --run '{$name['filename']} ".$params['param_str']."'";
		$r['cmd_dir'] = 'ns-allinone-3.20/ns-3.20/';
		
		return $r;
	}
?>