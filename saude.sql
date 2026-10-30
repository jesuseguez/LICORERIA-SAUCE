-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-09-2026 a las 08:52:44
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
-- Base de datos: `saude`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `nombre`, `telefono`, `direccion`) VALUES
(2, 'JESUS EGUEZ', '71621436', 'AV. 3 PASOS AL FRENTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nombre`, `telefono`, `email`, `direccion`) VALUES
(1, 'SUACÉ S.R.L', '71621436', 'contacto@suace.com', 'Santa Cruz, San Jose de Chiquitos, Bolivia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id`, `id_pedido`, `id_producto`, `cantidad`, `precio`, `subtotal`) VALUES
(1, 1, 5, 3, 15.00, 45.00),
(2, 2, 5, 3, 15.00, 45.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_permisos`
--

CREATE TABLE `detalle_permisos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_venta` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `leido` tinyint(1) DEFAULT 0,
  `respondido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `nombre`, `edad`, `telefono`, `correo`, `direccion`, `mensaje`, `fecha`, `leido`, `respondido`) VALUES
(1, 'JESUS EGUEZ', 32, '71621436', 'jesuseguezvaca@gmail.com', 'av. 3 pasos al frente 3er anillo', 'modo prueba', '2026-09-16 01:50:32', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('QR','Transferencia','Efectivo') DEFAULT 'QR',
  `estado` enum('pendiente','confirmado','enviado','cancelado') DEFAULT 'pendiente',
  `comprobante` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_usuario`, `id_cliente`, `fecha`, `total`, `metodo_pago`, `estado`, `comprobante`, `notas`) VALUES
(1, 5, 2, '2026-09-16 00:40:35', 45.00, 'QR', 'enviado', NULL, 'QUE ESTE BIEN FRIO'),
(2, 5, 2, '2026-09-16 00:51:30', 45.00, 'QR', 'enviado', NULL, 'QUE ESTE BIEN FRIA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`) VALUES
(2, 'clientes'),
(6, 'configuracion'),
(4, 'nueva_venta'),
(3, 'productos'),
(1, 'usuarios'),
(5, 'ventas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `descripcion_larga` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `existencia` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `categoria` varchar(50) DEFAULT 'General',
  `grado_alcohol` decimal(4,2) DEFAULT 0.00,
  `volumen_ml` int(11) DEFAULT 750
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `codigo`, `descripcion`, `imagen`, `descripcion_larga`, `precio`, `existencia`, `activo`, `categoria`, `grado_alcohol`, `volumen_ml`) VALUES
(1, 'LIC001', 'Tequila José Cuervo Especial 750ml', NULL, NULL, 190.00, 43, 1, 'Tequila', 38.00, 750),
(2, 'LIC002', 'Whisky Johnnie Walker Red Label 750ml', NULL, NULL, 220.00, 50, 1, 'Whisky', 40.00, 750),
(3, 'LIC003', 'Ron Bacardí Carta Blanca 750ml', NULL, NULL, 150.00, 60, 1, 'Ron', 37.50, 750),
(4, 'LIC004', 'Vodka Absolut Original 750ml', NULL, NULL, 180.00, 35, 1, 'Vodka', 40.00, 750),
(5, 'LIC005', 'Cerveza Paceña Botella 620ml', NULL, NULL, 15.00, 182, 1, 'Cerveza', 5.00, 620),
(6, 'LIC006', 'Cerveza Corona Extra 355ml', NULL, NULL, 25.00, 150, 1, 'Cerveza', 4.50, 355),
(7, 'LIC007', 'Vino Tinto Concha y Toro 750ml', NULL, NULL, 85.00, 80, 1, 'Vino', 13.50, 750),
(8, 'LIC008', 'Champagne Moët & Chandon 750ml', NULL, NULL, 650.00, 15, 1, 'Champagne', 12.00, 750),
(9, 'LIC009', 'Pisco Capel Reservado 700ml', NULL, NULL, 95.00, 45, 1, 'Pisco', 40.00, 700),
(10, 'LIC010', 'Singani Casa Real 750ml', NULL, NULL, 75.00, 100, 1, 'Singani', 40.00, 750),
(11, 'LIC011', 'Ginebra Beefeater 750ml', NULL, NULL, 210.00, 25, 1, 'Ginebra', 47.00, 750),
(12, 'LIC012', 'Licor Baileys Irish Cream 750ml', NULL, NULL, 175.00, 30, 1, 'Licor', 17.00, 750),
(13, 'BEER001', 'Cerveza ABC', 'images/product/beer/abc_beer.jpg', 'Fundada en 1931, ABC Extra Stout ha sido galardonada con más de 20 medallas de cerveza a nivel internacional.', 1800.00, 50, 1, 'Cerveza', 0.00, 750),
(14, 'BEER002', 'Cerveza Heineken', 'images/product/beer/heineken.jpeg', 'Cerveza premium de origen holandés, con un sabor equilibrado y refrescante.', 2100.00, 40, 1, 'Cerveza', 0.00, 750),
(15, 'BEER003', 'Cerveza Myanmar', 'images/product/beer/myanmar_beer.jpg', 'Cerveza de Myanmar, sabor suave y ligeramente dulce.', 1700.00, 60, 1, 'Cerveza', 0.00, 750),
(16, 'BEER004', 'Cerveza Tiger', 'images/product/beer/tiger_beer.jpg', 'Cerveza rubia de origen asiático, con un sabor fuerte y refrescante.', 1700.00, 45, 1, 'Cerveza', 0.00, 750),
(17, 'LIQ001', 'Baileys Almande', 'images/product/liqueur/baileys_almande.jpg', 'Licor de crema de almendras, vegano y sin lactosa.', 66000.00, 20, 1, 'Licor', 0.00, 750),
(18, 'LIQ002', 'Baileys Coffee', 'images/product/liqueur/baileys-coffee-liqueur.jpg', 'Delicioso licor de crema irlandesa con café.', 52500.00, 25, 1, 'Licor', 0.00, 750),
(19, 'LIQ003', 'Baileys Irish Cream', 'images/product/liqueur/baileys_irish_cream.jpg', 'El clásico licor de crema irlandesa con whisky.', 64500.00, 30, 1, 'Licor', 0.00, 750),
(20, 'LIQ004', 'Jagermeister', 'images/product/liqueur/jagermeister.png', 'Licor alemán de hierbas, con 56 ingredientes botánicos.', 69000.00, 15, 1, 'Licor', 0.00, 750),
(21, 'LIQ005', 'Jagermeister Coolpack', 'images/product/liqueur/jagermeister-coolpack.jpeg', 'Jagermeister en presentación coolpack.', 20600.00, 40, 1, 'Licor', 0.00, 750),
(22, 'RUM001', 'Bacardi Ginger', 'images/product/rum/bacardi_ginger.jpg', 'Ron Bacardí con toque de jengibre.', 57000.00, 22, 1, 'Ron', 0.00, 750),
(23, 'RUM002', 'Bacardi Black', 'images/product/rum/barcardi_black.jpg', 'Ron Bacardí Black, sabor intenso y añejado.', 57000.00, 28, 1, 'Ron', 0.00, 750),
(24, 'RUM003', 'Bacardi Gold', 'images/product/rum/barcardi_gold.jpg', 'Ron Bacardí Gold, suave y dorado.', 57000.00, 35, 1, 'Ron', 0.00, 750),
(25, 'RUM004', 'Bacardi Limon', 'images/product/rum/barcardi_limon.jpg', 'Ron Bacardí con toque cítrico de limón.', 57000.00, 30, 1, 'Ron', 0.00, 750),
(26, 'RUM005', 'Bacardi Spice', 'images/product/rum/barcardi_spiced.jpg', 'Ron Bacardí con especias.', 57000.00, 25, 1, 'Ron', 0.00, 750),
(27, 'RUM006', 'Captain Morgan Dark', 'images/product/rum/captain_morgan_dark.jpg', 'Ron oscuro Captain Morgan.', 57000.00, 20, 1, 'Ron', 0.00, 750),
(28, 'RUM007', 'Captain Morgan Gold', 'images/product/rum/captain_morgan_gold.jpg', 'Ron dorado Captain Morgan.', 57000.00, 24, 1, 'Ron', 0.00, 750),
(29, 'RUM008', 'Captain Morgan White', 'images/product/rum/captain_morgan_white.jpg', 'Ron blanco Captain Morgan.', 57000.00, 26, 1, 'Ron', 0.00, 750),
(30, 'TEQ001', 'Josecuervo Bocas', 'images/product/Tequila/josecuervo_bocas.png', 'Tequila Jose Cuervo, presentación especial.', 61500.00, 18, 1, 'Tequila', 0.00, 750),
(31, 'TEQ002', 'Josecuervo Gold', 'images/product/Tequila/josecuervo_gold.png', 'Tequila dorado Jose Cuervo.', 61500.00, 20, 1, 'Tequila', 0.00, 750),
(32, 'TEQ003', 'Josecuervo Silver', 'images/product/Tequila/josecuervo_silver.png', 'Tequila plateado Jose Cuervo.', 61500.00, 22, 1, 'Tequila', 0.00, 750),
(33, 'TEQ004', 'Josecuervo Tradicional', 'images/product/Tequila/josecuervo_tradicional.jpg', 'Tequila tradicional Jose Cuervo.', 60000.00, 15, 1, 'Tequila', 0.00, 750),
(34, 'TEQ005', 'Patron Anejo', 'images/product/Tequila/patron_anejo.jpg', 'Tequila Patrón Añejo, premium.', 65000.00, 12, 1, 'Tequila', 0.00, 750),
(35, 'TEQ006', 'Patron Coffee', 'images/product/Tequila/patron_coffee.jpg', 'Tequila Patrón con café.', 65000.00, 10, 1, 'Tequila', 0.00, 750),
(36, 'TEQ007', 'Patron Reposado', 'images/product/Tequila/patron_reposado.png', 'Tequila Patrón Reposado.', 65000.00, 14, 1, 'Tequila', 0.00, 750),
(37, 'TEQ008', 'Patron Silver', 'images/product/Tequila/patron-silver.jpg', 'Tequila Patrón Silver.', 65000.00, 16, 1, 'Tequila', 0.00, 750),
(38, 'VOD001', 'Absolut 100', 'images/product/vodka/absolut_100.jpg', 'Vodka Absolut 100 proof.', 49500.00, 20, 1, 'Vodka', 0.00, 750),
(39, 'VOD002', 'Absolut Apple', 'images/product/vodka/absolut_apple.jpg', 'Vodka Absolut sabor manzana.', 49500.00, 22, 1, 'Vodka', 0.00, 750),
(40, 'VOD003', 'Absolut Cherry', 'images/product/vodka/absolut_cherry.jpg', 'Vodka Absolut sabor cereza.', 49500.00, 18, 1, 'Vodka', 0.00, 750),
(41, 'VOD004', 'Absolut Vanilla', 'images/product/vodka/absolut_vanalia.jpg', 'Vodka Absolut sabor vainilla.', 49500.00, 25, 1, 'Vodka', 0.00, 750),
(42, 'VOD005', 'Absolut Vodka', 'images/product/vodka/absolut_vodka.jpg', 'Vodka Absolut original.', 49500.00, 30, 1, 'Vodka', 0.00, 750),
(43, 'VOD006', 'Smirnoff Vodka', 'images/product/vodka/smirnoff-no-21-vodka.jpeg', 'Vodka Smirnoff No. 21.', 46000.00, 28, 1, 'Vodka', 0.00, 750),
(44, 'VOD007', 'Smirnoff Raspberry', 'images/product/vodka/smirnoff_raspberry.jpg', 'Vodka Smirnoff sabor frambuesa.', 46000.00, 20, 1, 'Vodka', 0.00, 750),
(45, 'VOD008', 'Smirnoff Red & White Berry', 'images/product/vodka/Smirnoff Red White & Berry.png', 'Vodka Smirnoff Red & White Berry.', 50000.00, 15, 1, 'Vodka', 0.00, 750),
(46, 'VOD009', 'Smirnoff Vodka Proof', 'images/product/vodka/Smirnoff_Vodka_proof.jpg', 'Vodka Smirnoff Proof.', 46000.00, 22, 1, 'Vodka', 0.00, 750),
(47, 'WHI001', 'Ballantines 12 Years Old', 'images/product/whisky/ballatines_12yearsold.jpg', 'Whisky Ballantines 12 años.', 22500.00, 20, 1, 'Whisky', 0.00, 750),
(48, 'WHI002', 'Ballantines Barrel Smooth', 'images/product/whisky/ballatines_barrel_smooth.jpg', 'Whisky Ballantines Barrel Smooth.', 21000.00, 25, 1, 'Whisky', 0.00, 750);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` varchar(20) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `telefono`, `direccion`, `usuario`, `clave`, `rol`) VALUES
(1, 'Administrador', 'admin@licoreria.com', NULL, NULL, 'admin', '0192023a7bbd73250516f069df18b500', 'admin'),
(5, 'JESUS EGUEZ', 'jesuseguezvaca@gmail.com', '71621436', 'AV. 3 PASOS AL FRENTE', 'cliente', '7159bbe0c8ca2a67230a26b72dea7557', 'cliente'),
(6, 'JESUS EGUEZ VACA', 'admin@admin.com', '71621436', 'av. 3 pasos al frente 3er anillo', 'test', '098f6bcd4621d373cade4e832627b4f6', 'proveedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitas`
--

CREATE TABLE `visitas` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total` int(11) DEFAULT 0,
  `actualizado` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `visitas`
--

INSERT INTO `visitas` (`id`, `fecha`, `total`, `actualizado`) VALUES
(1, '2026-09-16', 2, '2026-09-16 02:52:21');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_permisos`
--
ALTER TABLE `detalle_permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `visitas`
--
ALTER TABLE `visitas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fecha` (`fecha`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `detalle_permisos`
--
ALTER TABLE `detalle_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `visitas`
--
ALTER TABLE `visitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`codproducto`);

--
-- Filtros para la tabla `detalle_permisos`
--
ALTER TABLE `detalle_permisos`
  ADD CONSTRAINT `detalle_permisos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_permisos_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD CONSTRAINT `detalle_temp_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_temp_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`codproducto`);

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`codproducto`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idusuario`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`idcliente`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idusuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
