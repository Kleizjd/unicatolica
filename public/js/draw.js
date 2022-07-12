"use strict";

$(document).on("click", "#btnSorteo", function () {
	$.ajax({
		url: "../../app/controller/draw.controller.php",
		method: "post",
		dataType: "json",
		data: {
			funcion: "sorteoSemana"
		},
	}).done((res) => {
		let max = res.max;
		let min = 1;
		setTimeout(function () {
			let NoRegA = $("#odometer").html(Math.floor(Math.random() * max) + min);
			$.ajax({
				url: "../../app/controller/draw.controller.php",
				method: "post",
				dataType: "json",
				data: {
					funcion: "ganador",
					No_Reg_A: NoRegA
				},
			}).done((res) => {
				let ganador = res.ganador.capitalize();
				setTimeout(() => {
					swal({
						backdrop: "rgba(0,0,123,0.4)",
						confirmButtonColor: "#428BCA",
						html: `<p class="h2 text-center">Felicidades al ganador!!</p>
								   <h1 class="text-center"><p class="badge badge-pill badge-dark">${ganador}</p></h1>`,
						allowOutsideClick: false,
					});
				}, 3000);
			});
		}, 150);
	});
});

$("#sorteoSemana").on('hidden.bs.modal', function () {
	$("#odometer").html("");
});

String.prototype.capitalize = function () {
	return this.toLowerCase().replace(/(^|\s)([a-z])/g, function (m, p1, p2) {
		return p1 + p2.toUpperCase();
	});
}