<?php 
	include_once 'infra_handler.php';
	include_once 'http_response_code.php';

	#these arrays a a set of regexps to be matched with the 
	#$application variable of the submit_job() function
	#the regexps must be compatible with the preg_match() php function
	$cluster_applications = array('namd', 'autodock');
	$cloud_applications = array('ns3', 'octave');
		
	function get_free_opennebula_vm() {
		$handlers = opennebula_handler::get_allocated_handlers();
		foreach ( $handlers as $handler ) { 
			$jobs = $handler->get_jobs();
			$free = true;
			foreach ($jobs as $job) {
				if( !$job->is_running() ) { 
					$free = false;
					break;
				}
			}
			if ($free) return $handler;
		}
	}

	function try_start_on_opennebula($application,$params) {
		$h = get_free_OpenNebula_vm();
		if (!$h) 
			$h = opennebula_handler::allocate_new_handler();
		if($h) {
			$h->start_job($application,$params);
			return true;
		} else {
			return false;
		}
	}
	
	function try_start_on_cluster($application,$params) {
		$h = cluster_handler::allocate_new_handler();
		$h->start_job($application,$params);
		return true;
	}
	
	function is_cloud_app($application) {
		global $cloud_applications;
		foreach ($cloud_applications as $regexp) {
			if(preg_match('/'.$regexp.'/i', $application)) 
				return true;
		}
		return false;
	}
	
	function is_cluster_app($application) {
		global $cluster_applications;
		foreach ($cluster_applications as $regexp) {
			if(preg_match('/'.$regexp.'/i', $application)) 
				return true;
		}
		return false;
	}

	function submit_job($application, $params) {		
		if(is_cloud_app($application)) {
			try_start_on_opennebula($application,$params);
			return true;
		}
		
		if(is_cluster_app($application)) { 
			try_start_on_cluster($application,$params);
			return true;
		}
		
		# $application did not match any regexp, start on any
		$cloud_start = try_start_on_opennebula($application,$params);
		if(!$cloud_start) {
			$cluster_start = try_start_on_cluster($application,$params);
			if(!$cluster_start) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
		
	}
	
?>