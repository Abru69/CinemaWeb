DROP DATABASE IF EXISTS cine_web;
CREATE DATABASE cine_web;
USE cine_web;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'empleado', 'admin') DEFAULT 'cliente',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Guarda historial de cuentas eliminadas
CREATE TABLE cancelaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    correo VARCHAR(100),
    fecha_cancelacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    motivo TEXT
);


CREATE TABLE peliculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    sinopsis TEXT,
    duracion_min INT NOT NULL,
    clasificacion VARCHAR(10),
    genero VARCHAR(50),
    imagen_url VARCHAR(255)
);


CREATE TABLE funciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelicula_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    sala VARCHAR(10) NOT NULL,
    total_asientos INT NOT NULL,
    FOREIGN KEY (pelicula_id) REFERENCES peliculas(id) ON DELETE CASCADE
);


CREATE TABLE asientos_reservados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    funcion_id INT NOT NULL,
    asiento VARCHAR(5) NOT NULL,
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (funcion_id, asiento),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (funcion_id) REFERENCES funciones(id) ON DELETE CASCADE
);



INSERT INTO usuarios (nombre, correo, contraseña) VALUES
('Ana López', 'ana@gmail.com', SHA2('ana1234', 256)),
('Carlos Pérez', 'carlos@gmail.com', SHA2('carlos123', 256));


INSERT INTO peliculas (titulo, sinopsis, duracion_min, clasificacion, genero, imagen_url) VALUES
('Inception', 'Un ladrón que roba secretos a través de los sueños.', 148, 'PG-13', 'Ciencia Ficción', 'https://ejemplo.com/inception.jpg'),
('Coco', 'Un niño se embarca en un viaje musical por la Tierra de los Muertos.', 105, 'A', 'Animación', 'https://ejemplo.com/coco.jpg');


INSERT INTO funciones (pelicula_id, fecha, hora, sala, total_asientos) VALUES
(1, '2025-07-02', '18:00:00', '1', 50),
(2, '2025-07-02', '20:00:00', '2', 60);


INSERT INTO asientos_reservados (usuario_id, funcion_id, asiento) VALUES
(1, 1, 'A1'),
(2, 2, 'B5');



INSERT INTO cancelaciones (usuario_id, correo, motivo)
SELECT id, correo, 'Cancelación solicitada por el usuario'
FROM usuarios
WHERE correo = 'ana@gmail.com';


DELETE FROM usuarios WHERE correo = 'ana@gmail.com';


select * from usuarios;
select * from peliculas;

SELECT hora FROM funciones WHERE id_pelicula = 1 ORDER BY hora ASC;


UPDATE usuarios
SET rol = 'admin'
WHERE correo = 'arreola@gmail.com';

SELECT * FROM usuarios WHERE correo = 'tu_correo@ejemplo.com';

select * from funciones;

select * from peliculas;

INSERT INTO peliculas (titulo, sinopsis, duracion_min, clasificacion, genero, imagen_url)
VALUES ('Sin asignar', 'Película dummy para salas sin asignar', 0, '', '', '');

SELECT * FROM asientos_reservados;

DELETE FROM asientos_reservados WHERE funcion_id = 7;

ALTER TABLE asientos_reservados ADD COLUMN confirmado TINYINT(1) NOT NULL DEFAULT 0;