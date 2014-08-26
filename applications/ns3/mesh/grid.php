<?php
	require 'app_assets/app_utils.php';
	
	function run_app($params, $outdir, $connection) {
		$graphs=false;
		if(isset($params['graphs'])) {
			$graphs = true;
			unset($params['graphs']);
		}
			
		$param_str = "";
		foreach($params as $name => $value)
			$param_str .= '--'.$name."=".$value." ";
		
		$r = array();

		if( $graphs ) {
			$r['params_description'] = $param_str;
			$r['cmd'] = "./main.sh grid $outdir $param_str";
			$r['cmd_dir'] = 'mesh_analisys_scripts/';
		} else {
			$r['params_description'] = $param_str;
			$r['cmd'] = "./waf --cwd=$outdir --run 'grid $param_str'";
			$r['cmd_dir'] = 'ns-allinone-3.??/ns-3.??/';
		}

		if($graphs)
			$r['params_description'] .= '--graphs=1';

		return $r;
	}
?>