-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-01-2026 a las 17:31:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `agenda_citas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`id`, `usuario`, `password`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `creado` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefono` varchar(20) NOT NULL,
  `programa` varchar(150) NOT NULL,
  `tramite` varchar(150) NOT NULL,
  `estatus` enum('Pendiente','Se presentó','No se presentó','Reagendado') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `matricula`, `nombre`, `fecha`, `hora`, `creado`, `telefono`, `programa`, `tramite`, `estatus`) VALUES
(2, '22005421', 'Leobardo Quintero Ruiz', '2026-01-16', '08:00:00', '2026-01-12 18:38:21', '8621115622', 'Ingenieria Desarrollo y Gastion de Software', 'Recoger título Técnico Superior Universitario', 'Reagendado'),
(3, '22005421', 'Leobardo Quintero Ruiz', '2026-01-15', '08:40:00', '2026-01-12 20:59:10', '8621115622', 'Ingenieria Desarrollo y Gastion de Software', 'Recoger título Ingeniería', 'Reagendado'),
(4, '22005421', 'Leobardo Quintero Ruiz', '2026-01-16', '09:00:00', '2026-01-13 15:46:57', '8621115622', 'Ingenieria Desarrollo y Gastion de Software', 'Recoger título Ingeniería', 'Reagendado'),
(5, '22005421', 'Leobardo Quintero Ruiz', '2026-01-13', '08:00:00', '2026-01-13 17:10:55', '8621115622', 'Ingenieria Desarrollo y Gastion de Software', 'Recoger título Ingeniería', 'Reagendado'),
(6, '22005421', 'Leobardo Quintero Ruiz', '2026-01-14', '08:00:00', '2026-01-15 14:06:17', '8621115622', 'Ingenieria Desarrollo y Gastion de Software', 'Recoger papelería', 'Pendiente'),
(7, '22005421', 'Leobardo Quintero Ruiz', '2026-01-16', '08:55:00', '2026-01-15 15:49:09', '8621115622', 'Ingenieria Desarrollo y Gastion de Software', 'Recoger título Técnico Superior Universitario', 'Pendiente'),
(8, '22005421', 'Leobardo Quintero Ruiz', '2026-01-16', '08:05:00', '2026-01-15 16:33:01', '8621115622', 'Ingenieria Desarrollo y Gastion de Software', 'Recoger papelería', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `tipo` enum('festivo','horario_bloqueado','turno_inicio','turno_fin','dia_habil') NOT NULL,
  `valor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `tipo`, `valor`) VALUES
(1, 'festivo', '2026-01-19'),
(2, 'horario_bloqueado', '13:00-14:00'),
(28, 'turno_inicio', '08:00'),
(29, 'turno_fin', '15:30'),
(38, 'dia_habil', '1'),
(39, 'dia_habil', '2'),
(40, 'dia_habil', '3'),
(41, 'dia_habil', '4'),
(42, 'dia_habil', '5'),
(43, 'festivo', '2026-01-27');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
