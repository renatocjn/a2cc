<?php
	require 'app_assets/app_utils.php';
	require 'app_assets/namd/default_namd_params.php';

	function run_app($params, $outdir, $connection) {
		$required_params = array('coordenatesFile', 'structureFile', 'inpFile', 'divisions');
		check_required_params($required_params, $params);

		$default_params = get_default_multiple_namd_params();
		foreach ($default_params as $k => $p) {
			if($p[0] == 'set') {
				$p_name = $p[1];
				$pos = 2;
			} else {
				$p_name = $p[0];
				$pos = 1;
			}
			
			if(array_key_exists($p_name, $params))
				$default_params[$k][$pos] = $params[$p_name];
		}
		
		$tmp_filename = tempnam('', '');
		$tmp_file = fopen($tmp_filename, 'w');
		foreach ($default_params as $p) {
			if($p[0] == 'run') $p[1] = ceil(intval($p[1])/intval($params['divisions'])); 
			fwrite($tmp_file, implode(' ',$p)."\n");
		}
		fclose($tmp_file);

		$connection->send_file($tmp_filename, $outdir.'base-namd.conf');
		$connection->send_file($params['coordenatesFile']['tmp_name'], $outdir.'namd.pdb');
		$connection->send_file($params['structureFile']['tmp_name'], $outdir.'namd.psf');
		$connection->send_file($params['inpFile']['tmp_name'], $outdir.'namd.inp');
		$connection->send_file('app_assets/namd/multiple_namd_script.sh', $outdir.'namd.sh');
		$connection->command('chmod +x '.$outdir.'namd.sh');
		
		$r = array();
		$r['params_description'] = 'namd simulation';
		$r['cmd'] = "{$outdir}namd.sh ".$params['divisions'];
		$r['cmd_dir'] = $outdir;

		return $r;
	}
?>