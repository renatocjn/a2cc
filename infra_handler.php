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

	function dispose_if_necessary();

	function clean_of_jobs();
}

/*
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

	function dispose_if_necessary() {
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
		if( preg_match('/ERROR:.', $vm_id) ) {
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


//		$this->connection->command('echo '.$params['user_description'].' > '.$outdir.'.params.txt');
		unset($params['user_description']);
		$this->connection->command('echo '.$application.' > '.$outdir.'.app');

		$r = run_app($params, $outdir, $this->connection);
		$this->connection->command('echo '.$r['params_description'].' > '.$outdir.'.params.txt');

		if (isset($r['cmd_dir'])) $this->connection->cd($r['cmd_dir']);
		$this->connection->command('nohup '.$r['cmd']." &> $outdir/job.log& echo $! > $outdir/.pid");
//		$this->connection->command($r['cmd']." &> $outdir/.job.log"); //benchmarking

		return true;
	}
//}'*/

class opennebula_handler implements infra_handler {
	var $connection;
	var $VMID;
	var $status;

	private static function get_allocated_vmids($user = NULL) {
		$con = mysqli_connect("localhost", "a2cc", "1!2@3#");
		$username = mysqli_real_escape_string($con, trim($user));
		$sql = "select vmid from a2cc.opennebula_allocated_vms";
		if($user)
			$sql .= " where username='$user'";
		$result = mysqli_query($con, $sql);

		$r = array();
		while(($t = mysqli_fetch_assoc($result)))
			$r[] = intval($t['vmid']);
		return $r;
	}

	private static function register_vm($vmid) {
		$con = mysqli_connect("localhost", "a2cc", "1!2@3#");
		$username = mysqli_real_escape_string($con, trim($_SESSION['usuarioLogin']));
		$vmid = mysqli_real_escape_string($con, $vmid);
		mysqli_query($con, "insert into a2cc.opennebula_allocated_vms values ('$username', $vmid)");
	}

	private static function unregister_vm($vmid) {
		$con = mysqli_connect("localhost", "a2cc", "1!2@3#");
		$username = mysqli_real_escape_string($con, trim($_SESSION['usuarioLogin']));
		$vmid = mysqli_real_escape_string($con, $vmid);
		mysqli_query($con, "delete from a2cc.opennebula_allocated_vms where username='$username' and vmid=$vmid");
	}

	static function getCloudConnection() {
		$cloud_connection = new ssh_conecta();
		$cloud_connection->set_host('200.19.191.230');
		$cloud_connection->set_port(33000);
		$cloud_connection->set_user('oneadmin');
		$cloud_connection->set_pub_key('/etc/ON-controller.key.pub');
		$cloud_connection->set_priv_key('/etc/ON-controller.key');
		if ($cloud_connection->login('key')) {
			return $cloud_connection;
		} else {
			throw new Exception("Can't connect to opennebula cloud");
		}
	}

	function dispose_if_necessary() {
		/*$cloud_connection = opennebula_handler::getCloudConnection();
		$machineIP = $cloud_connection->command("onevm delete ".$this->VMID);
		opennebula_handler::unregister_vm($this->VMID);*/
		if (empty($this->get_jobs()))
			opennebula_handler::unregister_vm($this->VMID);
	}

	function get_infra_type() {
		return 'opennebula';
	}

	public function get_id() {
		return $this->VMID;
	}

	function get_jobs() {
		$jobs = $this->connection->command('ls -1 '.opennebula_job::get_jobs_dir());
		$jobs = preg_split('/\s+/', $jobs);
		$r = array();
		foreach ($jobs as $job) {
			$job = trim($job);
			if($job == "") continue;
			$r[$job] = new opennebula_job($this->connection, $job);
		}
		return $r;
	}

	function clean_of_jobs() {
		$jobs = $this->connection->command("find jobs/ -maxdepth 2 -type d -regex .*/[0-9]+$");
		if(trim($jobs) == "")
			return true;
		else
			return false;
	}

	function is_ready() {
		return $this->status;
	}

	function __construct($vm_id, $machineIP=null) {
		$this->VMID = $vm_id;
		$cloud_connection = opennebula_handler::getCloudConnection();

		$owner = trim($cloud_connection->command("onevm show $vm_id|grep USER|xargs|cut -d ' ' -f 3"));

		if ($machineIP === null) {
			$machineIP = $cloud_connection->command("onevm show ".$this->VMID."|grep publica|xargs|cut -d ' ' -f 5");
			$machineIP = trim($machineIP);
		}

		$this->connection = new ssh_conecta();
		$this->connection->set_host($machineIP);
		$this->connection->set_port(22);
		$this->connection->set_user('clouduser');
		$this->connection->set_pub_key('/etc/ON-vm.key.pub');
		$this->connection->set_priv_key('/etc/ON-vm.key');
		if(!$this->connection->login('key')) {
			$this->status = false;
		} else {
			$this->status = true;
		}
	}

	static function allocate_new_handler() {
		$cloud_connection = opennebula_handler::getCloudConnection();
		$description = $cloud_connection->command('onevm create --disk 14 --memory 1024 --cpu 1 --nic oneadmin[publica] --net_context --ssh /etc/vm.key.pub --vnc --user a2cc --password 1!2@3#');
		if( !preg_match('/^ID: [0-9]+$/', $description) ) {
			return false;
		}
		$description = preg_split('/\s+/', $description);
		$vmid = $description[1];
		opennebula_handler::register_vm($vmid);
		sleep(60);
		return new opennebula_handler($vmid);
	}

	static function get_allocated_handlers($user = NULL) {
		if($user)
			$vm_list = opennebula_handler::get_allocated_vmids($user);
		else {
			$cloud_connection = opennebula_handler::getCloudConnection();
			$r = $cloud_connection->command('onevm list --csv --list ID,USER --filter USER=a2cc|cut -d, -f1');
			$vm_list = array_slice( preg_split('/\s+/', $r), 1 );
		}
		$handlers = array();
		foreach ($vm_list as $vmid) $handlers[] = new opennebula_handler($vmid);
		return $handlers;
	}

	function start_job($application, $params) {
		//$t = time();
		while (!$this->connection->login('key')) {
			sleep(15);
		}
		//print 'machineDeployTime '.(60 + time() - $t)."\n";
		include_once ("applications/".$application.".php");
		$existing_runs = explode("\n", $this->connection->command("ls -1 ".opennebula_job::get_jobs_dir()));
		while (true) {
			$id = rand();
			if (!in_array($id, $existing_runs))
				break;
		}
		opennebula_handler::register_vm($this->VMID);
		$outdir = opennebula_job::get_jobs_dir()."/".$id.'/';
		$this->connection->command("mkdir -p $outdir");

		//$this->connection->command('echo '.$params['user_description'].' > '.$outdir.'.params.txt');
		//unset($params['user_description']);
		$this->connection->command('echo '.$application.' > '.$outdir.'.app');

		$r = run_app($params, $outdir, $this->connection);
		$this->connection->command('echo '.$r['params_description'].' > '.$outdir.'.params.txt');

		if (isset($r['cmd_dir'])) $this->connection->cd($r['cmd_dir']);
		$this->connection->command('nohup '.$r['cmd']." &> ".$outdir."job.log& echo $! > ".$outdir.".pid");
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
		$this->outdir = trim(cluster_job::get_jobs_dir().'/');
		$this->cluster_connection->command('mkdir -p '.$this->outdir);
	}

	function set_cpus($count) {
			$this->cpus = $count;
	}

	// These are for compatibility with cloud
	static function get_allocated_handlers($user = NULL) {
		return new cluster_handler();
	}
	static function allocate_new_handler() {
		return new cluster_handler();
	}
	function dispose_if_necessary() {}
	function clean_of_jobs() {
		return empty($this->get_jobs());
	}

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

		$this->cluster_connection->command('echo '.$params['user_description'].' > '.$outdir.'.params.txt');
		unset($params['user_description']);
		$this->cluster_connection->command('echo '.$application.' > '.$outdir.'.app');

		$r = run_app($params, $outdir, $this->cluster_connection);
		$this->cluster_connection->command('echo '.$r['params_description'].' > '.$outdir.'.params.txt');

		if (isset($r['cmd_dir'])) $this->cluster_connection->cd($r['cmd_dir']);
//		print($r['cmd']);
		$this->cluster_connection->command('nohup srun -p gpu '.$r['cmd']." &> {$outdir}job.log& echo $! > $outdir.pid");
//		print $this->cluster_connection->command('srun -p gpu '.$r['cmd']);

		return true;
	}
}

class infra_controller {

	static function get_allocated_infrastructure($user) {
		$r = opennebula_handler::get_allocated_handlers($user);
//		$r2 = openstack_handler::get_allocated_handlers($user);
//		$r = array_merge($r1, $r2);
		$r[] = cluster_handler::get_allocated_handlers($user);
		return $r;
	}

	static function parse_description($description) {
		$description = explode('/', $description);
		$infra = $description[0];
		$infra_id = $description[1];
		$job_id = $description[2];
		$r = array();
		
		if ($infra == 'opennebula') {
			$handler = new opennebula_handler($infra_id);
		} else {
			$handler = new cluster_handler($infra_id);
		}
		$r['infra'] = $handler;
		
		$jobs = $handler->get_jobs();		
		$r['job'] = $jobs[$job_id];
		
		return $r;
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

		if ($handler->clean_of_jobs())
			$handler->dispose_if_necessary();
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

	static abstract function get_jobs_dir();

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
		return '/home/clouduser/jobs/'.$_SESSION['usuarioLogin'];
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