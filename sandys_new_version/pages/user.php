    <!-- Banner de Inicio de Sesión -->
    <section class="breadcrumb-section set-bg" data-setbg="./assets/img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>Panel de Administración</h2>
                        <div class="bt-option">
                            <a href="index.php">Inicio</a>
                            <span>Panel de Administración</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenido Principal -->
    <div class="container mt-5">
        <!-- Título del Panel -->
        <h2 class="text-center mb-4">Panel de Administración</h2>

        <!-- Información del Usuario -->
        <section class="card mb-4">
            <div class="card-header bg-primary text-white">
                Información del Usuario
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Datos Personales</h5>
                        <ul class="list-unstyled">
                            <li><strong>Nombre Completo:</strong> <?php echo $selSocioData['soc_nombres'] . ' ' . $selSocioData['soc_apepat'] . ' ' . $selSocioData['soc_apemat']; ?></li>
                            <li><strong>Género:</strong> <?php echo $selSocioData['soc_genero']; ?></li>
                            <li><strong>Fecha de Nacimiento:</strong> <?php echo $selSocioData['soc_fecha_nacimiento']; ?></li>
                            <li><strong>Correo Electrónico:</strong> <?php echo $selSocioData['soc_correo']; ?></li>
                            <li><strong>Teléfono Celular:</strong> <?php echo $selSocioData['soc_tel_cel']; ?></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Dirección</h5>
                        <ul class="list-unstyled">
                            <li><strong>Dirección:</strong> <?php echo $selSocioData['soc_direccion']; ?></li>
                            <li><strong>Colonia:</strong> <?php echo $selSocioData['soc_colonia']; ?></li>
                            <!-- Agregar más datos de dirección aquí -->
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Botones de Acción -->
        <section class="text-center mb-4">
            <a class="btn btn-primary mr-2" href="index.php?page=user_information"><i class="fas fa-edit"></i> Editar Usuario</a>
        </section>

        <!-- Tabla de Mensualidades Pagadas -->
        <section>
            <h2 class="text-center mb-4">Mensualidades Pagadas</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha de corte</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <?php
                    // Obtener el ID del socio
                    $correoSocio = $selSocioData['soc_correo'];
                    $consultaIdSocio = "SELECT soc_id_socio FROM san_socios WHERE soc_correo = :correoSocio";
                    $stmtIdSocio = $conn->prepare($consultaIdSocio);
                    $stmtIdSocio->bindParam(':correoSocio', $correoSocio);
                    $stmtIdSocio->execute();
                    $idSocio = $stmtIdSocio->fetch(PDO::FETCH_ASSOC)['soc_id_socio'];

                    // Consulta para obtener los pagos del socio
                    $consultaPagos = "SELECT * FROM san_pagos WHERE pag_id_socio = :idSocio ORDER BY pag_fecha_pago DESC";
                    $stmtPagos = $conn->prepare($consultaPagos);
                    $stmtPagos->bindParam(':idSocio', $idSocio);
                    $stmtPagos->execute();
                    $pagos = $stmtPagos->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <tbody>
                        <?php
                        // Verificar si la variable $pagos está definida y es un array o un objeto que implementa Countable
                        if (isset($pagos) && (is_array($pagos) || $pagos instanceof Countable)) {
                            // Verificar si hay pagos para mostrar
                            if (count($pagos) > 0) {
                                // Inicializar el contador para el número de registros
                                $contador = 1;
                                // Iterar sobre los pagos y mostrar en la tabla
                                foreach ($pagos as $pago) {
                                    echo "<tr>";
                                    echo "<td>" . $contador . "</td>"; // Mostrar el número del registro
                                    echo "<td>" . $pago['pag_fecha_fin'] . "</td>"; // Mostrar la fecha de fin del pago
                                    echo "<td>$" . $pago['pag_importe'] . "</td>";
                                    // Calcular si el pago está vigente o vencido
                                    $fechaInicio = new DateTime($pago['pag_fecha_ini']);
                                    $fechaFin = new DateTime($pago['pag_fecha_fin']);
                                    $hoy = new DateTime();
                                    if ($hoy >= $fechaInicio && $hoy <= $fechaFin) {
                                        echo "<td>Vigente</td>";
                                    } else {
                                        echo "<td>Vencido</td>";
                                    }
                                    echo "</tr>";
                                    // Incrementar el contador
                                    $contador++;
                                }
                            } else {
                                // Mostrar un mensaje si no hay pagos
                                echo "<tr><td colspan='4'>No hay pagos registrados para este usuario.</td></tr>";
                            }
                        } else {
                            // Mostrar un mensaje si la variable $pagos no está definida o no es un array
                            echo "<tr><td colspan='4'>Error al cargar los pagos. Por favor, inténtalo de nuevo más tarde.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Asegúrate de incluir la biblioteca jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- Incluir la biblioteca SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
