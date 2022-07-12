"use strict";

$(function modalSemana6(){
    $("#modalSemana6").modal("show");
    selectActividades();
});

$('.select2').select2({
    width: "100%",
    dropdownParent: $('#maraton-program')
});

function selectActividades() {
    $.ajax({
        url: "../../app/controller/home.controller.php",
        method: "post",
        data: {
            funcion: "selectActividades"
        }
    }).done((res) => {
        $("#selectActividades").html(res);
    });
}

function selectEstudiantes() {
    $.ajax({
        url: "../../app/controller/home.controller.php",
        method: "post",
        data: {
            funcion: "selectEstudiantes"
        }
    }).done((res) => {
        $("#selectEstudiantes").html(res);
    });
}

$(function cargarQR() {
    $.ajax({
        url: "../../app/controller/home.controller.php",
        method: "post",
        dataType: "json",
        data: {
            funcion: "cargarQR",
        }
    }).done((res) => {
        if (res.Cantidad > 0) {
            $("#verQR").prop("disabled", false);
            $("#verQR").on("click", function (event){
                swal({
                    confirmButtonColor: "#428BCA",
                    imageUrl: res.codigoQR
                });
            });
        }else{
            $("#verQR").prop("disabled", true);
        }
    });
});

$(function llenarDatos() {
    $(document).on("change", "#selectActividades", function () {
        var opcionSelect = $("#selectActividades").val();
        $.ajax({
            url: "../../app/controller/home.controller.php",
            method: "post",
            dataType: "json",
            data: {
                funcion: "llenarDatos",
                opcionSelect: opcionSelect,
            }
        }).done((res) => {
            if (opcionSelect != 0) {
                $("#fecha").val(res.Fecha);
                $("#hora").val(res.Hora);
                $("#sede").val(res.Sede+"-"+res.Auditorio);
                $("#expositor").val(res.Nombre_Expositor);
                $("#descripcion").val(res.Detalle);
            }
        });
    });
});

$(function tablaModalActividades() {
    var tablaModalActividades = $("#tablaModalActividades").DataTable({
        scrollX: true,
        destroy: true,
        searching: false,
        lengthChange: false,
        columnDefs: [{ "className": "dt-center", "targets": "_all" }, { "orderable": false, "targets": "_all" }],
        fnRowCallback: (row, data) => {
            $("#cantidadActividades").html("(" + data.cantidad + ")");
        },
        initComplete: (settings, json) => {
            var eliminarInscripcion = $(document).on("click", "#tablaModalActividades button", function () {
                var data = tablaModalActividades.row($(this).parents("tr")).data();
                swal({
                    title: 'Estás seguro(a) que deseas eliminar esta actividad?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#428BCA',
                    confirmButtonText: 'Sí, quiero eliminarla!',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.value == true) {
                        $.ajax({
                            url: "../../app/controller/home.controller.php",
                            method: "post",
                            data: "No_Reg_I=" + data.no_reg_i + "&funcion=eliminarInscripcion",
                        }).done((res) => {
                            swal({
                                type: 'success',
                                title: 'La actividad se ha eliminado correctamente!',
                                showConfirmButton: false,
                                timer: 2000,
                            });
                            tablaModalActividades.ajax.reload();
                            if (data.cantidad-1 == 0) {
                                $("#cantidadActividades").html("(" + (data.cantidad - 1) + ")");
                                $("#verQR").prop("disabled", true);
                                $("#verQR").off( "click" );
                            }
                        });
                    }
                });
            });
            if (json.data.length == 0) {
                $("#cantidadActividades").html("(0)");
            }
        },
        ajax: {
            method: "post",
            url: "../../app/controller/home.controller.php",
            data: {
                funcion: 'tablaModalActividades',
            }
        },
        columns: [
            { data: "nombre" },
            { data: "fecha" },
            { data: "hora" },
            { data: "sede" },
            { data: "expositor" },
            { data: "estado" },
            { data: "eliminar" }
        ],
    });
});

$(function tablaAgregarActividad() {
    var tablaAgregarActividad = $("#tablaAgregarActividad").DataTable({
        info: false,
        destroy: true,
        searching: false,
        pageLength: 8,
        lengthChange: false,
        columnDefs: [{ "className": "dt-center", "targets": "_all" }, { "orderable": false, "targets": "_all" }],   
        initComplete: () => {
            var agregarActividad = $(document).on("click", "#botonAgregarActividad", function () {
                var selectActividades = $("#selectActividades"),
                    textSelectActividad = $("#selectActividades option:selected").text(),
                    error = false;

                    if ($(selectActividades).val() != 0) {
                        $('input[name="actividad[]"]').each(function() {
                            if ($(this).val() == textSelectActividad) {
                                swal({
                                    type: 'warning',
                                    confirmButtonColor: "#428BCA",
                                    title: 'Ya se encuentra agregada esa actividad',
                                });
                                error = true;
                            }
                        });
                        if (error == false) {
                            tablaAgregarActividad.row.add([
                                '<input type="text" name="actividad[]" id="actividad" class="text-center form-control" value="' + textSelectActividad + '" readonly>',
                                '<button type="button" class="text-white btn fa fa-trash" style="background: #428BCA;"></button>'
                            ]).draw();
                        }else{
                            return false;
                        }
                    }else{
                        swal({
                            type: 'warning',
                            confirmButtonColor: "#428BCA",
                            title: 'Debe seleccionar una actividad',
                        });
                    }
            });

            var eliminarActividad = $(document).on("click", "#tablaAgregarActividad button", function () {
                var row = tablaAgregarActividad.row($(this).parents("tr"));
                tablaAgregarActividad.row(row).remove().draw();
            });

            var inscribirActividad = $(document).on("submit", "#formInscripciones", function (event) {
                event.preventDefault(); // Evitar ejecutar el submit del formulario.

                var formData = new FormData(event.target);
                formData.append("funcion", "inscribirActividad");
                $.ajax({
                    url: "../../app/controller/home.controller.php",
                    method: "post",
                    dataType: "json",
                    data: formData,
                    cache: false, 
                    contentType: false, 
                    processData: false
                }).done((res) => {
                    if (res.tipoRespuesta == "success") {
                        swal({
                            confirmButtonColor: "#428BCA",
                            title: 'Inscripción realizada con éxito!!',
                        }).then((result) => {
                            if (result.value) {
                                swal({
                                    confirmButtonColor: "#428BCA",
                                    imageUrl: res.codigoQR
                                });
                            }
                            $("#verQR").prop("disabled", false);
                            $("#verQR").on("click", function (event){
                                swal({
                                    confirmButtonColor: "#428BCA",
                                    imageUrl: res.codigoQR
                                });
                            });
                        });
                        $("#tablaModalActividades").DataTable().ajax.reload();
                        $(".swal2-modal").addClass("bg-success");
                        $(".swal2-title").addClass("text-white");
                        selectActividades();
                    } else if (res.tipoRespuesta == "error") {
                        if (res.noConferencia.length > 1) {
                            swal({
                                type: 'error',
                                confirmButtonColor: "#428BCA",
                                title: 'Ya se encuentran inscritas las actividades ' + res.noConferencia,
                            });
                        } else {
                            swal({
                                type: 'error',
                                confirmButtonColor: "#428BCA",
                                title: 'Ya se encuentra inscrita la actividad ' + res.noConferencia,
                            });
                        }
                    }
                });
            });
        },
    });
});

function cargarEstudianteMaraton(){

    window.contEstudiante = 2;

    $.ajax({
        url: "../../app/controller/home.controller.php",
        method: "post",
        dataType: "json",
        data: {
            funcion: "cargarEstudianteMaraton"
        }
    }).done((res) => {
        if (res.tipoRespuesta == "success") {
            $("#tablaAgregarEstudiante").DataTable().row.add([
                '<input type="text" name="idEstudiante[]" value="' + res.idEstudiante + '">',
                '<input type="text" name="estudiante[]" class="text-center form-control" value="' + res.nombres + '" readonly>',
                '<button type="button" class="text-white btn fa fa-trash" style="background: #428BCA;" onclick="eliminarEstudiante(this)"></button>',
                res.idRegistro,
            ]).draw();
        } else if (res.tipoRespuesta == "error") {
            contEstudiante = 1;
        }
    });
}

$(function tablaAgregarEstudiante() {

    selectEstudiantes();
    cargarEstudianteMaraton();
    
    var tablaAgregarEstudiante = $("#tablaAgregarEstudiante").DataTable({
        info: false,
        destroy: true,
        searching: false,
        bPaginate: false,
        lengthChange: false,
        columnDefs: [
            { "className": "d-none", "targets": [0] },
            { "className": "dt-center", "targets": [1,2,3] },
            { "orderable": false, "targets": "_all" },
            { "visible": false, "targets": [3] }
        ],
        initComplete: () => {
            var idRegistro = "";
            var agregarEstudiante = $(document).on("click", "#botonAgregarEstudiante", function () {
                var selectEstudiantes = $("#selectEstudiantes"),
                    textSelectEstudiante = $("#selectEstudiantes option:selected").text(),
                    error = false;
                idRegistro = textSelectEstudiante.substr(0, 2);

                if ($(selectEstudiantes).val() != 0) {
                    $('input[name="estudiante[]"]').each(function () {
                        if ($(this).val() == textSelectEstudiante) {
                            swal({
                                type: 'warning',
                                confirmButtonColor: "#428BCA",
                                title: 'Ya se encuentra agregado ese estudiante',
                            });
                            error = true;
                        }
                    });

                    if (contEstudiante <= 3) {
                        if (error == false) {
                            tablaAgregarEstudiante.row.add([
                                '<input type="text" name="idEstudiante[]" value="' + $(selectEstudiantes).val() + '">',
                                '<input type="text" name="estudiante[]" class="text-center form-control" value="' + textSelectEstudiante + '" readonly>',
                                '<button type="button" class="text-white btn fa fa-trash" style="background: #428BCA;" onclick="eliminarEstudiante(this)"></button>',
                                idRegistro,
                            ]).draw();
                            contEstudiante++;
                        } else {
                            return false;
                        }
                    } else {
                        swal({
                            type: 'error',
                            confirmButtonColor: "#428BCA",
                            title: 'El máximo de estudiantes permitidos son 3',
                        });
                        return false;
                    }
                } else {
                    swal({
                        type: 'warning',
                        confirmButtonColor: "#428BCA",
                        title: 'Debe seleccionar un estudiante',
                    });
                }
            });

            var inscribirEstudiante = $(document).on("submit", "#formMaraton", function (event) {
                event.preventDefault(); // Evitar ejecutar el submit del formulario.

                if ($('input[name="estudiante[]"]').val()) {
                    if (contEstudiante - 1 >= 2) {
                        var diaActual = new Date().getDate();
                        var idRegistro = tablaAgregarEstudiante.column(3).data().join("").toUpperCase() + diaActual;
                        var formData = new FormData(event.target);
                        formData.append("funcion", "inscribirEstudiante");
                        formData.append("idRegistro", idRegistro);
                        $.ajax({
                            url: "../../app/controller/home.controller.php",
                            method: "post",
                            dataType: "json",
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false
                        }).done((res) => {
                            if (res.tipoRespuesta == "success") {
                                if (res.noEstudiante) {
                                    if (res.noEstudiante.length > 1) {
                                        swal({
                                            type: 'error',
                                            confirmButtonColor: "#428BCA",
                                            title: 'Ya se encuentran inscritos los estudiantes ' + res.noEstudiante,
                                        });
                                    } else {
                                        swal({
                                            type: 'error',
                                            confirmButtonColor: "#428BCA",
                                            title: 'Ya se encuentra inscrito el estudiante ' + res.noEstudiante,
                                        });
                                    }
                                } else {
                                    swal({
                                        title: 'Inscripción realizada con éxito!!',
                                        html: 'Recuerda este código => ' + '<h4 class="d-inline"><span class="badge badge-dark">' + idRegistro + '</span></h4>',
                                        allowOutsideClick: false,
                                    });
                                    validarRegistroMaraton();
                                    selectEstudiantes();
                                    tablaAgregarEstudiante.clear().draw();
                                    contEstudiante = 2;
                                    $(".swal2-modal").addClass("bg-success");
                                    $(".swal2-content").addClass("text-white");
                                    $(".swal2-title").addClass("text-white");
                                    $.ajax({
                                        url: "../../app/controller/home.controller.php",
                                        method: "post",
                                        dataType: "json",
                                        data: {
                                            funcion: "validarRegistroMaraton"
                                        }
                                    }).done((res) => {
                                        if (res.registroMaraton == true) {
                                            $("#validarRegistroMaraton").show();
                                        }
                                    });
                                }
                            } else if (res.tipoRespuesta == "error") {
                                if (res.noEstudiante.length > 1) {
                                    swal({
                                        type: 'error',
                                        confirmButtonColor: "#428BCA",
                                        title: 'Ya se encuentran inscritos los estudiantes ' + res.noEstudiante,
                                    });
                                } else {
                                    swal({
                                        type: 'error',
                                        confirmButtonColor: "#428BCA",
                                        title: 'Ya se encuentra inscrito el estudiante ' + res.noEstudiante,
                                    });
                                }
                            }
                        });
                    } else {
                        swal({
                            type: 'error',
                            confirmButtonColor: "#428BCA",
                            title: 'Seleccione máximo 3 estudiantes mínimo 2',
                        });
                    }
                }
            });
        },
    });
});

function validarRegistroMaraton(){
    $.ajax({
        url: "../../app/controller/home.controller.php",
        method: "post",
        dataType: "json",
        data: {
            funcion: "validarRegistroMaraton"
        }
    }).done((res)=>{
        if (res.registroMaraton == true) {
            $("#validarRegistroMaraton").show();
            $("#validarRegistroMaraton").html(res.verEquipo);
        }else{
            $("#validarRegistroMaraton").show();
            $("#validarRegistroMaraton").html(res.verEquipo);
        }
    });
}

function eliminarEstudiante(a){
    var row = $("#tablaAgregarEstudiante").DataTable().row($(a).parents("tr"));
    $("#tablaAgregarEstudiante").DataTable().row(row).remove().draw();
    contEstudiante--;
}

$(function tablaEquipoMaraton() {
    validarRegistroMaraton();
    $(document).on("click", "#botonVerEquipoMaraton", function (event) {
        var tablaEquipoMaraton = $("#tablaEquipoMaraton").DataTable({
            destroy: true,
            searching: false,
            lengthChange: false,
            info: false,
            columnDefs: [{
                "className": "text-center",
                "targets": "_all"
            }, {
                "orderable": false,
                "targets": "_all"
            }],
            initComplete: (settings, json) => {
                if (json.data.length) {
                    if (json.data[0].idSessionEstudiante == json.data[0].idEstudianteRealizaInscripcion) {
                        var eliminarParticipante = $(document).on("click", "#tablaEquipoMaraton button", function () {
                            var data = tablaEquipoMaraton.row($(this).parents("tr")).data();
                            swal({
                                title: 'Estás seguro(a) que deseas eliminar a este participante?',
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#428BCA',
                                confirmButtonText: 'Sí, quiero eliminarlo!',
                                cancelButtonColor: '#d33',
                                cancelButtonText: 'Cancelar',
                            }).then((result) => {
                                if (result.value == true) {
                                    $.ajax({
                                        url: "../../app/controller/home.controller.php",
                                        method: "post",
                                        data: "Id_Estudiante=" + data.idEstudiante + "&funcion=eliminarParticipante",
                                    }).done((res) => {
                                        swal({
                                            type: 'success',
                                            title: 'El participante se ha eliminado correctamente!',
                                            showConfirmButton: false,
                                            timer: 2000,
                                        });
                                        validarRegistroMaraton();
                                        selectEstudiantes();
                                        cargarEstudianteMaraton();
                                        tablaEquipoMaraton.ajax.reload();
                                    });
                                }
                            });
                        });
                    } else {
                        var eliminarParticipante = $(document).on("click", "#tablaEquipoMaraton button", function () {
                            swal({
                                title: 'Solo el líder del equipo puede eliminar a los participantes',
                                type: 'error',
                            });
                        });
                    }
                }
            },
            ajax: {
                method: "post",
                url: "../../app/controller/home.controller.php",
                data: {
                    funcion: 'tablaEquipoMaraton',
                }
            },
            columns: [
                { data: "idRegistro" },
                { data: "nombres" },
                { data: "eliminarParticipante" },
            ],
        });
    });
});

$(function cerrarSesion() {
    $(document).on("click", "#salir", function (event) {
        $.ajax({
            url: "../../app/controller/login.controller.php",
            method: "post",
            data: {
                funcion: "cerrarSesion",
            }
        }).done(() => {
            window.location.href = "../../";
        });
    });
});