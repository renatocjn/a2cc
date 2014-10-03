<?php
	require 'app_assets/app_utils.php';
	
	function run_app($params, $outdir, $connection) {			
		
		$gpfFile = pathinfo($params['gpfFile']['name']);
		$connection->send_file($params['gpfFile']['tmp_name'], $outdir.$gpfFile['basename']);
		
		$mainpdbqtFile = pathinfo($params['mainpdbqtFile']['name']);
		$connection->send_file($params['mainpdbqtFile']['tmp_name'], $outdir.$mainpdbqtFile['basename']);
		
		$autodock_runs = $params['autodockRuns'];
		
		for ($i=0; $i<sizeof($params['datFiles']['name']); $i++) {
			$auxfile_path = $params['datFiles']['tmp_name'][$i];
			$auxname = pathinfo($params['datFiles']['name'][$i]);
			$connection->send_file($auxfile_path, $outdir.$auxname['basename']);
		}
		
		for ($i=0; $i<sizeof($params['pdfFiles']['name']); $i++) {
			$auxfile_path = $params['pdfFiles']['tmp_name'][$i];
			$auxname = pathinfo($params['pdfFiles']['name'][$i]);
			$connection->send_file($auxfile_path, $outdir.$auxname['basename']);
		}
		
		for ($i=0; $i<sizeof($params['otherpdbqts']['name']); $i++) {
			$auxfile_path = $params['otherpdbqts']['tmp_name'][$i];
			$auxname = pathinfo($params['otherpdbqts']['name'][$i]);
			$connection->send_file($auxfile_path, $outdir.$auxname['basename']);
		}
		
		$r = array();
		$r['params_description'] = "";
		$r['cmd'] = "./fullAutodock.v2.sh $outdir $autodock_runs";
		//$r['cmd_dir'] = $outdir;
		
		return $r;
	}
?>