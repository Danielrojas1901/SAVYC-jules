-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-07-2025 a las 06:51:26
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
-- Base de datos: `savycplus`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ActualizarStatusPago` (IN `tabla_origen` VARCHAR(30), IN `id_origen` INT, IN `nuevo_status` INT)   BEGIN
    DECLARE v_cod_caja INT;
    IF tabla_origen = 'cuenta_bancaria' THEN
        -- Actualiza tipos de pago asociados a la cuenta bancaria
        UPDATE detalle_tipo_pago
        SET status = nuevo_status
        WHERE cod_cuenta_bancaria = id_origen;

    ELSEIF tabla_origen = 'control_caja' THEN
        -- Obtener la caja asociada
        SELECT cod_caja INTO v_cod_caja
        FROM control
        WHERE cod_control = id_origen;

        -- Actualiza tipos de pago asociados a esa caja
        UPDATE detalle_tipo_pago
        SET status = nuevo_status
        WHERE cod_caja = v_cod_caja;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_saldoc` (IN `p_cod_cuenta` INT, IN `p_monto` DECIMAL(18,2), IN `p_tipo` VARCHAR(10))   BEGIN
    DECLARE v_naturaleza VARCHAR(10);

    -- Obtener la naturaleza de la cuenta
    SELECT naturaleza INTO v_naturaleza
    FROM cuentas_contables
    WHERE cod_cuenta = p_cod_cuenta;

    -- Aplicar la lógica contable
    IF (v_naturaleza = 'deudora' AND p_tipo = 'Debe') 
        OR (v_naturaleza = 'acreedora' AND p_tipo = 'Haber') THEN
        UPDATE cuentas_contables
        SET saldo = saldo + p_monto
        WHERE cod_cuenta = p_cod_cuenta;
    ELSE
        UPDATE cuentas_contables
        SET saldo = saldo - p_monto
        WHERE cod_cuenta = p_cod_cuenta;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `consultar_cuentas_contables` ()   BEGIN
WITH RECURSIVE CuentasRecursivas AS (
    -- Caso Base: Selecciona las cuentas de nivel 1 (que no tienen padre)
    SELECT 
        cod_cuenta, 
        codigo_contable, 
        nombre_cuenta, 
        naturaleza, 
        cuenta_padreid, 
        nivel,
    	saldo,
    	status,
        1 AS profundidad -- Indica la profundidad en la jerarquía
    FROM cuentas_contables
    WHERE cuenta_padreid IS NULL

    UNION ALL

    -- Recursión: Une las cuentas hijas con sus padres
    SELECT 
        c.cod_cuenta, 
        c.codigo_contable, 
        c.nombre_cuenta,
    	c.naturaleza,  
        c.cuenta_padreid, 
        c.nivel,
    	c.saldo,
    	c.status,
        cr.profundidad + 1 AS profundidad
    FROM cuentas_contables c
    INNER JOIN CuentasRecursivas cr ON c.cuenta_padreid = cr.cod_cuenta
)
SELECT * FROM CuentasRecursivas ORDER BY codigo_contable;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `gasto_fijos` (IN `fecha` DATE)   BEGIN
DECLARE descripcioN VARCHAR(100);
DECLARE cod_cat_gasto INT;
DECLARE cod_condicion INT;
DECLARE monto INT;
DECLARE fecha_vencimientoN DATE;
DECLARE fecha_creacionN DATE;

SELECT  g.descripcion, g.cod_cat_gasto, g.cod_condicion, g.monto, g.fecha_vencimiento,  DATE_ADD(g.fecha_vencimiento, INTERVAL f.dias DAY) AS fecha_vencimientoN
INTO descripcioN, cod_cat_gasto, cod_condicion, monto,fecha_creacionN, fecha_vencimientoN
FROM gasto g
JOIN categoria_gasto cat ON cat.cod_cat_gasto = g.cod_cat_gasto
JOIN frecuencia_gasto f ON f.cod_frecuencia = cat.cod_frecuencia
JOIN naturaleza_gasto n ON n.cod_naturaleza = cat.cod_naturaleza
WHERE n.nombre_naturaleza = 'fijo' AND g.fecha_vencimiento = fecha;

-- Calcular la próxima fecha de vencimiento sumando la frecuencia en días
    IF NOT EXISTS (
        SELECT 1
        FROM gasto
        WHERE descripcion = descripcioN AND fecha_creacion = CURDATE()
    ) THEN

INSERT INTO gasto(cod_cat_gasto,cod_condicion,descripcion,monto,fecha_creacion,fecha_vencimiento,status) VALUES(cod_cat_gasto,cod_condicion,descripcioN,monto,fecha_creacionN,fecha_vencimientoN,1);
 ELSE
        -- Opcionalmente, manejar el caso en que ya exista
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El gasto fijo ya existe con la misma descripción y fecha de creación.';
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getMovimientosControl` (IN `p_cod_control` INT)   BEGIN
    DECLARE v_cod_caja INT;
    DECLARE v_fecha_apertura DATETIME;
    DECLARE v_fecha_cierre DATETIME;

    -- Obtener datos de la tabla 'control'
    SELECT cod_caja, fecha_apertura, fecha_cierre
    INTO v_cod_caja, v_fecha_apertura, v_fecha_cierre
    FROM control
    WHERE cod_control = p_cod_control;

    -- Movimientos ENTRADA/SALIDA para esa caja entre las fechas del control
    SELECT 
        'VENTA' AS modulo,
        pr.fecha,
        'ENTRADA' AS tipo_movimiento,
        dpr.monto,
        CONCAT('Venta #', v.cod_venta) AS referencia,
        dtp.tipo_moneda,
        c.nombre AS caja
    FROM detalle_pago_recibido dpr
    JOIN pago_recibido pr ON dpr.cod_pago = pr.cod_pago
    JOIN ventas v ON pr.cod_venta = v.cod_venta
    JOIN detalle_tipo_pago dtp ON dpr.cod_tipo_pago = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = v_cod_caja
        AND pr.fecha BETWEEN v_fecha_apertura AND IFNULL(v_fecha_cierre, NOW())

    UNION ALL

    SELECT 
        'COMPRA',
        pe.fecha,
        'SALIDA',
        dpe.monto * -1,
        CONCAT('Compra #', co.cod_compra),
        dtp.tipo_moneda,
        c.nombre
    FROM detalle_pago_emitido dpe
    JOIN pago_emitido pe ON dpe.cod_pago_emitido = pe.cod_pago_emitido
    JOIN compras co ON pe.cod_compra = co.cod_compra
    JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = v_cod_caja
        AND pe.tipo_pago = 'compra'
        AND pe.fecha BETWEEN v_fecha_apertura AND IFNULL(v_fecha_cierre, NOW())

    UNION ALL

    SELECT 
        'GASTO',
        pe.fecha,
        'SALIDA',
        dpe.monto * -1,
        CONCAT('Gasto #', g.cod_gasto),
        dtp.tipo_moneda,
        c.nombre
    FROM detalle_pago_emitido dpe
    JOIN pago_emitido pe ON dpe.cod_pago_emitido = pe.cod_pago_emitido
    JOIN gasto g ON pe.cod_gasto = g.cod_gasto
    JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = v_cod_caja
        AND pe.tipo_pago = 'gasto'
        AND pe.fecha BETWEEN v_fecha_apertura AND IFNULL(v_fecha_cierre, NOW())

    UNION ALL

    SELECT 
        'VUELTO',
        pr.fecha,
        'SALIDA',
        dv.monto * -1,
        CONCAT('Vuelto Venta #', pr.cod_venta),
        dtp.tipo_moneda,
        c.nombre
    FROM detalle_vueltoe dv
    JOIN vuelto_emitido ve ON dv.cod_vuelto = ve.cod_vuelto
    JOIN pago_recibido pr ON pr.cod_vuelto = ve.cod_vuelto
    JOIN detalle_tipo_pago dtp ON dv.cod_tipo_pago = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = v_cod_caja
        AND pr.fecha BETWEEN v_fecha_apertura AND IFNULL(v_fecha_cierre, NOW())

    UNION ALL

    SELECT 
        'VUELTO',
        pe.fecha,
        'ENTRADA',
        dvr.monto,
        CONCAT('Vuelto Compra #', pe.cod_compra),
        dtp.tipo_moneda,
        c.nombre
    FROM detalle_pago_emitido dpe
    JOIN pago_emitido pe ON dpe.cod_pago_emitido = pe.cod_pago_emitido
    JOIN vuelto_recibido vr ON pe.cod_vuelto_r = vr.cod_vuelto_r
    JOIN detalle_vueltor dvr ON vr.cod_vuelto_r = dvr.cod_vuelto_r
    JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = v_cod_caja
        AND pe.tipo_pago = 'compra'
        AND pe.fecha BETWEEN v_fecha_apertura AND IFNULL(v_fecha_cierre, NOW())
    
    UNION ALL
    
    SELECT 
        'VUELTO',
        pe.fecha,
        'ENTRADA',
        dvr.monto,
        CONCAT('Vuelto Gasto #', pe.cod_gasto),
        dtp.tipo_moneda,
        c.nombre
    FROM detalle_pago_emitido dpe
    JOIN pago_emitido pe ON dpe.cod_pago_emitido = pe.cod_pago_emitido
    JOIN vuelto_recibido vr ON pe.cod_vuelto_r = vr.cod_vuelto_r
    JOIN detalle_vueltor dvr ON vr.cod_vuelto_r = dvr.cod_vuelto_r
    JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = v_cod_caja
    	AND pe.tipo_pago = 'gasto'
        AND pe.fecha BETWEEN v_fecha_apertura AND IFNULL(v_fecha_cierre, NOW())
    ORDER BY fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegistrarMovimientoPagoGeneral` (IN `p_cod_operacion` INT, IN `p_cod_tipo_op` INT, IN `p_cod_detalle_op` INT)   BEGIN
    DECLARE v_cod_relacionado INT;
    DECLARE v_condicion_pago VARCHAR(10);
    DECLARE v_status INT;
    DECLARE v_fecha DATETIME;
    DECLARE v_cod_condicion INT;

    -- Verifica si el movimiento ya existe
    IF NOT EXISTS (
        SELECT 1
        FROM movimientos
        WHERE cod_operacion = p_cod_operacion 
        AND cod_tipo_op = p_cod_tipo_op
        AND cod_detalle_op = p_cod_detalle_op
    ) THEN

        -- Pago recibido (venta)
        IF p_cod_detalle_op = 3 THEN
            SELECT cod_venta INTO v_cod_relacionado
            FROM pago_recibido
            WHERE cod_pago = p_cod_operacion
            LIMIT 1;

            SELECT condicion_pago, status, fecha
            INTO v_condicion_pago, v_status, v_fecha
            FROM ventas
            WHERE cod_venta = v_cod_relacionado
            LIMIT 1;

            IF v_condicion_pago = 'credito' THEN
                INSERT INTO movimientos (cod_operacion, cod_tipo_op, cod_detalle_op, fecha, status)
                VALUES (p_cod_operacion, p_cod_tipo_op, p_cod_detalle_op, DATE(v_fecha), 1);
            END IF;

        -- Pago emitido de compra
        ELSEIF p_cod_detalle_op = 4 THEN
            SELECT cod_compra INTO v_cod_relacionado
            FROM pago_emitido
            WHERE cod_pago_emitido = p_cod_operacion
            LIMIT 1;

            SELECT condicion_pago, status, fecha
            INTO v_condicion_pago, v_status, v_fecha
            FROM compras
            WHERE cod_compra = v_cod_relacionado
            LIMIT 1;

            IF v_condicion_pago = 'credito' THEN
                INSERT INTO movimientos (cod_operacion, cod_tipo_op, cod_detalle_op, fecha, status)
                VALUES (p_cod_operacion, p_cod_tipo_op, p_cod_detalle_op, DATE(v_fecha), 1);
            END IF;

        -- Pago emitido de gasto
        ELSEIF p_cod_detalle_op = 5 THEN
            SELECT cod_gasto INTO v_cod_relacionado
            FROM pago_emitido
            WHERE cod_pago_emitido = p_cod_operacion
            LIMIT 1;

            SELECT cod_condicion, status, fecha_creacion
            INTO v_cod_condicion, v_status, v_fecha
            FROM gasto
            WHERE cod_gasto = v_cod_relacionado
            LIMIT 1;

            IF v_cod_condicion IN (2, 3) THEN
                INSERT INTO movimientos (cod_operacion, cod_tipo_op, cod_detalle_op, fecha, status)
                VALUES (p_cod_operacion, p_cod_tipo_op, p_cod_detalle_op, DATE(v_fecha), 1);
            END IF;
        END IF;

    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrar_ajusteinventario` (IN `p_cod_operacion` INT, IN `p_cod_detalle_op` INT)   BEGIN
    DECLARE v_fecha DATETIME;
    DECLARE v_cod_tipo_op INT DEFAULT 5; -- 'ajuste'

    -- Obtener la fecha desde la tabla correspondiente
    IF p_cod_detalle_op = 6 THEN
        -- Carga
        SELECT fecha INTO v_fecha
        FROM carga
        WHERE cod_carga = p_cod_operacion
        LIMIT 1;

    ELSEIF p_cod_detalle_op = 7 THEN
        -- Descarga
        SELECT fecha INTO v_fecha
        FROM descarga
        WHERE cod_descarga = p_cod_operacion
        LIMIT 1;
    END IF;

    -- Evitar duplicados
    IF NOT EXISTS (
        SELECT 1
        FROM movimientos
        WHERE cod_operacion = p_cod_operacion
          AND cod_tipo_op = v_cod_tipo_op
          AND cod_detalle_op = p_cod_detalle_op
    ) THEN
        INSERT INTO movimientos (cod_operacion, cod_tipo_op, cod_detalle_op, fecha, status)
        VALUES (p_cod_operacion, v_cod_tipo_op, p_cod_detalle_op, DATE(v_fecha), 1);
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `R_movimiento_operacion` (IN `p_operacion` INT, IN `p_cod_tipo_op` INT)   BEGIN
    DECLARE o_condicion_pago VARCHAR(10);
    DECLARE o_status INT;
    DECLARE o_fecha DATETIME;
    DECLARE o_detalle_op INT;
    DECLARE o_cod_condicion INT;

    -- Validar si ya existe movimiento
    IF NOT EXISTS (
        SELECT 1
        FROM movimientos
        WHERE cod_operacion = p_operacion AND cod_tipo_op = p_cod_tipo_op
    ) THEN

        -- Obtener datos según tipo de operación
        IF p_cod_tipo_op = 1 THEN -- Venta
            SELECT condicion_pago, status, fecha
            INTO o_condicion_pago, o_status, o_fecha
            FROM ventas
            WHERE cod_venta = p_operacion
            LIMIT 1;

        ELSEIF p_cod_tipo_op = 2 THEN -- Compra
            SELECT condicion_pago, status, fecha
            INTO o_condicion_pago, o_status, o_fecha
            FROM compras
            WHERE cod_compra = p_operacion
            LIMIT 1;

        ELSEIF p_cod_tipo_op = 3 THEN -- Gasto
            SELECT cod_condicion, status, fecha_creacion
            INTO o_cod_condicion, o_status, o_fecha
            FROM gasto
            WHERE cod_gasto = p_operacion
            LIMIT 1;

            -- Mapear cod_condicion a texto equivalente (opcional para claridad)
            IF o_cod_condicion IN (1, 4) AND o_status = 3 THEN
                SET o_detalle_op = 1; -- prepago o contado → contado
            ELSEIF o_cod_condicion IN (2, 3) AND o_status = 1 THEN
                SET o_detalle_op = 2; -- pospago o crédito → crédito
            END IF;
        END IF;

        -- Para venta o compra (texto en condicion_pago)
        IF (p_cod_tipo_op IN (1,2)) THEN
            IF o_condicion_pago = 'contado' AND o_status = 3 THEN
                SET o_detalle_op = 1;
            ELSEIF o_condicion_pago = 'credito' AND o_status = 1 THEN
                SET o_detalle_op = 2;
            END IF;
        END IF;

        -- Insertar si se definió detalle_op válido
        IF o_detalle_op IS NOT NULL THEN
            INSERT INTO movimientos (cod_operacion, cod_tipo_op, cod_detalle_op, fecha, status)
            VALUES (p_operacion, p_cod_tipo_op, o_detalle_op, DATE(o_fecha), 1);
        END IF;

    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_movimientos_caja` (IN `p_cod_caja` INT, IN `p_fecha_apertura` DATETIME, IN `p_fecha_cierre` DATETIME)   BEGIN
    SELECT 
        'VENTA' AS modulo,
        pr.fecha,
        'ENTRADA' AS tipo_movimiento,
        dpr.monto,
        CONCAT('Venta #', v.cod_venta) AS referencia,
        dtp.tipo_moneda,
        c.nombre AS caja
    FROM detalle_pago_recibido dpr
    JOIN pago_recibido pr ON dpr.cod_pago = pr.cod_pago
    JOIN ventas v ON pr.cod_venta = v.cod_venta
    JOIN detalle_tipo_pago dtp ON dpr.cod_tipo_pago = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = p_cod_caja
        AND pr.fecha BETWEEN p_fecha_apertura AND IFNULL(p_fecha_cierre, NOW())

    UNION ALL

    SELECT 
        'COMPRA',
        pe.fecha,
        'SALIDA',
        dpe.monto * -1,
        CONCAT('Compra #', co.cod_compra),
        dtp.tipo_moneda,
        c.nombre
    FROM detalle_pago_emitido dpe
    JOIN pago_emitido pe ON dpe.cod_pago_emitido = pe.cod_pago_emitido
    JOIN compras co ON pe.cod_compra = co.cod_compra
    JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pago = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = p_cod_caja
        AND pe.tipo_pago = 'compra'
        AND pe.fecha BETWEEN p_fecha_apertura AND IFNULL(p_fecha_cierre, NOW())

    UNION ALL

    SELECT 
        'GASTO',
        pe.fecha,
        'SALIDA',
        dpe.monto * -1,
        CONCAT('Gasto #', g.cod_gasto),
        dtp.tipo_moneda,
        c.nombre
    FROM detalle_pago_emitido dpe
    JOIN pago_emitido pe ON dpe.cod_pago_emitido = pe.cod_pago_emitido
    JOIN gasto g ON pe.cod_gasto = g.cod_gasto
    JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pago = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = p_cod_caja
        AND pe.tipo_pago = 'gasto'
        AND pe.fecha BETWEEN p_fecha_apertura AND IFNULL(p_fecha_cierre, NOW())

    UNION ALL

    SELECT 
        'VUELTO',
        pr.fecha,
        'SALIDA',
        dv.monto * -1,
        CONCAT('Vuelto Venta #', pr.cod_venta),
        dtp.tipo_moneda,
        c.nombre
    FROM detalle_vueltoe dv
    JOIN vuelto_emitido ve ON dv.cod_vuelto = ve.cod_vuelto
    JOIN pago_recibido pr ON pr.cod_vuelto = ve.cod_vuelto
    JOIN detalle_tipo_pago dtp ON dv.cod_tipo_pago = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = p_cod_caja
        AND pr.fecha BETWEEN p_fecha_apertura AND IFNULL(p_fecha_cierre, NOW())

    UNION ALL

    SELECT 
        'VUELTO',
        pe.fecha,
        'ENTRADA',
        dpe.monto,
        CONCAT('Vuelto Compra #', pe.cod_compra),
        dtp.tipo_moneda,
        c.nombre
    FROM detalle_pago_emitido dpe
    JOIN pago_emitido pe ON dpe.cod_pago_emitido = pe.cod_pago_emitido
    JOIN vuelto_recibido vr ON pe.cod_vuelto_r = vr.cod_vuelto_r
    JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pago = dtp.cod_tipo_pago
    JOIN caja c ON dtp.cod_caja = c.cod_caja
    WHERE c.cod_caja = p_cod_caja
        AND pe.tipo_pago = 'compra'
        AND pe.fecha BETWEEN p_fecha_apertura AND IFNULL(p_fecha_cierre, NOW())

    ORDER BY fecha DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `truncaneitor` ()   BEGIN
    -- Desactiva temporalmente las restricciones de claves foráneas
    SET FOREIGN_KEY_CHECKS = 0;

    -- Truncamiento de tablas (en orden que evita errores de dependencias)
    TRUNCATE TABLE detalle_asientos;
    TRUNCATE TABLE detalle_pago_emitido;
    TRUNCATE TABLE detalle_pago_recibido;
    TRUNCATE TABLE detalle_vueltoe;
    TRUNCATE TABLE detalle_vueltor;
    TRUNCATE TABLE detalle_compras;
    TRUNCATE TABLE detalle_carga;
    TRUNCATE TABLE detalle_descarga;
    TRUNCATE TABLE detalle_productos;
    TRUNCATE TABLE detalle_ventas;

    TRUNCATE TABLE asientos_contables;
    TRUNCATE TABLE pago_emitido;
    TRUNCATE TABLE pago_recibido;
    TRUNCATE TABLE vuelto_emitido;
    TRUNCATE TABLE vuelto_recibido;
    
    TRUNCATE TABLE conciliacion;
    TRUNCATE TABLE control;
    TRUNCATE TABLE carga;
    TRUNCATE TABLE descarga;
    TRUNCATE TABLE compras;
    TRUNCATE TABLE ventas;
    TRUNCATE TABLE gasto;
    TRUNCATE TABLE movimientos;
    
    TRUNCATE TABLE analisis_rentabilidad;
    TRUNCATE TABLE presupuestoS;
    TRUNCATE TABLE proyecciones_futuras;
    TRUNCATE TABLE proyecciones_historicas;
    TRUNCATE TABLE stock_mensual;

    TRUNCATE TABLE productos;
    TRUNCATE TABLE presentacion_producto;
    TRUNCATE TABLE marcas;
    TRUNCATE TABLE unidades_medida;
    TRUNCATE TABLE categorias;
    TRUNCATE TABLE categoria_gasto;

    TRUNCATE TABLE proveedores;
    TRUNCATE TABLE prov_representantes;
    TRUNCATE TABLE tlf_proveedores;

    TRUNCATE TABLE empresa;

    -- Si la vista se materializa, usa DROP VIEW (no TRUNCATE)
    DROP VIEW IF EXISTS vista_pendientes_compras_gastos;

    -- Reactiva las restricciones de claves foráneas
    SET FOREIGN_KEY_CHECKS = 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `analisis_rentabilidad`
--

CREATE TABLE `analisis_rentabilidad` (
  `cod_analisis` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `mes` date NOT NULL,
  `ventas_totales` int(11) NOT NULL,
  `costo_ventas` decimal(10,2) DEFAULT NULL,
  `gastos` decimal(10,2) NOT NULL,
  `margen_bruto` decimal(10,2) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asientos_contables`
--

CREATE TABLE `asientos_contables` (
  `cod_asiento` int(11) NOT NULL,
  `cod_mov` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `status` enum('automático','apertura','manual') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banco`
--

CREATE TABLE `banco` (
  `cod_banco` int(11) NOT NULL,
  `nombre_banco` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `banco`
--

INSERT INTO `banco` (`cod_banco`, `nombre_banco`) VALUES
(1, 'Banco de Venezuela'),
(2, 'Banco Nacional de Crédito'),
(3, 'BBVA Provincial'),
(4, 'Banesco'),
(5, 'Mercantil Banco'),
(6, 'Banco del Tesoro'),
(7, 'Bancamiga'),
(8, 'Banplus'),
(9, 'Bancaribe'),
(10, 'Venezolano de Crédito'),
(11, 'Banco Plaza'),
(12, 'Banco Fondo Común'),
(13, 'Banco DELSUR'),
(14, 'Banco Exterior'),
(15, 'Banco Sofitasa'),
(16, 'Bancrecer'),
(17, 'Banco Caroní'),
(18, 'Banco Activo'),
(19, '100% Banco'),
(20, 'Mi Banco');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `cod_caja` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `cod_divisas` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `caja`
--

INSERT INTO `caja` (`cod_caja`, `nombre`, `saldo`, `cod_divisas`, `status`) VALUES
(1, 'Caja Principal', 12005.87, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambio_divisa`
--

CREATE TABLE `cambio_divisa` (
  `cod_cambio` int(11) NOT NULL,
  `cod_divisa` int(11) NOT NULL,
  `tasa` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cambio_divisa`
--

INSERT INTO `cambio_divisa` (`cod_cambio`, `cod_divisa`, `tasa`, `fecha`) VALUES
(1, 1, 1.00, '0000-00-00'),
(2, 2, 1.00, '0000-00-00'),
(3, 2, 110.56, '2025-07-04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carga`
--

CREATE TABLE `carga` (
  `cod_carga` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `cod_categoria` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`cod_categoria`, `nombre`, `status`) VALUES
(1, 'Charcutería', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_gasto`
--

CREATE TABLE `categoria_gasto` (
  `cod_cat_gasto` int(11) NOT NULL,
  `cod_tipo_gasto` int(11) NOT NULL,
  `cod_frecuencia` int(11) DEFAULT NULL,
  `cod_naturaleza` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `status_cat_gasto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `cod_cliente` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `cedula_rif` varchar(12) NOT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `email` varchar(70) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`cod_cliente`, `nombre`, `apellido`, `cedula_rif`, `telefono`, `email`, `direccion`, `status`) VALUES
(1, 'Generico', 'Generico', '1', '1', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `cod_compra` int(11) NOT NULL,
  `cod_prov` int(11) NOT NULL,
  `condicion_pago` enum('contado','credito') NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `impuesto_total` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `descuento` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Disparadores `compras`
--
DELIMITER $$
CREATE TRIGGER `trg_compras_update_status` AFTER UPDATE ON `compras` FOR EACH ROW BEGIN
    -- Solo ejecutar si cambió el status
    IF OLD.status <> NEW.status THEN
        CALL R_movimiento_operacion(NEW.cod_compra, 2); -- 2 = tipo_operacion para compras
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conciliacion`
--

CREATE TABLE `conciliacion` (
  `cod_conciliacion` int(11) NOT NULL,
  `url` varchar(200) NOT NULL,
  `fecha` datetime NOT NULL,
  `cod_cuenta_bancaria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condicion_pagoe`
--

CREATE TABLE `condicion_pagoe` (
  `cod_condicion` int(11) NOT NULL,
  `nombre_condicion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `condicion_pagoe`
--

INSERT INTO `condicion_pagoe` (`cod_condicion`, `nombre_condicion`) VALUES
(1, 'prepago'),
(2, 'pospago'),
(3, 'a credito'),
(4, 'al contado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control`
--

CREATE TABLE `control` (
  `cod_control` int(11) NOT NULL,
  `observacion` varchar(100) NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_apertura` decimal(10,2) NOT NULL,
  `monto_cierre` decimal(10,2) DEFAULT NULL,
  `cod_caja` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `control`
--
DELIMITER $$
CREATE TRIGGER `trg_insert_control_caja` AFTER INSERT ON `control` FOR EACH ROW BEGIN
    CALL ActualizarStatusPago('control_caja', NEW.cod_control, NEW.status);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_status_control_caja` AFTER UPDATE ON `control` FOR EACH ROW BEGIN
    IF OLD.status <> NEW.status THEN
        CALL ActualizarStatusPago('control_caja', NEW.cod_control, NEW.status);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_contables`
--

CREATE TABLE `cuentas_contables` (
  `cod_cuenta` int(11) NOT NULL,
  `codigo_contable` varchar(20) NOT NULL,
  `nombre_cuenta` varchar(100) NOT NULL,
  `naturaleza` enum('deudora','acreedora') NOT NULL,
  `cuenta_padreid` int(11) DEFAULT NULL,
  `nivel` int(11) NOT NULL,
  `saldo` decimal(20,2) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `cuentas_contables`
--

INSERT INTO `cuentas_contables` (`cod_cuenta`, `codigo_contable`, `nombre_cuenta`, `naturaleza`, `cuenta_padreid`, `nivel`, `saldo`, `status`) VALUES
(1, '1', 'ACTIVO', 'deudora', NULL, 1, 20.00, 1),
(2, '1.1', 'ACTIVO CORRIENTE', 'deudora', 1, 2, 0.00, 1),
(3, '1.1.1', 'EFECTIVO Y EQUIVALENTE DE EFECTIVO', 'deudora', 2, 3, 0.00, 1),
(4, '1.1.1.01', 'CAJA', 'deudora', 3, 4, 0.00, 1),
(5, '1.1.1.01.01', 'CAJA PRINCIPAL', 'deudora', 4, 5, 13564.46, 1),
(6, '1.1.1.02', 'BANCOS', 'deudora', 3, 4, 0.00, 1),
(7, '1.1.1.02.01', 'DISPONIBILIDADES EN BANCOS', 'deudora', 6, 5, 31506.68, 1),
(8, '1.1.2', 'CUENTAS POR COBRAR CORRIENTES', 'deudora', 2, 3, 0.00, 1),
(9, '1.1.2.01', 'CLIENTES', 'deudora', 8, 4, 0.00, 1),
(10, '1.1.2.01.01', 'CLIENTES NACIONALES', 'deudora', 9, 5, 3478.33, 1),
(11, '1.1.3', 'IMPUESTOS POR RECUPERAR', 'deudora', 2, 3, 0.00, 1),
(12, '1.1.3.01', 'IVA SOPORTADO', 'deudora', 11, 4, 0.00, 1),
(13, '1.1.3.01.01', 'IVA CRÉDITO FISCAL', 'deudora', 12, 5, 1385.94, 1),
(14, '1.1.4', 'INVENTARIOS', 'deudora', 2, 3, 0.00, 1),
(15, '1.1.4.01', 'INVENTARIO DE PRODUCTOS', 'deudora', 14, 4, 0.00, 1),
(16, '1.1.4.01.01', 'INVENTARIO GENERAL DE PRODUCTOS', 'deudora', 15, 5, 146337.40, 1),
(17, '1.2', 'ACTIVO NO CORRIENTE', 'deudora', 1, 2, 0.00, 1),
(18, '1.2.1', 'PROPIEDAD, PLANTA Y EQUIPO', 'deudora', 17, 3, 0.00, 1),
(19, '1.2.1.01', 'MAQUINARIA Y EQUIPOS', 'deudora', 18, 4, 0.00, 1),
(20, '1.2.1.01.01', 'MAQUINARIA INDUSTRIAL GENERAL', 'deudora', 19, 5, 0.00, 1),
(21, '2', 'PASIVO', 'acreedora', NULL, 1, 0.00, 1),
(22, '2.1', 'PASIVO CORRIENTE', 'acreedora', 21, 2, 0.00, 1),
(23, '2.1.1', 'CUENTAS POR PAGAR', 'acreedora', 22, 3, 0.00, 1),
(24, '2.1.1.01', 'PROVEEDORES POR COMPRAS', 'acreedora', 23, 4, 0.00, 1),
(25, '2.1.1.01.01', 'PROVEEDORES NACIONALES', 'acreedora', 24, 5, 27592.64, 1),
(26, '2.1.1.02', 'PROVEEDORES POR GASTOS', 'acreedora', 23, 4, 0.00, 1),
(27, '2.1.1.02.01', 'GASTOS PENDIENTES', 'acreedora', 26, 5, 0.00, 1),
(28, '2.1.2', 'IMPUESTOS POR PAGAR', 'acreedora', 22, 3, 0.00, 1),
(29, '2.1.2.01', 'IVA RECAUDADO', 'acreedora', 28, 4, 0.00, 1),
(30, '2.1.2.01.01', 'IVA DÉBITO FISCAL', 'acreedora', 29, 5, 4327.14, 1),
(31, '2.2', 'PASIVO NO CORRIENTE', 'acreedora', 21, 2, 0.00, 1),
(32, '2.2.1', 'PRÉSTAMOS A LARGO PLAZO', 'acreedora', 31, 3, 0.00, 1),
(33, '2.2.1.01', 'PRÉSTAMO BANCO XYZ', 'acreedora', 32, 4, 0.00, 1),
(34, '2.2.1.01.01', 'CUOTA PRÉSTAMO XYZ 2025', 'acreedora', 33, 5, 0.00, 1),
(35, '3', 'PATRIMONIO', 'acreedora', NULL, 1, 0.00, 1),
(36, '3.1', 'CAPITAL SOCIAL', 'acreedora', 35, 2, 0.00, 1),
(37, '3.1.1', 'CAPITAL SOCIAL GENERAL', 'acreedora', 36, 3, 0.00, 1),
(38, '3.1.1.01', 'APORTES DE SOCIOS', 'acreedora', 37, 4, 0.00, 1),
(39, '3.1.1.01.01', 'APORTE INICIAL GENERAL', 'acreedora', 38, 5, 0.00, 1),
(40, '4', 'INGRESOS', 'acreedora', NULL, 1, 0.00, 1),
(41, '4.1', 'VENTAS DE PRODUCTOS', 'acreedora', 40, 2, 0.00, 1),
(42, '4.1.1', 'INGRESOS POR VENTAS', 'acreedora', 41, 3, 0.00, 1),
(43, '4.1.1.01', 'VENTA DE MERCANCÍA', 'acreedora', 42, 4, 0.00, 1),
(44, '4.1.1.01.01', 'INGRESOS POR VENTA AL DETAL', 'acreedora', 43, 5, 44960.99, 1),
(45, '4.2', 'OTROS INGRESOS', 'acreedora', 40, 2, 0.00, 1),
(46, '4.2.1', 'INGRESOS EXTRAORDINARIOS', 'acreedora', 45, 3, 0.00, 1),
(47, '4.2.1.01', 'AJUSTES DE INVENTARIO', 'acreedora', 46, 4, 0.00, 1),
(48, '4.2.1.01.01', 'GANANCIA POR AJUSTE DE INVENTARIO', 'acreedora', 47, 5, 151389.05, 1),
(49, '5', 'GASTOS', 'deudora', NULL, 1, 0.00, 1),
(50, '5.1', 'GASTOS OPERATIVOS', 'deudora', 49, 2, 0.00, 1),
(51, '5.1.1', 'GASTOS GENERALES', 'deudora', 50, 3, 0.00, 1),
(52, '5.1.1.01', 'GASTOS DEL PERIODO', 'deudora', 51, 4, 0.00, 1),
(53, '5.1.1.01.01', 'GASTOS POR OPERACIÓN', 'deudora', 52, 5, 0.00, 1),
(54, '5.2', 'COSTOS DE VENTAS', 'deudora', 49, 2, 0.00, 1),
(55, '5.2.1', 'COSTO DE MERCANCÍA', 'deudora', 54, 3, 0.00, 1),
(56, '5.2.1.01', 'COSTO GENERAL DE PRODUCTOS', 'deudora', 55, 4, 0.00, 1),
(57, '5.2.1.01.01', 'COSTO DE VENTA', 'deudora', 56, 5, 38096.61, 1),
(58, '5.3', 'GASTOS FINANCIEROS', 'deudora', 49, 2, 0.00, 1),
(59, '5.3.1', 'INTERESES Y COMISIONES', 'deudora', 58, 3, 0.00, 1),
(60, '5.3.1.01', 'INTERESES PAGADOS', 'deudora', 59, 4, 0.00, 1),
(61, '5.3.1.01.01', 'GASTOS FINANCIEROS GENERALES', 'deudora', 60, 5, 0.00, 1),
(62, '5.4', 'GASTOS EXTRAORDINARIOS', 'deudora', 49, 2, 0.00, 1),
(63, '5.4.1', 'AJUSTES DE INVENTARIO', 'deudora', 62, 3, 0.00, 1),
(64, '5.4.1.01', 'PÉRDIDAS DE INVENTARIO', 'deudora', 63, 4, 0.00, 1),
(65, '5.4.1.01.01', 'PÉRDIDA POR AJUSTE DE INVENTARIO', 'deudora', 64, 5, 622.14, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_bancaria`
--

CREATE TABLE `cuenta_bancaria` (
  `cod_cuenta_bancaria` int(11) NOT NULL,
  `cod_banco` int(11) NOT NULL,
  `cod_tipo_cuenta` int(11) NOT NULL,
  `numero_cuenta` varchar(20) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `cod_divisa` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `cuenta_bancaria`
--
DELIMITER $$
CREATE TRIGGER `trg_status_cuenta_bancaria` AFTER UPDATE ON `cuenta_bancaria` FOR EACH ROW BEGIN
    IF OLD.status <> NEW.status THEN
        CALL ActualizarStatusPago('cuenta_bancaria', NEW.cod_cuenta_bancaria, NEW.status);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descarga`
--

CREATE TABLE `descarga` (
  `cod_descarga` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_asientos`
--

CREATE TABLE `detalle_asientos` (
  `cod_det_asiento` int(11) NOT NULL,
  `cod_asiento` int(11) NOT NULL,
  `cod_cuenta` int(11) NOT NULL,
  `monto` decimal(18,2) NOT NULL,
  `tipo` enum('Debe','Haber') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Disparadores `detalle_asientos`
--
DELIMITER $$
CREATE TRIGGER `trigger_actualizar_saldo` AFTER INSERT ON `detalle_asientos` FOR EACH ROW BEGIN
    CALL actualizar_saldoc(NEW.cod_cuenta, NEW.monto, NEW.tipo);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_carga`
--

CREATE TABLE `detalle_carga` (
  `cod_det_carga` int(11) NOT NULL,
  `cod_detallep` int(11) NOT NULL,
  `cod_carga` int(11) NOT NULL,
  `cantidad` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras`
--

CREATE TABLE `detalle_compras` (
  `cod_detallec` int(11) NOT NULL,
  `cod_compra` int(11) NOT NULL,
  `cod_detallep` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_descarga`
--

CREATE TABLE `detalle_descarga` (
  `cod_det_descarga` int(11) NOT NULL,
  `cod_detallep` int(11) NOT NULL,
  `cod_descarga` int(11) NOT NULL,
  `cantidad` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_operacion`
--

CREATE TABLE `detalle_operacion` (
  `cod_detalle_op` int(11) NOT NULL,
  `detalle_operacion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_operacion`
--

INSERT INTO `detalle_operacion` (`cod_detalle_op`, `detalle_operacion`) VALUES
(1, 'al contado'),
(2, 'a credito'),
(3, 'recibido'),
(4, 'emitido de compra'),
(5, 'emitido de gasto'),
(6, 'carga'),
(7, 'descarga');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pago_emitido`
--

CREATE TABLE `detalle_pago_emitido` (
  `cod_detallepagoe` int(11) NOT NULL,
  `cod_pago_emitido` int(11) NOT NULL,
  `cod_tipo_pagoe` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pago_recibido`
--

CREATE TABLE `detalle_pago_recibido` (
  `cod_detallepago` int(11) NOT NULL,
  `cod_pago` int(11) NOT NULL,
  `cod_tipo_pago` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_productos`
--

CREATE TABLE `detalle_productos` (
  `cod_detallep` int(11) NOT NULL,
  `cod_presentacion` int(11) NOT NULL,
  `stock` float NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `lote` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_tipo_pago`
--

CREATE TABLE `detalle_tipo_pago` (
  `cod_tipo_pago` int(11) NOT NULL,
  `cod_metodo` int(11) NOT NULL,
  `tipo_moneda` enum('efectivo','digital','','') NOT NULL,
  `cod_cuenta_bancaria` int(11) DEFAULT NULL,
  `cod_caja` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `detalle_tipo_pago`
--

INSERT INTO `detalle_tipo_pago` (`cod_tipo_pago`, `cod_metodo`, `tipo_moneda`, `cod_cuenta_bancaria`, `cod_caja`, `status`) VALUES
(1, 1, 'efectivo', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `cod_detallev` int(11) NOT NULL,
  `cod_venta` int(11) NOT NULL,
  `cod_detallep` int(11) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `cantidad` float(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_vueltoe`
--

CREATE TABLE `detalle_vueltoe` (
  `cod_detallev` int(11) NOT NULL,
  `cod_vuelto` int(11) NOT NULL,
  `cod_tipo_pago` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_vueltor`
--

CREATE TABLE `detalle_vueltor` (
  `cod_detallev_r` int(11) NOT NULL,
  `cod_vuelto_r` int(11) NOT NULL,
  `cod_tipo_pago` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `divisas`
--

CREATE TABLE `divisas` (
  `cod_divisa` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `abreviatura` varchar(5) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `divisas`
--

INSERT INTO `divisas` (`cod_divisa`, `nombre`, `abreviatura`, `status`) VALUES
(1, 'Bolívares', 'Bs', 1),
(2, 'Dolares', 'USD', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `cod` int(11) NOT NULL,
  `rif` varchar(15) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `direccion` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `telefono` varchar(12) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `email` varchar(70) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `frecuencia_gasto`
--

CREATE TABLE `frecuencia_gasto` (
  `cod_frecuencia` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `dias` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `frecuencia_gasto`
--

INSERT INTO `frecuencia_gasto` (`cod_frecuencia`, `nombre`, `dias`) VALUES
(1, 'diario', 1),
(2, 'semanal', 7),
(3, 'quincenal', 15),
(4, 'mensual', 30),
(5, 'trimestral', 90),
(6, 'semestral', 180),
(7, 'anual', 365);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gasto`
--

CREATE TABLE `gasto` (
  `cod_gasto` int(11) NOT NULL,
  `cod_cat_gasto` int(11) NOT NULL,
  `cod_condicion` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_creacion` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `gasto`
--
DELIMITER $$
CREATE TRIGGER `trg_update_movimiento_gasto` AFTER UPDATE ON `gasto` FOR EACH ROW BEGIN
    -- Solo ejecutar si cambió el status
    IF OLD.status <> NEW.status THEN
        CALL R_movimiento_operacion(NEW.cod_gasto, 3);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `cod_dia` int(11) NOT NULL,
  `cod` int(11) DEFAULT NULL,
  `dia` varchar(15) NOT NULL,
  `desde` time NOT NULL,
  `hasta` time NOT NULL,
  `cerrado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`cod_dia`, `cod`, `dia`, `desde`, `hasta`, `cerrado`) VALUES
(1, NULL, 'lunes', '10:00:00', '20:00:00', 0),
(2, NULL, 'martes', '10:00:00', '20:30:00', 0),
(3, NULL, 'miercoles', '10:00:00', '20:30:00', 0),
(4, NULL, 'jueves', '10:00:00', '20:30:00', 0),
(5, NULL, 'viernes', '10:00:00', '20:30:00', 0),
(6, NULL, 'sabado', '10:00:00', '20:30:00', 0),
(7, NULL, 'domingo', '10:00:00', '15:00:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `cod_marca` int(11) NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`cod_marca`, `nombre`, `status`) VALUES
(1, 'Alibal', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `cod_mov` int(11) NOT NULL,
  `cod_operacion` int(11) NOT NULL,
  `cod_tipo_op` int(11) NOT NULL,
  `cod_detalle_op` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `naturaleza_gasto`
--

CREATE TABLE `naturaleza_gasto` (
  `cod_naturaleza` int(11) NOT NULL,
  `nombre_naturaleza` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `naturaleza_gasto`
--

INSERT INTO `naturaleza_gasto` (`cod_naturaleza`, `nombre_naturaleza`) VALUES
(1, 'fijo'),
(2, 'variable');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_emitido`
--

CREATE TABLE `pago_emitido` (
  `cod_pago_emitido` int(11) NOT NULL,
  `tipo_pago` enum('compra','gasto') NOT NULL,
  `cod_vuelto_r` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `cod_compra` int(11) DEFAULT NULL,
  `cod_gasto` int(11) DEFAULT NULL,
  `monto_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_recibido`
--

CREATE TABLE `pago_recibido` (
  `cod_pago` int(11) NOT NULL,
  `cod_venta` int(11) NOT NULL,
  `cod_vuelto` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `monto_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacion_producto`
--

CREATE TABLE `presentacion_producto` (
  `cod_presentacion` int(11) NOT NULL,
  `cod_unidad` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `presentacion` varchar(30) DEFAULT NULL,
  `cantidad_presentacion` varchar(20) DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL,
  `porcen_venta` int(11) NOT NULL,
  `excento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `presentacion_producto`
--

INSERT INTO `presentacion_producto` (`cod_presentacion`, `cod_unidad`, `cod_producto`, `presentacion`, `cantidad_presentacion`, `costo`, `porcen_venta`, `excento`) VALUES
(1, 1, 1, 'Pieza', '4.5', 20.00, 30, 2),
(2, 1, 2, 'Pieza', '5.4', 25.00, 32, 2),
(3, 1, 3, 'Pieza', '2', 15.00, 28, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuestos`
--

CREATE TABLE `presupuestos` (
  `cod_presupuesto` int(11) NOT NULL,
  `cod_cat_gasto` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `mes` date NOT NULL,
  `notas` text DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `cod_producto` int(11) NOT NULL,
  `cod_categoria` int(11) NOT NULL,
  `cod_marca` int(11) DEFAULT NULL,
  `nombre` varchar(40) NOT NULL,
  `imagen` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`cod_producto`, `cod_categoria`, `cod_marca`, `nombre`, `imagen`) VALUES
(1, 1, 1, 'Jamon de pierna', 'vista/dist/img/productos/default.png'),
(2, 1, 1, 'Queso amarillo', 'vista/dist/img/productos/default.png'),
(3, 1, 1, 'Mortadela de pollo', 'vista/dist/img/productos/default.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `cod_prov` int(11) NOT NULL,
  `rif` varchar(15) NOT NULL,
  `razon_social` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prov_representantes`
--

CREATE TABLE `prov_representantes` (
  `cod_representante` int(11) NOT NULL,
  `cod_prov` int(11) NOT NULL,
  `cedula` varchar(12) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) DEFAULT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecciones_futuras`
--

CREATE TABLE `proyecciones_futuras` (
  `cod_proyeccion` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `mes` date NOT NULL,
  `valor_proyectado` decimal(10,2) NOT NULL,
  `ventana_ma` int(11) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecciones_historicas`
--

CREATE TABLE `proyecciones_historicas` (
  `cod_historico` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `mes` date NOT NULL,
  `valor_proyectado` decimal(10,2) NOT NULL,
  `valor_real` decimal(10,2) DEFAULT NULL,
  `precision_valor` int(11) DEFAULT NULL,
  `ventana_ma` int(11) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyecciones_historicas`
--

INSERT INTO `proyecciones_historicas` (`cod_historico`, `cod_producto`, `mes`, `valor_proyectado`, `valor_real`, `precision_valor`, `ventana_ma`, `create_at`) VALUES
(1, 1, '2025-01-01', 850.00, 820.75, 96, 3, '2025-07-04 03:02:04'),
(2, 1, '2025-02-01', 820.00, 780.50, 95, 3, '2025-07-04 03:02:04'),
(3, 1, '2025-03-01', 780.00, 920.80, 85, 3, '2025-07-04 03:02:04'),
(4, 1, '2025-04-01', 920.00, 985.90, 93, 3, '2025-07-04 03:02:04'),
(5, 1, '2025-05-01', 985.00, 1089.20, 90, 3, '2025-07-04 03:02:04'),
(6, 1, '2025-06-01', 1089.00, 1156.45, 94, 3, '2025-07-04 03:02:04'),
(7, 2, '2025-01-01', 600.00, 450.30, 75, 3, '2025-07-04 03:02:04'),
(8, 2, '2025-02-01', 450.00, 395.80, 88, 3, '2025-07-04 03:02:04'),
(9, 2, '2025-03-01', 395.00, 525.60, 75, 3, '2025-07-04 03:02:04'),
(10, 2, '2025-04-01', 525.00, 390.25, 74, 3, '2025-07-04 03:02:04'),
(11, 2, '2025-05-01', 390.00, 465.40, 84, 3, '2025-07-04 03:02:04'),
(12, 2, '2025-06-01', 465.00, 515.85, 90, 3, '2025-07-04 03:02:04'),
(13, 3, '2025-01-01', 320.00, 315.90, 99, 3, '2025-07-04 03:02:04'),
(14, 3, '2025-02-01', 315.00, 309.75, 98, 3, '2025-07-04 03:02:04'),
(15, 3, '2025-03-01', 309.00, 245.60, 79, 3, '2025-07-04 03:02:04'),
(16, 3, '2025-04-01', 245.00, 180.20, 74, 3, '2025-07-04 03:02:04'),
(17, 3, '2025-05-01', 180.00, 120.85, 67, 3, '2025-07-04 03:02:04'),
(18, 3, '2025-06-01', 120.00, 98.45, 82, 3, '2025-07-04 03:02:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_mensual`
--

CREATE TABLE `stock_mensual` (
  `cod_stock_mensual` int(11) NOT NULL,
  `cod_presentacion` int(11) NOT NULL,
  `mes` varchar(20) NOT NULL,
  `stock_inicial` decimal(10,2) DEFAULT NULL,
  `stock_final` decimal(10,2) DEFAULT NULL,
  `ventas_cantidad` decimal(10,2) DEFAULT NULL,
  `rotacion` decimal(8,2) DEFAULT NULL,
  `dias_rotacion` decimal(8,2) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stock_mensual`
--

INSERT INTO `stock_mensual` (`cod_stock_mensual`, `cod_presentacion`, `mes`, `stock_inicial`, `stock_final`, `ventas_cantidad`, `rotacion`, `dias_rotacion`, `create_at`) VALUES
(19, 1, '2025-01-01', 25.00, 18.50, 12.75, 0.51, 59.00, '2025-07-04 03:02:04'),
(20, 1, '2025-02-01', 18.50, 15.25, 8.90, 0.53, 57.00, '2025-07-04 03:02:04'),
(21, 1, '2025-03-01', 15.25, 22.10, 14.40, 0.65, 46.00, '2025-07-04 03:02:04'),
(22, 1, '2025-04-01', 22.10, 28.75, 18.65, 0.65, 46.00, '2025-07-04 03:02:04'),
(23, 1, '2025-05-01', 28.75, 35.30, 21.20, 0.60, 50.00, '2025-07-04 03:02:04'),
(24, 1, '2025-06-01', 35.30, 29.40, 17.85, 0.56, 54.00, '2025-07-04 03:02:04'),
(25, 2, '2025-01-01', 15.00, 8.20, 12.30, 1.50, 20.00, '2025-07-04 03:02:04'),
(26, 2, '2025-02-01', 8.20, 5.50, 8.80, 1.60, 19.00, '2025-07-04 03:02:04'),
(27, 2, '2025-03-01', 5.50, 2.75, 6.45, 2.35, 13.00, '2025-07-04 03:02:04'),
(28, 2, '2025-04-01', 2.75, 0.90, 4.25, 4.72, 6.00, '2025-07-04 03:02:04'),
(29, 2, '2025-05-01', 0.90, 3.15, 2.70, 3.00, 10.00, '2025-07-04 03:02:04'),
(30, 2, '2025-06-01', 3.15, 1.80, 4.35, 2.42, 12.00, '2025-07-04 03:02:04'),
(31, 3, '2025-01-01', 80.00, 85.75, 12.60, 0.15, 200.00, '2025-07-04 03:02:04'),
(32, 3, '2025-02-01', 85.75, 88.90, 8.15, 0.09, 333.00, '2025-07-04 03:02:04'),
(33, 3, '2025-03-01', 88.90, 92.45, 5.80, 0.06, 500.00, '2025-07-04 03:02:04'),
(34, 3, '2025-04-01', 92.45, 95.60, 4.90, 0.05, 600.00, '2025-07-04 03:02:04'),
(35, 3, '2025-05-01', 95.60, 98.30, 3.25, 0.03, 1000.00, '2025-07-04 03:02:04'),
(36, 3, '2025-06-01', 98.30, 101.75, 2.40, 0.02, 1250.00, '2025-07-04 03:02:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_cuenta`
--

CREATE TABLE `tipo_cuenta` (
  `cod_tipo_cuenta` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_cuenta`
--

INSERT INTO `tipo_cuenta` (`cod_tipo_cuenta`, `nombre`) VALUES
(1, 'AHORRO'),
(2, 'CORRIENTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_gasto`
--

CREATE TABLE `tipo_gasto` (
  `cod_tipo_gasto` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_gasto`
--

INSERT INTO `tipo_gasto` (`cod_tipo_gasto`, `nombre`) VALUES
(1, 'producto'),
(2, 'servicio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_operacion`
--

CREATE TABLE `tipo_operacion` (
  `cod_tipo_op` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_operacion`
--

INSERT INTO `tipo_operacion` (`cod_tipo_op`, `tipo`) VALUES
(1, 'venta'),
(2, 'compra'),
(3, 'gasto'),
(4, 'pago'),
(5, 'ajuste');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_pago`
--

CREATE TABLE `tipo_pago` (
  `cod_metodo` int(11) NOT NULL,
  `medio_pago` varchar(50) NOT NULL,
  `modalidad` enum('efectivo','digital') NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `tipo_pago`
--

INSERT INTO `tipo_pago` (`cod_metodo`, `medio_pago`, `modalidad`, `status`) VALUES
(1, 'Efectivo', 'efectivo', 1),
(2, 'Punto de Venta', 'digital', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tlf_proveedores`
--

CREATE TABLE `tlf_proveedores` (
  `cod_tlf` int(11) NOT NULL,
  `cod_prov` int(11) NOT NULL,
  `telefono` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `cod_unidad` int(11) NOT NULL,
  `tipo_medida` char(10) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `unidades_medida`
--

INSERT INTO `unidades_medida` (`cod_unidad`, `tipo_medida`, `status`) VALUES
(1, 'Kg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `cod_venta` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `condicion_pago` enum('contado','credito') NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `subtotal_v` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `impuesto_v` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Disparadores `ventas`
--
DELIMITER $$
CREATE TRIGGER `trg_ventas_update_status` AFTER UPDATE ON `ventas` FOR EACH ROW BEGIN
    -- Solo ejecutar si cambió el status
    IF OLD.status <> NEW.status THEN
        CALL R_movimiento_operacion(NEW.cod_venta, 1);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_pendientes_compras_gastos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_pendientes_compras_gastos` (
`cod_transaccion` int(11)
,`tipo` varchar(56)
,`asunto` varchar(152)
,`monto_total` decimal(10,2)
,`fecha_vencimiento` date
,`fecha` date
,`monto_pagado` decimal(32,2)
,`monto_pendiente` decimal(33,2)
,`dias_restantes` int(7)
,`status` varchar(12)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vuelto_emitido`
--

CREATE TABLE `vuelto_emitido` (
  `cod_vuelto` int(11) NOT NULL,
  `vuelto_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vuelto_recibido`
--

CREATE TABLE `vuelto_recibido` (
  `cod_vuelto_r` int(11) NOT NULL,
  `vuelto_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_pendientes_compras_gastos`
--
DROP TABLE IF EXISTS `vista_pendientes_compras_gastos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_pendientes_compras_gastos`  AS SELECT `c`.`cod_compra` AS `cod_transaccion`, concat('Compra','-',`c`.`condicion_pago`) AS `tipo`, `p`.`razon_social` AS `asunto`, `c`.`total` AS `monto_total`, `c`.`fecha_vencimiento` AS `fecha_vencimiento`, `c`.`fecha` AS `fecha`, coalesce(sum(`pe`.`monto_total`),0) AS `monto_pagado`, `c`.`total`- coalesce(sum(`pe`.`monto_total`),0) AS `monto_pendiente`, coalesce(to_days(`c`.`fecha_vencimiento`) - to_days(curdate()),0) AS `dias_restantes`, CASE WHEN `c`.`status` = 3 THEN 'Pagado' WHEN `c`.`fecha_vencimiento` is null THEN 'Vencido' WHEN `c`.`fecha_vencimiento` < curdate() THEN 'Vencido' WHEN `c`.`status` = 2 THEN 'Pago parcial' ELSE 'Pendiente' END AS `status` FROM ((`compras` `c` join `proveedores` `p` on(`p`.`cod_prov` = `c`.`cod_prov`)) left join `pago_emitido` `pe` on(`pe`.`cod_compra` = `c`.`cod_compra`)) WHERE `c`.`status` in (1,2) GROUP BY `c`.`cod_compra`, `p`.`razon_social`, `c`.`total`, `c`.`fecha`, `c`.`fecha_vencimiento`, `c`.`status`union all select `g`.`cod_gasto` AS `cod_transaccion`,concat('Gasto','-',`cp`.`nombre_condicion`) AS `tipo`,concat(`cg`.`nombre`,': ',`g`.`descripcion`) AS `asunto`,`g`.`monto` AS `monto_total`,case when `fg`.`dias` is not null then `g`.`fecha_creacion` + interval `fg`.`dias` day else NULL end AS `fecha_vencimiento`,`g`.`fecha_creacion` AS `fecha`,coalesce(sum(`pe`.`monto_total`),0) AS `monto_pagado`,`g`.`monto` - coalesce(sum(`pe`.`monto_total`),0) AS `monto_pendiente`,coalesce(case when `fg`.`dias` is not null then to_days(`g`.`fecha_creacion` + interval `fg`.`dias` day) - to_days(curdate()) else NULL end,0) AS `dias_restantes`,case when `g`.`status` = 3 then 'Pagado' when `g`.`status` = 2 then 'Pago parcial' when `g`.`fecha_creacion` + interval `fg`.`dias` day < curdate() then 'Vencido' else 'Pendiente' end AS `status` from ((((`gasto` `g` join `categoria_gasto` `cg` on(`cg`.`cod_cat_gasto` = `g`.`cod_cat_gasto`)) join `condicion_pagoe` `cp` on(`cp`.`cod_condicion` = `g`.`cod_condicion`)) left join `frecuencia_gasto` `fg` on(`fg`.`cod_frecuencia` = `cg`.`cod_frecuencia`)) left join `pago_emitido` `pe` on(`pe`.`cod_gasto` = `g`.`cod_gasto`)) where `g`.`status` in (1,2) group by `g`.`cod_gasto`,`g`.`descripcion`,`g`.`monto`,`g`.`fecha_creacion`,`fg`.`dias`,`g`.`status` order by `dias_restantes`  ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `analisis_rentabilidad`
--
ALTER TABLE `analisis_rentabilidad`
  ADD PRIMARY KEY (`cod_analisis`),
  ADD UNIQUE KEY `unq_producto_mes` (`cod_producto`,`mes`);

--
-- Indices de la tabla `asientos_contables`
--
ALTER TABLE `asientos_contables`
  ADD PRIMARY KEY (`cod_asiento`),
  ADD KEY `cod_mov` (`cod_mov`);

--
-- Indices de la tabla `banco`
--
ALTER TABLE `banco`
  ADD PRIMARY KEY (`cod_banco`);

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`cod_caja`),
  ADD KEY `cod_divisas` (`cod_divisas`);

--
-- Indices de la tabla `cambio_divisa`
--
ALTER TABLE `cambio_divisa`
  ADD PRIMARY KEY (`cod_cambio`),
  ADD KEY `cambiodivisa-divisa` (`cod_divisa`);

--
-- Indices de la tabla `carga`
--
ALTER TABLE `carga`
  ADD PRIMARY KEY (`cod_carga`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`cod_categoria`);

--
-- Indices de la tabla `categoria_gasto`
--
ALTER TABLE `categoria_gasto`
  ADD PRIMARY KEY (`cod_cat_gasto`),
  ADD KEY `cod_tipo_gasto` (`cod_tipo_gasto`),
  ADD KEY `cod_frecuencia` (`cod_frecuencia`),
  ADD KEY `cod_naturaleza` (`cod_naturaleza`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cod_cliente`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`cod_compra`),
  ADD KEY `compras-proveedores` (`cod_prov`);

--
-- Indices de la tabla `conciliacion`
--
ALTER TABLE `conciliacion`
  ADD KEY `cod_cuenta_bancaria` (`cod_cuenta_bancaria`);

--
-- Indices de la tabla `condicion_pagoe`
--
ALTER TABLE `condicion_pagoe`
  ADD PRIMARY KEY (`cod_condicion`);

--
-- Indices de la tabla `control`
--
ALTER TABLE `control`
  ADD PRIMARY KEY (`cod_control`),
  ADD KEY `cod_caja` (`cod_caja`);

--
-- Indices de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD PRIMARY KEY (`cod_cuenta`),
  ADD UNIQUE KEY `codigo_contable` (`codigo_contable`),
  ADD KEY `cuenta_padreid` (`cuenta_padreid`);

--
-- Indices de la tabla `cuenta_bancaria`
--
ALTER TABLE `cuenta_bancaria`
  ADD PRIMARY KEY (`cod_cuenta_bancaria`),
  ADD KEY `cod_banco` (`cod_banco`),
  ADD KEY `cod_tipo_cuenta` (`cod_tipo_cuenta`),
  ADD KEY `cod_divisa` (`cod_divisa`);

--
-- Indices de la tabla `descarga`
--
ALTER TABLE `descarga`
  ADD PRIMARY KEY (`cod_descarga`);

--
-- Indices de la tabla `detalle_asientos`
--
ALTER TABLE `detalle_asientos`
  ADD PRIMARY KEY (`cod_det_asiento`),
  ADD KEY `asiento_id` (`cod_asiento`),
  ADD KEY `cuenta_id` (`cod_cuenta`);

--
-- Indices de la tabla `detalle_carga`
--
ALTER TABLE `detalle_carga`
  ADD PRIMARY KEY (`cod_det_carga`),
  ADD KEY `detalle_carga-carga` (`cod_carga`),
  ADD KEY `detalle_carga-detallep` (`cod_detallep`);

--
-- Indices de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`cod_detallec`),
  ADD KEY `detalle_compras-compras` (`cod_compra`),
  ADD KEY `detalle_compras-detalle_productos` (`cod_detallep`);

--
-- Indices de la tabla `detalle_descarga`
--
ALTER TABLE `detalle_descarga`
  ADD PRIMARY KEY (`cod_det_descarga`),
  ADD KEY `detalle_descarga-detallep` (`cod_detallep`),
  ADD KEY `detalle_descarga-descarga` (`cod_descarga`);

--
-- Indices de la tabla `detalle_operacion`
--
ALTER TABLE `detalle_operacion`
  ADD PRIMARY KEY (`cod_detalle_op`);

--
-- Indices de la tabla `detalle_pago_emitido`
--
ALTER TABLE `detalle_pago_emitido`
  ADD PRIMARY KEY (`cod_detallepagoe`),
  ADD KEY `pagoe-dtpagoe` (`cod_pago_emitido`),
  ADD KEY `dtpagoe-tipopagoe` (`cod_tipo_pagoe`);

--
-- Indices de la tabla `detalle_pago_recibido`
--
ALTER TABLE `detalle_pago_recibido`
  ADD PRIMARY KEY (`cod_detallepago`),
  ADD KEY `detalle_pago-pago` (`cod_pago`),
  ADD KEY `tipo_pago-detalle_pago` (`cod_tipo_pago`);

--
-- Indices de la tabla `detalle_productos`
--
ALTER TABLE `detalle_productos`
  ADD PRIMARY KEY (`cod_detallep`),
  ADD KEY `detalle_producto-productos` (`cod_presentacion`);

--
-- Indices de la tabla `detalle_tipo_pago`
--
ALTER TABLE `detalle_tipo_pago`
  ADD PRIMARY KEY (`cod_tipo_pago`),
  ADD KEY `cod_cuenta_bancaria` (`cod_cuenta_bancaria`),
  ADD KEY `cod_metodo` (`cod_metodo`),
  ADD KEY `cod_caja` (`cod_caja`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`cod_detallev`),
  ADD KEY `cod_venta` (`cod_venta`),
  ADD KEY `detalle_ventas-detalle_productos` (`cod_detallep`);

--
-- Indices de la tabla `detalle_vueltoe`
--
ALTER TABLE `detalle_vueltoe`
  ADD PRIMARY KEY (`cod_detallev`),
  ADD KEY `cod_vuelto` (`cod_vuelto`),
  ADD KEY `cod_tipo_pago` (`cod_tipo_pago`);

--
-- Indices de la tabla `detalle_vueltor`
--
ALTER TABLE `detalle_vueltor`
  ADD PRIMARY KEY (`cod_detallev_r`),
  ADD KEY `cod_vuelto_r` (`cod_vuelto_r`),
  ADD KEY `cod_tipo_pago` (`cod_tipo_pago`);

--
-- Indices de la tabla `divisas`
--
ALTER TABLE `divisas`
  ADD PRIMARY KEY (`cod_divisa`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`cod`);

--
-- Indices de la tabla `frecuencia_gasto`
--
ALTER TABLE `frecuencia_gasto`
  ADD PRIMARY KEY (`cod_frecuencia`);

--
-- Indices de la tabla `gasto`
--
ALTER TABLE `gasto`
  ADD PRIMARY KEY (`cod_gasto`),
  ADD KEY `cod_cat_gasto` (`cod_cat_gasto`),
  ADD KEY `cod_condicion` (`cod_condicion`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`cod_dia`),
  ADD KEY `cod` (`cod`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`cod_marca`),
  ADD UNIQUE KEY `marca_unica` (`nombre`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`cod_mov`),
  ADD KEY `cod_tipo_op` (`cod_tipo_op`),
  ADD KEY `cod_detalle_op` (`cod_detalle_op`);

--
-- Indices de la tabla `naturaleza_gasto`
--
ALTER TABLE `naturaleza_gasto`
  ADD PRIMARY KEY (`cod_naturaleza`);

--
-- Indices de la tabla `pago_emitido`
--
ALTER TABLE `pago_emitido`
  ADD PRIMARY KEY (`cod_pago_emitido`),
  ADD KEY `compra-pago` (`cod_compra`),
  ADD KEY `cod_gasto` (`cod_gasto`),
  ADD KEY `cod_vuelto_r` (`cod_vuelto_r`);

--
-- Indices de la tabla `pago_recibido`
--
ALTER TABLE `pago_recibido`
  ADD PRIMARY KEY (`cod_pago`),
  ADD KEY `pagos-ventas` (`cod_venta`),
  ADD KEY `cod_vuelto` (`cod_vuelto`);

--
-- Indices de la tabla `presentacion_producto`
--
ALTER TABLE `presentacion_producto`
  ADD PRIMARY KEY (`cod_presentacion`),
  ADD KEY `cod_producto` (`cod_producto`),
  ADD KEY `cod_unidad` (`cod_unidad`);

--
-- Indices de la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  ADD PRIMARY KEY (`cod_presupuesto`),
  ADD KEY `fk_presupuestos_categoria_gasto` (`cod_cat_gasto`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`cod_producto`),
  ADD KEY `productos-categorias` (`cod_categoria`),
  ADD KEY `cod_marca` (`cod_marca`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`cod_prov`);

--
-- Indices de la tabla `prov_representantes`
--
ALTER TABLE `prov_representantes`
  ADD PRIMARY KEY (`cod_representante`),
  ADD KEY `prov_representantes_ibfk_1` (`cod_prov`);

--
-- Indices de la tabla `proyecciones_futuras`
--
ALTER TABLE `proyecciones_futuras`
  ADD PRIMARY KEY (`cod_proyeccion`),
  ADD UNIQUE KEY `unq_producto_mes` (`cod_producto`,`mes`),
  ADD KEY `fk_proyecciones_futuras_productos` (`cod_producto`);

--
-- Indices de la tabla `proyecciones_historicas`
--
ALTER TABLE `proyecciones_historicas`
  ADD PRIMARY KEY (`cod_historico`),
  ADD KEY `fk_proyecciones_historicas_productos` (`cod_producto`);

--
-- Indices de la tabla `stock_mensual`
--
ALTER TABLE `stock_mensual`
  ADD PRIMARY KEY (`cod_stock_mensual`) USING BTREE,
  ADD UNIQUE KEY `unq_presentacion_mes` (`cod_presentacion`,`mes`);

--
-- Indices de la tabla `tipo_cuenta`
--
ALTER TABLE `tipo_cuenta`
  ADD PRIMARY KEY (`cod_tipo_cuenta`);

--
-- Indices de la tabla `tipo_gasto`
--
ALTER TABLE `tipo_gasto`
  ADD PRIMARY KEY (`cod_tipo_gasto`);

--
-- Indices de la tabla `tipo_operacion`
--
ALTER TABLE `tipo_operacion`
  ADD PRIMARY KEY (`cod_tipo_op`);

--
-- Indices de la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
  ADD PRIMARY KEY (`cod_metodo`);

--
-- Indices de la tabla `tlf_proveedores`
--
ALTER TABLE `tlf_proveedores`
  ADD PRIMARY KEY (`cod_tlf`),
  ADD KEY `cod_prov` (`cod_prov`);

--
-- Indices de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`cod_unidad`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`cod_venta`),
  ADD KEY `ventas-clientes` (`cod_cliente`);

--
-- Indices de la tabla `vuelto_emitido`
--
ALTER TABLE `vuelto_emitido`
  ADD PRIMARY KEY (`cod_vuelto`);

--
-- Indices de la tabla `vuelto_recibido`
--
ALTER TABLE `vuelto_recibido`
  ADD PRIMARY KEY (`cod_vuelto_r`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `analisis_rentabilidad`
--
ALTER TABLE `analisis_rentabilidad`
  MODIFY `cod_analisis` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asientos_contables`
--
ALTER TABLE `asientos_contables`
  MODIFY `cod_asiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `banco`
--
ALTER TABLE `banco`
  MODIFY `cod_banco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `cod_caja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cambio_divisa`
--
ALTER TABLE `cambio_divisa`
  MODIFY `cod_cambio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `carga`
--
ALTER TABLE `carga`
  MODIFY `cod_carga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `cod_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `categoria_gasto`
--
ALTER TABLE `categoria_gasto`
  MODIFY `cod_cat_gasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cod_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `cod_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `condicion_pagoe`
--
ALTER TABLE `condicion_pagoe`
  MODIFY `cod_condicion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `control`
--
ALTER TABLE `control`
  MODIFY `cod_control` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  MODIFY `cod_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `cuenta_bancaria`
--
ALTER TABLE `cuenta_bancaria`
  MODIFY `cod_cuenta_bancaria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `descarga`
--
ALTER TABLE `descarga`
  MODIFY `cod_descarga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_asientos`
--
ALTER TABLE `detalle_asientos`
  MODIFY `cod_det_asiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_carga`
--
ALTER TABLE `detalle_carga`
  MODIFY `cod_det_carga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `cod_detallec` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_descarga`
--
ALTER TABLE `detalle_descarga`
  MODIFY `cod_det_descarga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_operacion`
--
ALTER TABLE `detalle_operacion`
  MODIFY `cod_detalle_op` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `detalle_pago_emitido`
--
ALTER TABLE `detalle_pago_emitido`
  MODIFY `cod_detallepagoe` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pago_recibido`
--
ALTER TABLE `detalle_pago_recibido`
  MODIFY `cod_detallepago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_productos`
--
ALTER TABLE `detalle_productos`
  MODIFY `cod_detallep` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_tipo_pago`
--
ALTER TABLE `detalle_tipo_pago`
  MODIFY `cod_tipo_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `cod_detallev` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_vueltoe`
--
ALTER TABLE `detalle_vueltoe`
  MODIFY `cod_detallev` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_vueltor`
--
ALTER TABLE `detalle_vueltor`
  MODIFY `cod_detallev_r` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `divisas`
--
ALTER TABLE `divisas`
  MODIFY `cod_divisa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `cod` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `frecuencia_gasto`
--
ALTER TABLE `frecuencia_gasto`
  MODIFY `cod_frecuencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `gasto`
--
ALTER TABLE `gasto`
  MODIFY `cod_gasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `cod_dia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `cod_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `cod_mov` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `naturaleza_gasto`
--
ALTER TABLE `naturaleza_gasto`
  MODIFY `cod_naturaleza` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pago_emitido`
--
ALTER TABLE `pago_emitido`
  MODIFY `cod_pago_emitido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago_recibido`
--
ALTER TABLE `pago_recibido`
  MODIFY `cod_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presentacion_producto`
--
ALTER TABLE `presentacion_producto`
  MODIFY `cod_presentacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  MODIFY `cod_presupuesto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `cod_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `cod_prov` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `prov_representantes`
--
ALTER TABLE `prov_representantes`
  MODIFY `cod_representante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyecciones_futuras`
--
ALTER TABLE `proyecciones_futuras`
  MODIFY `cod_proyeccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyecciones_historicas`
--
ALTER TABLE `proyecciones_historicas`
  MODIFY `cod_historico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `stock_mensual`
--
ALTER TABLE `stock_mensual`
  MODIFY `cod_stock_mensual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `tipo_gasto`
--
ALTER TABLE `tipo_gasto`
  MODIFY `cod_tipo_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_operacion`
--
ALTER TABLE `tipo_operacion`
  MODIFY `cod_tipo_op` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
  MODIFY `cod_metodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tlf_proveedores`
--
ALTER TABLE `tlf_proveedores`
  MODIFY `cod_tlf` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `cod_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `cod_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `vuelto_emitido`
--
ALTER TABLE `vuelto_emitido`
  MODIFY `cod_vuelto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `vuelto_recibido`
--
ALTER TABLE `vuelto_recibido`
  MODIFY `cod_vuelto_r` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `analisis_rentabilidad`
--
ALTER TABLE `analisis_rentabilidad`
  ADD CONSTRAINT `analisis_rentabilidad_ibfk_1` FOREIGN KEY (`cod_producto`) REFERENCES `productos` (`cod_producto`);

--
-- Filtros para la tabla `asientos_contables`
--
ALTER TABLE `asientos_contables`
  ADD CONSTRAINT `asientos_contables_ibfk_1` FOREIGN KEY (`cod_mov`) REFERENCES `movimientos` (`cod_mov`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `caja`
--
ALTER TABLE `caja`
  ADD CONSTRAINT `caja_ibfk_1` FOREIGN KEY (`cod_divisas`) REFERENCES `divisas` (`cod_divisa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cambio_divisa`
--
ALTER TABLE `cambio_divisa`
  ADD CONSTRAINT `cambiodivisa-divisa` FOREIGN KEY (`cod_divisa`) REFERENCES `divisas` (`cod_divisa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `categoria_gasto`
--
ALTER TABLE `categoria_gasto`
  ADD CONSTRAINT `categoria_gasto_ibfk_1` FOREIGN KEY (`cod_tipo_gasto`) REFERENCES `tipo_gasto` (`cod_tipo_gasto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categoria_gasto_ibfk_2` FOREIGN KEY (`cod_frecuencia`) REFERENCES `frecuencia_gasto` (`cod_frecuencia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categoria_gasto_ibfk_3` FOREIGN KEY (`cod_naturaleza`) REFERENCES `naturaleza_gasto` (`cod_naturaleza`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras-proveedores` FOREIGN KEY (`cod_prov`) REFERENCES `proveedores` (`cod_prov`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `conciliacion`
--
ALTER TABLE `conciliacion`
  ADD CONSTRAINT `conciliacion_ibfk_1` FOREIGN KEY (`cod_cuenta_bancaria`) REFERENCES `cuenta_bancaria` (`cod_cuenta_bancaria`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `control`
--
ALTER TABLE `control`
  ADD CONSTRAINT `control_ibfk_1` FOREIGN KEY (`cod_caja`) REFERENCES `caja` (`cod_caja`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cuentas_contables`
--
ALTER TABLE `cuentas_contables`
  ADD CONSTRAINT `cuentas_contables_ibfk_1` FOREIGN KEY (`cuenta_padreid`) REFERENCES `cuentas_contables` (`cod_cuenta`);

--
-- Filtros para la tabla `cuenta_bancaria`
--
ALTER TABLE `cuenta_bancaria`
  ADD CONSTRAINT `cuenta_bancaria_ibfk_1` FOREIGN KEY (`cod_banco`) REFERENCES `banco` (`cod_banco`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cuenta_bancaria_ibfk_2` FOREIGN KEY (`cod_tipo_cuenta`) REFERENCES `tipo_cuenta` (`cod_tipo_cuenta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cuenta_bancaria_ibfk_3` FOREIGN KEY (`cod_divisa`) REFERENCES `divisas` (`cod_divisa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_asientos`
--
ALTER TABLE `detalle_asientos`
  ADD CONSTRAINT `detalle_asientos_ibfk_1` FOREIGN KEY (`cod_asiento`) REFERENCES `asientos_contables` (`cod_asiento`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_asientos_ibfk_2` FOREIGN KEY (`cod_cuenta`) REFERENCES `cuentas_contables` (`cod_cuenta`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_carga`
--
ALTER TABLE `detalle_carga`
  ADD CONSTRAINT `detalle_carga-carga` FOREIGN KEY (`cod_carga`) REFERENCES `carga` (`cod_carga`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_carga-detallep` FOREIGN KEY (`cod_detallep`) REFERENCES `detalle_productos` (`cod_detallep`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD CONSTRAINT `detalle_compras-compras` FOREIGN KEY (`cod_compra`) REFERENCES `compras` (`cod_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_compras-detalle_productos` FOREIGN KEY (`cod_detallep`) REFERENCES `detalle_productos` (`cod_detallep`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_descarga`
--
ALTER TABLE `detalle_descarga`
  ADD CONSTRAINT `detalle_descarga-descarga` FOREIGN KEY (`cod_descarga`) REFERENCES `descarga` (`cod_descarga`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_descarga-detallep` FOREIGN KEY (`cod_detallep`) REFERENCES `detalle_productos` (`cod_detallep`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_pago_emitido`
--
ALTER TABLE `detalle_pago_emitido`
  ADD CONSTRAINT `detalle_pago_emitido_ibfk_1` FOREIGN KEY (`cod_pago_emitido`) REFERENCES `pago_emitido` (`cod_pago_emitido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pago_emitido_ibfk_2` FOREIGN KEY (`cod_tipo_pagoe`) REFERENCES `detalle_tipo_pago` (`cod_tipo_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_pago_recibido`
--
ALTER TABLE `detalle_pago_recibido`
  ADD CONSTRAINT `detalle_pago_recibido_ibfk_1` FOREIGN KEY (`cod_pago`) REFERENCES `pago_recibido` (`cod_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pago_recibido_ibfk_2` FOREIGN KEY (`cod_tipo_pago`) REFERENCES `detalle_tipo_pago` (`cod_tipo_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_productos`
--
ALTER TABLE `detalle_productos`
  ADD CONSTRAINT `detalle_productos_ibfk_1` FOREIGN KEY (`cod_presentacion`) REFERENCES `presentacion_producto` (`cod_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_tipo_pago`
--
ALTER TABLE `detalle_tipo_pago`
  ADD CONSTRAINT `detalle_tipo_pago_ibfk_1` FOREIGN KEY (`cod_cuenta_bancaria`) REFERENCES `cuenta_bancaria` (`cod_cuenta_bancaria`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_tipo_pago_ibfk_3` FOREIGN KEY (`cod_metodo`) REFERENCES `tipo_pago` (`cod_metodo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_tipo_pago_ibfk_4` FOREIGN KEY (`cod_caja`) REFERENCES `caja` (`cod_caja`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas-detalle_productos` FOREIGN KEY (`cod_detallep`) REFERENCES `detalle_productos` (`cod_detallep`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`cod_venta`) REFERENCES `ventas` (`cod_venta`);

--
-- Filtros para la tabla `detalle_vueltoe`
--
ALTER TABLE `detalle_vueltoe`
  ADD CONSTRAINT `detalle_vueltoe_ibfk_1` FOREIGN KEY (`cod_vuelto`) REFERENCES `vuelto_emitido` (`cod_vuelto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_vueltoe_ibfk_2` FOREIGN KEY (`cod_tipo_pago`) REFERENCES `detalle_tipo_pago` (`cod_tipo_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_vueltor`
--
ALTER TABLE `detalle_vueltor`
  ADD CONSTRAINT `detalle_vueltor_ibfk_1` FOREIGN KEY (`cod_vuelto_r`) REFERENCES `vuelto_recibido` (`cod_vuelto_r`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_vueltor_ibfk_2` FOREIGN KEY (`cod_tipo_pago`) REFERENCES `detalle_tipo_pago` (`cod_tipo_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `gasto`
--
ALTER TABLE `gasto`
  ADD CONSTRAINT `gasto_ibfk_1` FOREIGN KEY (`cod_cat_gasto`) REFERENCES `categoria_gasto` (`cod_cat_gasto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gasto_ibfk_2` FOREIGN KEY (`cod_condicion`) REFERENCES `condicion_pagoe` (`cod_condicion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`cod`) REFERENCES `empresa` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`cod_tipo_op`) REFERENCES `tipo_operacion` (`cod_tipo_op`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`cod_detalle_op`) REFERENCES `detalle_operacion` (`cod_detalle_op`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago_emitido`
--
ALTER TABLE `pago_emitido`
  ADD CONSTRAINT `pago_emitido_ibfk_1` FOREIGN KEY (`cod_compra`) REFERENCES `compras` (`cod_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pago_emitido_ibfk_2` FOREIGN KEY (`cod_gasto`) REFERENCES `gasto` (`cod_gasto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pago_emitido_ibfk_3` FOREIGN KEY (`cod_vuelto_r`) REFERENCES `vuelto_recibido` (`cod_vuelto_r`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago_recibido`
--
ALTER TABLE `pago_recibido`
  ADD CONSTRAINT `pago_recibido_ibfk_1` FOREIGN KEY (`cod_venta`) REFERENCES `ventas` (`cod_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pago_recibido_ibfk_2` FOREIGN KEY (`cod_vuelto`) REFERENCES `vuelto_emitido` (`cod_vuelto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presentacion_producto`
--
ALTER TABLE `presentacion_producto`
  ADD CONSTRAINT `presentacion_producto_ibfk_1` FOREIGN KEY (`cod_producto`) REFERENCES `productos` (`cod_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `presentacion_producto_ibfk_2` FOREIGN KEY (`cod_unidad`) REFERENCES `unidades_medida` (`cod_unidad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presupuestos`
--
ALTER TABLE `presupuestos`
  ADD CONSTRAINT `fk_presupuestos_categoria_gasto` FOREIGN KEY (`cod_cat_gasto`) REFERENCES `categoria_gasto` (`cod_cat_gasto`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos-categorias` FOREIGN KEY (`cod_categoria`) REFERENCES `categorias` (`cod_categoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`cod_marca`) REFERENCES `marcas` (`cod_marca`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `prov_representantes`
--
ALTER TABLE `prov_representantes`
  ADD CONSTRAINT `prov_representantes_ibfk_1` FOREIGN KEY (`cod_prov`) REFERENCES `proveedores` (`cod_prov`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyecciones_futuras`
--
ALTER TABLE `proyecciones_futuras`
  ADD CONSTRAINT `fk_proyecciones_futuras_productos` FOREIGN KEY (`cod_producto`) REFERENCES `productos` (`cod_producto`);

--
-- Filtros para la tabla `proyecciones_historicas`
--
ALTER TABLE `proyecciones_historicas`
  ADD CONSTRAINT `fk_proyecciones_historicas_productos` FOREIGN KEY (`cod_producto`) REFERENCES `productos` (`cod_producto`);

--
-- Filtros para la tabla `stock_mensual`
--
ALTER TABLE `stock_mensual`
  ADD CONSTRAINT `stock_mensual_ibfk_1` FOREIGN KEY (`cod_presentacion`) REFERENCES `presentacion_producto` (`cod_presentacion`);

--
-- Filtros para la tabla `tlf_proveedores`
--
ALTER TABLE `tlf_proveedores`
  ADD CONSTRAINT `tlf_proveedores_ibfk_1` FOREIGN KEY (`cod_prov`) REFERENCES `proveedores` (`cod_prov`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas-clientes` FOREIGN KEY (`cod_cliente`) REFERENCES `clientes` (`cod_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `registrar_gastos_fijos_diario` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-22 00:00:00' ON COMPLETION PRESERVE ENABLE DO CALL gasto_fijos(CURDATE())$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
