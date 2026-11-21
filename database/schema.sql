
CREATE DATABASE IF NOT EXISTS db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE db;


-- =========================================
-- TABLA: EVENTOS
-- =========================================

CREATE TABLE IF NOT EXISTS Eventos (
    evento_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    fecha DATETIME NOT NULL,
    fecha_fin DATETIME NULL,
    ubicacion VARCHAR(180) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- =========================================
-- TABLA: CLIENTES
-- =========================================

CREATE TABLE IF NOT EXISTS Clientes (
    cliente_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(25) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- =========================================
-- TABLA: RESERVACIONES
-- =========================================

CREATE TABLE IF NOT EXISTS Reservaciones (
    reservacion_id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    cliente_id INT NOT NULL,
    fecha_reservacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('Activa', 'Cancelada') NOT NULL DEFAULT 'Activa',

    CONSTRAINT fk_reservacion_evento
        FOREIGN KEY (evento_id)
        REFERENCES Eventos(evento_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_reservacion_cliente
        FOREIGN KEY (cliente_id)
        REFERENCES Clientes(cliente_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_reservacion_evento_cliente (evento_id, cliente_id)

) ENGINE=InnoDB;


-- =========================================
-- DATOS INICIALES: EVENTOS
-- =========================================

INSERT INTO Eventos (nombre, descripcion, fecha, fecha_fin, ubicacion) VALUES

('Conferencia de Tecnología',
 'Charlas sobre desarrollo de software.',
 '2027-02-15 09:00:00',
 '2027-02-15 12:00:00',
 'Auditorio principal'),

('Taller de Desarrollo Web',
 'Taller práctico de PHP y JavaScript.',
 '2027-02-20 14:00:00',
 '2027-02-20 17:00:00',
 'Laboratorio 2'),

('Conferencia de Inteligencia Artificial',
 'Exposición sobre inteligencia artificial en empresas.',
 '2027-03-05 09:00:00',
 '2027-03-05 12:00:00',
 'Auditorio UNIANDES'),

('Feria de Tecnología',
 'Presentación de proyectos tecnológicos e innovación.',
 '2027-03-10 10:00:00',
 '2027-03-10 16:00:00',
 'Centro de Convenciones'),

('Seminario de Ciberseguridad',
 'Buenas prácticas para proteger sistemas y redes.',
 '2027-03-15 08:30:00',
 '2027-03-15 12:30:00',
 'Sala de conferencias'),

('Curso de Bases de Datos',
 'Capacitación sobre MySQL y administración de bases de datos.',
 '2027-03-18 14:00:00',
 '2027-03-18 17:00:00',
 'Laboratorio 1'),

('Taller de JavaScript',
 'Desarrollo de aplicaciones interactivas con JavaScript.',
 '2027-03-20 09:00:00',
 '2027-03-20 12:00:00',
 'Laboratorio 3'),

('Congreso de Software',
 'Encuentro académico sobre ingeniería de software.',
 '2027-03-22 08:00:00',
 '2027-03-22 17:00:00',
 'Auditorio principal'),

('Exposición de Proyectos Web',
 'Presentación de proyectos desarrollados por estudiantes.',
 '2027-03-25 10:00:00',
 '2027-03-25 13:00:00',
 'Bloque académico'),

('Taller de Diseño Web',
 'Introducción a Bootstrap, CSS y diseño responsivo.',
 '2027-03-28 13:00:00',
 '2027-03-28 16:00:00',
 'Laboratorio 2'),

('Charla de Emprendimiento Digital',
 'Ideas y herramientas para iniciar negocios digitales.',
 '2027-03-30 15:00:00',
 '2027-03-30 17:00:00',
 'Sala de reuniones'),

('Capacitación de PHP y MySQL',
 'Desarrollo de sistemas web conectados a bases de datos.',
 '2027-04-05 09:00:00',
 '2027-04-05 13:00:00',
 'Laboratorio 4');


-- =========================================
-- DATOS FICTICIOS: CLIENTES
-- =========================================

INSERT INTO Clientes (nombre, apellido, email, telefono) VALUES

('Ivan', 'Bautista', 'ivanba86@uniandes.com', '0991234567'),

('Maria', 'Lopez', 'maria@uniandes.com', '0987654321'),

('Carlos', 'Mendoza', 'carlos.mendoza@gmail.com', '0991112233'),

('Andrea', 'Castro', 'andrea.castro@gmail.com', '0982223344'),

('Luis', 'Ramirez', 'luis.ramirez@gmail.com', '0973334455'),

('Daniela', 'Torres', 'daniela.torres@gmail.com', '0964445566'),

('Pedro', 'Sanchez', 'pedro.sanchez@gmail.com', '0955556677'),

('Sofia', 'Vargas', 'sofia.vargas@gmail.com', '0946667788'),

('Miguel', 'Herrera', 'miguel.herrera@gmail.com', '0937778899'),

('Valeria', 'Morales', 'valeria.morales@gmail.com', '0928889900'),

('Jorge', 'Alvarez', 'jorge.alvarez@gmail.com', '0919990011'),

('Camila', 'Reyes', 'camila.reyes@gmail.com', '0901234567');
