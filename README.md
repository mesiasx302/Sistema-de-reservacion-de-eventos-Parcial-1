# Sistema de Reservación de Eventos

Aplicación web para administrar eventos, clientes y reservaciones. Desarrollada con PHP, MySQL, DDEV, Bootstrap 5 y JavaScript con SweetAlert2.

## Requisitos
- Docker
- DDEV
- Git

## Iniciar el proyecto
```bash
ddev start
ddev import-db --file=database/schema.sql
ddev launch
```

Si la importación de la base de datos no se ejecuta automáticamente, también puedes entrar a phpMyAdmin o importar `database/schema.sql` desde el contenedor de base de datos.

## Funcionalidades
- Registrar, listar, editar y eliminar eventos.
- Registrar fecha y hora de inicio y finalización.
- Clasificar automáticamente los eventos según su fecha y hora:
- Pendiente: el evento todavía no ha comenzado.
- En curso: el evento ya comenzó y todavía no ha terminado.
- Finalizado: el evento terminó.
- Separar los eventos vigentes de los finalizados.
- Identificar visualmente los eventos finalizados mediante indicadores de color rojo.
- Actualizar automáticamente los estados de los eventos sin recargar la página.
- Validación básica del lado del servidor y consultas PDO preparadas.


## Tecnologías utilizadas

- PHP
- MySQL
- PDO
- DDEV
- Docker
- Bootstrap 5
- JavaScript
- SweetAlert2

## Estructura

proyecto/
│
├── config/
│   └── database.php
│
├── database/
│   └── schema.sql
│
├── models/
│   ├── Eventos.php
│   ├── Clientes.php
│   └── Reservaciones.php
│
├── public/
│   ├── index.php
│   │
│   ├── assets/
│   │   ├── css/
│   │   │   └── styles.css
│   │   │
│   │   └── js/
│   │       └── alerts.js
│   │
│   └── views/
│       ├── eventos.php
│       ├── clientes.php
│       └── reservas.php
│
└── README.md

