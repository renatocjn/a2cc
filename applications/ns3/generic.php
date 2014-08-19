<?php
	require 'app_assets/app_utils.php';
	
	function run_app($params, $outdir, $connection) {	
		
		$file_path = $_FILES['scriptFile']['tmp_name'];
		$file_name = $_FILES['scriptFile']['name'];
		$name = pathinfo($file_name);
		$name = $name['filename'];
		
		$connection->send_file($file_path, 'ns-allinone-3.19/ns-3.19/scratch/'.$file_name);
		
		$r = array();
		$r['params_description'] = $params['param_str'];
		$r['cmd'] = "./waf --cwd=$outdir --run '$name ".$params['param_str']."'";
		$r['cmd_dir'] = 'ns-allinone-3.19/ns-3.19/';
		
		return $r;
	}
?>