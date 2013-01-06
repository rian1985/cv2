$(document).ready(function() {
	
	/* --------------------------------------------------------------- */
	$('#porquecpf').click(function () {
		$('#CaixaCPF').show();
	});
	
	$('#CaixaCPF').click(function () {
		$('#CaixaCPF').hide();
	});
	$('#porquecnpj').click(function () {
		$('#CaixaCNPJ').show();
	});
	
	$('#CaixaCNPJ').click(function () {
		$('#CaixaCNPJ').hide();
	});
	
	$('#fisica').click(function () {
		$('.tableCNPJ').hide();
		$('#cpfCadastro').addClass('obrigatorio');
		$('.tableCPF').show();
		$('#razaoSocial').removeClass();
		$('#nomeFantasia').removeClass();
		$('#cnpjCadastro').removeClass();
	});
	
	$('#juridica').click(function () {
		$('.tableCNPJ').show();
		$('#cnpjCadastro').addClass('obrigatorio');
		$('#razaoSocial').addClass('obrigatorio');
		$('#nomeFantasia').addClass('obrigatorio');
		$('.tableCPF').hide();
		$('#cpfCadastro').removeClass();
	});
	/* --------------------------------------------------------------- */
	
	$( "#tabs" ).tabs();
	$( "#tabsHome" ).tabs();
	
	/* --------------------------------------------------------------- */
	
	$('form.validavel').submit(function(event) {
		$('.erro', this).hide();
		
		var valido = true;
		$('*:input:not(.bypass)', this).each(function(i) {
			var input = $(this);

			if (input.attr('disabled'))
				return;
			
			var inputValido = true;
			if (input.hasClass('obrigatorio'))
				inputValido = inputValido && $.trim(input.val()).length > 0;
			
			if (input.hasClass('email'))
				inputValido = inputValido && /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/.test(input.val());
			if (input.hasClass('cpf'))
				inputValido = inputValido && /^([0-9]{3}\.){2}[0-9]{3}-[0-9]{2}|[0-9]{11}$/.test(input.val());
			if (input.hasClass('cep'))
				inputValido = inputValido && /^[0-9]{5}-[0-9]{3}$/.test(input.val());
			if (input.hasClass('rg'))
				inputValido = inputValido && /^[0-9]{10}$/.test(input.val());
			if (input.hasClass('date'))
				inputValido = inputValido && /^\d{2}\/\d{2}\/\d{4}$/.test(input.val());
			if (input.hasClass('time'))
				inputValido = inputValido && /^([01]\d|2[0-3]):[0-5]\d$/.test(input.val());
			if (input.hasClass('inteiro'))
				inputValido = inputValido && /^\d+$/.test(input.val());
			
			valido = valido && inputValido;
			
			if (!inputValido) {
				var e = input;
				while (e.parent().size() > 0) {
					var erro = e.siblings('.erro');
					if (erro.size() > 0) {
						erro.fadeIn();
						break;
					}
					e = e.parent();
				}
			}
		});
		
		if (!valido)
			event.preventDefault();
		return valido;
	});
	
	/* --------------------------------------------------------------- */
	
	$(function(){  
		$("#cpfCadastro").mask("999.999.999-99");
		$("#cnpjCadastro").mask("99.999.999/9999-99");
		$("#telefoneCadastro").mask("(99) 9999.9999");
		$("#celularCadastro").mask("(99) 9999.9999");
		$("#cepCadastro").mask("99999-999");
	});
	
	/* ------------------------- SLIDER DESTAQUES -------------------------------------- */
	
	$('.prettyGallery:first').prettyGallery({
		'itemsPerPage':5,
		'of_label': '', /* The content in the page "1 of 2" */
		'navigation' : 'both',  /* top/bottom/both */
		'previous_title_label': 'Previous page', /* The title of the previous link */
		'next_title_label': 'Next page', /* The title of the next link */
		'previous_label': 'Previous', /* The content of the previous link */
		'next_label': 'Next' /* The content of the next link */
	});
	
	$('.prettyGallery:last').prettyGallery({
		'itemsPerPage':5,
		'of_label': '', /* The content in the page "1 of 2" */
		'navigation' : 'both',  /* top/bottom/both */
		'previous_title_label': 'Previous page', /* The title of the previous link */
		'next_title_label': 'Next page', /* The title of the next link */
		'previous_label': 'Previous', /* The content of the previous link */
		'next_label': 'Next' /* The content of the next link */
	});
	
	/* ------------------------- SLIDER DESTAQUES -------------------------------------- */

})