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

<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php print_title(); ?>
		<link rel="shortcut icon" href="../css/images/cenapad.png" type="image/gif" />
		<link rel="stylesheet" href="../css/start/jquery-ui-1.10.4.custom.min.css" />
		<link rel="stylesheet" href="../css/styles.css" type="text/css" media="all">
		<script type="text/javascript" src="../js/jquery-1.10.2.js"></script>
		<script type="text/javascript" src="../javascripts/script.js"></script>
		<script type="text/javascript" src="../js/jquery-ui-1.10.4.custom.min.js"></script>

		<script type='text/javascript'>
			var updateBlocked = false;
			var submitBlocked = false;

			function removeRow(elem) {
				$(elem).parents('tr').remove();
			}

			function mostrar_barra() {
				window.onbeforeunload = function (e) {
				  var confirmationMessage = "Seu ultimo comando pode não finalizar caso feche ou recarrege esta página agora.";
				
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
				alert('algo deu errado...\n'+d.responseText)
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
							jobRow += '<input type="hidden" value="'+j.find('description').text()+'">';
							jobRow += '<td> '+ j.find('startDate').text() + ' </td>';
							var pic = j.find('isrunning').text().trim() == 'true' ? "<img title='executando' src='../img/carregando.gif'>" : "<img title='finalizada' src='../img/check.svg'>";
							jobRow += "<td> "+ pic + "</td>"
							jobRow += '<td> <img class="app_img" src="../applications/'+ j.find('application').text().trim() +'.png" /> </td>';
							jobRow += "<td> " + j.find('params').text() + " </td>";
							jobRow += '<td width="15%"> <a href="../executaComando.php?down=' + j.find('description').text().trim() + '"><img src="../img/dowloads.jpg" height=25 title=Download></a></td>';
							jobRow += "<td width='15%'> <a class='delBttn' title=''> <img src=../img/excluir.jpg height=25 title=Excluir> </a> </td>";
							table.append(jobRow);
						});

						if (Jobs.length != 0) {
							table.append("<tr> <th colspan='6'> <a id='cleanBttn'> Limpar <img src=../img/apagarTudo.png height=25 title=Excluir> </a> </th> </tr>");
						} else {
							table.append("<tr> <th colspan='6'>Não foi localizado nenhum arquivo</th> </tr>");
						}

						$('#updateJobStatus img').attr('src', '../img/static_job_status.gif');
						loadTableActions();
						updateBlocked = false;
						document.body.style.cursor='default';
					},
					error: alertFail
				});
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
						success: updateJobStatus
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
						success: updateJobStatus
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
	    				for (var i = 0; i < $(this).prop('files').length; ++i) {
	    					formData.append(this.name, $(this).prop('files')[i]);
						}
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

				clickUpdate();
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
			<li><a href="#namd"> Namd </a> </li>
			<!--<li><a href="#gaussian"> Gaussian </a> </li> -->
			<li><a href="#autodock"> AutoDock </a> </li>
			<li><a href="#octave"> Octave </a> </li>
		</ul>

		<!--<div id='gaussian'> <input type='hidden' value='gaussian'> Não implementado! </div> -->
		
		<div id='autodock' class="tab"> <input type='hidden' value='autodock'>
			<ul>
			 	<li> <a href="#autodock-generic"> genérico </a> </li>
			 </ul>
			 
			 <div id="autodock-generic"> <input type='hidden' value='generic'>
				 <table width="100%">
						<tr>
							<td> Arquivos DAT de parâmetros </td>
							<td> <input name="datFiles[]" type="file" multiple> </td>
						</tr> <tr>
							<td> Arquivo GPF </td>
							<td> <input name="gpfFile" type="file"> </td>
						</tr>
							<td> Arquivos DPFs </td>
							<td> <input name="pdfFiles[]" type="file" multiple> </td>
						</tr> <tr>
							<td> Arquivo PDBQT da macromolécula </td>
							<td> <input name="mainpdbqtFile" type="file"> </td>
						</tr> <tr>
							<td> Arquivos PDBQT dos ligantes </td>
							<td> <input name="otherpdbqts[]" type="file" multiple> </td>
						</tr> <tr>
							<td> Quantidade de rodadas do autodock </td>
							<td> <input name="autodockRuns" type="number" value="1" min="1" max="10"> </td>
						</tr> 
					</table>
					<div class="help_container">
						<div class="help_bar"> Help <img src="../img/dropdown.png"></div>
						<div id="dialog" class="help_contents">
							<p> Este modo permite ao usuário executar qualquer script que desejar, desde que esteja em acordo com a versão padrão do octave,
							o usuário só precisa fazer upload do arquivo com o código de script e indicar quais parâmetros devem ser passados à simulação.</p>
	
							<p> Por exemplo, se você deseja executar algo assim: octave myScript.m param1 param2 <br>
							Então é necessário que o arquivo myScript.m seja enviado e que seja escrito "param1 param2" no campo referente aos parâmetros. </p>
						</div>
					</div>
			 </div>
		</div>
		
		<div id='octave' class="tab"> <input type='hidden' value='octave'>
			 <ul>
			 	<li> <a href="#octave-generic"> genérico </a> </li>
			 </ul>
			 
			 <div id="octave-generic"> <input type='hidden' value='generic'>
				 <table>
						<tr>
							<td> Arquivo de script principal </td>
							<td> <input name="scriptFile" type="file"> </td>
						</tr> <tr>
							<td width="50%"> Parâmetros que devem ser passados ao script </td>
							<td width="50%"> <input size="50%" name="param_str" type="text"> </td>
						</tr> <tr>
							<td> Outros arquivos necessários </td>
							<td> <input name="aux_files[]" type="file" size="10" multiple> </td>
						</tr> 
					</table>
					<div class="help_container">
						<div class="help_bar"> Help <img src="../img/dropdown.png"></div>
						<div id="dialog" class="help_contents">
							<p> Este modo permite ao usuário executar qualquer script que desejar, desde que esteja em acordo com a versão padrão do octave,
							o usuário só precisa fazer upload do arquivo com o código de script e indicar quais parâmetros devem ser passados à simulação.</p>
	
							<p> Por exemplo, se você deseja executar algo assim: octave myScript.m param1 param2 <br>
							Então é necessário que o arquivo myScript.m seja enviado e que seja escrito "param1 param2" no campo referente aos parâmetros. </p>
						</div>
					</div>
			 </div>
		</div>
		
		<div id='namd' class="tab"> <input type='hidden' value='namd'> <center>
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
			</center>
		</div>

		<div id='ns3' class='tab'> <input type='hidden' value='ns3'>
			<ul>
				<li><a href="#mesh_tab"> Redes Mesh </a> </li>
				<!--<li><a href="#lte"> Redes LTE </a> </li>
				<li><a href="#vannet"> Redes VANNETS </a> </li>-->
				<li><a href="#generic"> Script Generico </a> </li>
			</ul>
			<div id='generic'> <input type='hidden' value='generic'>
				<table>
					<tr>
						<td> Arquivo de script ns3 </td>
						<td> <input name="scriptFile" type="file"> </td>
					</tr>	<tr>
						<td width="50%"> Parametros que devem ser passados ao script </td>
						<td width="50%"> <input size="50%" name="param_str" type="text"> </td>
					</tr>
				</table>
				<div class="help_container">
					<div class="help_bar"> Help <img src="../img/dropdown.png"></div>
					<div id="dialog" class="help_contents">
						<p> Este modo permite ao usuário executar qualquer simulação que desejar, desde que esteja em acordo com a versão padrão do ns3,
						o usuário só precisa fazer upload do arquivo .cc e indicar quais parâmetros devem ser passados à simulação.</p>

						<p> Por exemplo, se você deseja executar algo assim: ./waf --run 'myScript --param1=1 --param2=2' <br>
						Então é necessário que o arquivo myScript.cc seja enviado e que seja escrito "--param1=1 --param2=2" no campo referente aos parâmetros. </p>
					</div>
				</div>
			</div>
			<!--<div id='lte'> <input type='hidden' value='lte'> Não implementado! </div>
			<div id='vannet'> <input type='hidden' value='vannet'> Não implementado! </div>-->
			<div id='mesh_tab' class='tab'>
				<input type='hidden' value="mesh">
				<ul>
					<li> <a href="#grid"> Topologia em grade </a> </li>
					<li> <a href="#uniform_disc"> Topologia em disco aleatório </a> </li>
				</ul>

				<div id='grid'> <input type='hidden' value="grid">
						<img class='mesh-help-pic' src="../img/mesh-grid.jpg">
						<h1> Parâmetros da simulação </h1>
						<table class="table-cadastro">
							<tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Representa o número de nós por linha da grade."> </td>
								<td> Tamanho Horizontal da grade (x-size) </td>
								<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="x-size" value="5"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Representa o número de nós por coluna da grade."> </td>
								<td> Tamanho vertical da grade (y-size) </td>
								<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="y-size" value="5"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Representa a distância física entre os nós da grade, essa distância é constante verticalmente e horizontalmente."> </td>
								<td> Espaço entre nós (step)</td>
								<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="step" value="100"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Representa o tempo total simulado, observação o tempo de para executar a simulação normalmente é bem maior que o tempo simulado."> </td>
								<td> Tempo de simulação (segundos)</td>
								<td> <input type='number' min="10" onkeypress="return SomenteNumero(event)" name="time" value="100"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Representa o número de interfaces de rádio que estão conectadas a cada nó."> </td>
								<td> Número de interfaces de rádio por nó </td>
								<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name='interfaces' value="1"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Representa o tempo de espera entre cada envio de pacote."> </td>
								<td> Intervalo de tempo entre transmissão pacotes (segundos) </td>
								<td> <input type='number' min="0.001" step="0.001" onkeypress="return SomenteNumero(event)" name='packet-interval' value="0.001"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Representa a quantidade de KBytes enviados em cada pacote da simulação"> </td>
								<td> Tamanho dos pacotes (Bytes) </td>
								<td> <input type='number' min="128" step="128" onkeypress="return SomenteNumero(event)" name='packet-size' value="1024"> </td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="'Complete Spread' coloca cada rádio em um canal sem fio diferente enquanto 'all on zero' coloca todas as interfaces de rádio trabalhando em um mesmo canal sem fio."> </td>
								<td> Política de escolha de canais (channels) </td>
								<td>
									<select name="channels">
										<option value="1" selected> Complete spread </option>
										<option value="0" > Same channel </option>
								   </select>
								</td>
							</tr> <tr>
								<td> <img class="help-ico" src="../img/help-icon.png" title="Escolha quais tipos de dados devem ser armazenados. Os Traces XML contém informações dos nós, como roteamento e vizinhança, enquanto os traces PCAP possuem todos os pacotes enviados por cada interface"> </td>
								<td>Tipos de trace desejado</td>
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
									Neste modo, será executada uma simulação onde os nós serão posicionados igualmente espaçados entre si,
									i.e seguindo uma topologia em grade padrão.
									O numero de nós é controlado pelas variáveis <b>x-size</b> e <b>y-size</b>.
								</p>
								<p>
									<b>Apenas 2 fluxos serão criados neste modelo de simulação</b>, entre cliente e servidor,
									que ficam localizados no canto inferior esquerdo e canto superior direito da grade respectivamente,
									seguindo o modelo de aplicação de rede udp echo disponibilizado no NS3. <br>
							 		O tamanho de cada fluxo pode ser controlado através das variáveis tamanho do pacote e intervalo entre transmissão de pacotes.
								</p>
								<p>
									A politica de aplicação de canais determina em qual canal cada interface de cada nó irá ser sintonizada.
									<ul>
										<li>
											Pela politica <b> Complete spread </b> cada interface será sintonizada em um canal diferente,
											mas estes canais serão os mesmos para todos os nós, i.e no nó 1, a interface 1 ficará sintonizada no canal 1,
											a interface 2 ficará sintonizada no canal 2, etc, o mesmo vale para os outros nós.
										</li>
										<li>
											Pela politica <b> Same channel </b> todas as interfaces de todos os nós serão sintonizadas no mesmo canal.
										</li>
									</ul>
								</p>
								<p>
									Os traces disponíveis de serem gerados são:
									<ul>
										<li>
											<b> XML </b>, Estes arquivos XML são criados pelo método report da classe MeshHelper disponibilizada pelo ns3,
											eles possuem informações como métricas do protocolo de roteamento e conexões dos nós
											e elas são necessárias para que estas informações sejam desenhadas pela opção graphs.
										</li>
										<li>
											<b> PCAP </b>, estes arquivos possuem todo o tráfico de cada interface de rede da simulação
											e podem ser visualizados utilizando programas como o Wireshark.
										</li>
										<li>
											<b> Graphs </b>, esta opção habilita a geração de arquivos gráficos ilustrando métricas extraídas da simulação,
											é necessário que outros traces sejam gerados para que esta opção funcione.
											Entre os gráficos gerados temos o posicionamento físico dos nós e as conexões entre os nós.
											Cada métrica do protocolo de roteamento é desenhada no formato de um gráfico de calor sobre a área da simulação.
										</li>
									</ul>
								</p>
							</div>
						</div>
				</div>
				<div id='uniform_disc'> <input type='hidden' value="uniform_disc">
					<img class='mesh-help-pic' src="../img/mesh-uniform_disc-pt.png">
					<h1> Parâmetros da simulação </h1>
					<table class="table-cadastro">
						<tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Número de nós que devem ser criados e alocados dentro do disco."> </td>
							<td> Número de nós </td>
							<td> <input type='number' min="2" onkeypress="return SomenteNumero(event)" name="number-of-nodes" value="10"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Raio do disco que servirá como borda para a alocação dos nós, os nós são colocados aleatoriamente no disco."> </td>
							<td> Raio do disco (metros) </td>
							<td> <input type='number' min="25" step="25" onkeypress="return SomenteNumero(event)" name="radius" value="100"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Representa o tempo total simulado, observação, o tempo para executar a simulação normalmente é bem maior que o tempo simulado."> </td>
							<td> Tempo de simulação (segundos) </td>
							<td> <input type='number' min="10" onkeypress="return SomenteNumero(event)" name="time" value="100"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Número de fluxos de dados a serem criados entre os nós, cada fluxo possui uma origem (cliente) diferente, mas o destino (servidor) é o mesmo para todos os fluxos."> </td>
							<td> Número de fluxos de dados na simulação </td>
							<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="flows" value="1"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Representa o número de interfaces de rádio que estão conectadas a cada nó."> </td>
							<td> Número de interfaces de rádio por nó </td>
							<td> <input type='number' min="1" onkeypress="return SomenteNumero(event)" name="interfaces" value="1"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Representa o tempo de espera entre cada envio de pacote."> </td>
							<td> Intervalo de tempo entre transmissão pacotes (segundos) </td>
							<td> <input type='number' min="0.001" step="0.001" onkeypress="return SomenteNumero(event)" name='packet-interval' value="0.001"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Representa a quantidade de Bytes enviados em cada pacote da simulação"> </td>
							<td> Tamanho dos pacotes (Bytes) </td>
							<td> <input type='number' min="128" step="128" onkeypress="return SomenteNumero(event)" name='packet-size' value="1024"> </td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="'Complete Spread' coloca cada rádio em um canal sem fio diferente enquanto 'Same channel' coloca todas as interfaces de rádio trabalhando em um mesmo canal sem fio."> </td>
							<td> Politica de escolha de canais (channels) </td>
							<td>
								<select name="channels">
									<option value="1" selected> complete spread </option>
									<option value="0" > Same channel </option>
							   </select>
							</td>
						</tr> <tr>
							<td> <img class="help-ico" src="../img/help-icon.png" title="Escolha quais tipos de dados devem ser armazenados. Os Traces XML contém informações dos nós, como roteamento e vizinhança, enquanto os traces PCAP possuem todos os pacotes enviados por cada interface"> </td>
							<td>Tipos de trace desejado</td>
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
							<p>
								Neste modo de simulação, será executado uma simulação onde os nós são posicionados em uma área circular,
								o tamanho da área é definido através da variável <b> raio do disco </b>.
								O posicionamento dos nós será realizado através do componente <b>UniformDiscPositionAllocator</b> disponibilizado pelo NS3.
							</p>

							<p>
							 	Cada fluxo da simulação possui o mesmo <b>destino (servidor)</b> mas possui um nó aleatório como <b>origem (cliente)</b>. <br>
							 	O tamanho de cada fluxo pode ser controlado através das variáveis tamanho do pacote e intervalo entre transmissão de pacotes.
							<p>
								A politica de aplicação de canais determina em qual canal cada interface de cada nó irá ser sintonizada.
								<ul>
									<li>
										Pela politica <b> Complete spread </b> cada interface será sintonizada em um canal diferente,
										mas estes canais serão os mesmos para todos os nós, i.e no nó 1, a interface 1 ficará sintonizada no canal 1,
										a interface 2 ficará sintonizada no canal 2, etc, o mesmo vale para os outros nós.
									</li>
									<li>
										Pela politica <b> Same channel </b> todas as interfaces de todos os nós serão sintonizadas no mesmo canal.
									</li>
								</ul>
							</p>
								Os traces disponíveis de serem gerados são:
								<ul>
									<li>
										<b> XML </b>, Estes arquivos XML são criados pelo método report da classe MeshHelper disponibilizada pelo ns3,
										eles possuem informações como métricas do protocolo de roteamento e conexões dos nós
										e elas são necessárias para que estas informações sejam desenhadas pela opção graphs.
									</li>
									<li>
										<b> PCAP </b>, estes arquivos possuem todo o tráfico de cada interface de rede da simulação
										e podem ser visualizados utilizando programas como o Wireshark.
									</li>
									<li>
										<b> Graphs </b>, esta opção habilita a geração de arquivos gráficos ilustrando métricas extraídas da simulação,
										é necessário que outros traces sejam gerados para que esta opção funcione.
										Entre os gráficos gerados temos o posicionamento físico dos nós e as conexões entre os nós.
										Cada métrica do protocolo de roteamento é desenhada no formato de um gráfico de calor sobre a área da simulação.
									</li>
								</ul>
							</p>
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
				<td width="50%"><input type="submit" id="submit_bttn" value="Simular" class="btn btnPrimary " id="enviar" /></td>
				<td width="50%"><input type="reset" value="Cancelar" class="btn btnPrimary" /></td>
			</tr>
		</table>
	</form>
	<div id='load' class='progressbar'> </div>
	<fieldset>
		<legend>
			Simulações
			<a id="updateJobStatus"> <img title="Atualizar status" src="../img/static_job_status.gif" alt="atualizar"> </a>
		</legend>
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
					<tr>
						<th colspan='6'> Aguarde enquanto a lista das suas simulações é carregada </th>
					</tr>
				</tbody>
			</table> <br/>
		</fieldset>
		</div>
		<div id="footer"><?php include "rodape.php";?></div>
	</body>
</html>
