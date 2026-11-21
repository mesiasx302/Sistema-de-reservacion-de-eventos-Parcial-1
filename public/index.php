<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Eventos.php';
require_once __DIR__ . '/../models/Clientes.php';
require_once __DIR__ . '/../models/Reservaciones.php';

$eventosModel = new Eventos($pdo);
$clientesModel = new Clientes($pdo);
$reservasModel = new Reservaciones($pdo);


function fechaParaMysql(string $fecha): string
{
    $fecha = str_replace('T', ' ', trim($fecha));

    if (strlen($fecha) === 16) {
        $fecha .= ':00';
    }

    return $fecha;
}

function h(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    try {

        switch ($accion) {


            case 'crear_evento':

                $datos = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'fecha' => fechaParaMysql($_POST['fecha'] ?? ''),
                    'fecha_fin' => fechaParaMysql($_POST['fecha_fin'] ?? ''),
                    'ubicacion' => trim($_POST['ubicacion'] ?? ''),
                ];

                if (
                    $datos['nombre'] === '' ||
                    $datos['fecha'] === '' ||
                    $datos['fecha_fin'] === '' ||
                    $datos['ubicacion'] === ''
                ) {
                    throw new InvalidArgumentException(
                        'Completa los campos obligatorios del evento.'
                    );
                }

                if ($datos['fecha_fin'] <= $datos['fecha']) {
                    throw new InvalidArgumentException(
                        'La fecha de finalización debe ser posterior a la fecha de inicio.'
                    );
                }

                $eventosModel->crear($datos);

                $_SESSION['flash'] = [
                    'tipo' => 'success',
                    'mensaje' => 'Evento registrado correctamente.'
                ];

                break;


            case 'editar_evento':

                $id = (int) ($_POST['id'] ?? 0);

                $datos = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'fecha' => fechaParaMysql($_POST['fecha'] ?? ''),
                    'fecha_fin' => fechaParaMysql($_POST['fecha_fin'] ?? ''),
                    'ubicacion' => trim($_POST['ubicacion'] ?? ''),
                ];

                if (
                    $id <= 0 ||
                    $datos['nombre'] === '' ||
                    $datos['fecha'] === '' ||
                    $datos['fecha_fin'] === '' ||
                    $datos['ubicacion'] === ''
                ) {
                    throw new InvalidArgumentException(
                        'Completa los campos obligatorios del evento.'
                    );
                }

                if ($datos['fecha_fin'] <= $datos['fecha']) {
                    throw new InvalidArgumentException(
                        'La fecha de finalización debe ser posterior a la fecha de inicio.'
                    );
                }

                $eventosModel->actualizar($id, $datos);

                $_SESSION['flash'] = [
                    'tipo' => 'success',
                    'mensaje' => 'Evento actualizado correctamente.'
                ];

                break;




            case 'eliminar_evento':

                $id = (int) ($_POST['id'] ?? 0);

                if ($id <= 0) {
                    throw new InvalidArgumentException('Evento no válido.');
                }

                $eventosModel->eliminar($id);

                $_SESSION['flash'] = [
                    'tipo' => 'success',
                    'mensaje' => 'Evento eliminado correctamente.'
                ];

                break;




            case 'crear_cliente':

                $datos = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'apellido' => trim($_POST['apellido'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'telefono' => trim($_POST['telefono'] ?? ''),
                ];

                if (
                    in_array('', $datos, true) ||
                    !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)
                ) {
                    throw new InvalidArgumentException(
                        'Ingresa todos los datos y un correo válido.'
                    );
                }

                $clientesModel->crear($datos);

                $_SESSION['flash'] = [
                    'tipo' => 'success',
                    'mensaje' => 'Cliente registrado correctamente.'
                ];

                break;






            case 'editar_cliente':

                $id = (int) ($_POST['id'] ?? 0);

                $datos = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'apellido' => trim($_POST['apellido'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'telefono' => trim($_POST['telefono'] ?? ''),
                ];

                if (
                    $id <= 0 ||
                    $datos['nombre'] === '' ||
                    $datos['apellido'] === '' ||
                    $datos['email'] === '' ||
                    $datos['telefono'] === ''
                ) {
                    throw new InvalidArgumentException(
                        'Completa los campos obligatorios del cliente.'
                    );
                }

                if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException(
                        'Ingresa un correo electrónico válido.'
                    );
                }

                $clientesModel->actualizar($id, $datos);

                $_SESSION['flash'] = [
                    'tipo' => 'success',
                    'mensaje' => 'Cliente actualizado correctamente.'
                ];

                break;






            case 'eliminar_cliente':

                $id = (int) ($_POST['id'] ?? 0);

                if ($id <= 0) {
                    throw new InvalidArgumentException('Cliente no válido.');
                }

                $clientesModel->eliminar($id);

                $_SESSION['flash'] = [
                    'tipo' => 'success',
                    'mensaje' => 'Cliente eliminado correctamente.'
                ];

                break;






            case 'crear_reserva':

                $eventoId = filter_input(
                    INPUT_POST,
                    'evento_id',
                    FILTER_VALIDATE_INT
                );

                $clienteId = filter_input(
                    INPUT_POST,
                    'cliente_id',
                    FILTER_VALIDATE_INT
                );

                if (!$eventoId || !$clienteId) {
                    throw new InvalidArgumentException(
                        'Selecciona un evento y un cliente.'
                    );
                }

                // Comprobar que el evento todavía está vigente.
                $eventoSeleccionado = $eventosModel->buscarPorId($eventoId);

                if (
                    !$eventoSeleccionado ||
                    (
                        $eventoSeleccionado['fecha_fin'] !== null &&
                        $eventoSeleccionado['fecha_fin'] < date('Y-m-d H:i:s')
                    )
                ) {
                    throw new InvalidArgumentException(
                        'No puedes reservar un evento finalizado.'
                    );
                }

                $reservasModel->crear($eventoId, $clienteId);

                $_SESSION['flash'] = [
                    'tipo' => 'success',
                    'mensaje' => 'Reservación creada correctamente.'
                ];

                break;




            case 'cancelar_reserva':

                $id = (int) ($_POST['id'] ?? 0);

                if ($id <= 0) {
                    throw new InvalidArgumentException(
                        'Reservación no válida.'
                    );
                }

                if (!$reservasModel->cancelar($id)) {
                    throw new InvalidArgumentException(
                        'No se puede cancelar esta reservación porque ya está finalizada o cancelada.'
                    );
                }

                $_SESSION['flash'] = [
                    'tipo' => 'success',
                    'mensaje' => 'Reservación cancelada correctamente.'
                ];

                break;






            default:

                throw new InvalidArgumentException('Acción no válida.');
        }
    } catch (Throwable $e) {

        $_SESSION['flash'] = [
            'tipo' => 'error',
            'mensaje' => $e instanceof PDOException
                ? 'No se pudo completar la operación. Verifica duplicados o reservaciones relacionadas.'
                : $e->getMessage()
        ];
    }




    

    switch ($accion) {

        case 'crear_cliente':
        case 'editar_cliente':
        case 'eliminar_cliente':

            $seccion = '?pestana=clientes#clientes';
            break;


        case 'crear_reserva':
        case 'cancelar_reserva':

            $seccion = '?pestana=reservas#reservas';
            break;


        default:

            $seccion = '?pestana=eventos#eventos';
            break;
    }

    header('Location: index.php' . $seccion);
    exit;
}



// Obtener eventos

$eventosVigentes = $eventosModel->vigentes();

$eventosFinalizados = $eventosModel->finalizados();

// Utilizar eventos vigentes para nuevas reservaciones.
$eventos = $eventosVigentes;

// Obtener todos los eventos para permitir editar cualquiera.
$todosEventos = $eventosModel->todos();



// Obtener clientes y reservaciones


$clientes = $clientesModel->todos();

$reservas = $reservasModel->todos();


// Buscar evento para editar

$eventoEditar = null;

if (isset($_GET['editar'])) {

    $idEditar = (int) $_GET['editar'];

    foreach ($todosEventos as $evento) {

        if ((int) $evento['evento_id'] === $idEditar) {
            $eventoEditar = $evento;
            break;
        }
    }
}


// Buscar cliente para editar

$clienteEditar = null;

if (isset($_GET['editar_cliente'])) {

    $idEditar = (int) $_GET['editar_cliente'];

    foreach ($clientes as $cliente) {

        if ((int) $cliente['cliente_id'] === $idEditar) {
            $clienteEditar = $cliente;
            break;
        }
    }
}


// Determinar pestaña activa

if (
    isset($_GET['pestana']) &&
    in_array($_GET['pestana'], ['eventos', 'clientes', 'reservas'], true)
) {
    $pestanaActiva = $_GET['pestana'];
} elseif ($clienteEditar) {

    $pestanaActiva = 'clientes';
} elseif ($eventoEditar) {

    $pestanaActiva = 'eventos';
} else {

    $pestanaActiva = 'eventos';
}


// Obtener alerta

$flash = $_SESSION['flash'] ?? null;

unset($_SESSION['flash']);

?>

<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistema de Reservación de Eventos</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="assets/css/styles.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <!-- Barra de navegación -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">

        <div class="container">

            <a class="navbar-brand fw-bold" href="index.php">
                Gestión de eventos y reservaciones
            </a>

            <span class="navbar-text text-white">
                UNIANDES EVENTOS
            </span>

        </div>

    </nav>


    <main class="container py-4">

        <!-- Encabezado -->

        <header class="mb-4">

            <h1 class="h3">
                Sistema de Reservación de Eventos
            </h1>

            <p class="text-secondary mb-0">
                Administración de eventos, clientes y sus reservaciones.
            </p>

        </header>


        <!-- Pestañas -->

        <ul class="nav nav-pills gap-2 mb-4" id="tabs" role="tablist">

            <li class="nav-item">

                <button
                    class="nav-link <?= $pestanaActiva === 'eventos' ? 'active' : '' ?>"
                    data-bs-toggle="pill"
                    data-bs-target="#eventos"
                    type="button">

                    Eventos

                </button>

            </li>


            <li class="nav-item">

                <button
                    class="nav-link <?= $pestanaActiva === 'clientes' ? 'active' : '' ?>"
                    data-bs-toggle="pill"
                    data-bs-target="#clientes"
                    type="button">

                    Clientes

                </button>

            </li>


            <li class="nav-item">

                <button
                    class="nav-link <?= $pestanaActiva === 'reservas' ? 'active' : '' ?>"
                    data-bs-toggle="pill"
                    data-bs-target="#reservas"
                    type="button">

                    Reservaciones

                </button>

            </li>

        </ul>



        <div class="tab-content">

            <?php require __DIR__ . '/views/eventos.php'; ?>

            <?php require __DIR__ . '/views/clientes.php'; ?>

            <?php require __DIR__ . '/views/reservas.php'; ?>

        </div>

    </main>




    <footer class="container py-4 text-center text-secondary small">

        Sistema de Reservación de Eventos · Proyecto académico

    </footer>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/alerts.js"></script>



    <?php if ($flash): ?>

        <script>
            mostrarAlerta(
                <?= json_encode($flash['tipo']) ?>,
                <?= json_encode(
                    $flash['mensaje'],
                    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
                ) ?>
            );
        </script>

    <?php endif; ?>

</body>

</html>