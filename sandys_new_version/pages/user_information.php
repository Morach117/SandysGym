    <!-- Área de Banner -->
    <section class="breadcrumb-section set-bg" data-setbg="./assets/img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>Editar Perfil</h2>
                        <div class="bt-option">
                            <a href="index.php">Inicio</a>
                            <span>Editar Perfil</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Fin del Área de Banner -->

    <!-- Área de Edición de Perfil -->
    <section class="profile-edit section_gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="profile-edit-form">
                        <h3>Editar Perfil</h3>
                        <form id="editarPerfilForm" method="POST">
                            <!-- Campos del formulario -->
                            <!-- Utiliza los valores de $selSocioData para prellenar los campos -->
                            <input type="hidden" id="id_socio" name="id_socio" value="<?php echo $selSocioData['soc_id_socio']; ?>">

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="nombres">Nombres *</label>
                                    <input type="text" id="nombres" name="nombres" class="form-control" value="<?php echo $selSocioData['soc_nombres']; ?>" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="ap_paterno">A. Paterno *</label>
                                    <input type="text" id="ap_paterno" name="ap_paterno" class="form-control" value="<?php echo $selSocioData['soc_apepat']; ?>" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="ap_materno">A. Materno</label>
                                    <input type="text" id="ap_materno" name="ap_materno" class="form-control" value="<?php echo $selSocioData['soc_apemat']; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="genero">Género</label>
                                    <select id="genero" name="genero" class="form-control">
                                        <option value="Masculino" <?php echo ($selSocioData['soc_genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                                        <option value="Femenino" <?php echo ($selSocioData['soc_genero'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="turno">Turno</label>
                                    <select id="turno" name="turno" class="form-control">
                                        <option value="Matutino" <?php echo ($selSocioData['soc_turno'] == 'Matutino') ? 'selected' : ''; ?>>Matutino</option>
                                        <option value="Vespertino" <?php echo ($selSocioData['soc_turno'] == 'Vespertino') ? 'selected' : ''; ?>>Vespertino</option>
                                    </select>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" id="direccion" name="direccion" class="form-control" value="<?php echo $selSocioData['soc_direccion']; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="colonia">Colonia</label>
                                    <input type="text" id="colonia" name="colonia" class="form-control" value="<?php echo $selSocioData['soc_colonia']; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="tel_fijo">Teléfono fijo</label>
                                    <input type="text" id="tel_fijo" name="tel_fijo" class="form-control" value="<?php echo $selSocioData['soc_tel_fijo']; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="tel_cel">Teléfono celular</label>
                                    <input type="text" id="tel_cel" name="tel_cel" class="form-control" value="<?php echo $selSocioData['soc_tel_cel']; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="correo">Correo</label>
                                    <input type="email" id="correo" name="correo" class="form-control" value="<?php echo $selSocioData['soc_correo']; ?>" readonly>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                                    <?php
                                    $readonly = ($selSocioData['soc_fecha_nacimiento'] != '0000-00-00') ? 'readonly' : '';
                                    ?>
                                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="<?php echo $selSocioData['soc_fecha_nacimiento']; ?>" <?php echo $readonly; ?> required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="emer_nombres">Nombres de Emergencia</label>
                                    <input type="text" id="emer_nombres" name="emer_nombres" class="form-control" value="<?php echo $selSocioData['soc_emer_nombres']; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="emer_parentesco">Parentesco</label>
                                    <input type="text" id="emer_parentesco" name="emer_parentesco" class="form-control" value="<?php echo $selSocioData['soc_emer_parentesco']; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="emer_direccion">Dirección de Emergencia</label>
                                    <input type="text" id="emer_direccion" name="emer_direccion" class="form-control" value="<?php echo $selSocioData['soc_emer_direccion']; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="emer_tel">Teléfono de Emergencia</label>
                                    <input type="text" id="emer_tel" name="emer_tel" class="form-control" value="<?php echo $selSocioData['soc_emer_tel']; ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="observaciones">Observaciones</label>
                                    <textarea id="observaciones" name="observaciones" class="form-control"><?php echo $selSocioData['soc_observaciones']; ?></textarea>
                                </div>
                                <!-- Puedes agregar más campos aquí si es necesario -->

                                <div class="col-md-12 form-group">
                                    <button id="guardarCambiosBtn" type="button" class="primary-btn">Guardar Cambios</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Asegúrate de incluir la biblioteca jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!-- Incluir la biblioteca SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // Función para enviar los datos del formulario mediante AJAX
        function guardarCambios() {
            // Obtener el formulario y el ID del socio
            var formulario = $("#editarPerfilForm");
            var idSocio = $("#id_socio").val();

            // Realizar la solicitud AJAX
            $.ajax({
                url: "./query/procesar_edicion.php",
                type: "POST",
                data: formulario.serialize(), // Serializar los datos del formulario
                success: function(response) {
                    // La solicitud fue exitosa
                    console.log(response);
                    // Mostrar una alerta de éxito con SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'La información del socio se ha actualizado correctamente.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Redireccionar a index.php?page=user
                        window.location.href = "index.php?page=user";
                    });
                },
                error: function(xhr, status, error) {
                    // Error en la solicitud
                    console.error(xhr.responseText);
                    // Mostrar una alerta de error con SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: 'Error al actualizar la información del socio.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Agregar un evento click al botón de guardar cambios
        $("#guardarCambiosBtn").click(function() {
            guardarCambios();
        });
    });
    </script>
</body>

</html>
