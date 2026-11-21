function mostrarAlerta(tipo, mensaje) {
    Swal.fire({
        icon: tipo,
        title: tipo === 'success' ? 'Correcto' : 'Atención',
        text: mensaje,
        confirmButtonText: 'Aceptar'
    });
}




const hash = window.location.hash;

if (hash) {
    const boton = document.querySelector(
        `[data-bs-target="${hash}"]`
    );

    if (boton) {
        const pestana = bootstrap.Tab.getOrCreateInstance(boton);
        pestana.show();
    }
}




function obtenerEstadoEvento(fechaInicio, fechaFin) {

    const ahora = new Date();

    const inicio = new Date(fechaInicio);
    const fin = fechaFin ? new Date(fechaFin) : null;

    if (ahora < inicio) {
        return 'Pendiente';
    }

    if (fin && ahora > fin) {
        return 'Finalizado';
    }

    return 'En curso';
}



function actualizarEtiqueta(elemento, estado) {

    elemento.textContent = estado;

    elemento.classList.remove(
        'bg-primary',
        'bg-success',
        'bg-danger',
        'text-bg-success',
        'text-bg-danger',
        'text-bg-secondary',
        'text-bg-primary'
    );

    switch (estado) {

        case 'Pendiente':
            elemento.classList.add('text-bg-primary');
            break;

        case 'En curso':
            elemento.classList.add('text-bg-success');
            break;

        case 'Finalizado':
            elemento.classList.add('text-bg-danger');
            break;

        case 'Cancelada':
            elemento.classList.add('text-bg-secondary');
            break;

        default:
            elemento.classList.add('text-bg-secondary');
    }
}




function actualizarMensajeVacio(tbody, id, texto, columnas) {

    const filas = tbody.querySelectorAll('tr[data-evento-id]');

    let mensaje = document.getElementById(id);

    if (filas.length === 0) {

        if (!mensaje) {

            mensaje = document.createElement('tr');
            mensaje.id = id;

            const celda = document.createElement('td');
            celda.colSpan = columnas;
            celda.className = 'text-center text-secondary py-4';
            celda.textContent = texto;

            mensaje.appendChild(celda);
            tbody.appendChild(mensaje);
        }

    } else if (mensaje) {

        mensaje.remove();
    }
}


// Actualizar contadores de eventos.

function actualizarContadoresEventos() {

    const vigentes = document.getElementById(
        'tabla-eventos-vigentes'
    );

    const finalizados = document.getElementById(
        'tabla-eventos-finalizados'
    );

    if (!vigentes || !finalizados) {
        return;
    }

    const contadorVigentes = document.getElementById(
        'contador-vigentes'
    );

    const contadorFinalizados = document.getElementById(
        'contador-finalizados'
    );

    const cantidadVigentes = vigentes.querySelectorAll(
        'tr[data-evento-id]'
    ).length;

    const cantidadFinalizados = finalizados.querySelectorAll(
        'tr[data-evento-id]'
    ).length;

    if (contadorVigentes) {
        contadorVigentes.textContent = cantidadVigentes;
    }

    if (contadorFinalizados) {
        contadorFinalizados.textContent = cantidadFinalizados;
    }

    actualizarMensajeVacio(
        vigentes,
        'sin-eventos-vigentes',
        'No hay eventos vigentes registrados.',
        7
    );

    actualizarMensajeVacio(
        finalizados,
        'sin-eventos-finalizados',
        'No hay eventos finalizados.',
        7
    );
}


// Revisar todos los eventos.

function actualizarEstadosEventos() {

    const tablaVigentes = document.getElementById(
        'tabla-eventos-vigentes'
    );

    const tablaFinalizados = document.getElementById(
        'tabla-eventos-finalizados'
    );

    if (!tablaVigentes || !tablaFinalizados) {
        return;
    }

    const filas = document.querySelectorAll(
        '#tabla-eventos-vigentes tr[data-evento-id], ' +
        '#tabla-eventos-finalizados tr[data-evento-id]'
    );

    filas.forEach((fila) => {

        const fechaInicio = fila.dataset.fechaInicio;
        const fechaFin = fila.dataset.fechaFin;

        if (!fechaInicio) {
            return;
        }

        const estado = obtenerEstadoEvento(
            fechaInicio,
            fechaFin
        );

        const etiqueta = fila.querySelector('.estado-evento');

        if (etiqueta) {
            actualizarEtiqueta(etiqueta, estado);
        }

        if (estado === 'Finalizado') {

            fila.classList.add('table-danger');

            if (fila.parentElement !== tablaFinalizados) {
                tablaFinalizados.appendChild(fila);
            }

        } else {

            fila.classList.remove('table-danger');

            if (fila.parentElement !== tablaVigentes) {
                tablaVigentes.appendChild(fila);
            }
        }

    });

    actualizarContadoresEventos();
}




function actualizarEstadosReservaciones() {

    const filas = document.querySelectorAll(
        '#tabla-reservaciones tr[data-reserva-id]'
    );

    filas.forEach((fila) => {

        const estadoOriginal = fila.dataset.estadoOriginal;

    
        if (estadoOriginal === 'Cancelada') {
            return;
        }

        const fechaInicio = fila.dataset.fechaInicio;
        const fechaFin = fila.dataset.fechaFin;

        if (!fechaInicio) {
            return;
        }

        const estado = obtenerEstadoEvento(
            fechaInicio,
            fechaFin
        );

        const etiqueta = fila.querySelector(
            '.estado-reservacion'
        );

        if (etiqueta) {
            actualizarEtiqueta(etiqueta, estado);
        }

        
        if (estado === 'Finalizado') {

            const formulario = fila.querySelector(
                '.form-cancelar'
            );

            if (formulario) {

                formulario.remove();

                const accion = document.createElement('span');
                accion.className = 'text-secondary accion-reservacion';
                accion.textContent = '—';

                fila.lastElementChild.appendChild(accion);
            }
        }

    });

    const contador = document.getElementById(
        'contador-reservaciones'
    );

    if (contador) {
        contador.textContent = filas.length;
    }
}


// Actualización automática en vivo

function actualizarSistemaEnVivo() {

    actualizarEstadosEventos();
    actualizarEstadosReservaciones();

}


actualizarSistemaEnVivo();

setInterval(actualizarSistemaEnVivo, 1000);




document.addEventListener('submit', function (evento) {

    const formulario = evento.target;

    
    if (formulario.matches('.form-eliminar')) {

        evento.preventDefault();

        Swal.fire({
            title: '¿Eliminar registro?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Volver'
        }).then((resultado) => {

            if (resultado.isConfirmed) {
                formulario.submit();
            }

        });

        return;
    }


    
    if (formulario.matches('.form-cancelar')) {

        evento.preventDefault();

        Swal.fire({
            title: '¿Cancelar reservación?',
            text: '¿Estás seguro de que deseas cancelar esta reservación?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'Volver'
        }).then((resultado) => {

            if (resultado.isConfirmed) {
                formulario.submit();
            }

        });

        return;
    }


    if (formulario.matches('.formulario-actualizar')) {

        evento.preventDefault();

        Swal.fire({
            title: '¿Guardar los cambios?',
            text: 'Se actualizará la información del cliente.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Volver'
        }).then((resultado) => {

            if (resultado.isConfirmed) {
                formulario.submit();
            }

        });

    }

});
