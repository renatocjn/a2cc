<?php
	require 'app_assets/app_utils.php';
	
	function run_app($params, $outdir, $connection) {	
		
		$file_path = $params['scriptFile']['tmp_name'];
		$file_name = $params['scriptFile']['name'];
		$name = pathinfo($file_name);
		$connection->send_file($file_path, $outdir.$name['basename']);
		
		for ($i=0; $i<sizeof($params['aux_files']['name']); $i++) {
			$auxfile_path = $params['aux_files']['tmp_name'][$i];
			$auxname = pathinfo($params['aux_files']['name'][$i]);
			$connection->send_file($auxfile_path, $outdir.$auxname['basename']);
		}
		
		$r = array();
		$r['params_description'] = $params['param_str'];
		$r['cmd'] = "octave ".$name['basename']." ".$params['param_str'];
		$r['cmd_dir'] = $outdir;
		
		return $r;
	}
?>