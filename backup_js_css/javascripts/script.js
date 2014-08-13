		$(document).ready(function(){
			$(".btn-toggle").click(function(){
				if( $(".btn-toggle").hasClass("open") ){
					$(".btn-toggle").removeClass("open");	
				} else {
					$(this).addClass("open");
				}
			});
			$(".btn-toggle").hover(function(){
				$(this).toggleClass("open");
				$(".open").removeClass("btn-toggle");
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
		
		$(function() {
		    $("#validade").datepicker({
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
		
		
		function AddCamposCadastrado() {
			var hidden1 = document.getElementById("hidden1");
			var hidden2 = document.getElementById("hidden2");
			
			//Executar apenas se houver possibilidade de inserção de novos campos:
			if (iCount < totalCampos) {

			//Limpar hidden1, para atualizar a lista dos campos que ainda estão vazios:
			hidden2.value = "";

			//Atualizando a lista dos campos que estão ocultos.
			//Essa lista ficará armazenada temporiariamente em hidden2;
			
			
			
			
			for (iLoop = 1; iLoop <= totalCampos; iLoop++) {
			        if (document.getElementById("linha2"+iLoop).style.display == "none") {
			                if (hidden2.value == "") {
			                        hidden2.value = "linha2"+iLoop;
			                }else{
			                        hidden2.value += ",linha2"+iLoop;
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
		function RemoverCamposCadastrado(id) {
			//Criando ponteiro para hidden1:        
			var hidden1 = document.getElementById("hidden1");

			//Pegar o valor do campo que será excluído:
			var campoValor = document.getElementById("part"+id).value;
			        //Se o campo não tiver nenhum valor, atribuir a string: vazio:
			        if (campoValor == "") {
			                campoValor = "vazio";
			        }

			    	if(confirm("O Participante:\n» "+campoValor+"\nserá removido!\n\nDeseja continuar?")){
			                document.getElementById("linha2"+id).style.display = "none"; iCount--;
			                
			                //Removendo o valor de hidden1:
			                if (hidden1.value.indexOf(",linha2"+id) != -1) {
			                        hidden1.value = hidden1.value.replace(",linha2"+id,"");
			                }else if (hidden1.value.indexOf("linha2"+id+",") == 0) {
			                        hidden1.value = hidden1.value.replace("linha2"+id+",","");
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
				//$("input[name='cadastrar'").addClass("hidden");
				//$("input[name='cancelar'").addClass("hidden");
			}
			else if (op == 2) 
			{
				document.getElementById('principal').style.display="none";
				document.getElementById('divum').style.display="block";
				document.getElementById('divdois').style.display="none";
				//$(".hidden").removeClass("hidden");
			}
			else 
			{
				document.getElementById('principal').style.display="none";
				document.getElementById('divum').style.display="none";
				document.getElementById('divdois').style.display="block";
				//$(".hidden").removeClass("hidden");
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
		function verificaOpcao(valor)
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
			 
			 
			//*Pego os valor vindo so select combo
			a = $("#part1").val();
			b = $("#part2").val();
			c = $("#part3").val();
			d = $("#part4").val();
			e = $("#part5").val();
			f = $("#part6").val();
			g = $("#part7").val();
			h = $("#part8").val();
			i = $("#part9").val();
			j = $("#part10").val();
			k = $("#part11").val();
			
			
			if(a > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq1']").val('.');
				$("input[name='cargo1']").val('.');
				$("input[name='email1']").val('.');
				$("input[name='login1']").val('.');
			
				//Desabilito os campos
			
				$("[id=arq1]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo1]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email1]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login1]").attr('disabled', true).css("background-color", "#008080");
				//alert("a > 0 "+a);
					
			}
			
			
			if(b > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq2']").val('.');
				$("input[name='cargo2']").val('.');
				$("input[name='email2']").val('.');
				$("input[name='login2']").val('.');
			
				//Desabilito os campos
				$("[id=arq2]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo2]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email2]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login2]").attr('disabled', true).css("background-color", "#008080");
				//alert("b > 0 "+b);
					
			}
			
			if(c > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq3']").val('.');
				$("input[name='cargo3']").val('.');
				$("input[name='email3']").val('.');
				$("input[name='login3']").val('.');
			
				//Desabilito os campos
				$("[id=arq3]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo3]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email3]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login3]").attr('disabled', true).css("background-color", "#008080");
				//alert("c > 0 "+b);
					
			}
			
			if(d > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq4']").val('.');
				$("input[name='cargo4']").val('.');
				$("input[name='email4']").val('.');
				$("input[name='login4']").val('.');
			
				//Desabilito os campos
			
				$("[id=arq4]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo4]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email4]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login4]").attr('disabled', true).css("background-color", "#008080");
				//alert("a > 0 "+a);
					
			}
			
			if(e > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq5']").val('.');
				$("input[name='cargo5']").val('.');
				$("input[name='email5']").val('.');
				$("input[name='login5']").val('.');
			
				//Desabilito os campos
			
				$("[id=arq5]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo5]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email5]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login5]").attr('disabled', true).css("background-color", "#008080");
				//alert("a > 0 "+a);
					
			}
			
			if(f > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq6']").val('.');
				$("input[name='cargo6']").val('.');
				$("input[name='email6']").val('.');
				$("input[name='login6']").val('.');
			
				//Desabilito os campos
			
				$("[id=arq6]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo6]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email6]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login6]").attr('disabled', true).css("background-color", "#008080");
				//alert("a > 0 "+a);
					
			}
			
			if(g > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq7']").val('.');
				$("input[name='cargo7']").val('.');
				$("input[name='email7']").val('.');
				$("input[name='login7']").val('.');
			
				//Desabilito os campos
			
				$("[id=arq7]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo7]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email7]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login7]").attr('disabled', true).css("background-color", "#008080");
				//alert("a > 0 "+a);
					
			}
			
			if(h > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq8']").val('.');
				$("input[name='cargo8']").val('.');
				$("input[name='email8']").val('.');
				$("input[name='login8']").val('.');
			
				//Desabilito os campos
			
				$("[id=arq8]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo8]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email8]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login8]").attr('disabled', true).css("background-color", "#008080");
				//alert("a > 0 "+a);
					
			}
			
			if(i > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq9']").val('.');
				$("input[name='cargo9']").val('.');
				$("input[name='email9']").val('.');
				$("input[name='login9']").val('.');
			
				//Desabilito os campos
			
				$("[id=arq9]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo9]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email9]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login9]").attr('disabled', true).css("background-color", "#008080");
				//alert("a > 0 "+a);
					
			}
			
			if(j > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq10']").val('.');
				$("input[name='cargo10']").val('.');
				$("input[name='email10']").val('.');
				$("input[name='login10']").val('.');
			
				//Desabilito os campos
			
				$("[id=arq10]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo10]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email10]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login10]").attr('disabled', true).css("background-color", "#008080");
				//alert("a > 0 "+a);
					
			}
			
			if(k > 0)
			{
				/*Limpo os campos para digitar dados*/
				$("input[name='arq11']").val('.');
				$("input[name='cargo11']").val('.');
				$("input[name='email11']").val('.');
				$("input[name='login11']").val('.');
			
				//Desabilito os campos
			
				$("[id=arq11]").attr('disabled', true).css("background-color", "#008080");
				$("[id=cargo11]").attr('disabled', true).css("background-color", "#008080");
				$("[id=email11]").attr('disabled', true).css("background-color", "#008080");
				$("[id=login11]").attr('disabled', true).css("background-color", "#008080");
				//alert("a > 0 "+a);
					
			}
			
			
			if(a == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq1']").val('');
				$("input[name='cargo1']").val('');
				$("input[name='email1']").val('');
				$("input[name='login1']").val('');
				
				//Habilito os campos
				$("[id=arq1]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo1]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email1]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login1]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("a == 0 "+a);
			}
			
			
			if(b == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq2']").val('');
				$("input[name='cargo2']").val('');
				$("input[name='email2']").val('');
				$("input[name='login2']").val('');
				
				//Habilito os campos
				$("[id=arq2]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo2]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email2]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login2]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("b == 0 "+b);
			}
			
			if(c == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq3']").val('');
				$("input[name='cargo3']").val('');
				$("input[name='email3']").val('');
				$("input[name='login3']").val('');
				
				//Habilito os campos
				$("[id=arq3]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo3]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email3]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login3]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("c == 0 "+b);
			}
			
			if(d == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq4']").val('');
				$("input[name='cargo4']").val('');
				$("input[name='email4']").val('');
				$("input[name='login4']").val('');
				
				//Habilito os campos
				$("[id=arq4]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo4]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email4]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login4]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("a == 0 "+a);
			}
			
			if(e == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq5']").val('');
				$("input[name='cargo5']").val('');
				$("input[name='email5']").val('');
				$("input[name='login5']").val('');
				
				//Habilito os campos
				$("[id=arq5]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo5]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email5]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login5]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("a == 0 "+a);
			}
			
			if(f == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq6']").val('');
				$("input[name='cargo6']").val('');
				$("input[name='email6']").val('');
				$("input[name='login6']").val('');
				
				//Habilito os campos
				$("[id=arq6]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo6]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email6]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login6]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("a == 0 "+a);
			}
			
			if(g == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq7']").val('');
				$("input[name='cargo7']").val('');
				$("input[name='email7']").val('');
				$("input[name='login7']").val('');
				
				//Habilito os campos
				$("[id=arq7]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo7]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email7]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login7]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("a == 0 "+a);
			}
			
			if(h == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq8']").val('');
				$("input[name='cargo8']").val('');
				$("input[name='email8']").val('');
				$("input[name='login8']").val('');
				
				//Habilito os campos
				$("[id=arq8]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo8]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email8]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login8]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("a == 0 "+a);
			}
			
			if(i == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq9']").val('');
				$("input[name='cargo9']").val('');
				$("input[name='email9']").val('');
				$("input[name='login9']").val('');
				
				//Habilito os campos
				$("[id=arq9]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo9]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email9]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login9]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("a == 0 "+a);
			}
			
			if(j == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq10']").val('');
				$("input[name='cargo10']").val('');
				$("input[name='email10']").val('');
				$("input[name='login10']").val('');
				
				//Habilito os campos
				$("[id=arq10]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo10]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email10]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login10]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("a == 0 "+a);
			}
			
			if(k == 0 )
			{
				
				/*Limpo os campos*/
				$("input[name='arq11']").val('');
				$("input[name='cargo11']").val('');
				$("input[name='email11']").val('');
				$("input[name='login11']").val('');
				
				//Habilito os campos
				$("[id=arq11]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=cargo11]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=email11]").attr('disabled', false).css("background-color", "#FFFAFA");
				$("[id=login11]").attr('disabled', false).css("background-color", "#FFFAFA");
				//alert("a == 0 "+a);
			}
					
		}
		
		   //Aceita apenas numeros
		   function SomenteNumero(e){
			    var tecla=(window.event)?event.keyCode:e.which;   
			    if((tecla>47 && tecla<58)) return true;
			    else{
			    	if (tecla==8 || tecla==0) return true;
				else  return false;
			    }
			}
		   
		 //Aceita apenas letras
		   function SomenteLetras(e){
			     var tecla=(window.event)?event.keyCode:e.which;
			     if((tecla > 65 && tecla < 90)||(tecla > 97 && tecla < 122)) 
			    	 return true;
			     else{
			         if (tecla != 8) return false;
			         else 
			        	 return true;
			    }
			     
			}
		   
		   //Validar campos do coordenador
		   function valida()
		   {
		               $("#coordenador_bd").attr("selectedIndex");
		   		   var coordenador_bd   = $("#coordenador_bd").val();
		   		   var nome_coordenador = $("input[name='nome_coordenador']").val();
		   		   var endereco         = $("input[name='endereco_coordenador']").val();
		   		   var bairro           = $("input[name='bairro_coordenador']").val();
		   		   var cidade           = $("input[name='cidade_coordenador']").val();
		   		   var telefone         = $("input[name='telefone_coordenador']").val();
		   		   var email            = $("input[name='email_coordenador']").val();
		   		   var login            = $("input[name='login_coordenador']").val();

		   		   if(coordenador_bd == "" && nome_coordenador == "")
		   		   {
		   			       alert("O Campo 'Coordenador' é de preenchimento obrigatório.");
		   			       return false;
		              }else{
		           	     if(coordenador_bd == "" && nome_coordenador != "" )
		       		     { 
		   	        	     if(endereco == "" || bairro == "" || cidade == "" || telefone == "" || email == "" || login == "")
		   	    		     {
		   		    		     alert("Os Campos do Coordenador: 'Endereço, Bairro, Cidade, Telefone, Email e Login' são de preenchimento obrigatório.");
		   	      		         return false;
		   	        		 }
		   	        	    
		       		     }
		           	       
		              }
		   		   
		   		//validando a data
                   var data_inicio = $("input[name='data_inicio_projs']").val();
                   var data_fim = $("input[name='data_fim_projs']").val();
                   if(data_inicio>data_fim)
                   {
                       alert("A data inicial deve ser anterior à data final!");
                       return false;
                   }
		   	}
		   
		   function valida_data()
		   {
			   
			   
			 //validando a data
               var data_inicio = $("input[name='data_inicio']").val();
               var data_fim = $("input[name='data']").val();
               if(data_inicio>data_fim)
               {
                   alert("A data inicial deve ser anterior à data final!");
                   return false;
               }
		   }
		   
		   function valida_data_consulta()
		   {
			   
			   
			 //validando a data
               var data_inicio = $("input[name='data_inicio_projs']").val();
               var data_fim = $("input[name='data_fim_projs']").val();
               if(data_inicio>data_fim)
               {
                   alert("A data inicial deve ser anterior à data final!");
                   return false;
               }
		   }
		   
		   
