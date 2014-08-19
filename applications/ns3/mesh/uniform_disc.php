<?php
	require 'app_assets/app_utils.php';
	
	function run_app($params, $outdir, $connection) {
		$param_str = "";
		foreach($params as $name => $value) 
			$param_str .= '--'.$name."=".$value." ";
	
		$r = array();
		$r['params_description'] = $param_str;
		$r['cmd'] = "./waf --cwd=$outdir --run 'uniform_disc $param_str'";
		$r['cmd_dir'] = 'ns-allinone-3.19/ns-3.19/';
		
		return $r;
	}
?>
