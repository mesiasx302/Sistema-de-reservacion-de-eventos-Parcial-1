
<?php
$pestanaActiva = $pestanaActiva ?? 'reservas';
$eventos = $eventos ?? [];
$clientes = $clientes ?? [];
$reservas = $reservas ?? [];
?>

<section
    class="tab-pane fade <?= $pestanaActiva === 'reservas' ? 'show active' : '' ?>"
    id="reservas">


    <div class="card shadow-sm mb-4">

        <div class="card-header fw-semibold">
            Crear reservación
        </div>

        <div class="card-body">

            <form method="POST" class="row g-3">

                <input
                    type="hidden"
                    name="accion"
                    value="crear_reserva">

                <div class="col-md-6">

                    <label class="form-label">Evento *</label>

                    <select class="form-select" name="evento_id" required>

                        <option value="">Selecciona un evento</option>

                        <?php foreach ($eventos as $evento): ?>

                            <option value="<?= (int) $evento['evento_id'] ?>">
                                <?= h($evento['nombre'] . ' - ' . $evento['fecha']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-6">

                    <label class="form-label">Cliente *</label>

                    <select class="form-select" name="cliente_id" required>

                        <option value="">Selecciona un cliente</option>

                        <?php foreach ($clientes as $cliente): ?>

                            <option value="<?= (int) $cliente['cliente_id'] ?>">
                                <?= h($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-12">

                    <button
                        class="btn btn-success"
                        <?= (!$eventos || !$clientes) ? 'disabled' : '' ?>>

                        Confirmar reservación

                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- Tabla de reservaciones -->

    <div class="card shadow-sm">

        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">

            <span>Reservaciones</span>

            <span class="badge bg-primary" id="contador-reservaciones">
                <?= count($reservas) ?>
            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>

                    <tr>
                        <th>Evento</th>
                        <th>Cliente</th>
                        <th>Inicio del evento</th>
                        <th>Finalización</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>

                </thead>

                <tbody id="tabla-reservaciones">

                    <?php foreach ($reservas as $reserva): ?>

                        <?php
                        $estadoOriginal = $reserva['estado'];

                        $claseEstado = match ($estadoOriginal) {
                            'Activa' => 'text-bg-success',
                            'Finalizada' => 'text-bg-danger',
                            'Cancelada' => 'text-bg-secondary',
                            default => 'text-bg-secondary'
                        };

                        $fechaInicio = $reserva['fecha_evento'] ?? '';
                        $fechaFin = $reserva['fecha_fin_evento'] ?? '';
                        ?>

                        <tr
                            data-reserva-id="<?= (int) $reserva['reservacion_id'] ?>"
                            data-fecha-inicio="<?= $fechaInicio
                                ? h(date('Y-m-d\TH:i:s', strtotime($fechaInicio)))
                                : '' ?>"
                            data-fecha-fin="<?= $fechaFin
                                ? h(date('Y-m-d\TH:i:s', strtotime($fechaFin)))
                                : '' ?>"
                            data-estado-original="<?= h($estadoOriginal) ?>">

                            <td>
                                <?= h($reserva['evento']) ?>
                            </td>

                            <td>
                                <?= h($reserva['nombre'] . ' ' . $reserva['apellido']) ?>
                            </td>

                            <td>
                                <?= h($fechaInicio) ?>
                            </td>

                            <td>
                                <?= h($fechaFin) ?>
                            </td>

                            <td>

                                <span class="badge <?= $claseEstado ?> estado-reservacion">
                                    <?= h($estadoOriginal) ?>
                                </span>

                            </td>

                            <td>

                                <?php if ($estadoOriginal === 'Activa'): ?>

                                    <form method="POST" class="form-cancelar">

                                        <input
                                            type="hidden"
                                            name="accion"
                                            value="cancelar_reserva">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $reserva['reservacion_id'] ?>">

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-warning">

                                            Cancelar

                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="text-secondary accion-reservacion">—</span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (!$reservas): ?>

                        <tr id="sin-reservaciones">

                            <td colspan="6" class="text-center text-secondary py-4">
                                No hay reservaciones.
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</section>
