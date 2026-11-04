-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 05-11-2026 a las 00:37:50
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
-- Base de datos: `deskflow_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos`
--

CREATE TABLE `archivos` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_mensaje` int(11) DEFAULT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `tamano_bytes` int(11) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `eliminado_por` int(11) DEFAULT NULL,
  `fecha_eliminacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activa','Inactiva') NOT NULL DEFAULT 'Activa',
  `creado_por` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`, `creado_por`, `fecha_creacion`, `modificado_por`, `fecha_actualizacion`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 'Soporte Técnico', 'Problemas con equipos, software o conexiones', 'Activa', NULL, '2026-10-15 15:29:29', 4, '2026-10-29 21:09:06', NULL, NULL),
(2, 'Facturación', 'Consultas sobre facturas, cobros o presupuestos', 'Activa', NULL, '2026-10-15 15:29:29', 4, '2026-10-21 19:37:58', NULL, NULL),
(3, 'Accesos y Cuentas', 'Problemas de contraseñas, permisos o bloqueos', 'Activa', NULL, '2026-10-15 15:29:29', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_tickets`
--

CREATE TABLE `historial_tickets` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `detalle` text DEFAULT NULL,
  `fecha_accion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_tickets`
--

INSERT INTO `historial_tickets` (`id`, `id_ticket`, `id_usuario`, `accion`, `detalle`, `fecha_accion`) VALUES
(1, 7, 3, 'Cierre por Cliente', 'El cliente marcó el ticket como resuelto y dejó un mensaje de cierre.', '2026-11-03 22:04:00'),
(2, 6, 2, 'Ticket Reabierto', 'El agente reabrió el ticket (vuelve a estado En Proceso).', '2026-11-04 20:13:16'),
(3, 6, 2, 'Cambio de Prioridad', 'El agente cambió la prioridad del ticket a: Baja', '2026-11-04 20:13:38'),
(4, 6, 2, 'Ticket Reasignado', 'El agente derivó el ticket al agente ID #3', '2026-11-04 20:15:16'),
(5, 8, 2, 'Ticket Tomado', 'El agente tomó el ticket y pasó a estado En Proceso.', '2026-11-04 20:16:53'),
(6, 8, 2, 'Cambio de Prioridad', 'El agente cambió la prioridad del ticket a: Critica', '2026-11-04 20:17:09'),
(7, 8, 2, 'Ticket Reasignado', 'El agente derivó el ticket al agente ID #3', '2026-11-04 20:17:28'),
(8, 1, 2, 'Ticket Reabierto', 'El agente reabrió el ticket (vuelve a estado En Proceso).', '2026-11-04 20:17:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_envio` datetime DEFAULT current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `id_ticket`, `id_usuario`, `contenido`, `fecha_envio`, `modificado_por`, `fecha_actualizacion`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 1, 1, 'Hola! Cuando lo vas a arreglar cdv?', '2026-10-15 15:45:42', NULL, NULL, NULL, NULL),
(2, 2, 3, 'Hola cdv agarrame el ticket', '2026-10-15 19:42:37', NULL, NULL, NULL, NULL),
(3, 1, 2, 'Nunca', '2026-10-15 20:39:57', NULL, NULL, NULL, NULL),
(4, 2, 2, 'Agarrame esta', '2026-10-15 21:01:48', NULL, NULL, NULL, NULL),
(5, 3, 2, 'No tiene solucion', '2026-10-20 21:07:17', NULL, '2026-10-29 19:47:34', '2026-10-29 19:47:34', 4),
(6, 5, 3, 'Hola, me lo podes solucionar YA', '2026-10-29 20:00:11', NULL, '2026-10-29 20:02:23', '2026-10-29 20:02:23', 4),
(7, 4, 2, 'Pasame la ip', '2026-10-29 20:01:22', NULL, NULL, NULL, NULL),
(8, 6, 3, 'No arranca la impresora', '2026-10-29 20:13:11', NULL, NULL, NULL, NULL),
(9, 6, 2, 'Ahi voy', '2026-10-29 20:13:50', NULL, NULL, NULL, NULL),
(10, 6, 3, '✅ TICKET RESUELTO POR EL CLIENTE:\nYa lo arregle', '2026-11-03 21:57:58', NULL, NULL, NULL, NULL),
(11, 7, 3, '✅ TICKET RESUELTO POR EL CLIENTE:\nYA LO ARREGLE', '2026-11-03 22:04:00', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_passwords`
--

CREATE TABLE `recuperacion_passwords` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(3, 'Administrador'),
(2, 'Agente'),
(1, 'Cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_agente` int(11) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `prioridad` enum('Baja','Media','Alta','Critica') NOT NULL,
  `estado` enum('Abierto','En Proceso','Cerrado') NOT NULL DEFAULT 'Abierto',
  `creado_por` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id`, `id_cliente`, `id_agente`, `id_categoria`, `titulo`, `descripcion`, `prioridad`, `estado`, `creado_por`, `fecha_creacion`, `modificado_por`, `fecha_actualizacion`, `fecha_cierre`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 1, 2, 1, 'No puedo apagar la computadora', 'Apreto el boton y no anda', 'Alta', 'En Proceso', NULL, '2026-10-15 15:36:20', 2, '2026-11-04 20:17:38', NULL, NULL, NULL),
(2, 3, 2, 1, 'No puedo abrir word', 'Se me tilda la compu', 'Alta', 'Cerrado', NULL, '2026-10-15 19:42:13', NULL, '2026-10-15 21:01:57', '2026-10-15 21:01:57', NULL, NULL),
(3, 1, 2, 2, 'No puedo facturar', 'No anda el sistema', 'Critica', 'Cerrado', NULL, '2026-10-16 20:27:35', NULL, '2026-10-29 21:04:35', '2026-10-20 21:08:05', '2026-10-29 21:04:35', 4),
(4, 1, 2, 1, 'No puedo escribir bien', 'Soy un burro', 'Critica', 'Cerrado', NULL, '2026-10-20 21:06:39', NULL, '2026-10-29 20:12:09', '2026-10-29 20:01:26', '2026-10-29 20:12:09', 4),
(5, 3, 2, 1, 'No puedo escribir bien', 'Soy bastante boludo', 'Media', 'En Proceso', NULL, '2026-10-29 19:58:31', NULL, '2026-10-29 20:12:00', NULL, '2026-10-29 20:12:00', 4),
(6, 3, 3, 1, 'No anda la impresora', 'No prende', 'Baja', 'En Proceso', NULL, '2026-10-29 20:12:52', 2, '2026-11-04 20:15:16', NULL, NULL, NULL),
(7, 3, NULL, 2, 'No puedo escribir bien', 'asdaSD', 'Media', 'Cerrado', NULL, '2026-11-03 22:03:47', NULL, '2026-11-03 22:04:00', '2026-11-03 22:04:00', NULL, NULL),
(8, 3, 3, 1, 'No anda la impresora', 'asdasdsa', 'Critica', 'En Proceso', NULL, '2026-11-03 22:04:44', 2, '2026-11-04 20:17:28', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `fecha_eliminacion` datetime DEFAULT NULL,
  `eliminado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `id_rol`, `nombre`, `apellido`, `email`, `password`, `creado_por`, `fecha_registro`, `modificado_por`, `fecha_actualizacion`, `fecha_eliminacion`, `eliminado_por`) VALUES
(1, 1, 'Juan', 'Pérez', 'juanperez@gmail.com', '$2y$10$trzCjVO9VtSNeqMshEZ3yOWZdx8j2LlIBhk2g.0VxnqtSHnDMzdAW', NULL, '2026-10-14 23:59:44', 4, '2026-10-29 21:04:46', '2026-10-29 21:04:46', 4),
(2, 2, 'Facundo Manuel', 'Torres', 'facuisei@gmail.com', '$2y$10$yB2pAxhL6ODHeuKtdATD3uKe0GtKkeIOEd93rtaMeZP23/1wrypg2', NULL, '2026-10-15 16:16:56', 4, '2026-10-29 21:19:48', NULL, NULL),
(3, 2, 'Ivan Gonzalo', 'Lapresa', 'Ivan@gmail.com', '$2y$10$Y5tTXaTJdYaRZP4eMqMYeuCnOSXouVzjugPRge.j9WbOpbG1yea8K', NULL, '2026-10-15 19:38:28', NULL, '2026-11-04 20:14:55', NULL, NULL),
(4, 3, 'Manuel Elian', 'Alvarez', 'admin@admin.com', '$2y$10$LZ4m43fWlFX1y9v.iV9WrOX/fgXeE.TW81jgByUkor7ogvHkD3dDO', NULL, '2026-10-15 21:25:35', NULL, '2026-10-15 21:28:01', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ticket` (`id_ticket`),
  ADD KEY `id_mensaje` (`id_mensaje`),
  ADD KEY `creado_por` (`creado_por`),
  ADD KEY `modificado_por` (`modificado_por`),
  ADD KEY `eliminado_por` (`eliminado_por`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creado_por` (`creado_por`),
  ADD KEY `modificado_por` (`modificado_por`),
  ADD KEY `eliminado_por` (`eliminado_por`);

--
-- Indices de la tabla `historial_tickets`
--
ALTER TABLE `historial_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ticket` (`id_ticket`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ticket` (`id_ticket`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `modificado_por` (`modificado_por`),
  ADD KEY `eliminado_por` (`eliminado_por`);

--
-- Indices de la tabla `recuperacion_passwords`
--
ALTER TABLE `recuperacion_passwords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_agente` (`id_agente`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `creado_por` (`creado_por`),
  ADD KEY `modificado_por` (`modificado_por`),
  ADD KEY `eliminado_por` (`eliminado_por`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `creado_por` (`creado_por`),
  ADD KEY `modificado_por` (`modificado_por`),
  ADD KEY `eliminado_por` (`eliminado_por`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `archivos`
--
ALTER TABLE `archivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial_tickets`
--
ALTER TABLE `historial_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `recuperacion_passwords`
--
ALTER TABLE `recuperacion_passwords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `archivos`
--
ALTER TABLE `archivos`
  ADD CONSTRAINT `archivos_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`),
  ADD CONSTRAINT `archivos_ibfk_2` FOREIGN KEY (`id_mensaje`) REFERENCES `mensajes` (`id`),
  ADD CONSTRAINT `archivos_ibfk_3` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `archivos_ibfk_4` FOREIGN KEY (`modificado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `archivos_ibfk_5` FOREIGN KEY (`eliminado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`eliminado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `categorias_ibfk_2` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `categorias_ibfk_3` FOREIGN KEY (`modificado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `historial_tickets`
--
ALTER TABLE `historial_tickets`
  ADD CONSTRAINT `historial_tickets_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`),
  ADD CONSTRAINT `historial_tickets_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`),
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `mensajes_ibfk_3` FOREIGN KEY (`eliminado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `mensajes_ibfk_4` FOREIGN KEY (`modificado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `recuperacion_passwords`
--
ALTER TABLE `recuperacion_passwords`
  ADD CONSTRAINT `recuperacion_passwords_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_agente`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `tickets_ibfk_4` FOREIGN KEY (`eliminado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `tickets_ibfk_5` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `tickets_ibfk_6` FOREIGN KEY (`modificado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`eliminado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_4` FOREIGN KEY (`modificado_por`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
