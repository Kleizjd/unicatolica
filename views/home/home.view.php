<?php @session_start(); include_once "../../config/env.php"; ?>

<?php if (isset($_SESSION["Id_Estudiante"])):  ?>

<?php if ($_SESSION["Id_Estudiante"] == 16882523): ?>
<?php header("Location: ./draw.view.php"); ?>

<?php else: ?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="shortcut icon" href="../../public/img/logo-unicatolica.png" type="image/x-icon">
	<link rel="stylesheet" href="../../vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../vendor/datatable/css/datatables.min.css">
	<link rel="stylesheet" href="../../vendor/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="../../vendor/sweetalert/css/sweetalert2.min.css">
	<link rel="stylesheet" href="../../vendor/select2/css/select2.min.css">
	<link rel="stylesheet" href="../../public/css/styles.css">
	<title>Unicatólica</title>
</head>

<body>

	<!-- Modal Semana de la Ingeniería 6 -->
	<div class="modal fade" id="modalSemana6" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="container-fluid">
				<div class="justify-content-center row">
					<div class="col-12">
						<img src="../../public/img/logo-semana-6-2018.png" alt="Logo Semana 6" class="img-fluid">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="mt-2 row">

			<div class="col-12 col-md-6">

				<div class="row">
					<div class="col-12">
						<img src="../../public/img/logo-unicatolica.png" alt="Logo" class="img-fluid">
					</div>
				</div>

				<div class="mt-4 row">
					<div class="mt-2 col-4 col-md-6 col-lg-6">
						<p>
							<span class="font-weight-bold">BIENVENIDO!!</span>
						</p>
					</div>

					<div class="col-4 col-lg-4">
						<!-- BOTÓN MODAL MIS ACTIVIDADES -->
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#misactividades" style="background: #428BCA;">
							Mis actividades <span id="cantidadActividades"></span>
						</button>
						<!-- FIN BOTÓN MODAL MIS ACTIVIDADES -->
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<?=$_SESSION['nombreUsuario'];?>
					</div>
				</div>

				<!-- MODAL MIS ACTIVIDADES -->
				<div class="modal fade" id="misactividades" tabindex="-1" role="dialog" aria-labelledby="misactividadesLabel"
				 aria-hidden="true">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="text-center modal-header">
								<h2 class="w-100 modal-title" id="misactividadesLabel">Tabla de Actividades</h2>
								<button type="button" class="text-danger close" data-dismiss="modal" aria-label="Close">
									<i class="fa fa-window-close fa-2x"></i>
								</button>
							</div>

							<table id="tablaModalActividades" class="table table-hover">
								<thead class="text-white" style="background: #428BCA;">
									<tr class="text-center">
										<th class="text-center">Nombre</th>
										<th class="text-center">Fecha</th>
										<th class="text-center">Hora</th>
										<th class="text-center">Sede</th>
										<th class="text-center">Expositor</th>
										<th class="text-center">Estado</th>
										<th class="text-center">Eliminar</th>
									</tr>
								</thead>

								<tbody></tbody>

							</table>

						</div>
					</div>
				</div>
				<!-- FIN MODAL MIS ACTIVIDADES -->

				<!-- MODAL MARATÓN PROGRAMACIÓN -->
				<div class="modal fade" id="maraton-program" tabindex="-1" role="dialog" aria-labelledby="maraton-programLabel"
				 aria-hidden="true">
					<div class="modal-dialog modal-md" role="document">
						<div class="modal-content">
							<div class="modal-header row">
								<div class="col-12">

									<div class="row">
										<div class="col-11">
											<h2 class="w-100 modal-title" id="misactividadesLabel">Selección de participantes</h2>
										</div>
										<div class="col-1">
											<button type="button" class="text-danger close" data-dismiss="modal" aria-label="Close">
												<i class="fa fa-window-close fa-2x"></i>
											</button>
										</div>
									</div>

								</div>

								<div class="col-12">

									<div class="row">
										<div class="text-center col-12">
											<label for="">(Seleccione máximo 3 estudiantes mínimo 2)</label>
										</div>
									</div>

								</div>
							</div>

							<form method="post" id="formMaraton">
								<div class="container">
									<div class="mt-4 form-group row">
										<div class="col-3">
											<label for="">Estudiante:</label>
										</div>
										<div class="col-9">
											<select name="nombreEstudiante[]" id="selectEstudiantes" class="select2 form-control"></select>
										</div>
									</div>

									<div class="mt-5 row">
										<div class="text-center col-12">
											<button type="button" id="botonAgregarEstudiante" class="text-white btn" style="background: #428BCA;">
												<i class="fa fa-sign-out-alt fa-3x fa-rotate-90"></i>
											</button>
										</div>
									</div>

									<div class="row">
										<div class="col-12">
											<p class="text-center">Agregar</p>
										</div>
									</div>

									<div class="mt-5 form-group row">
										<div class="col-12">
											<table id="tablaAgregarEstudiante" class="table table-hover">
												<thead class="text-white" style="background: #428BCA;">
													<tr>
														<th>idEstudiante</th>
														<th>Participantes</th>
														<th></th>
														<th>idRegistro</th>
													</tr>
												</thead>

												<tbody></tbody>
											</table>
										</div>
									</div>

									<div class="form-group row">
										<div class="text-center col-12">
											<input type="submit" id="inscribirEstudiante" class="text-white btn" style="background: #428BCA;" value="Inscribir">
										</div>
									</div>

								</div>
							</form>

						</div>
					</div>
				</div>
				<!-- FIN MODAL MARATÓN PROGRAMACIÓN -->

				<!-- MODAL MARATÓN DE LA PROGRAMACIÓN -->
				<div class="modal fade" id="equipoMaraton" tabindex="-1" role="dialog" aria-labelledby="equipoMaratonLabel"
				 aria-hidden="true">
					<div class="modal-dialog modal-md" role="document">
						<div class="modal-content">
							<div class="text-center modal-header">
								<h2 class="w-100 modal-title" id="equipoMaratonLabel">Mi Equipo</h2>
								<button type="button" class="text-danger close" data-dismiss="modal" aria-label="Close">
									<i class="fa fa-window-close fa-2x"></i>
								</button>
							</div>

							<table id="tablaEquipoMaraton" class="my-3 table table-hover">
								<thead class="text-white" style="background: #428BCA;">
									<tr class="text-center">
										<th class="text-center">ID</th>
										<th class="text-center">Nombres</th>
										<th class="text-center">Eliminar</th>
									</tr>
								</thead>

								<tbody></tbody>

							</table>

						</div>
					</div>
				</div>
				<!-- FIN MODAL MARATÓN DE LA PROGRAMACIÓN -->

				<div class="mt-4 row">
					<div class="align-self-center col-3 col-lg-5">
						<label for="selectActividades" class="font-weight-bold">Selecciona tu actividad:</label>
					</div>
					<div class="col-9 col-lg-6">
						<select name="selectactividades" id="selectActividades" class="form-control"></select>
					</div>
				</div>

				<div class="mt-4 row">
					<div class="col-10">

						<div class="row">
							<div class="col-3 col-md-3">
								<label class="font-weight-bold">Fecha:</label>
							</div>
							<div class="col-9 col-md-9">
								<div class="form-group">
									<input type="text" name="fecha" id="fecha" class="form-control" value="" readonly>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-3 col-md-3">
								<label class="font-weight-bold">Hora:</label>
							</div>
							<div class="col-9 col-md-9">
								<div class="form-group">
									<input type="text" name="hora" id="hora" class="form-control" value="" readonly>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-3 col-md-3">
								<label class="font-weight-bold">Sede:</label>
							</div>
							<div class="col-9 col-md-9">
								<div class="form-group">
									<input type="text" name="sede" id="sede" class="form-control" value="" readonly>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-3 col-md-3">
								<label class="font-weight-bold">Expositor:</label>
							</div>
							<div class="col-9 col-md-9">
								<div class="form-group">
									<input type="text" name="expositor" id="expositor" class="form-control" value="" readonly>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-3">
								<label class="font-weight-bold">Descripción:</label>
							</div>
							<div class="col-12 col-md-12">
								<div class="form-group">
									<textarea name="descripcion" id="descripcion" rows="5" class="form-control" readonly></textarea>
								</div>
							</div>
						</div>
					</div>


					<div class="mt-5 col-12 col-md-1 col-lg-2">
						<div class="text-center row">
							<div class="col-12">
								<button type="button" id="botonAgregarActividad" class="text-white btn" style="background: #428BCA;">
									<i class="d-none d-md-block fa fa-sign-out-alt fa-2x"></i>
									<i class="d-block d-md-none fa fa-sign-out-alt fa-2x fa-rotate-90"></i>
								</button>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<p class="text-center">Agregar</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-6">

				<div class="row">
					<div class="p-0 col-11">
						<img src="../../public/img/logo-semana-6-2.png" alt="Semana de la ingenieria" class="img-fluid">
					</div>
					<div class="align-self-center col-1">
						<button type="button" id="salir" class="text-white btn" style="background: #428BCA;">
							<i class="fa fa-sign-out-alt fa">Salir</i>
						</button>
					</div>
				</div>

				<div class="mt-4 row">
					<div class="col-6 col-md-12 text-md-center col-lg-6">
						<!-- BOTÓN MODAL MARATÓN PROGRAMACIÓN -->
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#maraton-program" style="background: #428BCA;">
							Maratón de la Programación
						</button>
						<!-- FIN BOTÓN MODAL MARATÓN PROGRAMACIÓN -->

						<div class="row">
							<div class="text-center col-11" id="validarRegistroMaraton">
							</div>
						</div>

					</div>

					<div class="col-6 col-md-12 text-md-center mt-md-4 col-lg-6 mt-lg-0">
						<!-- BOTÓN DESCARGAR INSTRUCCIONES -->
						<a class="btn btn-primary" href="../../public/files/ReglamentoMaratonProgramacion.pdf" download="ReglamentoMaratonProgramacion" style="background: #428BCA;">
							Descargar reglamento
						</a>
						<!-- FIN BOTÓN DESCARGAR INSTRUCCIONES -->
					</div>
				</div>
				
				<div class="mt-5 row">
					<div class="col-12">
						<form id="formInscripciones" method="post">

							<table id="tablaAgregarActividad" class="table table-hover">
								<thead style="background: #428BCA;">
									<tr>
										<th></th>
										<th></th>
									</tr>
								</thead>

								<tbody></tbody>
							</table>

							<div class="form-group">
								<div class="mt-4 justify-content-center row">
									<div class="col-4 col-sm-3 col-md-4 col-lg-3">
										<input type="submit" id="inscribir" class="text-white btn" style="background: #428BCA;" value="Inscribir">
									</div>
									<div class="col-3 col-lg-3">
										<input type="button" id="verQR" class="text-white btn" style="background: #428BCA;" value="Ver QR">
									</div>
								</div>
							</div>

						</form>
					</div>
				</div>


			</div>

		</div>

	</div>


	</div>
	</div>

	<script src="../../vendor/jquery/jquery-3.3.1.min.js"></script>
	<script src="../../vendor/datatable/js/datatables.min.js"></script>
	<script src="../../vendor/select2/js/select2.min.js"></script>
	<script src="../../vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="../../vendor/sweetalert/js/sweetalert2.min.js"></script>
	<script src="../../public/js/app.js"></script>
</body>

</html>
<?php endif;?>

<!-- SE REDIRIGE AL LOGIN SI NO HA INICIADO SESIÓN -->
<?php else:header("Location: ../../");?>
<?php endif;?>
