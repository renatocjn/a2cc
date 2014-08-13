<?php
	include("defaults.php");
	include_once ("seguranca3.php"); // Inclui o arquivo com o sistema de segurança
	include_once 'infra_handler.php';
	protegePagina(); // Chama a função que protege a página
	$nome_user=$_SESSION['usuarioNome'];//variavel que contem o login do usuario logado
	$data_atual = date('Y-m-d'); //Data do sistema
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="refresh" content="600">
		<?php print_title(); ?>
		<link rel="stylesheet" href="./css/styles.css" type="text/css" media="all">
		<link rel="shortcut icon" href="./css/images/cenapad.png" type="image/gif" />
		<link rel="stylesheet" href="./css/start/jquery-ui-1.10.4.custom.min.css" />
		<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
		<script type="text/javascript" src="javascripts/script.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script>
		
		<script type='text/javascript'>
		
			function removeRow(elem) {
				console.log(elem);
				$(elem).parents('tr').remove();
			}
			$( function() {
				$('.tab').tabs();
				$('.progressbar').progressbar().progressbar('value', false);
				$('#load').hide();
			
				function mostrar_barra() {
					$('#load').show('fast');
				}
		
				function ocultar_barra() {
					$('#load').hide('fast');
				}
			
				function alertFail(d) {
					alert('algo deu errado...\n'+d.responseText)
				}
				
				$('#namdCustomParams').change( function() {
					var option = $(this).find("option:selected");
					var name = option.attr('name');
					var val = option.val();
					
					if($('#namdCustomParamsTable input[name='+name+']').length) return;
					$('#namdCustomParamsTable').append('<tr> <td>'+ name +'</td> <td> <input type="text" name="'+name+'" value="'+ val +'"> </td> <td> <a onclick="removeRow(this)"> <img src="img/excluir.png" alt="remover parametro"> </a> </td> </tr>');
				});
				
				$('.delBttn').click(function () {
					var description = $(this).parent().parent().children('input[type=hidden]').val();
					jQuery.ajax({
						url: "executaComando.php", 
						data: { excluir: description },
						complete: ocultar_barra,
						beforeSend: mostrar_barra,
						method: 'GET',
						error: alertFail,
						success: function() {location.reload();}
					});
				});
				
				$('a').css({"cursor":"pointer"});
				$("#cleanBttn").click( function() {
					jQuery.ajax({
						url: "executaComando.php", 
						data: { rmAll:"" },
						method: 'GET',
						complete: ocultar_barra,
						beforeSend: mostrar_barra,
						error: alertFail,
						success: function() {location.reload();}
					});
				});
				$('#form1').submit( function(event) {
					event.preventDefault();
					var panel = $(this);
					var appURI = "";
					do {
						var href = panel.children('ul').find('li a').eq(panel.tabs('option', 'active')).attr('href');
						panel = panel.find(href);
						appURI = appURI + panel.children('input[type=hidden]').first().val() + '/';
					} while (panel.hasClass('tab'));
					appURI = appURI.slice(0, -1)
					
					var formData = new FormData();
					formData.append('application', appURI);
					
					$(this).find(':input:visible').not('input[type=file]').each( function() {
						if (($(this).is('input[type=checkbox]') && $(this).is(':checked')) || !$(this).is('input[type=checkbox]')) {
							if( !$(this).attr('name') ) return;
							formData.append($(this).attr('name'), $(this).val());
						}
					});
					
					$(this).find(':visible:input[type=file]').each( function() {
	    				formData.append(this.name, $(this).prop('files')[0]);
					});
					
					jQuery.ajax({
						url: 'executaComando.php',
						data: formData,
						method: 'POST',
						cache: false,
						contentType: false,
						processData: false,
						error: alertFail,
						beforeSend: mostrar_barra,
						complete: ocultar_barra,
						success: function() {location.reload();}
					});
				});
			});
		</script>
	</head>
	
	<body style="padding:0; margin:0">

	<div id="header"></div>

	<?php include_once 'navbar.php'; 
			 require 'app_assets/namd/default_namd_params.php';
	?>
	<div id="midsection2">
	
	<BR>
	<div id='load' class='progressbar'> </div>
	<BR>
	
	<center> <form id="form1" class="tab validate formLogin" method="post" enctype="multipart/form-data"> <input type='hidden' value=''>	
		<ul>
			<li><a href="#ns3"> NS3 </a> </li>
			<li><a href="#namd"> Namd </a> </li>			
			<li><a href="#gaussian"> Gaussian </a> </li>
			<li><a href="#siesta"> Siesta </a> </li>
			<li><a href="#octave"> Octave </a> </li>
		</ul>
		
		<div id='gaussian'> <input type='hidden' value='gaussian'> Não implementado! </div>
		<div id='siesta'> <input type='hidden' value='siesta'> Não implementado! </div>
		<div id='octave'> <input type='hidden' value='octave'> Não implementado! </div>		
		
		<div id='namd' class="tab"> <input type='hidden' value='namd'> 
			<ul>
				<li> <a href="#norun"> Minimização </a> </li>
				<li> <a href="#singlerun"> Execução simples </a> </li>
				<li> <a href="#multiplerun"> Execução multipla </a> </li>
			</ul>	
			
			<div id='norun' class="tab"> <input type='hidden' value='norun'>
				 <ul>
					<li> <a href="#start_norun"> Iniciar nova simulação </a> </li>
					<li> <a href="#continue_norun"> Continuar simulação </a> </li>
				</ul>
				<div id="start_norun"> <input type='hidden' value='start_norun'> </div>
				<div id="continue_norun"> <input type='hidden' value='continue_norun'> 
					<table>
						</tr>	<tr>
							<td>Arquivo COOR com coordenadas a serem iniciadas: </td>
							<td><input type="file" name="coorFile" accept=".coor"></td>
						</tr>	<tr>
							<td>Arquivo VEL com velocidades iniciais: </td>
							<td><input type="file" name="velFile" accept=".vel"></td>
						</tr>	<tr>
							<td>Valor do passo inicial</td>
							<td><input type="number" name="initialStep" min="0"></td>
						</tr>
					</table>
				</div>
			</div>
			<div id='singlerun' class="tab"> <input type='hidden' value='singlerun'>
				<ul>
					<li> <a href="#start_singlerun"> Iniciar nova simulação </a> </li>
					<li> <a href="#continue_singlerun"> Continuar simulação </a> </li>
				</ul>
				<div id="start_singlerun"> <input type='hidden' value='start_singlerun'> </div>
				<div id="continue_singlerun"> <input type='hidden' value='continue_singlerun'> 
					<table>
						</tr>	<tr>
							<td>Arquivo COOR com coordenadas a serem iniciadas: </td>
							<td><input type="file" name="coorFile" accept=".coor"></td>
						</tr>	<tr>
							<td>Arquivo VEL com velocidades iniciais: </td>
							<td><input type="file" name="velFile" accept=".vel"></td>
						</tr>	<tr>
							<td>Valor do passo inicial</td>
							<td><input type="number" name="initialStep" min="0"></td>
						</tr>
					</table>
				</div>
			</div>
			<div id='multiplerun' class="tab"> <input type='hidden' value='multiplerun'>
				<ul>
					<li><a href="#start_multiplerun"> Iniciar nova simulação </a> </li>
					<li><a href="#continue_multiplerun"> Continuar simulação </a> </li>
				</ul>
								
				<div id="start_multiplerun"> <input type='hidden' value='start_multiplerun'> </div>
				<div id="continue_multiplerun"> <input type='hidden' value='continue_multiplerun'> 
					<table>
						</tr>	<tr>
							<td>Arquivo COOR com coordenadas a serem iniciadas: </td>
							<td><input type="file" name="coorFile" accept=".coor"></td>
						</tr>	<tr>
							<td>Arquivo VEL com velocidades iniciais: </td>
							<td><input type="file" name="velFile" accept=".vel"></td>
						</tr>	<tr>
							<td>Valor do passo inicial</td>
							<td><input type="number" name="initialStep" min="0"></td>
						</tr>
					</table>
				</div>

				<table> <tr>
						<td> Numero de vezes que deseja dividir a execução </td>
						<td> <input type="number" name="divisions" min="1" max="30"> </td>
				</tr> </table>								
				 
			</div>
			<select id='namdCustomParams'>
				<option disabled selected> Por favor selecione os parametros que deseja customizar </option>
				<?php
					$default_params = get_customizeable_namd_params();
					foreach ($default_params as $p_key => $p_val) 
						echo "<option value='$p_val' name='$p_key'>{$p_key}, default value: $p_val</option>";
				?>
			</select>
			<table>
				</tr>	<tr>
					<td>Arquivo PDB com coordenadas: </td>
					<td><input type="file" name="coordenatesFile" accept=".pdb"></td>
				</tr>	<tr>
					<td>Arquivo PSF com estruturas: </td>
					<td><input type="file" name="structureFile" accept=".psf"></td>
				</tr>	<tr>
					<td>Arquivo INP ou XPLOR com parametros</td>
					<td><input type="file" name="inpFile" accept=".inp,.xplor"></td>
				</tr>
			</table>
			
			<table id="namdCustomParamsTable" > 	</table>
		
		</div>		

		<div id='ns3' class='tab'> <input type='hidden' value='ns3'>
			<ul>
				<li><a href="#mesh_tab"> Redes Mesh </a> </li>
				<li><a href="#lte"> Redes LTE </a> </li>
				<li><a href="#vannet"> Redes VANNETS </a> </li>
				<li><a href="#generic"> Script Generico </a> </li>
			</ul>
			<div id='generic'> <input type='hidden' value='generic'> Não implementado! </div>
			<div id='lte'> <input type='hidden' value='lte'> Não implementado! </div>
			<div id='vannet'> <input type='hidden' value='vannet'> Não implementado! </div> 
			<div id='mesh_tab' class='tab'>
				<input type='hidden' value="mesh">
				<ul>
					<li> <a href="#grid"> Topologia em grade </a> </li>
					<li> <a href="#uniform_disc"> Topologia em disco aleatorio </a> </li>
				</ul>
		
				<div id='grid'>
					<input type='hidden' value="grid">
						<h1> Parametros da simulação </h1>
						<table class="table-cadastro">
							<tr>
								<td> Tamanho Horizontal (x-size) </td>
								<td> <input type='text' onkeypress="return SomenteNumero(event)" name="gradeX" value="10" size=5> </td>
							</tr> <tr>
								<td> Tamanho vertical (y-size) </td>
								<td> <input type="text" onkeypress="return SomenteNumero(event)" name="gradeY" value="10" size= 5> </td>
							</tr> <tr>
								<td> Espaço entre nós (step)</td>
								<td> <input type='text' onkeypress="return SomenteNumero(event)" name="step" value="100" size=5> </td>
							</tr> <tr>
								<td> Numero de interfaces de cada nó (interfaces) </td>
								<td> <input type='text' onkeypress="return SomenteNumero(event)" name='interface' value="3" size=5> </td>
							</tr> <tr>
								<td>Tipo de traces</td>
								<td>
									<input type="checkbox" checked='on' name="xml">XML<br>
									<input type="checkbox" name="pcap">PCAP<br>
								</td>
							</tr>
						</table>
				</div>
				<div id='uniform_disc'> 
					<input type='hidden' value="uniform_disc">
						<h1> Parametros da simulação </h1>
						<table class="table-cadastro">
							<tr>
								<td> Raio do disco (radius) </td>
								<td> <input type='text' onkeypress="return SomenteNumero(event)" name="radius" id='grade' value="" size=5> </td>
							</tr> <tr>
								<td> Numero de nos </td>
								<td> <input type="text" onkeypress="return SomenteNumero(event)" name="size" id="grade" value="" size=5> </td>
							</tr> <tr>
								<td> Numero de interfaces de cada nó (interfaces) </td>
								<td> <input type='text' onkeypress="return SomenteNumero(event)" name="interface" id='interface' value="" size=5> </td>
							</tr> <tr>
								<td>Tipo de traces</td>
								<td>
									<input type="checkbox" name="xml">XML<br>
									<input type="checkbox" name="pcap">PCAP<br>
								</td>
							</tr> 
						</table>
				</div>
			</div>
		</div>
		<table width="100%">
			<tr width="100%"> 
				<td width="50%" align="right"> Descrição: </td> 
				<td width="50%"> <textarea name="user_description" rows="3" cols="20" ></textarea>  </td>
			</tr> <tr width="100%">
				<td width="50%"><input type="submit" id="submit_bttn" value="Simular" class="btn btnPrimary " id="enviar" /></td>
				<td width="50%"><input type="reset" value="Cancelar" class="btn btnPrimary" /></td>
			</tr>
		</table>
	</form> </center> 
	
	<fieldset>
		<legend>Simulações</legend>
			<table class="table table-consulta">
				<thead>
					<tr>
						<th>Inicio da Simulação</th>
						<th>status</th>
						<th>Programa</th>
						<th>Parametros da simulação</th>
						<th>Baixar</th>
						<th>Excluir</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$allocated_infra = infra_controller::get_allocated_infrastructure($nome_user);
						$flag = false;
						foreach( $allocated_infra as $infra )	{
							if (!$infra->is_ready()) continue;
							$jobs = $infra->get_jobs();
//							var_dump($jobs);
							foreach ($jobs as $jobID => $job) {
								$dataInicio = $job->get_start_date();
								$params = $job->get_params();
								$runn = $job->is_running();
								$description = infra_controller::job_to_description($infra, $job);
								echo "<tr>
										<input type='hidden' value=".$description." />
										<td> $dataInicio </td>
										<td>".( $runn ? "<img title='executando' src='img/carregando.gif'>" : "<img title='finalizada' src='img/check.svg'>" )."</td>
										<td> <img class=\"app_img\" height=\"1000\" width=\"1000\" src=applications/".trim($job->get_app()).".png /> </td>
										<td> $params </td>
										<td width='15%'><a href='executaComando.php?down=$description'><img src=img/dowloads.jpg height=25 title=Download></a></td>
										<td width='15%'><a class='delBttn') title=''><img src=img/excluir.jpg height=25 title=Excluir></a></td>
									</tr>";
								$flag = true;
							}
						}
						if($flag) {
							echo"<tr>
									<th colspan='6	'> <a id='cleanBttn'> Limpar <img src=img/apagarTudo.png height=25 title=Excluir> </a> </th>
								</tr>";
						}else{
							echo"<tr>
									<th colspan='6'>Não foi localizado nenhum arquivo</th>
								</tr>";
						}
					?>
				</tbody> </table> <br/> </fieldset>
		</div>
		<div id="footer"><?php include "rodape.php";?></div>
	</body>
</html>
