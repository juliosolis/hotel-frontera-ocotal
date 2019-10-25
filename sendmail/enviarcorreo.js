$(function() {
	/* ---------------------------------------------------------------------- */
	/*	Contact Form
	/* ---------------------------------------------------------------------- */

	var submitContact = $('#formulario-contacto, #contacto-footer');
		//message = $('.mensaje');

	submitContact.on('submit', function(e){
		e.preventDefault();

		var $this = $(this);
		idioma = $this.children("#idioma").val();
		message = $this.children('#mensaje');

		//idioma = $("#idioma").val();
		texto_1 = 'Enviar';
		texto_2 = 'Enviando...';
		if(idioma !== 'es'){
			texto_1 = 'Send';
			texto_2 = 'Sending...';
		}

		$this.children(".boton").attr('disabled', 'disabled');
		$this.children(".boton").val(texto_2);

		$.ajax({
			type: "POST",
			url: '/api/send-email',
			dataType: 'json',
			cache: false,
			data: $this.serialize(),
			success: function(data) {
				$this.children(".boton").removeAttr('disabled');
				$this.children(".boton").val(texto_1);
				if(data.success){
					$this.children(".reset").val('');
					message.hide().removeClass('alert-success').removeClass('alert-danger').addClass('alert-success').html(data.msg).fadeIn('slow').delay(5000).fadeOut('slow');
				} else {
					message.hide().removeClass('alert-success').removeClass('alert-danger').addClass('alert-danger').html(data.msg).fadeIn('slow').delay(5000).fadeOut('slow');
				}
			}
		});
	});
});
