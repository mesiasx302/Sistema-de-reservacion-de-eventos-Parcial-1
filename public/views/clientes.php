<?php
$pestanaActiva = $pestanaActiva ?? 'clientes';
$clienteEditar = $clienteEditar ?? null;
$clientes = $clientes ?? [];
?>
<section
    class="tab-pane fade <?= $pestanaActiva === 'clientes' ? 'show active' : '' ?>"
    id="clientes">


    <div class="card shadow-sm mb-4">

        <div class="card-header fw-semibold">
            <?= $clienteEditar ? 'Editar cliente' : 'Registrar cliente' ?>
        </div>

        <div class="card-body">

            <form
                method="POST"
                class="row g-3 <?= $clienteEditar ? 'formulario-actualizar' : '' ?>">

                <input
                    type="hidden"
                    name="accion"
                    value="<?= $clienteEditar ? 'editar_cliente' : 'crear_cliente' ?>">

                <?php if ($clienteEditar): ?>
                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $clienteEditar['cliente_id'] ?>">
                <?php endif; ?>

                <div class="col-md-6">
                    <label class="form-label">Nombre *</label>
                    <input
                        class="form-control"
                        name="nombre"
                        value="<?= h($clienteEditar['nombre'] ?? '') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Apellido *</label>
                    <input
                        class="form-control"
                        name="apellido"
                        value="<?= h($clienteEditar['apellido'] ?? '') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Correo electrónico *</label>
                    <input
                        class="form-control"
                        type="email"
                        name="email"
                        value="<?= h($clienteEditar['email'] ?? '') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Teléfono *</label>
                    <input
                        class="form-control"
                        name="telefono"
                        value="<?= h($clienteEditar['telefono'] ?? '') ?>"
                        required>
                </div>

                <div class="col-12">

                    <button type="submit" class="btn btn-primary">
                        <?= $clienteEditar ? 'Actualizar cliente' : 'Guardar cliente' ?>
                    </button>

                    <?php if ($clienteEditar): ?>
                        <a
                            href="index.php?pestana=clientes#clientes"
                            class="btn btn-secondary">
                            Cancelar
                        </a>
                    <?php endif; ?>

                </div>

            </form>

        </div>

    </div>


    <!-- Tabla Clientes -->

    <div class="card shadow-sm">

        <div class="card-header fw-semibold">
            Clientes registrados
        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($clientes as $cliente): ?>

                        <tr>

                            <td>
                                <?= h($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
                            </td>

                            <td><?= h($cliente['email']) ?></td>
                            <td><?= h($cliente['telefono']) ?></td>

                            <td class="text-nowrap">

                                <a
                                    href="index.php?editar_cliente=<?= (int) $cliente['cliente_id'] ?>#clientes"
                                    class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>

                                <form method="POST" class="form-eliminar d-inline">

                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="eliminar_cliente">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $cliente['cliente_id'] ?>">

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger">
                                        Eliminar
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (!$clientes): ?>
                        <tr>
                            <td colspan="4" class="text-center text-secondary">
                                No hay clientes registrados.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</section>