<?php
	include 'http_response_code.php';
	
	function check_required_params($required_params, $params) {
		foreach ($required_params as $rp) {
			if (!array_key_exists($rp, $params)) {
				abort_execution("Parametro $rp necessário");
			}
		}
	}
	
	function abort_execution($reason = null) {
		global $connection, $outdir;
		if ( isset($reason) ) print $reason;
		$connection->command("rm -R $outdir");
		http_response_code(500);
		die;
	}

?>
