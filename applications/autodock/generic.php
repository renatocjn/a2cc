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
		
		for ($i=0; $i<sizeof($params['dpfFiles']['name']); $i++) {
			$auxfile_path = $params['dpfFiles']['tmp_name'][$i];
			$auxname = pathinfo($params['dpfFiles']['name'][$i]);
			$connection->send_file($auxfile_path, $outdir.$auxname['basename']);
		}
		
		for ($i=0; $i<sizeof($params['otherpdbqts']['name']); $i++) {
			$auxfile_path = $params['otherpdbqts']['tmp_name'][$i];
			$auxname = pathinfo($params['otherpdbqts']['name'][$i]);
			$connection->send_file($auxfile_path, $outdir.$auxname['basename']);
		}
		
		for ($i=0; $i<sizeof($params['others']['name']); $i++) {
			$auxfile_path = $params['others']['tmp_name'][$i];
			$auxname = pathinfo($params['others']['name'][$i]);
			$connection->send_file($auxfile_path, $outdir.$auxname['basename']);
		}		
		
		$r = array();
		$r['params_description'] = "";
		$r['cmd'] = array();
		$r['cmd'][0] = "autogrid4 -p ".$gpfFile['basename']." -l ".$gpfFile['filename'].".gpg";
		$r['cmd'][1] = array(); 
		foreach ($params['dpfFiles']['name'] as $ligand) {
			$ligand = pathinfo($ligand);
			$outdockRunCmd = "autodock4 -p ".$ligand['basename']." -l ".$ligand['filename'];
			
			for($i=0; $i<$autodock_runs; $i++) {
				$r['cmd'][1][] = $outdockRunCmd.$i.".pgf";
			}
		}		
		$r['cmd_dir'] = $outdir;
		
		return $r;
	}
?>