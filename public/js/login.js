(function validarLogin() {
	$("#login-form").on("submit", function (event) {
		event.preventDefault(); // Evitar ejecutar el submit del formulario.

		var id = $("#id").val();
		var cedula = $("#cedula").val();

		if (id != "" && cedula != "") {
			var formData = new FormData(event.target);
			formData.append("funcion", "validar_sesion");
			$.ajax({
				url: "app/controller/login.controller.php",
				method: "post",
				dataType: "json",
				data: formData,
				cache: false, 
				contentType: false, 
				processData: false
			}).done((res) => {
				if (res.tipoRespuesta == "success") {
					location.href = "views/home/home.view.php";
				} else if (res.tipoRespuesta == "error") {
					swal({
						title: 'El usuario ingresado no existe',
						type: 'warning',
					});
				}
			});
		}
	});
}());