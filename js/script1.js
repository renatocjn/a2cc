		$(document).ready(function(){
			$(".btn-toggle").click(function(){
				if( $(".btn-toggle").hasClass("open") ){
					$(".btn-toggle").removeClass("open");	
				} else {
					$(this).toggleClass("open");
				}
			});
		});
		
		//Calendário
		$(function() {
		    $("#data_inicio").datepicker({
		        dateFormat: 'yy/mm/dd',
		        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
		        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
		        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
		        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
		    });
		});
		
		$(function() {
		    $("#data_fim").datepicker({
		        dateFormat: 'yy/mm/dd',
		        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
		        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
		        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
		        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
		    });
		});
		
		function toggle(obj) {
			var el = document.getElementById(obj);
			if ( el.style.display != "none" ) {
				el.style.display = 'none';
			}
			else {
				el.style.display = '';
			}
		}
		

		//Mostra campos  
		function showMe(){
			var ids=['didfv1','div2','div3','div4','div5'];
			var inp=document.getElementById('myform').getElementsByTagName('input'), el, i=0, k=0;
			while(el=inp[i++]){
				if(el.name=='mype'||el.name=='modtype'){
				document.getElementById(ids[k]).style.display=el.checked?'block':'none';
				k++;
				}
			}
			}
		
		//Criando campos
		//Total máximo de campos que você permitirá criar em seu site:
		var totalCampos = 11;

		//Não altere os valores abaixo, pois são variáveis controle;
		var iLoop = 1;
		var iCount = 0;
		var linhaAtual;


		function AddCampos() {
		var hidden1 = document.getElementById("hidden1");
		var hidden2 = document.getElementById("hidden2");

		//Executar apenas se houver possibilidade de inserção de novos campos:
		if (iCount < totalCampos) {

		//Limpar hidden1, para atualizar a lista dos campos que ainda estão vazios:
		hidden2.value = "";

		//Atualizando a lista dos campos que estão ocultos.
		//Essa lista ficará armazenada temporiariamente em hidden2;
		
		
		
		
		for (iLoop = 1; iLoop <= totalCampos; iLoop++) {
		        if (document.getElementById("linha"+iLoop).style.display == "none") {
		                if (hidden2.value == "") {
		                        hidden2.value = "linha"+iLoop;
		                }else{
		                        hidden2.value += ",linha"+iLoop;
		                }
		        }
		}
		//Quebrando a lista que foi armazenada em hidden2 em array:

		linhasOcultas = hidden2.value.split(",");


		        if (linhasOcultas.length > 0) {
		                //Tornar visível o primeiro elemento de linhasOcultas:
		                document.getElementById(linhasOcultas[0]).style.display = "block"; iCount++;
		                
		                //Acrescentando o índice zero a hidden1:
		                if (hidden1.value == "") {
		                        hidden1.value = linhasOcultas[0];
		                }else{
		                        hidden1.value += ","+linhasOcultas[0];
		                }
		                
		               
		        }
		}
		}
		
		function RemoverCampos(id) {
		//Criando ponteiro para hidden1:        
		var hidden1 = document.getElementById("hidden1");

		//Pegar o valor do campo que será excluído:
		var campoValor = document.getElementById("arq"+id).value;
		        //Se o campo não tiver nenhum valor, atribuir a string: vazio:
		        if (campoValor == "") {
		                campoValor = "vazio";
		        }

		    	if(confirm("O Participante:\n» "+campoValor+"\nserá removido!\n\nDeseja continuar?")){
		                document.getElementById("linha"+id).style.display = "none"; iCount--;
		                
		                //Removendo o valor de hidden1:
		                if (hidden1.value.indexOf(",linha"+id) != -1) {
		                        hidden1.value = hidden1.value.replace(",linha"+id,"");
		                }else if (hidden1.value.indexOf("linha"+id+",") == 0) {
		                        hidden1.value = hidden1.value.replace("linha"+id+",","");
		                }else{
		                        hidden1.value = "";
		                }
		        }
		}
		
		
		//coordenador
		function OpenSel(op) 
		{
				if (op == 1) 
				{
					document.getElementById('principal').style.display="block";
					document.getElementById('divum').style.display="none";
					document.getElementById('divdois').style.display="none";
				}
				else if (op == 2) 
				{
					document.getElementById('principal').style.display="none";
					document.getElementById('divum').style.display="block";
					document.getElementById('divdois').style.display="none";
				}
				else 
				{
					document.getElementById('principal').style.display="none";
					document.getElementById('divum').style.display="none";
					document.getElementById('divdois').style.display="block";
		        }
		}
		
		
		//Mostrao combo dentro da div do participante
		function id( el ){
		        return document.getElementById( el );
		}
		function mostra( el ){
		        id( el ).style.display = 'block';
		}
		function esconde_todos( el, tagName ){
		        var tags = el.getElementsByTagName( tagName );
		        for( var i=0; i<tags.length; i++ )
		        {
		                tags[i].style.display = 'none';
		        }
		}
		window.onload = function()
		{
		        id('Masculino').style.display = 'none';
		        id('Feminino').style.display = 'none';
		       
		        id('sel-sexo').onchange = function()
		        {
		                esconde_todos( id('palco'), 'div' );
		                mostra( this.value );
		        }
		        var radios = document.getElementsByTagName('input');
		        for( var i=0; i<radios.length; i++ ){
		                if( radios[i].type=='radio' )
		                {
		                        radios[i].onclick = function(){
		                                esconde_todos( id('palco'), 'div' );
		                                mostra( this.value );
		                        }
		                }
		        }
		}
		//Jquery para pegar dados de campos dinâmicos select combo 
		function verificaOpcao(clicked_id, valor)
		{
			//passo o valor selecionado ao clicar no select combo
			$("#part1").attr("selectedIndex");
			$("#part2").attr("selectedIndex");
			$("#part3").attr("selectedIndex");
			$("#part4").attr("selectedIndex");
			$("#part5").attr("selectedIndex");
			$("#part6").attr("selectedIndex");
			$("#part7").attr("selectedIndex");
			$("#part8").attr("selectedIndex");
			$("#part9").attr("selectedIndex");
			$("#part10").attr("selectedIndex");
			$("#part11").attr("selectedIndex");
			
			//Pego os valor vindo so select combo
			a = $("#part1").val();
			b = $("#part2").val();
			c = clicked_id;
			d = $("#part3").val();
			e = $("#part4").val();
			f = $("#part5").val();
			g = $("#part6").val();
			h = $("#part7").val();
			i = $("#part8").val();
			j = $("#part9").val();
			k = $("#part10").val();
			l = $("#part11").val();
			
			//Deixo campos invisíveis se a opção do combo 1
			if(a != 0 && c == "part1")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno1]").html("Carregando dados1...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq1']").val('');
							$("input[name='cargo1']").val('');
							$("input[name='email1']").val('');
							$("input[name='login1']").val('');
							
							//Desabilito os campos
							$("[id=arq1]").prop('disabled', true);
							$("[id=cargo1]").prop('disabled', true);
							$("[id=email1]").prop('disabled', true);
							$("[id=login1]").prop('disabled', true);
							
							document.getElementById("campos1").style.display="none";
							$("[id=retorno1]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 1
			if(a == 0 && c == "part1")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno1]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq1']").val('');
				$("input[name='cargo1']").val('');
				$("input[name='email1']").val('');
				$("input[name='login1']").val('');
				
				//Habilito os campos
				$("[id=arq1]").prop('disabled', true);
				$("[id=cargo1]").prop('disabled', true);
				$("[id=email1]").prop('disabled', true);
				$("[id=login1]").prop('disabled', true);
				
				document.getElementById("campos1").style.display="block";
				
					$("[id=retorno1]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
			
			
			
			//Deixo campos invisíveis se a opção do combo 2
			if(b != 0 && c == "part2")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno1]").html("Carregando dados2...");
				
				        //Recebo o valor do ajax
						setTimeout(function(){
							
							//Limpo os campos
							$("input[name='arq2']").val('');
							$("input[name='cargo2']").val('');
							$("input[name='email2']").val('');
							$("input[name='login2']").val('');
							
							//Desabilito os campos
							$("[id=arq2]").prop('disabled', true);
							$("[id=cargo2]").prop('disabled', true);
							$("[id=email2]").prop('disabled', true);
							$("[id=login2]").prop('disabled', true);
							
							document.getElementById("campos1").style.display="none";
							$("[id=retorno1]").load("ajaxValor1.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 2
			if(b == 0 && c == "part2")
			{
				
				//Pego valor para enviar ao ajax...
				$("[id=retorno2]").html("Aguarde1...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq2']").val('');
				$("input[name='cargo2']").val('');
				$("input[name='email2']").val('');
				$("input[name='login2']").val('');
				
				//Habilito os campos
				$("[id=arq2]").prop('disabled', false);
				$("[id=cargo2]").prop('disabled', false);
				$("[id=email2]").prop('disabled', false);
				$("[id=login2]").prop('disabled', false);
				
				document.getElementById("campos2").style.display="block";
				
				$("[id=retorno2]").load("ajaxValor1.php",{id:valor});
			}, 1000);
			
			}
			
			//Deixo campos invisíveis se a opção do combo 3
			if(d != 0 && c == "part3")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno3]").html("Carregando dados3...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq3']").val('');
							$("input[name='cargo3']").val('');
							$("input[name='email3']").val('');
							$("input[name='login3']").val('');
							
							//Desabilito os campos
							$("[id=arq3]").prop('disabled', true);
							$("[id=cargo3]").prop('disabled', true);
							$("[id=email3]").prop('disabled', true);
							$("[id=login3]").prop('disabled', true);
							
							document.getElementById("campos3").style.display="none";
							$("[id=retorno3]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 3
			if(d == 0 && c == "part3")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno3]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq3']").val('');
				$("input[name='cargo3']").val('');
				$("input[name='email3']").val('');
				$("input[name='login3']").val('');
				
				//Habilito os campos
				$("[id=arq3]").prop('disabled', true);
				$("[id=cargo3]").prop('disabled', true);
				$("[id=email3]").prop('disabled', true);
				$("[id=login3]").prop('disabled', true);
				
				document.getElementById("campos3").style.display="block";
				
					$("[id=retorno3]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
			
			//Deixo campos invisíveis se a opção do combo 4
			if(e != 0 && c == "part4")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno4]").html("Carregando dados4...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq4']").val('');
							$("input[name='cargo4']").val('');
							$("input[name='email4']").val('');
							$("input[name='login4']").val('');
							
							//Desabilito os campos
							$("[id=arq4]").prop('disabled', true);
							$("[id=cargo4]").prop('disabled', true);
							$("[id=email4]").prop('disabled', true);
							$("[id=login4]").prop('disabled', true);
							
							document.getElementById("campos4").style.display="none";
							$("[id=retorno4]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 4
			if(e == 0 && c == "part4")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno4]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq4']").val('');
				$("input[name='cargo4']").val('');
				$("input[name='email4']").val('');
				$("input[name='login4']").val('');
				
				//Habilito os campos
				$("[id=arq4]").prop('disabled', true);
				$("[id=cargo4]").prop('disabled', true);
				$("[id=email4]").prop('disabled', true);
				$("[id=login4]").prop('disabled', true);
				
				document.getElementById("campos4").style.display="block";
				
					$("[id=retorno4]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
			
			//Deixo campos invisíveis se a opção do combo 5
			if(f != 0 && c == "part5")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno5]").html("Carregando dados5...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq5']").val('');
							$("input[name='cargo5']").val('');
							$("input[name='email5']").val('');
							$("input[name='login5']").val('');
							
							//Desabilito os campos
							$("[id=arq5]").prop('disabled', true);
							$("[id=cargo5]").prop('disabled', true);
							$("[id=email5]").prop('disabled', true);
							$("[id=login5]").prop('disabled', true);
							
							document.getElementById("campos5").style.display="none";
							$("[id=retorno5]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 5
			if(f == 0 && c == "part5")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno5]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq5']").val('');
				$("input[name='cargo5']").val('');
				$("input[name='email5']").val('');
				$("input[name='login5']").val('');
				
				//Habilito os campos
				$("[id=arq5]").prop('disabled', true);
				$("[id=cargo5]").prop('disabled', true);
				$("[id=email5]").prop('disabled', true);
				$("[id=login5]").prop('disabled', true);
				
				document.getElementById("campos5").style.display="block";
				
					$("[id=retorno5]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
			
			//Deixo campos invisíveis se a opção do combo 6
			if(g != 0 && c == "part6")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno6]").html("Carregando dados6...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq6']").val('');
							$("input[name='cargo6']").val('');
							$("input[name='email6']").val('');
							$("input[name='login6']").val('');
							
							//Desabilito os campos
							$("[id=arq6]").prop('disabled', true);
							$("[id=cargo6]").prop('disabled', true);
							$("[id=email6]").prop('disabled', true);
							$("[id=login6]").prop('disabled', true);
							
							document.getElementById("campos6").style.display="none";
							$("[id=retorno6]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 4
			if(g == 0 && c == "part6")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno6]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq6']").val('');
				$("input[name='cargo6']").val('');
				$("input[name='email6']").val('');
				$("input[name='login6']").val('');
				
				//Habilito os campos
				$("[id=arq6]").prop('disabled', true);
				$("[id=cargo6]").prop('disabled', true);
				$("[id=email6]").prop('disabled', true);
				$("[id=login6]").prop('disabled', true);
				
				document.getElementById("campos6").style.display="block";
				
					$("[id=retorno6]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
			
			//Deixo campos invisíveis se a opção do combo 7
			if(h != 0 && c == "part7")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno7]").html("Carregando dados7...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq7']").val('');
							$("input[name='cargo7']").val('');
							$("input[name='email7']").val('');
							$("input[name='login7']").val('');
							
							//Desabilito os campos
							$("[id=arq7]").prop('disabled', true);
							$("[id=cargo7]").prop('disabled', true);
							$("[id=email7]").prop('disabled', true);
							$("[id=login7]").prop('disabled', true);
							
							document.getElementById("campos7").style.display="none";
							$("[id=retorno7]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 7
			if(h == 0 && c == "part7")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno7]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq7']").val('');
				$("input[name='cargo7']").val('');
				$("input[name='email7']").val('');
				$("input[name='login7']").val('');
				
				//Habilito os campos
				$("[id=arq7]").prop('disabled', true);
				$("[id=cargo7]").prop('disabled', true);
				$("[id=email7]").prop('disabled', true);
				$("[id=login7]").prop('disabled', true);
				
				document.getElementById("campos7").style.display="block";
				
					$("[id=retorno7]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
			
			//Deixo campos invisíveis se a opção do combo 8
			if(i != 0 && c == "part8")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno8]").html("Carregando dados8...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq8']").val('');
							$("input[name='cargo8']").val('');
							$("input[name='email8']").val('');
							$("input[name='login8']").val('');
							
							//Desabilito os campos
							$("[id=arq8]").prop('disabled', true);
							$("[id=cargo8]").prop('disabled', true);
							$("[id=email8]").prop('disabled', true);
							$("[id=login8]").prop('disabled', true);
							
							document.getElementById("campos8").style.display="none";
							$("[id=retorno8]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 4
			if(i == 0 && c == "part8")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno8]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq8']").val('');
				$("input[name='cargo8']").val('');
				$("input[name='email8']").val('');
				$("input[name='login8']").val('');
				
				//Habilito os campos
				$("[id=arq8]").prop('disabled', true);
				$("[id=cargo8]").prop('disabled', true);
				$("[id=email8]").prop('disabled', true);
				$("[id=login8]").prop('disabled', true);
				
				document.getElementById("campos8").style.display="block";
				
					$("[id=retorno8]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
			
			//Deixo campos invisíveis se a opção do combo 9
			if(j != 0 && c == "part9")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno9]").html("Carregando dados9...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq9']").val('');
							$("input[name='cargo9']").val('');
							$("input[name='email9']").val('');
							$("input[name='login9']").val('');
							
							//Desabilito os campos
							$("[id=arq9]").prop('disabled', true);
							$("[id=cargo9]").prop('disabled', true);
							$("[id=email9]").prop('disabled', true);
							$("[id=login9]").prop('disabled', true);
							
							document.getElementById("campos9").style.display="none";
							$("[id=retorno9]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 9
			if(j == 0 && c == "part9")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno9]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq9']").val('');
				$("input[name='cargo9']").val('');
				$("input[name='email9']").val('');
				$("input[name='login9']").val('');
				
				//Habilito os campos
				$("[id=arq9]").prop('disabled', true);
				$("[id=cargo9]").prop('disabled', true);
				$("[id=email9]").prop('disabled', true);
				$("[id=login9]").prop('disabled', true);
				
				document.getElementById("campos4").style.display="block";
				
					$("[id=retorno9]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
			
			//Deixo campos invisíveis se a opção do combo 10
			if(k != 0 && c == "part10")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno10]").html("Carregando dados10...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq10']").val('');
							$("input[name='cargo10']").val('');
							$("input[name='email10']").val('');
							$("input[name='login10']").val('');
							
							//Desabilito os campos
							$("[id=arq10]").prop('disabled', true);
							$("[id=cargo10]").prop('disabled', true);
							$("[id=email10]").prop('disabled', true);
							$("[id=login10]").prop('disabled', true);
							
							document.getElementById("campos10").style.display="none";
							$("[id=retorno10]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 10
			if(k == 0 && c == "part10")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno10]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq10']").val('');
				$("input[name='cargo10']").val('');
				$("input[name='email10']").val('');
				$("input[name='login10']").val('');
				
				//Habilito os campos
				$("[id=arq10]").prop('disabled', true);
				$("[id=cargo10]").prop('disabled', true);
				$("[id=email10]").prop('disabled', true);
				$("[id=login10]").prop('disabled', true);
				
				document.getElementById("campos10").style.display="block";
				
					$("[id=retorno10]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
			
			//Deixo campos invisíveis se a opção do combo 11
			if(l != 0 && c == "part11")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno11]").html("Carregando dados11...");
				        //Recebo o valor do ajax
						setTimeout(function(){
							//Limpo os campos
							$("input[name='arq11']").val('');
							$("input[name='cargo11']").val('');
							$("input[name='email11']").val('');
							$("input[name='login11']").val('');
							
							//Desabilito os campos
							$("[id=arq11]").prop('disabled', true);
							$("[id=cargo11]").prop('disabled', true);
							$("[id=email11]").prop('disabled', true);
							$("[id=login11]").prop('disabled', true);
							
							document.getElementById("campos11").style.display="none";
							$("[id=retorno11]").load("ajaxValor.php",{id:valor});
						}, 1000);
			}
			 //Deixo campos invisíveis se a opção do combo 4
			if(l == 0 && c == "part11")
			{
				//Pego valor para enviar ao ajax...
				$("[id=retorno11]").html("Aguarde...");
				
				//Recebo o valor do ajax
				setTimeout(function(){
				
				//Limpo os campos
				$("input[name='arq11']").val('');
				$("input[name='cargo11']").val('');
				$("input[name='email11']").val('');
				$("input[name='login11']").val('');
				
				//Habilito os campos
				$("[id=arq11]").prop('disabled', true);
				$("[id=cargo11]").prop('disabled', true);
				$("[id=email11]").prop('disabled', true);
				$("[id=login11]").prop('disabled', true);
				
				document.getElementById("campos11").style.display="block";
				
					$("[id=retorno11]").load("ajaxValor.php",{id:valor});
				}, 1000);
			
			}
				
		}	
		
		
		
		
		
		
		
		
		
		
						