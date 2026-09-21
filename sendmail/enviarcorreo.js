$(function() {
	/* ---------------------------------------------------------------------- */
	/*	Contact Form
	/* ---------------------------------------------------------------------- */

	var submitContact = $('#formulario-contacto, #contacto-footer');
		//message = $('.mensaje');

	submitContact.on('submit', function(e){
		e.preventDefault();

		var $this = $(this);
		var idioma = $this.children("#idioma").val();
		var message = $this.children('#mensaje');

		//idioma = $("#idioma").val();
		var texto_1 = 'Enviar';
		var texto_2 = 'Enviando...';
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
			complete: function() {
				$this.children(".boton").removeAttr('disabled').val(texto_1);
			},
			success: function(data) {
				if(data.success){
					$this.children(".reset").val('');
					message.hide().removeClass('alert-success').removeClass('alert-danger').addClass('alert-success').html(data.msg).fadeIn('slow').delay(5000).fadeOut('slow');
				} else {
					message.hide().removeClass('alert-success').removeClass('alert-danger').addClass('alert-danger').html(data.msg).fadeIn('slow').delay(5000).fadeOut('slow');
				}
			},
			error: function() {
				message.hide().removeClass('alert-success').addClass('alert-danger').html('No fue posible enviar el mensaje. Intente de nuevo.').fadeIn('slow');
			}
		});
	});
});
