-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 16-09-2025 a las 13:58:42
-- Versión del servidor: 10.11.10-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u130454517_modulo_vista`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aportante`
--

CREATE TABLE `aportante` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `valor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autorizaciones`
--

CREATE TABLE `autorizaciones` (
  `id` int(11) NOT NULL,
  `cedula` int(11) DEFAULT NULL,
  `nombres` varchar(50) DEFAULT NULL,
  `direccion` varchar(50) DEFAULT NULL,
  `localidad` varchar(50) DEFAULT NULL,
  `barrio` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `celular` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `autorizacion` text NOT NULL,
  `correo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `camara_comercio`
--

CREATE TABLE `camara_comercio` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) NOT NULL,
  `tiene_camara` text NOT NULL,
  `nombre` text NOT NULL,
  `razon` text NOT NULL,
  `activdad` text NOT NULL,
  `observacion` text NOT NULL DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `composicion_familiar`
--

CREATE TABLE `composicion_familiar` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `id_parentesco` int(11) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `id_ocupacion` int(11) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `id_conviven` varchar(50) DEFAULT NULL,
  `observacion` text NOT NULL DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concepto_final_evaluador`
--

CREATE TABLE `concepto_final_evaluador` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `actitud` varchar(200) DEFAULT NULL,
  `condiciones_vivienda` varchar(200) DEFAULT NULL,
  `dinamica_familiar` varchar(200) DEFAULT NULL,
  `condiciones_economicas` varchar(200) DEFAULT NULL,
  `condiciones_academicas` varchar(200) DEFAULT NULL,
  `evaluacion_experiencia_laboral` varchar(200) DEFAULT NULL,
  `observaciones` varchar(200) DEFAULT NULL,
  `id_concepto_final` int(11) DEFAULT NULL,
  `nombre_evaluador` varchar(200) DEFAULT NULL,
  `id_concepto_seguridad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_bancarias`
--

CREATE TABLE `cuentas_bancarias` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `id_entidad` text DEFAULT NULL,
  `id_tipo_cuenta` text DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `observaciones` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `data_credito`
--

CREATE TABLE `data_credito` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) NOT NULL,
  `entidad` text NOT NULL,
  `cuotas` text NOT NULL,
  `pago_mensual` text NOT NULL,
  `deuda` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `id` int(11) NOT NULL,
  `departamento` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `nit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_salud`
--

CREATE TABLE `estados_salud` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `id_estado_salud` int(11) DEFAULT NULL,
  `tipo_enfermedad` int(11) DEFAULT NULL,
  `tipo_enfermedad_cual` varchar(200) DEFAULT 'N/A',
  `limitacion_fisica` int(11) DEFAULT NULL,
  `limitacion_fisica_cual` varchar(200) DEFAULT 'N/A',
  `tipo_medicamento` int(11) DEFAULT NULL,
  `tipo_medicamento_cual` varchar(200) DEFAULT 'N/A',
  `ingiere_alcohol` int(11) DEFAULT NULL,
  `ingiere_alcohol_cual` varchar(200) DEFAULT 'N/A',
  `fuma` int(11) DEFAULT NULL,
  `observacion` text NOT NULL DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_vivienda`
--

CREATE TABLE `estado_vivienda` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  `observacion` text NOT NULL DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudios`
--

CREATE TABLE `estudios` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `centro_estudios` varchar(50) DEFAULT NULL,
  `id_jornada` text DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `anno` int(11) DEFAULT NULL,
  `titulos` text DEFAULT NULL,
  `id_resultado` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluados`
--

CREATE TABLE `evaluados` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `id_tipo_documentos` int(11) DEFAULT NULL,
  `cedula_expedida` int(11) DEFAULT NULL,
  `nombres` varchar(50) DEFAULT NULL,
  `apellidos` varchar(50) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `fecha_expedicion` date DEFAULT NULL,
  `lugar_nacimiento` varchar(50) DEFAULT NULL,
  `celular_1` int(20) DEFAULT NULL,
  `celular_2` int(20) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `id_rh` int(11) DEFAULT NULL,
  `id_estatura` int(11) DEFAULT NULL,
  `peso_kg` int(11) DEFAULT NULL,
  `id_estado_civil` int(11) DEFAULT NULL,
  `hacer_cuanto` int(11) NOT NULL DEFAULT 0,
  `numero_hijos` int(11) DEFAULT NULL,
  `direccion` varchar(50) DEFAULT NULL,
  `id_ciudad` int(11) DEFAULT NULL,
  `localidad` varchar(50) DEFAULT NULL,
  `barrio` varchar(50) DEFAULT NULL,
  `id_estrato` int(11) DEFAULT NULL,
  `correo` varchar(50) DEFAULT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `tiene_multa_simit` tinyint(1) DEFAULT 0,
  `tiene_tarjeta_militar` tinyint(1) DEFAULT 0,
  `observacion` text NOT NULL DEFAULT 'N/A',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencia_fotografica`
--

CREATE TABLE `evidencia_fotografica` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `ruta` varchar(50) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `experiencia_laboral`
--

CREATE TABLE `experiencia_laboral` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `empresa` varchar(50) DEFAULT NULL,
  `tiempo` varchar(50) DEFAULT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `salario` varchar(50) DEFAULT NULL,
  `retiro` varchar(50) DEFAULT NULL,
  `concepto` varchar(50) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `firmas`
--

CREATE TABLE `firmas` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) NOT NULL,
  `ruta` varchar(50) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formularios`
--

CREATE TABLE `formularios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `version` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foto_perfil_autorizacion`
--

CREATE TABLE `foto_perfil_autorizacion` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `ruta` varchar(50) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foto_perfil_visita`
--

CREATE TABLE `foto_perfil_visita` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) NOT NULL,
  `ruta` text NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gasto`
--

CREATE TABLE `gasto` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `alimentacion_val` text DEFAULT NULL,
  `educacion_val` text DEFAULT NULL,
  `salud_val` text DEFAULT NULL,
  `recreacion_val` text DEFAULT NULL,
  `cuota_creditos_val` text DEFAULT NULL,
  `arriendo_val` text DEFAULT NULL,
  `servicios_publicos_val` text DEFAULT NULL,
  `otros_val` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informacion_judicial`
--

CREATE TABLE `informacion_judicial` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `denuncias_opc` int(11) DEFAULT NULL,
  `denuncias_desc` text DEFAULT 'N/A',
  `procesos_judiciales_opc` int(11) DEFAULT NULL,
  `procesos_judiciales_desc` text DEFAULT 'N/A',
  `preso_opc` int(11) DEFAULT NULL,
  `preso_desc` text DEFAULT 'N/A',
  `familia_detenido_opc` int(11) DEFAULT NULL,
  `familia_detenido_desc` text DEFAULT 'N/A',
  `centros_penitenciarios_opc` int(11) DEFAULT NULL,
  `centros_penitenciarios_desc` text DEFAULT 'N/A',
  `revi_fiscal` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informacion_pareja`
--

CREATE TABLE `informacion_pareja` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `cedula` int(11) NOT NULL,
  `id_tipo_documentos` int(11) DEFAULT NULL,
  `cedula_expedida` int(11) DEFAULT NULL,
  `nombres` varchar(50) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `id_genero` int(11) DEFAULT NULL,
  `id_nivel_academico` int(11) DEFAULT NULL,
  `actividad` varchar(50) DEFAULT NULL,
  `empresa` varchar(50) DEFAULT NULL,
  `antiguedad` int(11) DEFAULT NULL,
  `direccion_empresa` varchar(50) DEFAULT NULL,
  `telefono_1` varchar(50) DEFAULT NULL,
  `telefono_2` varchar(50) DEFAULT NULL,
  `vive_candidato` int(11) DEFAULT NULL,
  `observacion` text NOT NULL DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos_mensuales`
--

CREATE TABLE `ingresos_mensuales` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `salario_val` text DEFAULT NULL,
  `pension_val` text DEFAULT NULL,
  `arriendo_val` text DEFAULT NULL,
  `trabajo_independiente_val` text DEFAULT NULL,
  `otros_val` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_enseres`
--

CREATE TABLE `inventario_enseres` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `televisor_cant` int(11) DEFAULT NULL,
  `dvd_cant` int(11) DEFAULT NULL,
  `teatro_casa_cant` int(11) DEFAULT NULL,
  `equipo_sonido_cant` int(11) DEFAULT NULL,
  `computador_cant` int(11) DEFAULT NULL,
  `impresora_cant` int(11) DEFAULT NULL,
  `movil_cant` int(11) DEFAULT NULL,
  `estufa_cant` int(11) DEFAULT NULL,
  `nevera_cant` int(11) DEFAULT NULL,
  `lavadora_cant` int(11) DEFAULT NULL,
  `microondas_cant` int(11) DEFAULT NULL,
  `moto_cant` int(11) DEFAULT NULL,
  `carro_cant` int(11) DEFAULT NULL,
  `observacion` text NOT NULL DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id_municipio` int(11) NOT NULL,
  `municipio` varchar(50) DEFAULT NULL,
  `estado` int(11) NOT NULL,
  `departamento_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `observaciones_academicas`
--

CREATE TABLE `observaciones_academicas` (
  `id` int(11) NOT NULL,
  `id_cedula` varchar(20) NOT NULL,
  `observacion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `observaciones_laborales`
--

CREATE TABLE `observaciones_laborales` (
  `id` int(11) NOT NULL,
  `id_cedula` varchar(20) NOT NULL,
  `observacion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opciones`
--

CREATE TABLE `opciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_concepto_final`
--

CREATE TABLE `opc_concepto_final` (
  `id_concepto_final` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_concepto_seguridad`
--

CREATE TABLE `opc_concepto_seguridad` (
  `id_concepto_seguridad` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_conviven`
--

CREATE TABLE `opc_conviven` (
  `id_conviven` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_cuenta`
--

CREATE TABLE `opc_cuenta` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_entidad`
--

CREATE TABLE `opc_entidad` (
  `id_entidad` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_estados`
--

CREATE TABLE `opc_estados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_estado_civiles`
--

CREATE TABLE `opc_estado_civiles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_estado_vivienda`
--

CREATE TABLE `opc_estado_vivienda` (
  `id_estado` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_estaturas`
--

CREATE TABLE `opc_estaturas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_estratos`
--

CREATE TABLE `opc_estratos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_genero`
--

CREATE TABLE `opc_genero` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_informacion_judicial`
--

CREATE TABLE `opc_informacion_judicial` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_inventario_enseres`
--

CREATE TABLE `opc_inventario_enseres` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_jornada`
--

CREATE TABLE `opc_jornada` (
  `id_jornada` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_marca`
--

CREATE TABLE `opc_marca` (
  `id_marca` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_modelo`
--

CREATE TABLE `opc_modelo` (
  `id_modelo` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_nivel_academico`
--

CREATE TABLE `opc_nivel_academico` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_num_hijos`
--

CREATE TABLE `opc_num_hijos` (
  `id` int(11) NOT NULL,
  `nombre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_ocupacion`
--

CREATE TABLE `opc_ocupacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_parametro`
--

CREATE TABLE `opc_parametro` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_parentesco`
--

CREATE TABLE `opc_parentesco` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_peso`
--

CREATE TABLE `opc_peso` (
  `id` int(11) NOT NULL,
  `nombre` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_propiedad`
--

CREATE TABLE `opc_propiedad` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_resultado`
--

CREATE TABLE `opc_resultado` (
  `id_resultado` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_rh`
--

CREATE TABLE `opc_rh` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_sector`
--

CREATE TABLE `opc_sector` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_servicios_publicos`
--

CREATE TABLE `opc_servicios_publicos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_tipo_cuenta`
--

CREATE TABLE `opc_tipo_cuenta` (
  `id_tipo_cuenta` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_tipo_documentos`
--

CREATE TABLE `opc_tipo_documentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_tipo_inversion`
--

CREATE TABLE `opc_tipo_inversion` (
  `id_tipo_inversion` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_tipo_vivienda`
--

CREATE TABLE `opc_tipo_vivienda` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_vehiculo`
--

CREATE TABLE `opc_vehiculo` (
  `id_vehiculo` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opc_viven`
--

CREATE TABLE `opc_viven` (
  `id_vive_candidato` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pasivos`
--

CREATE TABLE `pasivos` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `item` text DEFAULT 'N/A',
  `id_entidad` text DEFAULT 'N/A',
  `id_tipo_inversion` text DEFAULT 'N/A',
  `id_ciudad` int(11) DEFAULT NULL,
  `deuda` text DEFAULT 'N/A',
  `cuota_mes` text DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `patrimonio`
--

CREATE TABLE `patrimonio` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `valor_vivienda` text DEFAULT 'N/A',
  `direccion` text DEFAULT 'N/A',
  `id_vehiculo` text DEFAULT 'N/A',
  `id_marca` text DEFAULT 'N/A',
  `id_modelo` text DEFAULT 'N/A',
  `id_ahorro` text DEFAULT 'N/A',
  `otros` text DEFAULT 'N/A',
  `observacion` text NOT NULL DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_publicos`
--

CREATE TABLE `servicios_publicos` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `agua` int(11) DEFAULT NULL,
  `luz` int(11) DEFAULT NULL,
  `gas` int(11) DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `alcantarillado` int(11) DEFAULT NULL,
  `internet` int(11) DEFAULT NULL,
  `administracion` int(11) DEFAULT NULL,
  `parqueadero` int(11) DEFAULT NULL,
  `observacion` text NOT NULL DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_vivienda`
--

CREATE TABLE `tipo_vivienda` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `id_tipo_vivienda` int(11) DEFAULT NULL,
  `id_sector` int(11) DEFAULT NULL,
  `id_propietario` int(11) DEFAULT NULL,
  `numero_de_familia` int(11) DEFAULT NULL,
  `personas_nucleo_familiar` int(11) DEFAULT NULL,
  `tiempo_sector` date DEFAULT NULL,
  `numero_de_pisos` int(11) DEFAULT NULL,
  `observacion` text NOT NULL DEFAULT 'N/A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

CREATE TABLE `ubicacion` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) DEFAULT NULL,
  `longitud` varchar(50) DEFAULT NULL,
  `latitud` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion_autorizacion`
--

CREATE TABLE `ubicacion_autorizacion` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) NOT NULL,
  `ruta` text NOT NULL,
  `nombre` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion_foto`
--

CREATE TABLE `ubicacion_foto` (
  `id` int(11) NOT NULL,
  `id_cedula` int(11) NOT NULL,
  `ruta` text NOT NULL,
  `nombre` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `cedula` int(20) DEFAULT NULL,
  `rol` int(11) DEFAULT NULL,
  `correo` varchar(50) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1 COMMENT 'Estado activo del usuario',
  `ultimo_acceso` timestamp NULL DEFAULT NULL COMMENT 'Último acceso del usuario',
  `intentos_fallidos` int(11) DEFAULT 0 COMMENT 'Contador de intentos fallidos',
  `bloqueado_hasta` timestamp NULL DEFAULT NULL COMMENT 'Fecha hasta cuando está bloqueado',
  `fecha_actualizacion` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha de última actualización'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aportante`
--
ALTER TABLE `aportante`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `autorizaciones`
--
ALTER TABLE `autorizaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `camara_comercio`
--
ALTER TABLE `camara_comercio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `composicion_familiar`
--
ALTER TABLE `composicion_familiar`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `concepto_final_evaluador`
--
ALTER TABLE `concepto_final_evaluador`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `data_credito`
--
ALTER TABLE `data_credito`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estados_salud`
--
ALTER TABLE `estados_salud`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estado_vivienda`
--
ALTER TABLE `estado_vivienda`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estudios`
--
ALTER TABLE `estudios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `evaluados`
--
ALTER TABLE `evaluados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_cedula` (`id_cedula`);

--
-- Indices de la tabla `evidencia_fotografica`
--
ALTER TABLE `evidencia_fotografica`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `experiencia_laboral`
--
ALTER TABLE `experiencia_laboral`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `firmas`
--
ALTER TABLE `firmas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_cedula` (`id_cedula`);

--
-- Indices de la tabla `formularios`
--
ALTER TABLE `formularios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `foto_perfil_autorizacion`
--
ALTER TABLE `foto_perfil_autorizacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_cedula` (`id_cedula`);

--
-- Indices de la tabla `foto_perfil_visita`
--
ALTER TABLE `foto_perfil_visita`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `gasto`
--
ALTER TABLE `gasto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `informacion_judicial`
--
ALTER TABLE `informacion_judicial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `informacion_pareja`
--
ALTER TABLE `informacion_pareja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ingresos_mensuales`
--
ALTER TABLE `ingresos_mensuales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario_enseres`
--
ALTER TABLE `inventario_enseres`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id_municipio`);

--
-- Indices de la tabla `observaciones_academicas`
--
ALTER TABLE `observaciones_academicas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `observaciones_laborales`
--
ALTER TABLE `observaciones_laborales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opciones`
--
ALTER TABLE `opciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_concepto_final`
--
ALTER TABLE `opc_concepto_final`
  ADD PRIMARY KEY (`id_concepto_final`);

--
-- Indices de la tabla `opc_concepto_seguridad`
--
ALTER TABLE `opc_concepto_seguridad`
  ADD PRIMARY KEY (`id_concepto_seguridad`);

--
-- Indices de la tabla `opc_conviven`
--
ALTER TABLE `opc_conviven`
  ADD PRIMARY KEY (`id_conviven`);

--
-- Indices de la tabla `opc_cuenta`
--
ALTER TABLE `opc_cuenta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_entidad`
--
ALTER TABLE `opc_entidad`
  ADD PRIMARY KEY (`id_entidad`);

--
-- Indices de la tabla `opc_estados`
--
ALTER TABLE `opc_estados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_estado_civiles`
--
ALTER TABLE `opc_estado_civiles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_estado_vivienda`
--
ALTER TABLE `opc_estado_vivienda`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `opc_estaturas`
--
ALTER TABLE `opc_estaturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_estratos`
--
ALTER TABLE `opc_estratos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_genero`
--
ALTER TABLE `opc_genero`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_informacion_judicial`
--
ALTER TABLE `opc_informacion_judicial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_inventario_enseres`
--
ALTER TABLE `opc_inventario_enseres`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_jornada`
--
ALTER TABLE `opc_jornada`
  ADD PRIMARY KEY (`id_jornada`);

--
-- Indices de la tabla `opc_marca`
--
ALTER TABLE `opc_marca`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indices de la tabla `opc_modelo`
--
ALTER TABLE `opc_modelo`
  ADD PRIMARY KEY (`id_modelo`);

--
-- Indices de la tabla `opc_nivel_academico`
--
ALTER TABLE `opc_nivel_academico`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_num_hijos`
--
ALTER TABLE `opc_num_hijos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_ocupacion`
--
ALTER TABLE `opc_ocupacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_parametro`
--
ALTER TABLE `opc_parametro`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_parentesco`
--
ALTER TABLE `opc_parentesco`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_peso`
--
ALTER TABLE `opc_peso`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_propiedad`
--
ALTER TABLE `opc_propiedad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_resultado`
--
ALTER TABLE `opc_resultado`
  ADD PRIMARY KEY (`id_resultado`);

--
-- Indices de la tabla `opc_rh`
--
ALTER TABLE `opc_rh`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_sector`
--
ALTER TABLE `opc_sector`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_servicios_publicos`
--
ALTER TABLE `opc_servicios_publicos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_tipo_cuenta`
--
ALTER TABLE `opc_tipo_cuenta`
  ADD PRIMARY KEY (`id_tipo_cuenta`);

--
-- Indices de la tabla `opc_tipo_documentos`
--
ALTER TABLE `opc_tipo_documentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_tipo_inversion`
--
ALTER TABLE `opc_tipo_inversion`
  ADD PRIMARY KEY (`id_tipo_inversion`);

--
-- Indices de la tabla `opc_tipo_vivienda`
--
ALTER TABLE `opc_tipo_vivienda`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opc_vehiculo`
--
ALTER TABLE `opc_vehiculo`
  ADD PRIMARY KEY (`id_vehiculo`);

--
-- Indices de la tabla `opc_viven`
--
ALTER TABLE `opc_viven`
  ADD PRIMARY KEY (`id_vive_candidato`);

--
-- Indices de la tabla `pasivos`
--
ALTER TABLE `pasivos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `patrimonio`
--
ALTER TABLE `patrimonio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicios_publicos`
--
ALTER TABLE `servicios_publicos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_vivienda`
--
ALTER TABLE `tipo_vivienda`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ubicacion_autorizacion`
--
ALTER TABLE `ubicacion_autorizacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_cedula` (`id_cedula`);

--
-- Indices de la tabla `ubicacion_foto`
--
ALTER TABLE `ubicacion_foto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuarios_activo` (`activo`),
  ADD KEY `idx_usuarios_ultimo_acceso` (`ultimo_acceso`),
  ADD KEY `idx_usuarios_intentos_fallidos` (`intentos_fallidos`),
  ADD KEY `idx_usuarios_bloqueado_hasta` (`bloqueado_hasta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aportante`
--
ALTER TABLE `aportante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `autorizaciones`
--
ALTER TABLE `autorizaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `camara_comercio`
--
ALTER TABLE `camara_comercio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `composicion_familiar`
--
ALTER TABLE `composicion_familiar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `concepto_final_evaluador`
--
ALTER TABLE `concepto_final_evaluador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `data_credito`
--
ALTER TABLE `data_credito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estados_salud`
--
ALTER TABLE `estados_salud`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_vivienda`
--
ALTER TABLE `estado_vivienda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estudios`
--
ALTER TABLE `estudios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `evaluados`
--
ALTER TABLE `evaluados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `evidencia_fotografica`
--
ALTER TABLE `evidencia_fotografica`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `experiencia_laboral`
--
ALTER TABLE `experiencia_laboral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `firmas`
--
ALTER TABLE `firmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `formularios`
--
ALTER TABLE `formularios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `foto_perfil_autorizacion`
--
ALTER TABLE `foto_perfil_autorizacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `foto_perfil_visita`
--
ALTER TABLE `foto_perfil_visita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gasto`
--
ALTER TABLE `gasto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informacion_judicial`
--
ALTER TABLE `informacion_judicial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `informacion_pareja`
--
ALTER TABLE `informacion_pareja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresos_mensuales`
--
ALTER TABLE `ingresos_mensuales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_enseres`
--
ALTER TABLE `inventario_enseres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id_municipio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `observaciones_academicas`
--
ALTER TABLE `observaciones_academicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `observaciones_laborales`
--
ALTER TABLE `observaciones_laborales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opciones`
--
ALTER TABLE `opciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_concepto_final`
--
ALTER TABLE `opc_concepto_final`
  MODIFY `id_concepto_final` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_concepto_seguridad`
--
ALTER TABLE `opc_concepto_seguridad`
  MODIFY `id_concepto_seguridad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_cuenta`
--
ALTER TABLE `opc_cuenta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_estados`
--
ALTER TABLE `opc_estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_estado_civiles`
--
ALTER TABLE `opc_estado_civiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_estaturas`
--
ALTER TABLE `opc_estaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_estratos`
--
ALTER TABLE `opc_estratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_genero`
--
ALTER TABLE `opc_genero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_nivel_academico`
--
ALTER TABLE `opc_nivel_academico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_num_hijos`
--
ALTER TABLE `opc_num_hijos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_ocupacion`
--
ALTER TABLE `opc_ocupacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_parametro`
--
ALTER TABLE `opc_parametro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_parentesco`
--
ALTER TABLE `opc_parentesco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_peso`
--
ALTER TABLE `opc_peso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_propiedad`
--
ALTER TABLE `opc_propiedad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_rh`
--
ALTER TABLE `opc_rh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_sector`
--
ALTER TABLE `opc_sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_tipo_documentos`
--
ALTER TABLE `opc_tipo_documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opc_tipo_vivienda`
--
ALTER TABLE `opc_tipo_vivienda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pasivos`
--
ALTER TABLE `pasivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `patrimonio`
--
ALTER TABLE `patrimonio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `servicios_publicos`
--
ALTER TABLE `servicios_publicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_vivienda`
--
ALTER TABLE `tipo_vivienda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ubicacion_autorizacion`
--
ALTER TABLE `ubicacion_autorizacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ubicacion_foto`
--
ALTER TABLE `ubicacion_foto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
