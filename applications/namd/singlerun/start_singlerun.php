<?php
	require 'app_assets/app_utils.php';
	require 'app_assets/namd/default_namd_params.php';

	function run_app($params, $outdir, $connection) {
		$required_params = array('coordenatesFile', 'structureFile', 'inpFile');
		check_required_params($required_params, $params);

		$default_params = get_default_namd_params();
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
			fwrite($tmp_file, implode(' ',$p)."\n");
		}
		fclose($tmp_file);

		$connection->send_file($tmp_filename, $outdir.'namd.conf');
		$connection->send_file($params['coordenatesFile']['tmp_name'], $outdir.'namd.pdb');
		$connection->send_file($params['structureFile']['tmp_name'], $outdir.'namd.psf');
		$connection->send_file($params['inpFile']['tmp_name'], $outdir.'namd.inp');

		$r = array();
		$r['params_description'] = 'namd simulation';
		$r['cmd'] = "./namd/namd2 {$outdir}namd.conf";
		$r['cmd_dir'] = "";

		return $r;
	}
?>