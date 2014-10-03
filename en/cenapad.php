<?php
	chdir('..');
	include("defaults.php");
	include_once ("seguranca3.php"); // Inclui o arquivo com o sistema de segurança
	include_once 'infra_handler.php';
	protegePagina(); // Chama a função que protege a página
	$nome_user=$_SESSION['usuarioNome'];//variavel que contem o login do usuario logado
	$data_atual = date('Y-m-d'); //Data do sistema
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="refresh" content="600">
		<?php print_title(); ?>
		<link rel="shortcut icon" href="../css/images/cenapad.png" type="image/gif" />
		<link rel="stylesheet" href="../css/start/jquery-ui-1.10.4.custom.min.css" />
		<link rel="stylesheet" href="../css/styles.css" type="text/css" media="all">
		<script type="text/javascript" src="../js/jquery-1.10.2.js"></script>
		<script type="text/javascript" src="../javascripts/script.js"></script>
		<script type="text/javascript" src="../js/jquery-ui-1.10.4.custom.min.js"></script>

		<script type='text/javascript'>
			var updateBlocked = false;
			var submitBlocked

			function removeRow(elem) {
				$(elem).parents('tr').remove();
			}

			function mostrar_barra() {
				window.onbeforeunload = function (e) {
				  var confirmationMessage = "Your last command may not complete if you close or reload this page.";
				
				  (e || window.event).returnValue = confirmationMessage;     //Gecko + IE
				  return confirmationMessage;                                //Webkit, Safari, Chrome etc.
				}
				document.body.style.cursor='wait';
				$('#load').show('fast');
			}

			function ocultar_barra() {
				document.body.style.cursor='auto';
				window.onbeforeunload = null;
				$('#load').hide('fast');
			}

			function alertFail(d) {
				alert('Something went wrong...\n'+d.responseText)
			}

			function updateJobStatus() {
				if (updateBlocked) {
					return;
				}
				updateBlocked = true;
				$('#updateJobStatus img').attr('src', '../img/dinamic_job_status.gif');
				$.ajax( {
					url: '../job_status.php',
					dataType: 'xml',
					success: function (data) {
						var table = $('.table-consulta tbody');
						table.children().remove();
						var Jobs = $(data).find('job');
						Jobs.each( function () {
							var j = $(this);
							var jobRow = "<tr>";
							jobRow += '<input type="hidden" value="'+j.find('description').text().trim()+'">';
							jobRow += '<td> '+ j.find('startDate').text().trim() + ' </td>';
							var pic = j.find('isrunning').text().trim() == 'true' ? "<img title='Running' src='../img/carregando.gif'>" : "<img title='Finished' src='../img/check.svg'>";
							jobRow += "<td> "+ pic + "</td>"
							jobRow += '<td> <img class="app_img" src="../applications/'+ j.find('application').text().trim() +'.png" /> </td>';
							jobRow += "<td> " + j.find('params').text().trim() + " </td>";
							jobRow += '<td width="15%"> <a href="../executaComando.php?down=' + j.find('description').text().trim() + '"><img src="../img/dowloads.jpg" height=25 title=Download></a></td>';
							jobRow += "<td width='15%'> <a class='delBttn' title=''> <img src=../img/excluir.jpg height=25 title=Delete> </a> </td>";
							table.append(jobRow);
						});

						if (Jobs.length != 0) {
							table.append("<tr> <th colspan='6'> <a id='cleanBttn'> Clean all <img src=../img/apagarTudo.png height=25 title=Delete all simulations> </a> </th> </tr>");
						} else {
							table.append("<tr> <th colspan='6'>No simulation found</th> </tr>");
						}

						$('#updateJobStatus img').attr('src', '../img/static_job_status.gif');
						loadTableActions();
						updateBlocked = false;
						document.body.style.cursor='default';
					},
					error: alertFail
				});
			}

			function clickUpdate() {
				document.body.style.cursor='wait';
				updateJobStatus();
			}

			function loadTableActions() {
				$('.delBttn').click(function () {
					var description = $(this).parent().siblings('input[type=hidden]').val();
					jQuery.ajax({
						url: "../executaComando.php",
						data: { excluir: description.trim() },
						complete: ocultar_barra,
						beforeSend: mostrar_barra,
						method: 'GET',
						error: alertFail,
						success: clickUpdate
					});
				});

				$("#cleanBttn").click( function() {
					jQuery.ajax({
						url: "../executaComando.php",
						data: { rmAll:"" },
						method: 'GET',
						complete: ocultar_barra,
						beforeSend: mostrar_barra,
						error: alertFail,
						success: clickUpdate
					});
				});
			}

			$( function() {
				$('.help_container .help_contents').hide();
				$('.help_container .help_bar').click( function () {
					$(this).siblings('.help_contents').toggle(700);
				});

				$('.tab').tabs();
				$('.progressbar').progressbar().progressbar('value', false);
				$('#load').hide();

				$('#namdCustomParams').change( function() {
					var option = $(this).find("option:selected");
					var name = option.attr('name');
					var val = option.val();

					if($('#namdCustomParamsTable input[name='+name+']').length) return;
					$('#namdCustomParamsTable').append('<tr> <td>'+ name +'</td> <td> <input type="text" name="'+name+'" value="'+ val +'"> </td> <td> <a onclick="removeRow(this)"> <img src="../img/excluir.png" alt="remover parâmetro"> </a> </td> </tr>');
				});
				$('#form1').submit( function(event) {
					event.preventDefault();
					if (submitBlocked) {
						return;
					}
					submitBlocked = true;
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
						url: '../executaComando.php',
						data: formData,
						method: 'POST',
						cache: false,
						contentType: false,
						processData: false,
						error: alertFail,
						beforeSend: mostrar_barra,
						complete: function() { submitBlocked = false; ocultar_barra(); },
						success: updateJobStatus
					});
				});
				$('.help-ico').tooltip();

				function clickUpdate() {
					document.body.style.cursor='wait';
					updateJobStatus();
				}

				$('#updateJobStatus').click( clickUpdate );

				updateJobStatus();

				setInterval(updateJobStatus, 30000);
			});
		</script>
	</head>

	<body style="padding:0; margin:0">

	<div id="header"></div>

	<?php include_once 'navbar.php';
			 require 'app_assets/namd/default_namd_params.php';
	?>
	<div id="midsection2">

	<form id="form1" class="tab validate formLogin" method="post" enctype="multipart/form-data"> <input type='hidden' value=''>
		<ul>
			<li><a href="#ns3"> NS3 </a> </li>
			<!--<li><a href="#namd"> Namd </a> </li>
			<li><a href="#gaussian"> Gaussian </a> </li>
			<li><a href="#siesta"> Siesta </a> </li> -->
			<li><a href="#octave"> Octave </a> </li>
		</ul>

		<!-- <div id='gaussian'> <input type='hidden' value='gaussian'> Não implementado! </div>
		<div id='siesta'> <input type='hidden' value='siesta'> Não implementado! </div> -->
		<div id='octave' class="tab"> <input type='hidden' value='octave'>
			 <ul>
			 	<li> <a href="#octave-generic"> generic script </a> </li>
			 </ul>
			 
			 <div id="octave-generic"> <input type='hidden' value='generic'>
				 <table>
						<tr>
							<td> Main script file </td>
							<td> <input name="scriptFile" type="file"> </td>
						</tr> <tr>
							<td width="50%"> execution parameters for the script </td>
							<td width="50%"> <input size="50%" name="param_str" type="text"> </td>
						</tr> <tr>
							<td> Other necessary files </td>
							<td> <input name="aux_files[]" type="file" size="10" multiple> </td>
						</tr> 
					</table>
					<div class="help_container">
						<div class="help_bar"> Help <img src="../img/dropdown.png"></div>
						<div id="dialog" class="help_contents">
							<p> This mode allows you to run any script you desire,
						you need to upload the main script file, add the parameters you want to add to that execution and any other files tou need for that execution, like files with data or source code. </p>

						<p>For Example, if you want to run something like "octave myScript.m param1 param2" <br>
							you need to:
							<ol>
								<li> upload the file myScript.m, </li>
								<li> write "param1 param2" in the text field where the parameters are requested and</li>
								<li> upload any other file you need for the execution of the script, they will be put on the same directory of the main script file. </li>
							</ol>
						</p>
						</div>
					</div>
			 </div>
		</div>

		<!--<div id='namd' class="tab"> <input type='hidden' value='namd'>
			<ul>
				<li> <a href="#norun"> Minimização </a> </li>
				<li> <a href="#singlerun"> Execução simples </a> </li>
				<li> <a href="#multiplerun"> Execução multipla </a> </li>
			</ul>

			<div id='norun' class="tab"> <input type='hidden' value='norun'>
				<div class="help"> <span class="red"> IMPORTANTE: </span> <br> Neste formato de simulação não é executado nenhuma dinamica, isso é, o valor de 'run' é zerado no .conf final.</div>
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
				<div class="help"> <span class="red"> IMPORTANTE: </span> <br> Neste Formato de simulação a quantidade total de passos definida no parametro 'run' é dividida igualmente entre a quantidade de divisões desejadas.</div>
				<ul>
					<li> <a href="#start_multiplerun"> Iniciar nova simulação </a> </li>
					<li> <a href="#continue_multiplerun"> Continuar simulação </a> </li>
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
				<option disabled selected> Por favor selecione os parâmetros que deseja customizar </option>
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
					<td>Arquivo INP ou XPLOR com parâmetros</td>
					<td><input type="file" name="inpFile" accept=".inp,.xplor"></td>
				</tr>
			</table>

			<table id="namdCustomParamsTable" > 	</table>

		</div>
		-->
		<div id='ns3' class='tab'> <input type='hidden' value='ns3'>
			<ul>
				<li><a href="#mesh_tab"> Mesh Networks </a> </li>
				<!--<li><a href="#lte"> LTE Networks </a> </li>
				<li><a href="#vannet"> VANNET Networks </a> </li>-->
				<li><a href="#generic"> Generic script </a> </li>
			</ul>
			<div id='generic'> <input type='hidden' value='generic'>
				<table>
					<tr>
						<td> NS3 script file </td>
						<td> <input name="scriptFile" type="file"> </td>
					</tr>	<tr>
						<td width="50%"> Parameters for the simulation </td>
						<td width="50%"> <input size="50%" name="param_str" type="text"> </td>
					</tr>
				</table>
				<div class="help_container">
					<div class="help_bar"> Help <img src="../img/dropdown.png"></div>
					<div id="dialog" class="help_contents">
						<p> This mode allows you to run any NS3 script you desire,
						you need to upload the .cc code file and add the parameters you want to add to that simulation. </p>

						<p>For Example, if you want to run something like ./waf --run myScript --param1=1 --param2=2 <br>
						you need to upload the file myScript.cc and write "--param1=1 --param2=2" in the text field where the parameters are requested.</p>
					</div>
				</div>
			</div>
			<!--<div id='lte'> <input type='hidden' value='lte'> Não implementado! </div>
			<div id='vannet'> <input type='hidden' value='vannet'> Não implementado! </div> -->
			<div id='mesh_tab' class='tab'>
				<input type='hidden' value="mesh">
				<ul>
					<li> <a href="#grid"> Grid topology </a> </li>
					<li> <a href="#uniform_disc"> Random disc topology </a> </li>
				</ul>

				<div id='grid'> <input type='hidden' value="grid">
						<img class='mesh-help-pic' src="../img/mesh-grid.jpg">
						<h1> Simulation Parameters </h1>
						<table class="table-cadastro">
							<tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the number of nodes in each line of the grid."> </td>
								<td> Horizontal size of the disc (x-size) </td>
								<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="x-size" value="5"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the number of nodes in each column of the grid."> </td>
								<td> Vertical size of the disc (y-size) </td>
								<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="y-size" value="5"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the physical distance between nodes of the grid, this distance is constant vertically and horizontally."> </td>
								<td> Distance among nodes (step)</td>
								<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="step" value="100"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the total time to be simulated, the time to run the simulation is usually larger than the simulated time."> </td>
								<td> Simulation Time (seconds)</td>
								<td> <input type='number' min="10" onkeypress="return SomenteNumero(event)" name="time" value="100"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the number of radio interfaces connected to each node."> </td>
								<td> Number of radio interfaces in each node </td>
								<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name='interfaces' value="1"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the wait time between each packet transmission on the simulation."> </td>
								<td> Packet transmission interval (seconds) </td>
								<td> <input type='number' min="0.001" step="0.001" onkeypress="return SomenteNumero(event)" name='packet-interval' value="0.001"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Byte size of each packet to be sent on the network."> </td>
								<td> Packet size (Bytes) </td>
								<td> <input type='number' min="128" step="128" onkeypress="return SomenteNumero(event)" name='packet-size' value="1024"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="'Complete Spread' puts each radio interface on a separated wireless channel while 'Same channel' puts every interface of the nodes on the same interface"> </td>
								<td> Channel allocation strategy (channels) </td>
								<td>
									<select name="channels">
										<option value="1" selected> Complete spread </option>
										<option value="0" > Same channel </option>
								   </select>
								</td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Choose which trace information must be saved, XML traces contain node information like neighboors and routing statistics, PCAP traces contain all the packets sent by each interface and Graphs are visual representation of the XML traces, nodes positions and FlowMonitor results."> </td>
								<td>Select desired traces</td>
								<td>
									<input type="checkbox" checked name="xml" value="1">XML<br>
									<input type="checkbox" name="pcap" value="1">PCAP<br>
									<input type="checkbox" checked name="graphs" value="1">Graphs<br>
								</td>
							</tr>
						</table>

						<div class="help_container">
							<div class="help_bar"> Help <img src="../img/dropdown.png"></div>
							<div id="dialog" class="help_contents">
								<p>
									In this mode, ns3 will run a simulation where the nodes will be positioned equally spaced among themselves following a basic grid topology.
									The amount of nodes of the grid is controlled by the parameters <b>x-size</b> and <b>y-size</b>.
								</p>
								<p>
									<b>Only two flows will be created</b>, from one end to the other of the grid and back, between the client and the server,
									according to the ns3 application Udp Echo.
									The flow size can be controlled from the variables Packet size and Packet transmission interval.
								</p>
								<p>
									The channel allocation strategy will determinate which channel each interface antenna of each node will be tuned to.
									<ul>
										<li>
										With strategy <b>Complete spread</b>, each interface will be tuned to a different channel, but these channels will be the same for every node,
										i.e in node 1, interface 1 will be tuned to channel 1, interface 2 will be tuned to channel 2, etc, the same is true for node 2 and every other node.
										</li>
										<li>
										With strategy <b>Same channel</b>, all interfaces of every node will be tuned to the same channel.
										</li>
									</ul>
								</p>
								<p>
									Possible traces to be generated are:
									<ul>
										<li>
											<b>XML</b>, these traces are the XML files created when the report method is called on the MeshHelper class on ns3,
											this is required to generate graphics for the routing protocol metrics.
										</li>
										<li>
											<b>PCAP</b>, these are the entire dump of each interface network traffic. These can be read by wireshark.
										</li>
										<li>
											<b>Graphs</b>, these are visual illustrations of metrics extracted from the simulation,
											this requires other traces to be generated. <br>
											Among the graphics generated there are the positions and connections of the nodes and
											each metric of the routing protocol is plotted as a heat graph in the area of the simulation.
										</li>
									</ul>
								</p>
							</div>
						</div>
				</div>
				<div id='uniform_disc'> <input type='hidden' value="uniform_disc">
					<img class='mesh-help-pic' src="../img/mesh-uniform_disc-en.png">
					<h1> Simulation Parameters </h1>
					<table class="table-cadastro">
						<tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Number of nodes to be created and allocated on the disc."> </td>
							<td> Number of nodes </td>
							<td> <input type='number' min="2" onkeypress="return SomenteNumero(event)" name="number-of-nodes" value="10"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Radious of the disc that will bound the allocation of the nodes, these nodes are allocated in a random matter."> </td>
							<td> Radius of the disc (meters) </td>
							<td> <input type='number' min="25" step="25" onkeypress="return SomenteNumero(event)" name="radius" value="100"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the total time to be simulated, the time to run the simulation is normally larger than the simulated time."> </td>
							<td> Simulation time (seconds) </td>
							<td> <input type='number' min="10" onkeypress="return SomenteNumero(event)" name="time" value="100"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the number of flows to be created on the simulation, every flow has the same destination(server) but have different origins(clients)"> </td>
							<td> Number of flows to be generated </td>
							<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="flows" value="1"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the number of radio interfaces conected on each node."> </td>
							<td> Number of radios interfaces per node </td>
							<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="interfaces" value="1"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Represents the wait time between each packet transmission on the simulation."> </td>
							<td> Time interval between packet transmission (seconds) </td>
							<td> <input type='number' min="0.001" step="0.001" onkeypress="return SomenteNumero(event)" name='packet-interval' value="0.001"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="KByte size of each packet to be sent on the network."> </td>
							<td> Packet size (KBytes) </td>
							<td> <input type='number' min="128" step="128" onkeypress="return SomenteNumero(event)" name='packet-size' value="1024"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="'Complete Spread' puts each radio interface on a separated wireless channel while 'all on zero' puts every interface of the nodes on the same interface"> </td>
							<td> Channel allocation strategy (channels) </td>
							<td>
								<select name="channels">
									<option value="1" selected> Complete spread </option>
									<option value="0" > Same channel </option>
							   </select>
							</td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Choose which trace information must be saved, XML traces contain node information like neighboors and routing statistics, PCAP traces contain all the packets sent by each interface and Graphs are visual representation of the XML traces, nodes positions and FlowMonitor results."> </td>
							<td>Select desired traces</td>
							<td>
								<input type="checkbox" value="1" checked name="xml">XML<br>
								<input type="checkbox" value="1" name="pcap">PCAP<br>
								<input type="checkbox" checked name="graphs" value="1">Graphs<br>
							</td>
						</tr>
					</table>

					<div class="help_container">
						<div class="help_bar"> Help <img src="../img/dropdown.png"></div>
						<div id="dialog" class="help_contents">
							<p> In this mode, ns3 will run a simulation where the nodes will be positioned in a circular area, the size of the area is controlled by the variable <b>radius of the disc</b>.
							The positioning will be determined by the position allocator <b>UniformDiscPositionAllocator</b> which is provided by NS3 </p>

							<p> Each flow of the simulation have the same <b> destination (server) </b> but has a random node as <b> origin (client) </b>.
							The flow size can be controlled from the variables Packet size and Packet transmission interval. </p>

							<p>The channel allocation strategy will determinate which channel each interface antenna of each node will be tuned to.
							<ul>
								<li>
								With strategy <b>Complete spread</b>, each interface will be tuned to a different channel, but these channels will be the same for every node,
								i.e in node 1, interface 1 will be tuned to channel 1, interface 2 will be tuned to channel 2, etc, the same is true for node 2 and every other node.
								</li>
								<li>
								With strategy <b>Same channel</b>, all interfaces of every node will be tuned to the same channel.
								</li>
							</ul></p>

							<p> Possible traces to be generated are:
							<ul>
								<li>
									<b>XML</b>, these traces are the XML files created when the report method is called on the MeshHelper class on ns3,
									this is required to generate graphics for the routing protocol metrics.
								</li>
								<li>
									<b>PCAP</b>, these are the entire dump of each interface network traffic. These can be read by wireshark.
								</li>
								<li>
									<b>Graphs</b>, these are visual illustrations of metrics extracted from the simulation,
									this requires other traces to be generated. <br>
									Among the graphics generated there are the positions and connections of the nodes and
									each metric of the routing protocol is plotted as a heat graph in the area of the simulation.
								</li>
							</ul> </p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<table width="100%">
			<!--<tr width="100%">
				<td width="50%" align="right"> Descrição: </td>
				<td width="50%"> <textarea name="user_description" rows="3" cols="20" > Não Funciona </textarea>  </td>
			</tr>--> <tr width="100%">
				<td width="50%"><input type="submit" id="submit_bttn" value="Simulate" class="btn btnPrimary " id="enviar" /></td>
				<td width="50%"><input type="reset" value="Cancel" class="btn btnPrimary" /></td>
			</tr>
		</table>
	</form>
	<div id='load' class='progressbar'> </div>
	<fieldset>
		<legend>
			Simulations
			<a id="updateJobStatus"> <img title="Update status" src="../img/static_job_status.gif" alt="update"> </a>
		</legend>
			<table class="table table-consulta">
				<thead>
					<tr>
						<th>Start of the Simulation</th>
						<th>Status</th>
						<th>Application</th>
						<th>Simulation Parameters</th>
						<th>Download</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th colspan='6'> Wait while your simulations are loaded </th>
					</tr>
				</tbody> </table> <br/> </fieldset>
		</div>
		<div id="footer"><?php include "rodape.php";?></div>
	</body>
</html>
