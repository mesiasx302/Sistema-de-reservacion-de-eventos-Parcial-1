
<?php
$pestanaActiva = $pestanaActiva ?? 'eventos';
$eventoEditar = $eventoEditar ?? null;
$eventosVigentes = $eventosVigentes ?? [];
$eventosFinalizados = $eventosFinalizados ?? [];
?>

<section
    class="tab-pane fade <?= $pestanaActiva === 'eventos' ? 'show active' : '' ?>"
    id="eventos">


    <div class="card shadow-sm mb-4">

        <div class="card-header fw-semibold">
            <?= $eventoEditar ? 'Editar evento' : 'Registrar evento' ?>
        </div>

        <div class="card-body">

            <form method="POST" class="row g-3">

                <input
                    type="hidden"
                    name="accion"
                    value="<?= $eventoEditar ? 'editar_evento' : 'crear_evento' ?>">

                <?php if ($eventoEditar): ?>
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $eventoEditar['evento_id'] ?>">
                <?php endif; ?>

                <div class="col-md-6">
                    <label class="form-label">Nombre del evento *</label>

                    <input
                        class="form-control"
                        name="nombre"
                        value="<?= h($eventoEditar['nombre'] ?? '') ?>"
                        required
                        maxlength="120">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ubicación *</label>

                    <input
                        class="form-control"
                        name="ubicacion"
                        value="<?= h($eventoEditar['ubicacion'] ?? '') ?>"
                        required
                        maxlength="180">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha y hora de inicio *</label>

                    <input
                        class="form-control"
                        type="datetime-local"
                        name="fecha"
                        value="<?= $eventoEditar && !empty($eventoEditar['fecha'])
                            ? h(date('Y-m-d\TH:i', strtotime($eventoEditar['fecha'])))
                            : '' ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha y hora de finalización *</label>

                    <input
                        class="form-control"
                        type="datetime-local"
                        name="fecha_fin"
                        value="<?= $eventoEditar && !empty($eventoEditar['fecha_fin'])
                            ? h(date('Y-m-d\TH:i', strtotime($eventoEditar['fecha_fin'])))
                            : '' ?>"
                        required>
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción</label>

                    <textarea
                        class="form-control"
                        name="descripcion"
                        rows="3"><?= h($eventoEditar['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="col-12">

                    <button class="btn btn-primary">
                        <?= $eventoEditar ? 'Actualizar evento' : 'Guardar evento' ?>
                    </button>

                    <?php if ($eventoEditar): ?>
                        <a
                            href="index.php?pestana=eventos#eventos"
                            class="btn btn-secondary">
                            Cancelar
                        </a>
                    <?php endif; ?>

                </div>

            </form>

        </div>
    </div>



    <!-- Eventos vigentes -->

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">

            <span>
                <i class="bi bi-calendar-check"></i>
                Eventos vigentes
            </span>

            <span class="badge bg-success" id="contador-vigentes">
                <?= count($eventosVigentes) ?>
            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Inicio</th>
                        <th>Finalización</th>
                        <th>Estado</th>
                        <th>Ubicación</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody id="tabla-eventos-vigentes">

                    <?php foreach ($eventosVigentes as $evento): ?>

                        <tr
                            data-evento-id="<?= (int) $evento['evento_id'] ?>"
                            data-fecha-inicio="<?= h(date('Y-m-d\TH:i:s', strtotime($evento['fecha']))) ?>"
                            data-fecha-fin="<?= !empty($evento['fecha_fin'])
                                ? h(date('Y-m-d\TH:i:s', strtotime($evento['fecha_fin'])))
                                : '' ?>">

                            <td>
                                <strong><?= h($evento['nombre']) ?></strong>
                            </td>

                            <td><?= h($evento['fecha']) ?></td>

                            <td><?= h($evento['fecha_fin'] ?? '') ?></td>

                            <td>
                                <span class="badge estado-evento">
                                    Pendiente
                                </span>
                            </td>

                            <td><?= h($evento['ubicacion']) ?></td>

                            <td><?= h($evento['descripcion']) ?></td>

                            <td class="text-nowrap">

                                <a
                                    href="index.php?editar=<?= (int) $evento['evento_id'] ?>&pestana=eventos#eventos"
                                    class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>

                                <form method="POST" class="form-eliminar d-inline">

                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="eliminar_evento">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $evento['evento_id'] ?>">

                                    <button class="btn btn-sm btn-outline-danger">
                                        Eliminar
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (!$eventosVigentes): ?>

                        <tr id="sin-eventos-vigentes">
                            <td colspan="7" class="text-center text-secondary py-4">
                                No hay eventos vigentes registrados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- Eventos finalizados -->


    <div class="card shadow-sm border-danger">

        <div class="card-header bg-danger-subtle text-danger fw-semibold d-flex justify-content-between align-items-center">

            <span>
                <i class="bi bi-calendar-x"></i>
                Eventos finalizados
            </span>

            <span class="badge bg-danger" id="contador-finalizados">
                <?= count($eventosFinalizados) ?>
            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-danger">

                    <tr>
                        <th>Nombre</th>
                        <th>Inicio</th>
                        <th>Finalización</th>
                        <th>Estado</th>
                        <th>Ubicación</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                    </tr>

                </thead>

                <tbody id="tabla-eventos-finalizados">

                    <?php foreach ($eventosFinalizados as $evento): ?>

                        <tr
                            class="table-danger"
                            data-evento-id="<?= (int) $evento['evento_id'] ?>"
                            data-fecha-inicio="<?= h(date('Y-m-d\TH:i:s', strtotime($evento['fecha']))) ?>"
                            data-fecha-fin="<?= !empty($evento['fecha_fin'])
                                ? h(date('Y-m-d\TH:i:s', strtotime($evento['fecha_fin'])))
                                : '' ?>">

                            <td>
                                <strong><?= h($evento['nombre']) ?></strong>
                            </td>

                            <td><?= h($evento['fecha']) ?></td>

                            <td><?= h($evento['fecha_fin']) ?></td>

                            <td>
                                <span class="badge text-bg-danger estado-evento">
                                    Finalizado
                                </span>
                            </td>

                            <td><?= h($evento['ubicacion']) ?></td>

                            <td><?= h($evento['descripcion']) ?></td>

                            <td class="text-nowrap">

                                <a
                                    href="index.php?editar=<?= (int) $evento['evento_id'] ?>&pestana=eventos#eventos"
                                    class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>

                                <form method="POST" class="form-eliminar d-inline">

                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="eliminar_evento">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $evento['evento_id'] ?>">

                                    <button class="btn btn-sm btn-outline-danger">
                                        Eliminar
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (!$eventosFinalizados): ?>

                        <tr id="sin-eventos-finalizados">
                            <td colspan="7" class="text-center text-secondary py-4">
                                No hay eventos finalizados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</section>
