<?php
include 'conexion.php';

$sql = "
CREATE TABLE IF NOT EXISTS admin (
  id SERIAL PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS citas (
  id SERIAL PRIMARY KEY,
  matricula VARCHAR(20) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  fecha DATE NOT NULL,
  hora TIME NOT NULL,
  creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  telefono VARCHAR(20) NOT NULL,
  programa VARCHAR(150) NOT NULL,
  tramite VARCHAR(150) NOT NULL,
  estatus VARCHAR(20) DEFAULT 'Pendiente'
);

CREATE TABLE IF NOT EXISTS configuracion (
  id SERIAL PRIMARY KEY,
  tipo VARCHAR(50) NOT NULL,
  valor VARCHAR(50) NOT NULL
);
";

$conn->exec($sql);
echo "Tablas creadas correctamente!";
