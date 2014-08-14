<?php
include_once 'connecta1.php';

interface infra_handler {
	//returns a handler for each VM and for the cluster
	static function get_allocated_handlers($user);

	//allocates a new handler, normally can be a VM or a connection to the cluster
	static function allocate_new_handler();

	//returns array of jobs that are under that infrastructure, indexed by the ids
	function get_jobs();

	//get allocation status for that handler
	function is_ready();	
	
	//Start a job using this infrastructure
	function start_job ($application, $params);
	
	public function get_id();
	
	function get_infra_type();
	
	function dispose();
}

class openstack_handler implements infra_handler {
	var $connection;
	var $VMID;
	var $status;	
	
	static function getCloudConnection() {
		$cloud_connection = new ssh_conecta();
		$cloud_connection->set_host('200.19.191.240');
		$cloud_connection->set_port(22);
		$cloud_connection->set_user('cenacloud00');
		$cloud_connection->set_passwd('senha.123');
		if ($cloud_connection->login()) {
			return $cloud_connection;
		} else { 
			throw new Exception("Can't connect to openstack cloud");
		}
	}
	
	function dispose() {
		$cloud_connection = opennebula_handler::getCloudConnection();
		$machineIP = $cloud_connection->command("source admin-openrc.sh && nova show {$this->VMID}|grep network|xargs|cut -d ' ' -f 6");
		$cloud_connection->command("source admin-openrc.sh && nova delete ".$this->VMID);
		$cloud_connection->command("source admin-openrc.sh && nova floating-ip-delete ".$machineIP);
	}

	function get_infra_type() {
		return 'openstack';
	}
	
	public function get_id() {
		return $this->VMID;
	}

	function get_jobs() {
		$jobs = $this->connection->command('ls -1 jobs');
		$jobs = preg_split('/\s+/', $jobs);
		$r = array();
		foreach ($jobs as $job) {
			$job = trim($job);
			if($job == "") continue;
			$r[$job] = new opennebula_job($this->connection, $job);
		}

		return $r;
	}

	function is_ready() {
		return $this->status;
	}

	function __construct($vm_id, $machineIP=null) {
		$this->VMID = $vm_id;
		$cloud_connection = openstack_handler::getCloudConnection();
		$user = $_SESSION['usuarioLogin'];
		
		$aux = preg_split("/\s+/", trim($cloud_connection->command("nova --os-tenant-name $user show {$vm_id}|grep -e network -e tenant")));
		$owner = $aux[9];
		 
		if ($owner != $user) {
			throw new Exception("Usuario não é dono da maquina!");
		}
		
		$machineIP = $aux[4];

		$this->connection = new ssh_conecta();
		$this->connection->set_host($machineIP);
		$this->connection->set_port(22);
		$this->connection->set_user('root');
		$this->connection->set_pub_key('/etc/openstack.key.pub');
		$this->connection->set_priv_key('/etc/openstack.key');
		if(!$this->connection->login('key')) {
			$this->status = false;
		} else {
			$this->status = true;
		}
	}

	static function allocate_new_handler() {
		$cloud_connection = openstack_handler::getCloudConnection();
		$vm_id = $cloud_connection->command("./a2c_scripts/vm_create.sh ".$_SESSION['usuarioLogin']);
		if( preg_match('/ERROR:.*/', $vm_id) ) {
			return false;
		}
		return new opennebula_handler($vm_id);
	}

	static function get_allocated_handlers($user) {
		$cloud_connection = opennebula_handler::getCloudConnection();
		$user = $_SESSION['usuarioLogin'];
		
		$vm_list = explode("\n",$cloud_connection->command("source admin-openrc.sh && nova --os-tenant-name $user list --minimal"));
		$handlers = array();
		foreach ($vm_list as $vm) {
			if(trim($vm) == "") continue; //skip blank lines
			$vm_description = preg_split('/\s+/', $vm);
			$VMID = $vm_description[1];
			if($VMID == "ID") continue; //skip header line
			$handlers[] = new opennebula_handler($VMID);
		}
		return $handlers;
	}
	
	function start_job($application, $params) {
//		$t = time(); //benchmarking
		while (!$this->connection->login('key')) {			
			sleep(15);
		}
//		print 'machineDeployTime '.(time() - $t)."\n"; //benchmarking
		include_once ("applications/".$application.".php");
		$existing_runs = explode("\n", $this->connection->command("ls -1 /root/jobs"));
		while (true) {
			$id = rand();
			if (!in_array($id, $existing_runs))
				break;
		}
		$outdir = "/root/jobs/".$id.'/';
		$this->connection->command("mkdir -p $outdir");
		$r = run_app($params, $outdir, $this->connection);
		$this->connection->command('echo '.$r['params_description'].' > '.$outdir.'.params.txt');
		$this->connection->command('echo '.$application.' > '.$outdir.'.app');
		if (isset($r['cmd_dir'])) $this->connection->cd($r['cmd_dir']);
		$this->connection->command('nohup '.$r['cmd']." &> $outdir/job.log& echo $! > $outdir/.pid");
//		$this->connection->command($r['cmd']." &> $outdir/.job.log"); //benchmarking
		
		return true;
	}
}

class opennebula_handler implements infra_handler {
	var $connection;
	var $VMID;
	var $status;	
	
	static function getCloudConnection() {
		$cloud_connection = new ssh_conecta();
		$cloud_connection->set_host('200.19.191.230');
		$cloud_connection->set_port(33000);
		$cloud_connection->set_user('controller');
		$cloud_connection->set_passwd('senha.123');
		if ($cloud_connection->login()) {
			return $cloud_connection;
		} else { 
			throw new Exception("Can't connect to openstack cloud");
		}
	}
	
	function dispose() {
		$cloud_connection = opennebula_handler::getCloudConnection();
		$machineIP = $cloud_connection->command("export ONE_AUTH=~/one_auth && onevm delete ".$this->VMID);
	}

	function get_infra_type() {
		return 'opennebula';
	}
	
	public function get_id() {
		return $this->VMID;
	}

	function get_jobs() {
		$jobs = $this->connection->command('ls -1 jobs');
		$jobs = preg_split('/\s+/', $jobs);
		$r = array();
		foreach ($jobs as $job) {
			$job = trim($job);
			if($job == "") continue;
			$r[$job] = new opennebula_job($this->connection, $job);
		}

		return $r;
	}

	function is_ready() {
		return $this->status;
	}

	function __construct($vm_id, $machineIP=null) {
		$this->VMID = $vm_id;
		$cloud_connection = opennebula_handler::getCloudConnection();
		
		$owner = trim($cloud_connection->command("export ONE_AUTH=~/one_auth && onevm show $vm_id|grep USER|xargs|cut -d ' ' -f 3"));
		if ($owner != $_SESSION['usuarioLogin']) {
			throw new Exception("Usuario não é dono da maquina!");
		}
		
		if ($machineIP === null) {	
			$machineIP = $cloud_connection->command("export ONE_AUTH=~/one_auth && onevm show ".$this->VMID."|grep private|xargs|cut -d ' ' -f 5");
			$machineIP = trim($machineIP);
		}

		$this->connection = new ssh_conecta();
		$this->connection->set_host($machineIP);
		$this->connection->set_port(22);
		$this->connection->set_user('root');
		$this->connection->set_pub_key('/etc/opennebula.key.pub');
		$this->connection->set_priv_key('/etc/opennebula.key');
		if(!$this->connection->login('key')) {
			$this->status = false;
		} else {
			$this->status = true;
		}
	}

	static function allocate_new_handler() {
		$cloud_connection = opennebula_handler::getCloudConnection();
		$description = $cloud_connection->command("export ONE_AUTH=~/one_auth && onevm create --disk 25 --memory 1024 --cpu 1 --nic oneadmin[private] --ssh ~/.ssh/id_rsa.pub --vnc --user {$_SESSION['usuarioLogin']} --password {$_SESSION['usuarioSenha']}");
		if( !preg_match('/^ID: [0-9]+$/', $description) ) {
			return false;
		}
		$description = preg_split('/\s+/', $description);
		$vmid = $description[1];
		return new opennebula_handler($vmid);
	}

	static function get_allocated_handlers($user) {
		$cloud_connection = opennebula_handler::getCloudConnection();
		$vm_list = explode("\n",$cloud_connection->command("export ONE_AUTH=~/one_auth && onevm list -l ID,USER --filter USER=$user --CSV"));
		$handlers = array();
		foreach ($vm_list as $vm) {
			if(trim($vm) == "") continue; //skip blank lines
			$vm_description = explode(',', $vm);
			$VMID = $vm_description[0];
			if($VMID == "ID") continue; //skip header line
			$tmp = $cloud_connection->command("export ONE_AUTH=~/one_auth && onevm show $VMID|grep private");
			$tmp = preg_split('/\s+/', trim($tmp));			
			$machineIP = $tmp[4];	
			$handlers[] = new opennebula_handler($VMID, $machineIP);
		}
		return $handlers;
	}
	
	function start_job($application, $params) {
//		$t = time();
		while (!$this->connection->login('key')) {			
			sleep(15);
		}
//		print 'machineDeployTime '.(time() - $t)."\n";
		include_once ("applications/".$application.".php");
		$existing_runs = explode("\n", $this->connection->command("ls -1 /root/jobs"));
		while (true) {
			$id = rand();
			if (!in_array($id, $existing_runs))
				break;
		}
		$outdir = "/root/jobs/".$id.'/';
		$this->connection->command("mkdir -p $outdir");
		$r = run_app($params, $outdir, $this->connection);
		$this->connection->command('echo '.$r['params_description'].' > '.$outdir.'.params.txt');
		$this->connection->command('echo '.$application.' > '.$outdir.'.app');
		if (isset($r['cmd_dir'])) $this->connection->cd($r['cmd_dir']);
		$this->connection->command('nohup '.$r['cmd']." &> $outdir/job.log& echo $! > $outdir/.pid");
//		$this->connection->command($r['cmd']." &> $outdir/.job.log");
		
		return true;
	}
}

class cluster_handler implements infra_handler {
	var $cluster_connection;
	var $outdir; 
	var $cpus;
	
	function __construct($id = null) {
		$this->cluster_connection = new ssh_conecta();
		$this->cluster_connection->set_host('padufc.cenapad.ufc.br');
		$this->cluster_connection->set_port(22);
		$this->cluster_connection->set_user($_SESSION['usuarioLogin']);
		$this->cluster_connection->set_passwd($_SESSION['usuarioSenha']);
		if(!$this->cluster_connection->login()) {
			throw new Exception('Usuário desconhecido ou cluster indisponivel');
		}
		$this->outdir = trim($this->cluster_connection->command('pwd')).'/jobs/';
		$this->cluster_connection->command('mkdir -p '.$this->outdir); 
	}
	
	function set_cpus($count) {
			$this->cpus = $count; 
	}
	
	// These are for compatibility with cloud
	static function get_allocated_handlers($user) {
		return new cluster_handler();
	}
	static function allocate_new_handler() {
		return new cluster_handler();
	}
	function dispose() {}

	function get_infra_type() {
		return 'cluster';
	}
	
	function get_id() {
		return 0;
	}

	// The cluster is always ready ;)
	function is_ready() {
		return true;
	}
	
	function get_jobs() {
		$jobs = $this->cluster_connection->command('ls -1 '.$this->outdir);
		$jobs = preg_split('/\s+/', $jobs);

		$r = array();
		foreach ($jobs as $job) {
			if(trim($job) == "") continue;
			$r[$job] = new cluster_job($this->cluster_connection, $job);
		}

		return $r;
	}
	
	function start_job($application, $params) {
		include_once ("applications/".$application.".php");
		$existing_runs = explode("\n", $this->cluster_connection->command("ls -1 ".$this->outdir));
		while (true) {
			$id = rand();
			if (!in_array($id, $existing_runs))
				break;
		}
		$outdir = $this->outdir.$id.'/';
		$this->cluster_connection->command("mkdir -p $outdir");

		$r = run_app($params, $outdir, $this->cluster_connection);		
		$this->cluster_connection->command('echo '.$r['params_description'].' > '.$outdir.'.params.txt');
		$this->cluster_connection->command('echo '.$application.' > '.$outdir.'.app');
		
		if (isset($r['cmd_dir'])) $this->cluster_connection->cd($r['cmd_dir']);

		$this->cluster_connection->command('nohup srun -p gpu '.$r['cmd']." &> {$outdir}job.log& echo $! > $outdir.pid");
//		$this->cluster_connection->command('srun -p long '.$r['cmd']." &> $outdir.job.log");
		
		return true;
	}
}

class infra_controller {
	
	static function get_allocated_infrastructure($user) {
		$r1 = opennebula_handler::get_allocated_handlers($user);
		$r2 = openstack_handler::get_allocated_handlers($user);
		$r = array_merge($r1, $r2);
		$r[] = cluster_handler::get_allocated_handlers($user);
		return $r;
	}
	
	static function job_from_description($description) {
		$description = explode('/', $description);
		$infra = $description[0];
		$infra_id = $description[1];
		$job_id = $description[2];

		if ($infra == 'opennebula') {
			$handler = new opennebula_handler($infra_id);
		} else {
			$handler = new cluster_handler($infra_id);
		}
		$jobs = $handler->get_jobs();
		return $jobs[$job_id];
	}
	
	static function dispose_vm ($description) {
		$description = explode('/', $description);
		$infra = $description[0];
		$infra_id = $description[1];
		
		if ($infra == 'opennebula') {
			$handler = new opennebula_handler($infra_id);
		} else {
			$handler = new cluster_handler($infra_id);
		}
		
		$handler->dispose();
	}
	
	static function job_to_description($infra, $job) {
		$type = $infra->get_infra_type();
		$infra_id = $infra->get_id();
		$job_id = $job->get_id();

		return $type.'/'.$infra_id.'/'.$job_id;
	}
}

abstract class job {
	protected $connection;
	protected $sim_id;
	protected $job_dir;
	private $startDate;
	
	abstract static function get_jobs_dir();

	function __construct($con, $sid) {
		$this->connection = clone $con;
		$this->sim_id = trim($sid);
		
		$this->job_dir = static::get_jobs_dir();
		$this->connection->cd();
		$tmp = $this->connection->command("ls -l ".$this->job_dir."|grep ".$this->sim_id);
		$tmp = preg_split('/\s+/',$tmp);
		$this->startDate = implode(' ', array_slice($tmp, 5, 3));
		$this->job_dir = $this->job_dir.'/'.$this->sim_id;
	}

	function is_running() {
		$pid = $this->connection->command("cat ".$this->job_dir."/.pid");
		return (trim($this->connection->command("ps hp $pid")) == "") ? false : true;
	}

	abstract function dispose();
	
	function download_all_files() {
		$nome_user = $_SESSION['usuarioLogin'];
		$arquivo_remoto = 'files.tgz';
		$this->connection->cd($this->job_dir);
		
		$this->connection->command("tar czf $arquivo_remoto --exclude '.*' *");
		if (!file_exists('/tmp/ssh-down/'. $nome_user)) {
			if (!file_exists('/tmp/ssh-down/')) {
				mkdir('/tmp/ssh-down');
			}
			mkdir('/tmp/ssh-down/'. $nome_user);
		}
		$pasta_tmp = '/tmp/ssh-down/'. $nome_user;

		$arquivo = $pasta_tmp.'/'.rand();
		if (!file_exists($arquivo)) {
			shell_exec("touch {$arquivo}");
		}
		$this->connection->recv_file($arquivo_remoto, $arquivo);
		$this->connection->command("rm $arquivo_remoto");

		$arquivoLocal = $arquivo; // caminho absoluto do arquivo
		$arquivoNome = $arquivo_remoto; // nome do arquivo que será enviado p/ download
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = preg_replace('/^(.*[A-Za-z])[^A-Za-z]*$/', '\1', finfo_file($finfo, $arquivo) . "\n");
		// Verifica se o arquivo não existe
		// Configuramos os headers que serão enviados para o browser
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="'.$arquivoNome.'"');
		header('Content-Type: "'.$mime.'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($arquivo));
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: no-cache');
		header('Expires: 0');
		// Envia o arquivo para o cliente
		readfile($arquivoLocal);

		unlink($arquivo);
		$this->connection->cd();
	}

	function get_start_date() {
		return $this->startDate;
	}
	
	function get_params() {
		return $this->connection->command('cat '.$this->job_dir.'/.params.txt');
	}
	
	
	function get_app() {
		return $this->connection->command('cat '.$this->job_dir.'/.app');
	}
	
	function get_id() {
		return $this->sim_id;
	}
}

class cluster_job extends job {
	static function get_jobs_dir() {
		return "/home/{$_SESSION['usuarioLogin']}/jobs";
	}
	
	function dispose() {
		if ($this->is_running()) {
			$pid = trim($this->connection->command("cat ".$this->job_dir."/.pid"));
			$this->connection->command("kill -9 ".$pid);
		}
		$this->connection->command("rm -R ".$this->job_dir);
	}
}
	
class opennebula_job extends job {
	static function get_jobs_dir() {
		return '/root/jobs';
	}
	
	function dispose() {
	if ($this->is_running()) {
			$pid = trim($this->connection->command("cat ".$this->job_dir."/.pid"));
			$gid = trim($this->connection->command("ps -o %r hp $pid"));
			$this->connection->command("kill -9 -".$gid);
		}
		$this->connection->command("rm -R ".$this->job_dir);
	}
}

?>