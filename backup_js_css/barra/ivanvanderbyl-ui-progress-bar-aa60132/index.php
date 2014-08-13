<?php
	include("./defaults.php");
	include_once ("./seguranca3.php"); // Inclui o arquivo com o sistema de segurança
	protegePagina(); // Chama a função que protege a página
	$nome_user=$_SESSION['usuarioNome'];//variavel que contem o login do usuario logado
	$data_atual = date('Y-m-d'); //Data do sistema
    //exemplo: 0d2e5d60f49c47fe84d5c6ce0924adde
    ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Aguarde...</title>
  
  <!-- Make it look good on the iPhone (: -->
  <meta name="viewport" content="width=device-width">
  
  <link rel="stylesheet" href="stylesheets/ui.css">
  <link rel="stylesheet" href="stylesheets/ui.progress-bar.css">
  <link media="only screen and (max-device-width: 480px)" href="stylesheets/ios.css" type="text/css" rel="stylesheet" />
</head>
<body>
  
  <div id="container">
    
    <p>A2C: Efetuando a solicitação para a máquina</p>
    
    <!-- Progress bar -->
    <div id="progress_bar" class="ui-progress-bar ui-container">
      <div class="ui-progress" style="width: 79%;">
        <span class="ui-label" style="display:none;">Carregando <b class="value">79%</b></span>
      </div>
    </div>
    <!-- /Progress bar -->
    
    
    <div class="content" id="main_content" style="display: none;">
  <?php   
	
if($_POST['nome'] !="" and $_POST['imagem'] !="" and $_POST['flavors'] != "")
{	
	
  $tenat   = "0d2e5d60f49c47fe84d5c6ce0924adde"; 
  $nome    = $_POST['nome'];
  $imagem  = $_POST['imagem']; 
  $flavors = $_POST['flavors'];
  $nic_net = "751a5581-ae64-4756-b764-72d515529d53";
  $retorno = explode("\n", $ssh_con->command("source cred && nova --os-tenant-id $tenat boot --image $imagem --flavor $flavors --nic net-id=$nic_net $nome"));
  //echo "nova --os-tenant-id $tenat boot --image $imagem --flavor $flavors --nic net-id=$nic_net $nome";
  //echo '<script type="text/javascript">window.location.href="http://localhost/a2c1/index1.php"</script>';

	
}

if($_GET['id1'] !="")
{
	$id_pausa = $_GET['id1'];
	$retorno = explode("\n", $ssh_con->command("source cred && nova pause $id_pausa"));
	//echo '<script type="text/javascript">window.location.href="http://localhost/a2c1/index1.php"</script>';
}

if($_GET['id2'] !="")
{
	$id_excluir = $_GET['id2'];
	$retorno = explode("\n", $ssh_con->command("source cred && nova delete $id_excluir"));
	echo '<script type="text/javascript">window.location.href="http://localhost/a2c1/index1.php"</script>';
}

if($_GET['id3'] !="")
{
	$id_reiniciar = $_GET['id3'];
	$retorno = explode("\n", $ssh_con->command("source cred && nova unpause $id_reiniciar"));
	//echo '<script type="text/javascript">window.location.href="http://localhost/a2c1/index1.php"</script>';
}

if($_GET['id4'] !="")
{
	$id_excluir = $_GET['id4'];
	$retorno = explode("\n", $ssh_con->command("source cred && nova delete $id_excluir"));
	//echo '<script type="text/javascript">window.location.href="http://localhost/a2c1/index1.php"</script>';
}






if($_POST['nome'] =="" and $_POST['imagem'] =="" and $_POST['flavors'] == "" and $_GET['id1']=="" and $_GET['id3'] ==""){
	echo '<script type="text/javascript">alert("Escolha uma opção")</script>';
	//echo '<script type="text/javascript">window.location.href="http://localhost/a2c1/index1.php"</script>';
}

  
  
?>
    <script language= "JavaScript">
          setTimeout("document.location = 'http://localhost/a2c1/index1.php'",7000);
    </script>
    </div>
    
  </div>
  <script src="javascripts/jquery.js" type="text/javascript" charset="utf-8"></script>
  <script src="javascripts/progress.js" type="text/javascript" charset="utf-8"></script>
   
</body>
</html>
