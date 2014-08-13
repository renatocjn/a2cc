<?php

class ssh_arq_handler {
	private $file;
	private $name;
	private $size;
	private $datatime;
	private $property;
	//public $last;
		
	function __construct($name) {
		$cut_last = false;
		
		$tmp2 = preg_replace('/\s\s+/',' ', $name); 
		$tmp2 = explode(' ', $tmp2); 
		

		
		$this->name = $tmp2[8]; // CLuster - 8 Local - 7
		$i= 9; // Cluster - 9 Local - 8
		while (isset($tmp2[$i])){
			$this->name = $this->name. " " .$tmp2[$i];
			$i++;
		}
		$this->size = $tmp2[4];
		$this->datatime = $tmp2[5];
		/*$this->size = $tmp2[6];
		$this->size = $tmp2[7];*/

		$check_type = array('_/$_' => 'dir',
					   '/\*$/' => 'exec');
		foreach ($check_type as $regex => $type) {
			$check = preg_match($regex, $this->name);
			$this->property[$type] = $check;
			if ($check) {
				$cut_last = true;
			}
		}

		if ($cut_last) {
			$this->name = preg_replace('/^(.*).$/', '\1', $this->name);
		}
	}

	function isFile() {
		return !$this->property['dir'];
	}

	function isExec() {
		return $this->property['exec'];
	}

	function getFilename() {
		return $this->name;
	}
	
	function getFilesize() {
		return $this->size;
	}
	
	function getDatatime(){
		return $this->datatime;
		}
}

/**
 * \brief Classe que encapsula funcionalidades de SSH.
 *
 * \details Classe que encasula funcionalidades de SSH, provendo,
 * 	assim, acesso e controle ideal para sistemas remotos UNIX.
 * 	Extremamente importante caso se deseje fazer autenticações,
 * 	o que, feito "ad-hoc", significaria mecher no shadow e no passwd.
 */

/*
 * atributos:
 *	host, port, user, passwd(?), working dir
 * métodos:
 *	__ login __, __ cd/chwd __, __ listar conteúdo* __, __ trasferência de arquivos __,
 *	__ comando arbitrário __,
 *	upload, download, compactar, descompactar
 *
 *	upload e download estão relacionados com transferência de arquivos
 *
 * TODO:
 * 	upload?
 * 	download?
 * 	compactar?
 * 	descompactar?
 *
 * DOING:
 * 	listar conteúdo
 *
 * DONE:
 * 	transferência de arquivos: remoto para local
 * 	transferência de arquivos: local para remoto
 * 	command
 * 	cd/chwd
 * 	login
 *
 * TESTING:
 * 	transferência de arquivos: local para remoto
 * 	command
 * 	cd/chwd
 *
 * TESTED:
 * 	login
 *
 *
 * \fn listar_dir()
 * \brief Retorna informação sobre o conteúdo do diretório atual.
 * \details As informações contidas estão armazenadas em um array de objetos ssh_arq_handler, que permite
 * 	listar propriedades de arquivos da mesma maneira que usando o método anterior.
 * \return Array com os os arquivos e pastas contidos no diretório atual.
 *
 * \fn get_path()
 * \brief Retorna o diretório atual.
 * \details Faz o cálculo do path, devido o diretório atual ser armazenado em vetor.
 * \return O caminho (relativo a partir da home do usuário) atual.
 *
 * \fn send_file($nome_arq_local, $nome_arq_remoto = null)
 * \brief Envia um arquivo local para o computador conectado em SSH.
 * \details .
 * \param[in] $nome_arq_local	O caminho do arquivo que será enviado.
 * \param[in] $nome_arq_remoto	O caminho onde o arquivo será gravado; se for usado referência relativa,
 * 	será relativa ao path atual. Se não for informado qual o nome, assume que é o mesmo do arquivo
 * 	enviado.
 * \warning A opção default para o nome do arquivo de recebimento está buggada na implementação atual,
 * 	precisa ser corrigida.
 *
 * \fn recv_file($nome_arq_local, $nome_arq_remoto = null)
 * \brief Grava localmente um arquivo do computador conectado via SSH.
 * \details .
 * \param[in] $nome_arq_remoto	O caminho do arquivo que será enviado; se for usado referência relativa,
 * 	será relativa ao path atual.
 * \param[in] $nome_arq_local	O caminho onde o arquivo será gravado. Se não for informado qual o nome, 
 * 	assume que é o mesmo do arquivo enviado.
 * \warning A opção default para o nome do arquivo de recebimento está buggada na implementação atual,
 * 	precisa ser corrigida.
 *
 * \fn logged()
 * \brief Retorna se está loggado.
 * \details Só deve retornar verdade após chamar o método login com sucesso.
 * \return Booleano, indicando se já foi loggado ou não.
 *
 * \fn get_command_return()
 * \brief Pega a saída do último comando executado.
 * \details Nulo caso não tenha sido executado comando algum; senão, uma string com o comando passado.
 * \return Uma string com a saída do comando ou nulo.
 *
 * \fn command($cmd, $args = array())
 * \brief Executa um comando com, opicionalmente, diversos argumentos.
 * \details Atualiza o conteúdo do retorno de comando. Funciona sobre o diretório atual, portanto cuidado
 * 	com as referências. Deve ser efetuado login com sucesso antes de chamar esse método.
 * \param[in] $cmd	Uma string com o comando que será rodado. Pode-se colocar aqui um script inteiro.
 * \param[in] $args	Um vetor de strings com os argumentos do comando passado. Deve ser necessariamente um vetor de strings.
 * \return A saída do comando, ou nulo, caso haja algum erro.
 * \warning Se o caminho atual estiver setado para um lugar inexistente, será considerado uma falha de comando shell, retornando
 * 	silenciosamente uma string vazia.
 * \todo Avisar que houve falha de caminho, parar de imprimir mensagens de erro, atualizar a variável de mensagens de erro.
 *
 * \fn login($method = 'passwd')
 * \brief Faz o login via ssh.
 * \details É necessário setar usuário, password/chave pública, host, porta (default é 22) e demais outros dados \em antes de
 * 	se executar esse método. Possui suporte para 3 tipos de login, sendo essa escolha passada como parâmetro.
 * \param[in] $method	O método de login. O padrão é login via password 'passwd', mas também aceita sem senha 'none' e baseado
 * 	em chave 'key'.
 * \return Verdade caso consiga executar se logar; falso caso contrário. Mensagens de erro podem ser capturadas através do método
 * 	get_err().
 * \warning Não atualiza o estado interno de erro, mas sim escrevendo via echo o erro que aconteceu; apenas a funcionalidade de logar-se com
 * 	senha foi implementada, as outras retornam falso imediatamente.
 *
 * \fn cd($path = null)
 * \brief Muda o diretório atual.
 * \details Muda o diretório atual para o especificado no parâmetro. Caso comece com '/' ou '~/', trata de referência a partir da home
 * 	do usuário. '~outrouser' é considerado inválido para questão de segurança. Não é possível retornar para \em antes da home
 * 	do usuário. Caso seja passado valor nulo, retorna a home do usuário.
 * \param[in] $path	Padrão é retornar para a home do usuário. Aceita referência relativa por padrão, incluindo diretório
 * 	atual '.' e diretório pai '..'; considera múltiplas barras, como 'folder///////another//lastone', como sendo apenas uma,
 * 	portanto o exemplo seria interpretado como 'folder/another/lastone'. Para fazer referência a partir da home do usuário,
 * 	basta colocar na frente uma barra '/' ou um tilde-barra '~/'. Referência a home de outros usuários é inválida. Aceita
 * 	transparentemente links simbólicos de pastas.
 * \return Nada.
 * \warning Todas as mudanças de diretório não são imediatamente submetidas ao SSH para saber se são válidas!
 */
class ssh_conecta {
	private $user;
	private $passwd;
	private $pub_key;
	private $priv_key;
	private $host;
	private $port;

	private $pwd;
	private $pwd_depth;
	private $path;

	private $conn;
	private $logged_b;

	private $command_return;

	private $err;

	function get_err() {
		return $this->err;
	}

	function listar_dir() {
		$old_command = $this->command_return;

		$pre_array = explode("\n", $this->command('ls -plhLF'));
		
		print_r($pre_array);

		
		
		$file_list = array();
		$first=true; // Flag para retirar a primeira linha do comando ls (total ...)
		foreach ($pre_array as $file_name) {
			if (!$first){
				if ($file_name != "") {
					$file_list[] = new ssh_arq_handler($file_name);
				}				
			}
			$first=false;
		}


		$this->command_return = $old_command;

		return $file_list;
	}
	
	

	function get_path() {
		//if ($this->path === null) {
		//	if ($this->pwd_depth == 0) {
		//		$this->path = '';
		//	} else {
		//		$this->path = implode('/', $this->pwd). '/';
		//	}
		//}

		return $this->path;
	}

	function send_file($nome_arq_local,	$nome_arq_remoto = null) {

		if ($nome_arq_remoto === null) {
			$nome_arq_remoto = &$nome_arq_local;
		}
		/*echo "ssh2_scp_send({$this->conn}, {$nome_arq_local},".
			$this->get_path(). $nome_arq_remoto. ");";*/
		return ssh2_scp_send($this->conn, $nome_arq_local,
			$this->get_path(). $nome_arq_remoto);
	}

	function recv_file($nome_arq_remoto, $nome_arq_local = null) {
		if ($nome_arq_local === null) {
			$nome_arq_local = $nome_arq_remoto;
		}
		return ssh2_scp_recv($this->conn,
			$this->get_path().$nome_arq_remoto, $nome_arq_local);
	}

	function logged() {
		return $this->logged_b;
	}

	function set_user($new_user) {
		$this->user = $new_user;
	}

	function set_passwd($new_passwd) {
		$this->passwd = $new_passwd;
	}

	function set_host($new_host) {
		$this->host = $new_host;
	}

	function set_port($new_port) {
		$this->port = $new_port;
	}
	
	function set_pub_key($new_pub_key) {
		$this->pub_key = $new_pub_key;
	}
	
	function set_priv_key($new_priv_key) {
		$this->priv_key = $new_priv_key;
	}

	function get_command_return() {
		return $this->command_return;
	}

	function command($cmd, $args = array()) {
		if (!$this->logged_b) {
			$this->err = "Necessário fazer login antes de executar comando";
			return null;
		}
		$arg = implode(' ', $args);
		/*echo "\$stream = ssh2_exec(\$this->conn, 'cd ".
			implode('/', $this->pwd). " && ({$cmd} {$arg})')\n";*/
		//$stream = ssh2_exec($this->conn, "{$cmd} {$arg}");
		$stream = ssh2_exec($this->conn, "cd ".
			$this->get_path(). " && ({$cmd} {$arg})");
		
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, true);
		$this->err = stream_get_contents($errorStream);       
		fclose($errorStream);
		
		if (!$stream) {
			$this->command_return = null;
			return $this->command_return;
		}

		stream_set_blocking($stream, true);
		$this->command_return = '';

		while ($buf = fread($stream, 4096)) {
			$this->command_return .= $buf;
		}
		fclose($stream);

		return $this->command_return;
	}
	
	function assync_command($cmd, $args = array()) {
		if (!$this->logged_b) {
			$this->err = "Necessário fazer login antes de executar comando";
			return null;
		}
		$arg = implode(' ', $args);
		/*echo "\$stream = ssh2_exec(\$this->conn, 'cd ".
			implode('/', $this->pwd). " && ({$cmd} {$arg})')\n";*/
		//$stream = ssh2_exec($this->conn, "{$cmd} {$arg}");
		$stream = ssh2_exec($this->conn, "cd ".$this->get_path(). " && ({$cmd} {$arg})");
		stream_set_blocking($stream, false);
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking($errorStream, false);
		return;
	}

	function login($method = 'passwd') {
		$this->conn = ssh2_connect($this->host, $this->port);

		if ($this->conn) {
			switch ($method) {
			case 'pass':
			case 'passwd':
			case 'password':
				if (ssh2_auth_password($this->conn, $this->user, $this->passwd)) {
					$this->logged_b = true;
				} else {
					$this->err = "Falha no login (método passwd)";
					$this->logged_b = false;
				}
				break;
			case 'key':
			case 'public_key':
				if (ssh2_auth_pubkey_file($this->conn, $this->user,
                          $this->pub_key,
                          $this->priv_key)) {
                	$this->logged_b = true;
				} else {
					$this->err = 'Public Key Authentication Failed';
					$this->logged_b = false;
				}
				break;
			case '':
			case 'none':
				$this->err = "Método de autenticação ainda a ser implementado";
				$this->logged_b = false;
				break;
			default:
				$this->err = "Método de autenticação desconhecido";
				//echo "Tente usar 'passwd', 'key' ou ".  "'none'\n";
				$this->logged_b = false;
			}
		} else {
			$this->err = "Falha na coexão";
			$this->logged_b = false;
		}
		return $this->logged_b;
	}

	function __construct() {
		$this->path = null;
		$this->pwd = array();
		$this->pwd_depth = 0;

		$this->host = '200.19.191.252';
//		$this->host = '192.168.1.36';
		//$this->port = '33000';
		$this->port = '22';

		$this->command_return = '';
		$this->logged_b = false;

		$this->conn = null;
	}

	function cd($path = "") {
		$path = trim($path);
		if (substr($path, -1) != "/" && $path != "") 
			$path = $path.'/';
		$this->path = $path;
		
		/*if (!isset($path) or $path == '') {
			$this->pwd_depth = 0;
			$this->pwd = array();
			return;
		}
		$dirs = explode('/', $path);
		if ($dirs[0][0] == '~' and isset($dirs[0][1])) {
			$this->pwd_depth = 0;
			$this->pwd = array();
			return;
		}
		if ($dirs[0] == '' or $dirs[0] == '~') {
			unset($dirs[0]);
			$this->pwd_depth = 0;
			$this->pwd = array();
		}
		foreach ($dirs as $step) {
			switch ($step) {
			case '..':
				if ($this->pwd_depth > 0) {
					$this->pwd_depth -= 1;
					unset($this->pwd[$this->pwd_depth]);
				}
				break;
			case '.':
			case '':
				break;
			default:
				$this->pwd[$this->pwd_depth] = $step;
				$this->pwd_depth += 1;
			}
		}*/
	}
}
?>
