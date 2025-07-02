-- Crear base de datos
CREATE DATABASE IF NOT EXISTS cine;
USE cine;

-- Tabla de usuarios
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  rol ENUM('cliente', 'editor', 'admin') DEFAULT 'cliente'
);

-- Tabla de películas
CREATE TABLE peliculas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(100),
  sinopsis TEXT,
  genero VARCHAR(50),
  clasificacion VARCHAR(10),
  duracion VARCHAR(10),
  imagen VARCHAR(100),
  id_sala INT NOT NULL
);

-- Tabla de salas
CREATE TABLE salas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero INT,
  tipo VARCHAR(20),
  capacidad INT
);

-- Tabla de funciones
CREATE TABLE funciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_pelicula INT,
  id_sala INT,
  fecha DATE,
  hora TIME,
  FOREIGN KEY (id_pelicula) REFERENCES peliculas(id),
  FOREIGN KEY (id_sala) REFERENCES salas(id)
);

-- Datos de ejemplo
INSERT INTO peliculas (titulo, sinopsis, genero, clasificacion, duracion, imagen)
VALUES
('Lilo y Sticht', 'una prueba de consulta', 'caricatura', '+3', '2h30m', 'lilo.png'),
 ('28 años despues', 'Han pasado casi tres décadas desde que el virus de la rabia escapó de un laboratorio de armas biológicas y ahora, aún en una cuarentena impuesta sin piedad, algunos han encontrado formas de existir entre los infectados.', 'Terror', 'B18', '1h30m', 'Exterminio.jpg'),
('Matrix', 'Un hacker descubre la verdad sobre su realidad.', 'Ciencia ficción', 'B15', '2h16m', 'matrix.jpg');


INSERT INTO salas (numero, tipo, capacidad) VALUES (1, '2D', 100),
(2, '3D', 100);

INSERT INTO funciones (id_pelicula, id_sala, fecha, hora)
VALUES (2, 2, '2025-07-01', '16:30:00'),
(1, 1, '2025-07-01', '18:00:00');

insert into usuarios (nombre, email, password, rol)
values
('Alan', 'alanhg665@gmail.com', '1234', 'admin');

select * from usuarios;
select * from peliculas;
select * from salas;
select * from funciones;

SET SQL_SAFE_UPDATES = 0;

DELETE FROM peliculas;

ALTER TABLE usuarios AUTO_INCREMENT = 1;

DELETE  FROM usuarios
WHERE nombre = 'Alan';

SET SQL_SAFE_UPDATES = 1;
