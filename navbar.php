<script>
$(document).ready(function(){
				$("a[rel=modal]").click( function(ev){
					ev.preventDefault();

					var id = $(this).attr("href");

					var alturaTela = $(document).height();
					var larguraTela = $(window).width();
	
					//colocando o fundo preto
					$('#mascara').css({'width':larguraTela,'height':alturaTela});
					$('#mascara').fadeIn(1000);	
					$('#mascara').fadeTo("slow",0.8);

					var left = ($(window).width() /2) - ( $(id).width() / 2 );
					var top = ($(window).height() / 2) - ( $(id).height() / 2 );
					
					$(id).css({'top':top,'left':left});
					$(id).show();	
 				});

 				$("#mascara").click( function(){
 					$(this).hide();
 					$(".window").hide();
 				});

 				$('.fechar').click(function(ev){
 					ev.preventDefault();
 					$("#mascara").hide();
 					$(".window").hide();
 				});
			});
</script>


<div id="navbar">
	<a class="whitelink" href="login.php">Início</a>&nbsp|
	<a class="whitelink" href="#">Histórico</a>&nbsp|
	<a class="whitelink" href="#janela1" rel="modal">Ajuda</a>&nbsp|
	<a class="whitelink" href="logoff.php">Sair</a>
</div> 

<div class="window" id="janela1">
    <a href="#" class="fechar">X Fechar</a>
    <h4>Sobre o Sistema a2c:</h4>
   	<p align="justify">Facilitar o uso dos recursos do CENAPAD-UFC por parte da comunidade cientíﬁca (e.g., estudantes, professores e pesquisadores).</p> 
    <p>Opções:</p>
    <p align="justify"><b>Submissão de jobs</b> - O a2c possibilita a criação de um arquivo .srm, que é um script no qual são passados os parâmetros para a submissão de um job..</p>
    <p align="justify"><b>Status</b> - Acompanhamento do estado de processamento de jobs e realização de cancelamento dos mesmos.</p>
    <p align="justify"><b>Gerenciador de Arquivos</b> - Auxilia na organização dos documentos do usuário. Permite a criação, edição, exclusão dos arquivos.</p>
    
    
    
</div>
<!-- mascara para cobrir o site -->  
<div id="mascara"></div>


