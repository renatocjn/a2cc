try{
$(function() {

	$('.validate span').hide();
    /* Required message */
    var requiredMsg = "Campo obrigatório não preenchido!";
    /* E-mail message */
    var mailMsg = "O e-mail informado é inválido!";
    /* CPF message */
    var cpfMsg = "CPF informado é inválido!";
    /* cnpj message */
    var cnpjMsg = "CNPJ informado é inválido!";
    /* Data message */
    var dataMsg = "Data informada é inválida!";
    /* Password message */
    var passwordMsg = "Senhas não conferem!";    
    
    
    /* mascaras */
    $('head').append('<script src="js/jquery.mask.js" type="text/javascript"></script>');
    /* mascara data */
    $('.data').mask('99/99/9999');
    /* mascara cpf */
    $('.cpf').mask('999.999.999-99');
    /* mascara cnpj */
    $('.cnpj').mask('99.999.999/9999-99');
    /* mascara placa */
    $('.placa').mask('aaa-9999');
    /* mascara telefone */
    $('.fone').mask('(99)99999-9999'); 
    /* mascara telefone */
    $('.cep').mask('99999-999'); 	
    /* validate style - comentar alinha abaixo para omitir o style */
    
    //$('head').append('<link href="css/validate.css" type="text/css" media="screen" rel="stylesheet" />');
    /* button style - comentar alinha abaixo para omitir o style do button */
    //$('head').append('<link href="css/button.css" type="text/css" media="screen" rel="stylesheet" />');
    /* botao reset - limpa forms*/
    $('.reset').live('click',function(){
        $('form').attr('onsubmit','return false');
        $('form').find('*').val('');
        $('form').find('*').removeClass('invalid');
        return false;
    });
    /* Aplicando Placeholder com texto do SPAN */


    $('.validate').submit(function(e) {
		e.stopPropagation();
        var valid = true;       
        $(this).find('.required').each(function(i,elm){				
		
            /* required */
            if ( $(this).hasClass('required') && $.trim( $(this).val() ) == "" ){
                $(this).addClass('invalid');
                $(this).focus();
                $(this).attr('placeholder',requiredMsg).fadeOut(300).fadeIn(300);
                valid = false;
                return false;
            }
            else
            {
                $(this).removeClass('invalid');
            }
			
            /* cep value */
            if ( $(elm).hasClass('required') && $(elm).hasClass('cep') ){
				var valcep = $.trim($(this).val().replace('-',''));
				var urlws = 'http://cep.republicavirtual.com.br/web_cep.php?cep='+valcep+'&formato=json';	
				var cepr =  $.ajax({url:urlws,async: false}).responseText;
				console.log(cepr);
				cepr = $.parseJSON(cepr);
				if(cepr.resultado == 0){
					$(this).addClass('invalid');
					$(this).select();
	                $(this).attr('placeholder','Cep não encontrado, informe um CEP válido.').fadeOut(300).fadeIn(300);
					valid = false;
					return false;								
				}else{
					$(this).removeClass('invalid');
				}
            }	
			
            /* email */
            if ( $(this).hasClass('email') ){
                var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
                if (!er.test($.trim( $(this).val() ))){
                    $(this).addClass('invalid');
                    $(this).select();
                    $(this).val('');
	                $(this).attr('placeholder',mailMsg).fadeOut(300).fadeIn(300);
                    valid = false;
                    return false;
                }
                else{
                    $(this).removeClass('invalid');
                }
            } 
            
            /* data */
            if ( $(this).hasClass('data') ){
                
                var sdata = $(this).val();
                if(sdata.length!=10)
                {
                    $(this).addClass('invalid');
                    $(this).select();
	                $(this).attr('placeholder',dataMsg).fadeOut(300).fadeIn(300);
                    valid = false;
                    return false;
                }
                var data        = sdata;
                var dia         = data.substr(0,2);
                var barra1      = data.substr(2,1);
                var mes         = data.substr(3,2);
                var barra2      = data.substr(5,1);
                var ano         = data.substr(6,4);
                if(data.length!=10||barra1!="/"||barra2!="/"||isNaN(dia)||isNaN(mes)||isNaN(ano)||dia>31||mes>12)
                {
                    $(this).addClass('invalid');
                    $(this).select();
	                $(this).attr('placeholder',dataMsg).fadeOut(300).fadeIn(300);
                    valid = false;
                    return false;            
                }
                if((mes==4||mes==6||mes==9||mes==11) && dia==31){
                    $(this).addClass('invalid');
                    $(this).select();
	                $(this).attr('placeholder',dataMsg).fadeOut(300).fadeIn(300);
                    valid = false;
                    return false;
                }
                if(mes==2 && (dia>29||(dia==29 && ano%4!=0))){
                    $(this).addClass('invalid');
                    $(this).select();
	                $(this).attr('placeholder',dataMsg).fadeOut(300).fadeIn(300);
                    valid = false;
                    return false;
                }
                if(ano < 1900)
                {
                    $(this).addClass('invalid');
                    $(this).select();
	                $(this).attr('placeholder',dataMsg).fadeOut(300).fadeIn(300);
                    valid = false;
                    return false;
                }                
                else{
                    $(this).removeClass('invalid');
                }
            } 
            
            /* cpf */
            if ( $(this).hasClass('cpf') ){
                var cpf = $(this).val().replace('.','');
                cpf = cpf.replace('.','');
                cpf = cpf.replace('-','');
                while(cpf.length < 11) cpf = "0"+ cpf;
                var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
                var a = [];
                var b = new Number;
                var c = 11;
                for (i=0; i<11; i++){
                    a[i] = cpf.charAt(i);
                    if (i < 9) b += (a[i] * --c);
                }
                if ((x = b % 11) < 2) {
                    a[9] = 0;
                } else {
                    a[9] = 11-x;
                }
                b = 0;
                c = 11;
                for (y=0; y<10; y++) b += (a[y] * c--);
                if ((x = b % 11) < 2) {
                    a[10] = 0;
                } else {
                    a[10] = 11-x;
                }
                if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg))
                {
                    $(this).addClass('invalid');
                    $(this).select();
	                $(this).attr('placeholder',cpfMsg).fadeOut(300).fadeIn(300);
                    valid = false;
                    return false;
                }
                else{
                    $(this).removeClass('invalid');
                }
            } 
            
            /*valida cnpj*/
            if($(this).hasClass('cnpj'))
            {
                var cnpj = $(this).val();
                cnpj = cnpj.replace('.','');
                cnpj = cnpj.replace('.','');
                cnpj = cnpj.replace('/','');
                cnpj = cnpj.replace('-','');
                var a = new Array();
                var b = new Number;
                var c = [6,5,4,3,2,9,8,7,6,5,4,3,2];
                for (i=0; i<12; i++){
                    a[i] = cnpj.charAt(i);
                    b += a[i] * c[i+1];
                }
                if ((x = b % 11) < 2) {
                    a[12] = 0;
                } else {
                    a[12] = 11-x;
                }
                b = 0;
                for ( y=0; y<13; y++) {
                    b += (a[y] * c[y]);
                }
                if ((x = b % 11) < 2) {
                    a[13] = 0;
                } else {
                    a[13] = 11-x;
                }
                if ((cnpj.charAt(12) != a[12]) || (cnpj.charAt(13) != a[13])){
                    
                    $(this).addClass('invalid');
                    $(this).select();
	                $(this).attr('placeholder',cnpjMsg).fadeOut(300).fadeIn(300);        
                    valid = false;
                    return false;
                }
                else
                {
                    $(this).removeClass('invalid');
                }
            }
	
            /* password */
            if ( $('.password2').val() != $('.password').val() ){
          
               $('.password2').addClass('invalid');
               $('.password2').focus().val('');
	           $('.password2').attr('placeholder',passwordMsg).fadeOut(300).fadeIn(300);
               valid = false;
               return false;
            } else {
            	valid = true;
              	return true;
            }
        });
		return valid;
    });
});
}catch(err){alert("error in "+err.description);}