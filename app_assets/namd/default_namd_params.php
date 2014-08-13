<?php
function get_default_namd_params($file = 'app_assets/namd/namd_default_params.conf') {
	$contents = file_get_contents($file);
	$lines = explode(PHP_EOL, $contents);
	$params = array();
	foreach ($lines as $l) {
		if (trim($l) == "") continue;
		$tmp = preg_split('/\s+/', trim($l));
		if ($tmp[0] == 'set') { //set commands with multiple values
			$aux = array_slice($tmp, 2); //
			$tmp = array_slice($tmp,0,2);
			$tmp[2] = implode(' ', $aux);
		} else { //commmon command with multiple values
			$aux = array_slice($tmp, 1);
			$tmp = array_slice($tmp,0,1);
			$tmp[1] = implode(' ', $aux);
		}
		$params[] = $tmp;
	}

	return $params;
}

function get_default_continue_namd_params() {
	return get_default_namd_params('app_assets/namd/namd_default_continue_params.conf');
}

function get_default_multiple_namd_params() {
	return get_default_namd_params('app_assets/namd/namd_default_multiple_params.conf');
}

function get_customizeable_namd_params() {
	$all_params = get_default_namd_params();
	$customizeable_keys = file_get_contents('app_assets/namd/namd_customizeable_params.txt');
	$customizeable_keys = explode("\n", $customizeable_keys);
	$r = array();
	
	foreach ($all_params as $p) {
		if ($p[0] == 'set') {
			if (in_array($p[1], $customizeable_keys)) $r[$p[1]] = $p[2];
		} else {
			if (in_array($p[0], $customizeable_keys)) $r[$p[0]] = $p[1];
		}
	}
	return $r;
}

?>
