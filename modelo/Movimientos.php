<?php

namespace Modelo;

use Modelo\Conexion;
use Exception;
use PDO;
use PDOException;
use Modelo\Traits\ValidadorTrait;

class Movimientos extends Conexion
{
    use ValidadorTrait;
    private $descripcion;
    private $fecha;
    private $status;
    private $errores = [];
    private $detalles = [];


    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function setDatos($datos)
    {
        if (isset($datos['status'])) {
            $r = $this->validarSelect($datos['status'], ['manual', 'apertura'], 'Tipo de Asiento');
            if ($r === true) {
                $this->status = $datos['status'];
            } else {
                $this->errores['status'] = $r;
            }
        }

        if (isset($datos['descripcion'])) {
            $r = $this->validarDescripcion($datos['descripcion'], 'Descripcion', 5, 50);
            if ($r === true) {
                $this->descripcion = $datos['descripcion'];
            } else {
                $this->errores['descripcion'] = $r;
            }
        }

        if (isset($datos['fecha'])) {
            $r = $this->validarDescripcion($datos['fecha'], 'fecha', 5, 50);
            if ($r === true) {
                $this->fecha = $datos['fecha'];
            } else {
                $this->errores['fecha'] = $r;
            }
        }

        if (isset($datos['detalles'])) {
            $re = $this->validarDetalles($datos['detalles'], $datos['status'] ?? 'manual');
            if ($re === true) {
                $this->detalles = $datos['detalles'];
            } else {
                $this->errores['detalles'] = $re;
            }
        }
    }

    public function getDetalles()
    {
        return $this->detalles;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function getFecha()
    {
        return $this->fecha;
    }


    public function check()
    {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    private function validarDetalles(array $detalles, string $status)
    {
        if (empty($detalles)) {
            throw new Exception("Los detalles no están formateados correctamente.");
        }

        $cuentas = array_column($detalles, 'cuenta');
        if (empty($cuentas)) {
            throw new Exception("Debe registrar al menos una cuenta válida.");
        }

        // Solo si es apertura, verificar si ya existen cuentas usadas
        if ($status === 'apertura') {
            parent::conectarBD();
            $placeholders = implode(',', array_fill(0, count($cuentas), '?'));

            $sql = "SELECT DISTINCT c.codigo_contable, c.nombre_cuenta 
                FROM detalle_asientos d
                JOIN asientos_contables a ON d.cod_asiento = a.cod_asiento
                JOIN cuentas_contables c ON c.cod_cuenta = d.cod_cuenta
                WHERE a.status = 'apertura' AND d.cod_cuenta IN ($placeholders)";
            $stmt = $this->conex->prepare($sql);
            foreach ($cuentas as $i => $cuenta) {
                $stmt->bindValue($i + 1, $cuenta, PDO::PARAM_INT);
            }
            $stmt->execute();
            $usadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            parent::desconectarBD();

            if (count($usadas) > 0) {
                $cuentasTexto = implode(', ', array_map(function ($c) {
                    return "{$c['codigo_contable']} - {$c['nombre_cuenta']}";
                }, $usadas));
                throw new Exception("Las siguientes cuentas ya fueron usadas en un asiento de apertura: $cuentasTexto");
            }

            // Verificar si alguna cuenta tiene movimientos (no debe permitir si ya tiene)
            foreach ($cuentas as $i => $codCuenta) {
                // Obtener información de la cuenta
                parent::conectarBD();
                $sqlCuenta = "SELECT nivel, codigo_contable, nombre_cuenta FROM cuentas_contables WHERE cod_cuenta = :cod_cuenta";
                $stmtCuenta = $this->conex->prepare($sqlCuenta);
                $stmtCuenta->bindParam(':cod_cuenta', $codCuenta, PDO::PARAM_INT);
                $stmtCuenta->execute();
                $cuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC);

                if ($cuenta && $cuenta['nivel'] == 5) {
                    $sqlMovs = "SELECT COUNT(*) FROM detalle_asientos WHERE cod_cuenta = :cod_cuenta";
                    $stmtMovs = $this->conex->prepare($sqlMovs);
                    $stmtMovs->bindParam(':cod_cuenta', $codCuenta, PDO::PARAM_INT);
                    $stmtMovs->execute();
                    $movimientos = $stmtMovs->fetchColumn();
                    parent::desconectarBD();
                    
                    if ($movimientos > 0) {
                        parent::desconectarBD();
                        throw new Exception("La cuenta {$cuenta['codigo_contable']} - {$cuenta['nombre_cuenta']} ya tiene movimientos contables y no puede usarse en un asiento de apertura.");
                    }
                }
            }
        }

        // 🔁 Validación de montos
        $totalDebe = 0;
        $totalHaber = 0;
        $lineasValidas = 0;

        foreach ($detalles as $i => $mov) {
            if (empty($mov['cuenta'])) {
                throw new Exception("Cuenta vacía en la fila " . ($i + 1));
            }

            $debe  = isset($mov['debe'])  && is_numeric($mov['debe'])  ? floatval($mov['debe'])  : 0;
            $haber = isset($mov['haber']) && is_numeric($mov['haber']) ? floatval($mov['haber']) : 0;

            if ($debe > 0 && $haber > 0) {
                throw new Exception("No se puede tener DEBE y HABER en la misma línea (fila " . ($i + 1) . ")");
            }

            if ($debe <= 0 && $haber <= 0) {
                continue;
            }

            $totalDebe  += $debe;
            $totalHaber += $haber;
            $lineasValidas++;
        }

        if ($lineasValidas < 2) {
            throw new Exception("Debe registrar al menos dos cuentas contables con monto.");
        }

        if (round($totalDebe, 2) !== round($totalHaber, 2)) {
            throw new Exception("El asiento no está cuadrado.");
        }

        return true;
    }



    private function registrarAsiento()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            // Calcular total debe
            $totalDebe = 0;
            foreach ($this->detalles as $mov) {
                if (isset($mov['debe']) && is_numeric($mov['debe'])) {
                    $totalDebe += floatval($mov['debe']);
                }
            }

            // Registrar asiento
            $sqlAsiento = "INSERT INTO asientos_contables (fecha, descripcion, total, status)
        VALUES (:fecha, :descripcion, :total, :status)";
            $stmt = $this->conex->prepare($sqlAsiento);
            $stmt->bindParam(':fecha', $this->fecha);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':total', $totalDebe);
            $stmt->bindParam(':status', $this->status);
            if (!$stmt->execute()) {
                throw new Exception("Error al registrar el asiento.");
            }

            $cod_asiento = $this->conex->lastInsertId();

            // Registrar detalles
            $sqlDetalle = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo)
                        VALUES (:cod_asiento, :cod_cuenta, :monto, :tipo)";
            $stmtDetalle = $this->conex->prepare($sqlDetalle);

            foreach ($this->detalles as $mov) {
                if (empty($mov['cuenta'])) continue;

                $cuenta = $mov['cuenta'];
                $debe = isset($mov['debe']) ? floatval($mov['debe']) : 0;
                $haber = isset($mov['haber']) ? floatval($mov['haber']) : 0;

                if ($debe > 0) {
                    $monto = $debe;
                    $tipo = 'Debe';
                } elseif ($haber > 0) {
                    $monto = $haber;
                    $tipo = 'Haber';
                } else {
                    continue; // Nada que registrar en esta fila
                }

                $stmtDetalle->bindParam(':cod_asiento', $cod_asiento);
                $stmtDetalle->bindParam(':cod_cuenta', $cuenta);
                $stmtDetalle->bindParam(':monto', $monto);
                $stmtDetalle->bindParam(':tipo', $tipo);

                if (!$stmtDetalle->execute()) {
                    throw new Exception("Error al registrar el detalle del asiento.");
                }
            }

            $this->conex->commit();
            return 1;
        } catch (Exception $e) {
            $this->conex->rollBack();
            return "Error: " . $e->getMessage();
        } finally {
            parent::desconectarBD();
        }
    }


    public function getregistrarapertura()
    {
        return $this->registrarAsiento();
    }


    /////////
    public function consultar()
    {
        $sql = "SELECT 
                m.cod_mov,
                m.fecha,
                m.status,
                tpo.tipo AS tipo_operacion,
                dpo.detalle_operacion AS detalle_operacion,

                -- Código de la operación asociada
                CASE 
                    WHEN m.cod_tipo_op = 1 THEN v.cod_venta
                    WHEN m.cod_tipo_op = 2 THEN c.cod_compra
                    WHEN m.cod_tipo_op = 3 THEN g.cod_gasto
                    WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 3 THEN prc.cod_pago  -- Pago recibido
                    WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 4 THEN pem.cod_pago_emitido  -- Pago emitido
                    WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 5 THEN peg.cod_pago_emitido  -- Pago emitido
                    WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 6 THEN crg.cod_carga   -- Carga
                    WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 7 THEN dsc.cod_descarga -- Descarga
                    ELSE NULL
                END AS cod_operacion,

                -- Descripción relacionada
                CASE 
                    WHEN m.cod_tipo_op = 1 THEN CONCAT(tpo.tipo, ' ',dpo.detalle_operacion, ' #', m.cod_operacion ) -- venta
                    WHEN m.cod_tipo_op = 2 THEN CONCAT(tpo.tipo, ' ',dpo.detalle_operacion, ' #', m.cod_operacion )  -- compra
                    WHEN m.cod_tipo_op = 3 THEN CONCAT(tpo.tipo, ' ', cp.nombre_condicion, ' #', g.cod_gasto) -- gasto
                    WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 3 THEN CONCAT(tpo.tipo, ' ',dpo.detalle_operacion, ' de venta #',vrc.cod_venta) -- pago recibido
                    WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 4 THEN CONCAT(tpo.tipo, ' ',dpo.detalle_operacion, ' #',cem.cod_compra) -- pago emitido de compra
                    WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 5 THEN CONCAT(tpo.tipo, ' ',dpo.detalle_operacion, ' #',geg.cod_gasto) -- pago emitido de gasto
                    WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 6 THEN CONCAT(tpo.tipo, ' por ', dpo.detalle_operacion, ' de inventario #', crg.cod_carga) -- carga
                    WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 7 THEN CONCAT(tpo.tipo, ' por ', dpo.detalle_operacion, ' de inventario #', dsc.cod_descarga) -- descarga
                    ELSE NULL
                END AS descripcion_operacion,

                -- Monto relacionado
                CASE 
                    WHEN m.cod_tipo_op = 1 THEN v.total
                    WHEN m.cod_tipo_op = 2 THEN c.total
                    WHEN m.cod_tipo_op = 3 THEN g.monto
                    WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 3 THEN prc.monto_total
                    WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 4 THEN pem.monto_total
                    WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 5 THEN peg.monto_total
                    WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 6 THEN crg.costo
                    WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 7 THEN dsc.costo
                    ELSE NULL
                END AS monto

            FROM movimientos m
            JOIN tipo_operacion tpo ON m.cod_tipo_op = tpo.cod_tipo_op
            JOIN detalle_operacion dpo ON m.cod_detalle_op = dpo.cod_detalle_op

            -- Operaciones base
            LEFT JOIN ventas v ON m.cod_tipo_op = 1 AND m.cod_operacion = v.cod_venta
            LEFT JOIN clientes cl ON v.cod_cliente = cl.cod_cliente

            LEFT JOIN compras c ON m.cod_tipo_op = 2 AND m.cod_operacion = c.cod_compra
            LEFT JOIN proveedores pr ON c.cod_prov = pr.cod_prov

            LEFT JOIN gasto g ON m.cod_tipo_op = 3 AND m.cod_operacion = g.cod_gasto
            LEFT JOIN condicion_pagoe cp ON g.cod_condicion = cp.cod_condicion

            -- Pagos recibidos de ventas
            LEFT JOIN pago_recibido prc ON m.cod_tipo_op = 4 AND m.cod_detalle_op = 3 AND m.cod_operacion = prc.cod_pago
            LEFT JOIN ventas vrc ON prc.cod_venta = vrc.cod_venta
            LEFT JOIN clientes clrc ON vrc.cod_cliente = clrc.cod_cliente

            -- Pagos emitidos de compras
            LEFT JOIN pago_emitido pem ON m.cod_tipo_op = 4 AND m.cod_detalle_op = 4 AND m.cod_operacion = pem.cod_pago_emitido
            LEFT JOIN compras cem ON pem.cod_compra = cem.cod_compra
            LEFT JOIN proveedores prem ON cem.cod_prov = prem.cod_prov

			-- pago emitido de gastos
            LEFT JOIN pago_emitido peg ON m.cod_tipo_op = 4 AND m.cod_detalle_op = 5 AND m.cod_operacion = peg.cod_pago_emitido
			LEFT JOIN gasto geg ON peg.cod_gasto = geg.cod_gasto


            -- Carga y descarga
            LEFT JOIN carga crg ON m.cod_tipo_op = 5 AND m.cod_detalle_op = 6 AND m.cod_operacion = crg.cod_carga
            LEFT JOIN descarga dsc ON m.cod_tipo_op = 5 AND m.cod_detalle_op = 7 AND m.cod_operacion = dsc.cod_descarga
            
            ORDER BY m.cod_mov DESC;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $result;
    }

    public function get_sincronizar($valor)
    {
        return $this->sincronizar($valor);
    }

    private function sincronizar($ventas)
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            foreach ($ventas as $venta) {
                if ($venta['tipo_operacion'] == 'venta') {
                    $cventa = "SELECT 
                                v.*,
                                SUM(dv.cantidad * dv.costo_unitario) AS costo_total
                            FROM ventas v
                            JOIN detalle_ventas dv ON dv.cod_venta = v.cod_venta
                            WHERE v.cod_venta = :cod_operacion;";
                    $stmt = $this->conex->prepare($cventa);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $costo = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el costo de la venta.");
                    }
                    $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                            VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                    $stmt = $this->conex->prepare($sql);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $stmt->bindParam(':descripcion', $venta['descripcion']);
                    $stmt->bindParam(':total', $costo['costo_total']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al insertar el asiento contable.");
                    }
                    $cod_asiento = $this->conex->lastInsertId();
                    $cuentas = $this->d_cuentas($venta);
                    $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                            VALUES (:cod_asiento, :cod_cuenta, :monto, 1), (:cod_asiento, :cod_cuenta1, :monto, 2);";
                    $stmt = $this->conex->prepare($det);
                    $stmt->bindParam(':cod_asiento', $cod_asiento);
                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe']);
                    $stmt->bindParam(':monto', $costo['costo_total']);
                    $stmt->bindParam(':cod_cuenta1', $cuentas['asiento_simple']['haber']);
                    $resu = $stmt->execute();
                    if (!$resu) {
                        throw new Exception("Error al insertar el detalle del asiento contable.");
                    }
                    if ($venta['detalle_operacion'] == 'al contado') {
                        $pago = "SELECT 
                                    SUM(p.monto_total) AS total_pagado
                                FROM pago_recibido p
                                WHERE p.cod_venta = :cod_operacion;";
                        $stmt = $this->conex->prepare($pago);
                        $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                        $resul = $stmt->execute();
                        $totalp = $stmt->fetchcolumn();
                        if (!$resul) {
                            throw new Exception("Error al obtener el total pagado.");
                        }
                        $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                                VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                        $stmt = $this->conex->prepare($sql);
                        $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                        $stmt->bindParam(':descripcion', $venta['descripcion']);
                        $stmt->bindParam(':total', $totalp);
                        $resul = $stmt->execute();
                        if (!$resul) {
                            throw new Exception("Error al insertar el asiento contable.");
                        }
                        $cod_asien = $this->conex->lastInsertId();
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asien);
                        $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber']);
                        $stmt->bindParam(':monto', $costo['subtotal_v']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                        if ($costo['impuesto_v'] > 0) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                    VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asien);
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['iva']);
                            $stmt->bindParam(':monto', $costo['impuesto_v']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }
                        $dpago = "SELECT 
                                    dtp.tipo_moneda,
                                    SUM(dpr.monto) AS total_monto
                                FROM ventas v
                                JOIN pago_recibido p ON p.cod_venta = v.cod_venta
                                JOIN detalle_pago_recibido dpr ON dpr.cod_pago = p.cod_pago
                                JOIN detalle_tipo_pago dtp ON dpr.cod_tipo_pago = dtp.cod_tipo_pago
                                WHERE v.cod_venta = :cod_operacion
                                GROUP BY dtp.tipo_moneda;";
                        $stmt = $this->conex->prepare($dpago);
                        $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                        $resul = $stmt->execute();
                        $pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (!$resul) {
                            throw new Exception("Error al obtener el detalle del pago.");
                        }
                        foreach ($pago as $p) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                    VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asien);
                            if ($p['tipo_moneda'] == 'digital') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['debe_b']);
                            } else if ($p['tipo_moneda'] == 'efectivo') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['debe_c']);
                            }
                            $stmt->bindParam(':monto', $p['total_monto']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }

                        $vuelto = "SELECT 
                                    dtp.tipo_moneda,
                                    SUM(dv.monto) AS total_monto
                                FROM ventas v
                                JOIN pago_recibido p ON p.cod_venta = v.cod_venta
                                JOIN vuelto_emitido vto ON vto.cod_vuelto = p.cod_vuelto
                                JOIN detalle_vueltoe dv ON dv.cod_vuelto = vto.cod_vuelto
                                JOIN detalle_tipo_pago dtp ON dv.cod_tipo_pago = dtp.cod_tipo_pago
                                WHERE v.cod_venta = :cod_venta
                                GROUP BY dtp.tipo_moneda;";
                        $stmt = $this->conex->prepare($vuelto);
                        $stmt->bindParam(':cod_venta', $venta['cod_operacion']);
                        $resul = $stmt->execute();
                        $vuel = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (!$resul) {
                            throw new Exception("Error al obtener el detalle del vuelto.");
                        }
                        if (!empty($vuel)) {
                            foreach ($vuel as $v) {
                                $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                        VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                                $stmt = $this->conex->prepare($det);
                                $stmt->bindParam(':cod_asiento', $cod_asien);
                                if ($v['tipo_moneda'] == 'digital') {
                                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['debe_b']);
                                } else if ($v['tipo_moneda'] == 'efectivo') {
                                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['debe_c']);
                                }
                                $stmt->bindParam(':monto', $v['total_monto']);
                                $resu = $stmt->execute();
                                if (!$resu) {
                                    throw new Exception("Error al insertar el detalle del asiento contable.");
                                }
                            }
                        }
                    } else if ($venta['detalle_operacion'] == 'a credito') {
                        $asien = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                                VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                        $stmt = $this->conex->prepare($asien);
                        $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                        $stmt->bindParam(':descripcion', $venta['descripcion']);
                        $stmt->bindParam(':total', $venta['monto']);
                        $resul = $stmt->execute();
                        if (!$resul) {
                            throw new Exception("Error al insertar el asiento contable.");
                        }
                        $cod_asiento = $this->conex->lastInsertId();
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 1), (:cod_asiento, :cod_cuenta1, :monto1, 2);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['debe']);
                        $stmt->bindParam(':cod_cuenta1', $cuentas['asiento_compuesto']['haber']);
                        $stmt->bindParam(':monto', $venta['monto']);
                        $stmt->bindParam(':monto1', $costo['subtotal_v']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                        if ($costo['impuesto_v'] > 0) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                    VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asiento);
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['iva']);
                            $stmt->bindParam(':monto', $costo['impuesto_v']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }
                    }

                    $update = "UPDATE movimientos SET status= 2 WHERE cod_mov = :cod_mov;";
                    $stmt = $this->conex->prepare($update);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al actualizar el movimiento.");
                    }

                    //////////// COMPRAS ////////////////

                } else if ($venta['tipo_operacion'] == 'compra') {
                    $ccompra = "SELECT * FROM compras WHERE cod_compra=:cod_operacion;";
                    $stmt = $this->conex->prepare($ccompra);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $compra = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener informacion de la compra.");
                    }

                    if ($venta['detalle_operacion'] == 'al contado') {
                        $pcompra = "SELECT 
                                    c.cod_compra,
                                    c.total AS total_compra,
                                    IFNULL(SUM(p.monto_total), 0) AS total_pagado,
                                    (c.total - IFNULL(SUM(p.monto_total), 0)) AS monto_pendiente
                                FROM compras c
                                LEFT JOIN pago_emitido p ON p.cod_compra = c.cod_compra
                                WHERE c.cod_compra = :cod_operacion
                                GROUP BY c.cod_compra, c.total;";
                        $stmt = $this->conex->prepare($pcompra);
                        $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                        $resul = $stmt->execute();
                        $pago = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (!$resul) {
                            throw new Exception("Error al obtener el pago de la compra.");
                        }

                        $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                            VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                        $stmt = $this->conex->prepare($sql);
                        $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                        $stmt->bindParam(':descripcion', $venta['descripcion']);
                        $stmt->bindParam(':total', $pago['total_pagado']);
                        $resul = $stmt->execute();
                        if (!$resul) {
                            throw new Exception("Error al insertar el asiento contable.");
                        }
                        $cod_asiento = $this->conex->lastInsertId();
                        $cuentas = $this->d_cuentas($venta);
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe']);
                        $stmt->bindParam(':monto', $compra['subtotal']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                        if ($compra['impuesto_total'] > 0) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asiento);
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['iva']);
                            $stmt->bindParam(':monto', $compra['impuesto_total']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }
                        $dpago = "SELECT 
                                    dtp.tipo_moneda,
                                    SUM(dpe.monto) AS total_monto
                                FROM compras c
                                JOIN pago_emitido p ON p.cod_compra = c.cod_compra
                                JOIN detalle_pago_emitido dpe ON dpe.cod_pago_emitido = p.cod_pago_emitido
                                JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
                                WHERE c.cod_compra = :cod_operacion
                                GROUP BY dtp.tipo_moneda;";
                        $stmt = $this->conex->prepare($dpago);
                        $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                        $resul = $stmt->execute();
                        $pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (!$resul) {
                            throw new Exception("Error al obtener el detalle del pago.");
                        }
                        foreach ($pago as $p) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                    VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asiento);
                            if ($p['tipo_moneda'] == 'digital') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber_b']);
                            } else if ($p['tipo_moneda'] == 'efectivo') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber_c']);
                            }
                            $stmt->bindParam(':monto', $p['total_monto']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }
                        $vuelto = "SELECT 
                                    dtp.tipo_moneda,
                                    SUM(dv.monto) AS total_monto
                                FROM compras c
                                JOIN pago_emitido p ON p.cod_compra = c.cod_compra
                                JOIN vuelto_recibido vto ON vto.cod_vuelto_r = p.cod_vuelto_r
                                JOIN detalle_vueltor dv ON dv.cod_vuelto_r = vto.cod_vuelto_r
                                JOIN detalle_tipo_pago dtp ON dv.cod_tipo_pago = dtp.cod_tipo_pago
                                WHERE c.cod_compra = :cod_operacion
                                GROUP BY dtp.tipo_moneda;";
                        $stmt = $this->conex->prepare($vuelto);
                        $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                        $resul = $stmt->execute();
                        $vuel = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (!$resul) {
                            throw new Exception("Error al obtener el detalle del vuelto.");
                        }
                        if (!empty($vuel)) {
                            foreach ($vuel as $v) {
                                $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                        VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                                $stmt = $this->conex->prepare($det);
                                $stmt->bindParam(':cod_asiento', $cod_asiento);
                                if ($v['tipo_moneda'] == 'digital') {
                                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber_b']);
                                } else if ($v['tipo_moneda'] == 'efectivo') {
                                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber_c']);
                                }
                                $stmt->bindParam(':monto', $v['total_monto']);
                                $resu = $stmt->execute();
                                if (!$resu) {
                                    throw new Exception("Error al insertar el detalle del asiento contable.");
                                }
                            }
                        }
                    } else if ($venta['detalle_operacion'] == 'a credito') {
                        $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                            VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                        $stmt = $this->conex->prepare($sql);
                        $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                        $stmt->bindParam(':descripcion', $venta['descripcion']);
                        $stmt->bindParam(':total', $venta['monto']);
                        $resul = $stmt->execute();
                        if (!$resul) {
                            throw new Exception("Error al insertar el asiento contable.");
                        }
                        $cod_asiento = $this->conex->lastInsertId();
                        $cuentas = $this->d_cuentas($venta);
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe']);
                        $stmt->bindParam(':monto', $compra['subtotal']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                        if ($compra['impuesto_total'] > 0) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asiento);
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['iva']);
                            $stmt->bindParam(':monto', $compra['impuesto_total']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber']);
                        $stmt->bindParam(':monto', $compra['total']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                    }

                    $update = "UPDATE movimientos SET status= 2 WHERE cod_mov = :cod_mov;";
                    $stmt = $this->conex->prepare($update);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al actualizar el movimiento.");
                    }

                    ////////////// GASTOS ////////////////

                } else if ($venta['tipo_operacion'] == 'gasto') {
                    if ($venta['detalle_operacion'] == 'al contado') {

                        $pgasto = "SELECT 
                            g.cod_gasto,
                            g.monto AS total_gasto,
                            IFNULL(SUM(p.monto_total), 0) AS total_pagado,
                            (g.monto - IFNULL(SUM(p.monto_total), 0)) AS monto_pendiente
                        FROM gasto g
                        LEFT JOIN pago_emitido p ON p.cod_gasto = g.cod_gasto
                        WHERE g.cod_gasto = :cod_operacion
                        GROUP BY g.cod_gasto, g.monto;";
                        $stmt = $this->conex->prepare($pgasto);
                        $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                        $resul = $stmt->execute();
                        $pago = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (!$resul) {
                            throw new Exception("Error al obtener el pago del gasto.");
                        }

                        $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                    VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                        $stmt = $this->conex->prepare($sql);
                        $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                        $stmt->bindParam(':descripcion', $venta['descripcion']);
                        $stmt->bindParam(':total', $pago['total_pagado']);
                        $resul = $stmt->execute();
                        if (!$resul) {
                            throw new Exception("Error al insertar el asiento contable.");
                        }
                        $cod_asiento = $this->conex->lastInsertId();
                        $cuentas = $this->d_cuentas($venta);
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                            VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe']);
                        $stmt->bindParam(':monto', $venta['monto']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                        $dpago = "SELECT 
                                dtp.tipo_moneda,
                                SUM(dpe.monto) AS total_monto
                            FROM gasto c
                            JOIN pago_emitido p ON p.cod_gasto = c.cod_gasto
                            JOIN detalle_pago_emitido dpe ON dpe.cod_pago_emitido = p.cod_pago_emitido
                            JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
                            WHERE c.cod_gasto = :cod_operacion
                            GROUP BY dtp.tipo_moneda;";
                        $stmt = $this->conex->prepare($dpago);
                        $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                        $resul = $stmt->execute();
                        $pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (!$resul) {
                            throw new Exception("Error al obtener el detalle del pago.");
                        }
                        foreach ($pago as $p) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asiento);
                            if ($p['tipo_moneda'] == 'digital') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber_b']);
                            } else if ($p['tipo_moneda'] == 'efectivo') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber_c']);
                            }
                            $stmt->bindParam(':monto', $p['total_monto']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }
                        $vuelto = "SELECT 
                                    dtp.tipo_moneda,
                                    SUM(dv.monto) AS total_monto
                                FROM gasto g
                                JOIN pago_emitido p ON p.cod_gasto = g.cod_gasto
                                JOIN vuelto_recibido vto ON vto.cod_vuelto_r = p.cod_vuelto_r
                                JOIN detalle_vueltor dv ON dv.cod_vuelto_r = vto.cod_vuelto_r
                                JOIN detalle_tipo_pago dtp ON dv.cod_tipo_pago = dtp.cod_tipo_pago
                                WHERE g.cod_gasto = :cod_operacion
                                GROUP BY dtp.tipo_moneda;";
                        $stmt = $this->conex->prepare($vuelto);
                        $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                        $resul = $stmt->execute();
                        $vuel = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (!$resul) {
                            throw new Exception("Error al obtener el detalle del vuelto.");
                        }
                        if (!empty($vuel)) {
                            foreach ($vuel as $v) {
                                $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                    VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                                $stmt = $this->conex->prepare($det);
                                $stmt->bindParam(':cod_asiento', $cod_asiento);
                                if ($v['tipo_moneda'] == 'digital') {
                                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber_b']);
                                } else if ($v['tipo_moneda'] == 'efectivo') {
                                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber_c']);
                                }
                                $stmt->bindParam(':monto', $v['total_monto']);
                                $resu = $stmt->execute();
                                if (!$resu) {
                                    throw new Exception("Error al insertar el detalle del asiento contable.");
                                }
                            }
                        }
                    } else if ($venta['detalle_operacion'] == 'a credito') {
                        $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                            VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                        $stmt = $this->conex->prepare($sql);
                        $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                        $stmt->bindParam(':descripcion', $venta['descripcion']);
                        $stmt->bindParam(':total', $venta['monto']);
                        $resul = $stmt->execute();
                        if (!$resul) {
                            throw new Exception("Error al insertar el asiento contable.");
                        }
                        $cod_asiento = $this->conex->lastInsertId();
                        $cuentas = $this->d_cuentas($venta);
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                            VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe']);
                        $stmt->bindParam(':monto', $venta['monto']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }

                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        $stmt->bindParam(':cod_cuenta', $cuentas['asiento_compuesto']['haber']);
                        $stmt->bindParam(':monto', $venta['monto']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                    }

                    $update = "UPDATE movimientos SET status= 2 WHERE cod_mov = :cod_mov;";
                    $stmt = $this->conex->prepare($update);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al actualizar el movimiento.");
                    }

                    ///////////// PAGO RECIBIDO ////////////////

                } else if ($venta['tipo_operacion'] == 'pago' && $venta['detalle_operacion'] == 'recibido') {

                    $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                        VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                    $stmt = $this->conex->prepare($sql);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $stmt->bindParam(':descripcion', $venta['descripcion']);
                    $stmt->bindParam(':total', $venta['monto']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al insertar el asiento contable.");
                    }
                    $cod_asiento = $this->conex->lastInsertId();
                    $cuentas = $this->d_cuentas($venta);

                    $vuel = "SELECT 
                            p.cod_pago,
                            p.cod_venta,
                            p.cod_vuelto,
                            IFNULL(v.vuelto_total, 0) AS monto_vuelto,
                            CASE 
                                WHEN p.cod_vuelto IS NOT NULL THEN 'Sí'
                                ELSE 'No'
                            END AS tiene_vuelto
                        FROM pago_recibido p
                        LEFT JOIN vuelto_emitido v ON p.cod_vuelto = v.cod_vuelto
                        WHERE p.cod_pago = :cod_pago;";
                    $stmt = $this->conex->prepare($vuel);
                    $stmt->bindParam(':cod_pago', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $v = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el vuelto recibido.");
                    }

                    $monto = $v['monto_vuelto'] > 0 ? $venta['monto'] - $v['monto_vuelto'] : $venta['monto'];
                    $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                        VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                    $stmt = $this->conex->prepare($det);
                    $stmt->bindParam(':cod_asiento', $cod_asiento);
                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['haber']);
                    $stmt->bindParam(':monto', $monto);
                    $resu = $stmt->execute();
                    if (!$resu) {
                        throw new Exception("Error al insertar el detalle del asiento contable.");
                    }
                    $pago = "SELECT 
                            dtp.tipo_moneda,
                            SUM(dpr.monto) AS total_monto
                        FROM pago_recibido p
                        JOIN detalle_pago_recibido dpr ON dpr.cod_pago = p.cod_pago
                        JOIN detalle_tipo_pago dtp ON dpr.cod_tipo_pago = dtp.cod_tipo_pago
                        WHERE p.cod_pago = :cod_operacion
                        GROUP BY dtp.tipo_moneda;";
                    $stmt = $this->conex->prepare($pago);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el detalle del pago.");
                    }
                    foreach ($pago as $p) {
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                            VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        if ($p['tipo_moneda'] == 'digital') {
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe_b']);
                        } else if ($p['tipo_moneda'] == 'efectivo') {
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe_c']);
                        }
                        $stmt->bindParam(':monto', $p['total_monto']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                    }
                    $vuelto = "SELECT 
                            dtp.tipo_moneda,
                            SUM(dv.monto) AS total_monto
                        FROM pago_recibido p
                        JOIN vuelto_emitido vto ON vto.cod_vuelto = p.cod_vuelto
                        JOIN detalle_vueltoe dv ON dv.cod_vuelto = vto.cod_vuelto
                        JOIN detalle_tipo_pago dtp ON dv.cod_tipo_pago = dtp.cod_tipo_pago
                        WHERE p.cod_pago = :cod_operacion
                        GROUP BY dtp.tipo_moneda;";
                    $stmt = $this->conex->prepare($vuelto);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $vuel = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el detalle del vuelto.");
                    }
                    if (!empty($vuel)) {
                        foreach ($vuel as $v) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asiento);
                            if ($v['tipo_moneda'] == 'digital') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe_b']);
                            } else if ($v['tipo_moneda'] == 'efectivo') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe_c']);
                            }
                            $stmt->bindParam(':monto', $v['total_monto']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }
                    }
                    $update = "UPDATE movimientos SET status= 2 WHERE cod_mov = :cod_mov;";
                    $stmt = $this->conex->prepare($update);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al actualizar el movimiento.");
                    }

                    ////////////// PAGO EMITIDO DE COMPRA ////////////////

                } else if ($venta['tipo_operacion'] == 'pago' && $venta['detalle_operacion'] == 'emitido de compra') {
                    $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                        VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                    $stmt = $this->conex->prepare($sql);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $stmt->bindParam(':descripcion', $venta['descripcion']);
                    $stmt->bindParam(':total', $venta['monto']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al insertar el asiento contable.");
                    }
                    $cod_asiento = $this->conex->lastInsertId();
                    $cuentas = $this->d_cuentas($venta);
                    $vuel = "SELECT 
                            p.cod_pago_emitido,
                            p.cod_compra,
                            p.cod_vuelto_r,
                            IFNULL(v.vuelto_total, 0) AS monto_vuelto,
                            CASE 
                                WHEN p.cod_vuelto_r IS NOT NULL THEN 'Sí'
                                ELSE 'No'
                            END AS tiene_vuelto
                        FROM pago_emitido p
                        LEFT JOIN vuelto_recibido v ON p.cod_vuelto_r = v.cod_vuelto_r
                        WHERE p.cod_pago_emitido = :cod_operacion;";
                    $stmt = $this->conex->prepare($vuel);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $v = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el vuelto recibido.");
                    }
                    $monto = $v['monto_vuelto'] > 0 ? $venta['monto'] - $v['monto_vuelto'] : $venta['monto'];
                    $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                        VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                    $stmt = $this->conex->prepare($det);
                    $stmt->bindParam(':cod_asiento', $cod_asiento);
                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe']);
                    $stmt->bindParam(':monto', $monto);
                    $resu = $stmt->execute();
                    if (!$resu) {
                        throw new Exception("Error al insertar el detalle del asiento contable.");
                    }
                    $pago = "SELECT 
                            dtp.tipo_moneda,
                            SUM(dpe.monto) AS total_monto
                        FROM pago_emitido p
                        JOIN detalle_pago_emitido dpe ON dpe.cod_pago_emitido = p.cod_pago_emitido
                        JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
                        WHERE p.cod_pago_emitido = :cod_operacion
                        GROUP BY dtp.tipo_moneda;";
                    $stmt = $this->conex->prepare($pago);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el detalle del pago.");
                    }
                    foreach ($pago as $p) {
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                            VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        if ($p['tipo_moneda'] == 'digital') {
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['haber_b']);
                        } else if ($p['tipo_moneda'] == 'efectivo') {
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['haber_c']);
                        }
                        $stmt->bindParam(':monto', $p['total_monto']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                    }
                    $vuelto = "SELECT 
                            dtp.tipo_moneda,
                            SUM(dv.monto) AS total_monto
                        FROM pago_emitido p
                        JOIN vuelto_recibido vto ON vto.cod_vuelto_r = p.cod_vuelto_r
                        JOIN detalle_vueltor dv ON dv.cod_vuelto_r = vto.cod_vuelto_r
                        JOIN detalle_tipo_pago dtp ON dv.cod_tipo_pago = dtp.cod_tipo_pago
                        WHERE p.cod_pago_emitido = :cod_operacion
                        GROUP BY dtp.tipo_moneda;";
                    $stmt = $this->conex->prepare($vuelto);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $vuel = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el detalle del vuelto.");
                    }
                    if (!empty($vuel)) {
                        foreach ($vuel as $v) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asiento);
                            if ($v['tipo_moneda'] == 'digital') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['haber_b']);
                            } else if ($v['tipo_moneda'] == 'efectivo') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['haber_c']);
                            }
                            $stmt->bindParam(':monto', $v['total_monto']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }
                    }
                    $update = "UPDATE movimientos SET status= 2 WHERE cod_mov = :cod_mov;";
                    $stmt = $this->conex->prepare($update);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al actualizar el movimiento.");
                    }

                    ////////////////// PAGO EMITIDO DE GASTO ////////////////

                } else if ($venta['tipo_operacion'] == 'pago' && $venta['detalle_operacion'] == 'emitido de gasto') {
                    $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                        VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                    $stmt = $this->conex->prepare($sql);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $stmt->bindParam(':descripcion', $venta['descripcion']);
                    $stmt->bindParam(':total', $venta['monto']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al insertar el asiento contable.");
                    }
                    $cod_asiento = $this->conex->lastInsertId();
                    $cuentas = $this->d_cuentas($venta);
                    $vuel = "SELECT 
                            p.cod_pago_emitido,
                            p.cod_gasto,
                            p.cod_vuelto_r,
                            IFNULL(v.vuelto_total, 0) AS monto_vuelto,
                            CASE 
                                WHEN p.cod_vuelto_r IS NOT NULL THEN 'Sí'
                                ELSE 'No'
                            END AS tiene_vuelto
                        FROM pago_emitido p
                        LEFT JOIN vuelto_recibido v ON p.cod_vuelto_r = v.cod_vuelto_r
                        WHERE p.cod_pago_emitido = :cod_operacion;";
                    $stmt = $this->conex->prepare($vuel);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $v = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el vuelto recibido.");
                    }
                    $monto = $v['monto_vuelto'] > 0 ? $venta['monto'] - $v['monto_vuelto'] : $venta['monto'];
                    $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                        VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                    $stmt = $this->conex->prepare($det);
                    $stmt->bindParam(':cod_asiento', $cod_asiento);
                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe']);
                    $stmt->bindParam(':monto', $monto);
                    $resu = $stmt->execute();
                    if (!$resu) {
                        throw new Exception("Error al insertar el detalle del asiento contable.");
                    }
                    $pago = "SELECT 
                            dtp.tipo_moneda,
                            SUM(dpe.monto) AS total_monto
                        FROM pago_emitido p
                        JOIN detalle_pago_emitido dpe ON dpe.cod_pago_emitido = p.cod_pago_emitido
                        JOIN detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
                        WHERE p.cod_pago_emitido = :cod_operacion
                        GROUP BY dtp.tipo_moneda;";
                    $stmt = $this->conex->prepare($pago);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el detalle del pago.");
                    }
                    foreach ($pago as $p) {
                        $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                            VALUES (:cod_asiento, :cod_cuenta, :monto, 2);";
                        $stmt = $this->conex->prepare($det);
                        $stmt->bindParam(':cod_asiento', $cod_asiento);
                        if ($p['tipo_moneda'] == 'digital') {
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['haber_b']);
                        } else if ($p['tipo_moneda'] == 'efectivo') {
                            $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['haber_c']);
                        }
                        $stmt->bindParam(':monto', $p['total_monto']);
                        $resu = $stmt->execute();
                        if (!$resu) {
                            throw new Exception("Error al insertar el detalle del asiento contable.");
                        }
                    }
                    $vuelto = "SELECT 
                            dtp.tipo_moneda,
                            SUM(dv.monto) AS total_monto
                        FROM pago_emitido p
                        JOIN vuelto_recibido vto ON vto.cod_vuelto_r = p.cod_vuelto_r
                        JOIN detalle_vueltor dv ON dv.cod_vuelto_r = vto.cod_vuelto_r
                        JOIN detalle_tipo_pago dtp ON dv.cod_tipo_pago = dtp.cod_tipo_pago
                        WHERE p.cod_pago_emitido = :cod_operacion
                        GROUP BY dtp.tipo_moneda;";
                    $stmt = $this->conex->prepare($vuelto);
                    $stmt->bindParam(':cod_operacion', $venta['cod_operacion']);
                    $resul = $stmt->execute();
                    $vuel = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!$resul) {
                        throw new Exception("Error al obtener el detalle del vuelto.");
                    }
                    if (!empty($vuel)) {
                        foreach ($vuel as $v) {
                            $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                                VALUES (:cod_asiento, :cod_cuenta, :monto, 1);";
                            $stmt = $this->conex->prepare($det);
                            $stmt->bindParam(':cod_asiento', $cod_asiento);
                            if ($v['tipo_moneda'] == 'digital') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['haber_b']);
                            } else if ($v['tipo_moneda'] == 'efectivo') {
                                $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['haber_c']);
                            }
                            $stmt->bindParam(':monto', $v['total_monto']);
                            $resu = $stmt->execute();
                            if (!$resu) {
                                throw new Exception("Error al insertar el detalle del asiento contable.");
                            }
                        }
                    }
                    $update = "UPDATE movimientos SET status= 2 WHERE cod_mov = :cod_mov;";
                    $stmt = $this->conex->prepare($update);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al actualizar el movimiento.");
                    }

                    ////////////////// AJUSTE DE CARGA ////////////////

                } else if ($venta['tipo_operacion'] == 'ajuste' && $venta['detalle_operacion'] == 'carga') {
                    $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                        VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                    $stmt = $this->conex->prepare($sql);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $stmt->bindParam(':descripcion', $venta['descripcion']);
                    $stmt->bindParam(':total', $venta['monto']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al insertar el asiento contable.");
                    }
                    $cod_asiento = $this->conex->lastInsertId();
                    $cuentas = $this->d_cuentas($venta);
                    $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                            VALUES (:cod_asiento, :cod_cuenta, :monto, 1), (:cod_asiento, :cod_cuenta1, :monto, 2);";
                    $stmt = $this->conex->prepare($det);
                    $stmt->bindParam(':cod_asiento', $cod_asiento);
                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe']);
                    $stmt->bindParam(':monto', $venta['monto']);
                    $stmt->bindParam(':cod_cuenta1', $cuentas['asiento_simple']['haber']);
                    $resu = $stmt->execute();
                    if (!$resu) {
                        throw new Exception("Error al insertar el detalle del asiento contable.");
                    }

                    $update = "UPDATE movimientos SET status= 2 WHERE cod_mov = :cod_mov;";
                    $stmt = $this->conex->prepare($update);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al actualizar el movimiento.");
                    }

                    /////////////// AJUSTE DE DESCARGA ////////////////

                } else if ($venta['tipo_operacion'] == 'ajuste' && $venta['detalle_operacion'] == 'descarga') {
                    $sql = "INSERT INTO asientos_contables (cod_mov, fecha, descripcion, total, status) 
                        VALUES (:cod_mov, NOW(), :descripcion, :total, 1);";
                    $stmt = $this->conex->prepare($sql);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $stmt->bindParam(':descripcion', $venta['descripcion']);
                    $stmt->bindParam(':total', $venta['monto']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al insertar el asiento contable.");
                    }
                    $cod_asiento = $this->conex->lastInsertId();
                    $cuentas = $this->d_cuentas($venta);
                    $det = "INSERT INTO detalle_asientos (cod_asiento, cod_cuenta, monto, tipo) 
                            VALUES (:cod_asiento, :cod_cuenta, :monto, 1), (:cod_asiento, :cod_cuenta1, :monto, 2);";
                    $stmt = $this->conex->prepare($det);
                    $stmt->bindParam(':cod_asiento', $cod_asiento);
                    $stmt->bindParam(':cod_cuenta', $cuentas['asiento_simple']['debe']);
                    $stmt->bindParam(':monto', $venta['monto']);
                    $stmt->bindParam(':cod_cuenta1', $cuentas['asiento_simple']['haber']);
                    $resu = $stmt->execute();
                    if (!$resu) {
                        throw new Exception("Error al insertar el detalle del asiento contable.");
                    }
                    $update = "UPDATE movimientos SET status= 2 WHERE cod_mov = :cod_mov;";
                    $stmt = $this->conex->prepare($update);
                    $stmt->bindParam(':cod_mov', $venta['cod_mov']);
                    $resul = $stmt->execute();
                    if (!$resul) {
                        throw new Exception("Error al actualizar el movimiento.");
                    }
                }
            }
            $this->conex->commit();
            return true;
        } catch (PDOException $e) {
            echo "<script>console.log('Error: " . $e->getMessage() . "');</script>";
            $this->conex->rollBack();
            return "Error: " . $e->getMessage();
        } finally {
            parent::desconectarBD();
        }
    }

    public function rmovimiento($resul, $tipo)
    {
        $sql = "CALL R_movimiento_operacion(:cod_operacion, :cod_tipo_op);";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod_operacion', $resul);
        $stmt->bindParam(':cod_tipo_op', $tipo);
        $resul = $stmt->execute();
        parent::desconectarBD();
    }

    public function d_cuentas($operacion)
    {
        $cuentas = [];
        switch ($operacion['tipo_operacion']) {
            case 'venta':
                $cuentas['asiento_simple']['debe'] = 57; //costo de venta
                $cuentas['asiento_simple']['haber'] = 16; //inventario
                $cuentas['asiento_simple']['iva'] = 30; //iva debito fiscal
                if ($operacion['detalle_operacion'] == 'al contado') {
                    $cuentas['asiento_compuesto']['haber'] = 44; //venta de productos
                    $cuentas['asiento_compuesto']['debe_b'] = 7; //banco
                    $cuentas['asiento_compuesto']['debe_c'] = 5; //caja
                } else if ($operacion['detalle_operacion'] == 'a credito') {
                    $cuentas['asiento_compuesto']['haber'] = 44; //venta de productos
                    $cuentas['asiento_compuesto']['debe'] = 10; //cuenta por cobrar
                }
                break;
            case 'compra':
                $cuentas['asiento_simple']['debe'] = 16; //inventario
                $cuentas['asiento_simple']['iva'] = 13;  //iva credito fiscal
                if ($operacion['detalle_operacion'] == 'al contado') {
                    $cuentas['asiento_compuesto']['haber_b'] = 7; //banco
                    $cuentas['asiento_compuesto']['haber_c'] = 5; //caja
                } else if ($operacion['detalle_operacion'] == 'a credito') {
                    $cuentas['asiento_compuesto']['haber'] = 25; //cuenta por pagar
                }
                break;
            case 'gasto':
                $cuentas['asiento_simple']['debe'] = 53; //gasto de operacion
                if ($operacion['detalle_operacion'] == 'al contado') {
                    $cuentas['asiento_compuesto']['haber_b'] = 7; //banco
                    $cuentas['asiento_compuesto']['haber_c'] = 5; //caja
                } else if ($operacion['detalle_operacion'] == 'a credito') {
                    $cuentas['asiento_compuesto']['haber'] = 27; //cuenta por pagar
                }
            case 'pago':
                if ($operacion['detalle_operacion'] == 'recibido') {
                    $cuentas['asiento_simple']['debe_b'] = 7; //banco
                    $cuentas['asiento_simple']['debe_c'] = 5; //caja
                    $cuentas['asiento_simple']['haber'] = 10; //cuenta por cobrar
                } else if ($operacion['detalle_operacion'] == 'emitido de compra') {
                    $cuentas['asiento_simple']['debe'] = 25; //cuenta por pagar
                    $cuentas['asiento_simple']['haber_b'] = 7; //banco
                    $cuentas['asiento_simple']['haber_c'] = 5; //caja
                } else if ($operacion['detalle_operacion'] == 'emitido de gasto') {
                    $cuentas['asiento_simple']['debe'] = 27; //cuenta por pagar
                    $cuentas['asiento_simple']['haber_b'] = 7; //banco
                    $cuentas['asiento_simple']['haber_c'] = 5; //caja
                }
                break;
            case 'ajuste':
                if ($operacion['detalle_operacion'] == 'carga') {
                    $cuentas['asiento_simple']['debe'] = 16; //inventario
                    $cuentas['asiento_simple']['haber'] = 48; //ganancia por ajuste
                } else if ($operacion['detalle_operacion'] == 'descarga') {
                    $cuentas['asiento_simple']['debe'] = 65; //perdida por ajuste 
                    $cuentas['asiento_simple']['haber'] = 16; //inventario
                }
        }
        return $cuentas;
    }

    public function get_c_asientos($valor)
    {
        return $this->c_asientos($valor);
    }

    private function c_asientos($cod_mov)
    {
        $sql = "SELECT 
                ac.cod_asiento,
                ac.cod_mov,
                ac.fecha,
                ac.descripcion,
                ac.total,
                ac.status,

                da.cod_det_asiento,
                da.cod_cuenta,
                da.monto,
                da.tipo, -- 1 = debe, 2 = haber

                pc.nombre_cuenta AS nombre_cuenta,
                pc.codigo_contable AS codigo_cuenta,
                pc.naturaleza

            FROM asientos_contables ac
            JOIN detalle_asientos da ON da.cod_asiento = ac.cod_asiento
            JOIN cuentas_contables pc ON da.cod_cuenta = pc.cod_cuenta

            WHERE ac.cod_mov = :cod_mov
            ORDER BY ac.fecha, ac.cod_asiento, da.tipo;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod_mov', $cod_mov);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $result;
    }

    public function mpagos($cod_pago, $tipo, $detalle)
    {
        $pago = "CALL RegistrarMovimientoPagoGeneral(:cod_operacion, :cod_tipo_op, :cod_detalle_op);";
        parent::conectarBD();
        $stmt = $this->conex->prepare($pago);
        $stmt->bindParam(':cod_operacion', $cod_pago);
        $stmt->bindParam(':cod_tipo_op', $tipo);
        $stmt->bindParam(':cod_detalle_op', $detalle);
        $resul = $stmt->execute();
        parent::desconectarBD();
        if ($resul) {
        }
    }

    public function r_ajuste($cod_ajuste, $detalle)
    {
        $sql = "CALL registrar_ajusteinventario(:cod_operacion, :cod_detalle_op);";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod_operacion', $cod_ajuste);
        $stmt->bindParam(':cod_detalle_op', $detalle);
        $resul = $stmt->execute();
        parent::desconectarBD();
    }

    public function reporte1($fecha_inicio = null, $fecha_fin = null, $codigo_cuenta_filtro = null)
    {
        $sql = "
            SELECT
                cc.cod_cuenta AS CodCuenta,
                cc.codigo_contable AS CodigoCuenta,
                cc.nombre_cuenta AS NombreCuenta,
                DATE(ac.fecha) AS FechaAsiento,
                ac.cod_asiento AS NumeroAsiento,
                ac.descripcion AS DescripcionAsiento,
                CASE
                    WHEN da.tipo = 'Debe' THEN da.monto
                    ELSE 0.00
                END AS Debe,
                CASE
                    WHEN da.tipo = 'Haber' THEN da.monto
                    ELSE 0.00
                END AS Haber,
                SUM(
                    CASE
                        WHEN cc.naturaleza = 'deudora' AND da.tipo = 'Debe' THEN da.monto
                        WHEN cc.naturaleza = 'deudora' AND da.tipo = 'Haber' THEN -da.monto
                        WHEN cc.naturaleza = 'acreedora' AND da.tipo = 'Haber' THEN da.monto
                        WHEN cc.naturaleza = 'acreedora' AND da.tipo = 'Debe' THEN -da.monto
                        ELSE 0.00
                    END
                ) OVER (
                    PARTITION BY da.cod_cuenta
                    ORDER BY ac.fecha, ac.cod_asiento, da.cod_det_asiento
                ) AS SaldoAcumulado
            FROM
                detalle_asientos da
            JOIN
                asientos_contables ac ON da.cod_asiento = ac.cod_asiento
            JOIN
                cuentas_contables cc ON da.cod_cuenta = cc.cod_cuenta
            ORDER BY
                cc.cod_cuenta,
                ac.fecha,
                ac.cod_asiento,
                da.cod_det_asiento;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        //$stmt->bindValue(':fecha_inicio', $fecha_inicio . ' 00:00:00');
        //$stmt->bindValue(':fecha_fin', $fecha_fin . ' 23:59:59');
        if (!empty($codigo_cuenta_filtro)) {
            //$stmt->bindValue(':codigo_cuenta_filtro', $codigo_cuenta_filtro);
        }
        $stmt->execute();
        $data = $stmt->fetchAll();
        parent::desconectarBD();
        return $data;
    }


    //CUENTAS NIVEL 5
    public function consultarapertura()
    {
        $sql = "SELECT * FROM cuentas_contables
        WHERE nivel = 5;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($result) {
            return $result;
        } else {
            return [];
        }
    }

    public function consultar_cuentasM(){
        $sql = "SELECT * FROM cuentas_contables
        WHERE nivel = 5
        AND status=2;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($result) {
            return $result;
        } else {
            return [];
        }
    }

    public function con_rep()
    {
        $sql = "SELECT * FROM cuentas_contables
        WHERE nivel = 5;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($result) {
            return $result;
        } else {
            return [];
        }
    }

    public function consulta_asientos()
    {
        $sql = "SELECT 
            m.cod_mov,
            NULL AS cod_asiento,
            m.fecha,
            m.status,
            tpo.tipo AS tipo_operacion,
            dpo.detalle_operacion,

            -- Código de la operación asociada
            CASE 
                WHEN m.cod_tipo_op = 1 THEN v.cod_venta
                WHEN m.cod_tipo_op = 2 THEN c.cod_compra
                WHEN m.cod_tipo_op = 3 THEN g.cod_gasto
                WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 3 THEN prc.cod_pago
                WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 4 THEN pem.cod_pago_emitido
                WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 5 THEN peg.cod_pago_emitido
                WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 6 THEN crg.cod_carga
                WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 7 THEN dsc.cod_descarga
                ELSE NULL
            END AS cod_operacion,

            CASE 
                WHEN m.cod_tipo_op = 1 THEN CONCAT(tpo.tipo, ' ', dpo.detalle_operacion, ' #', m.cod_operacion)
                WHEN m.cod_tipo_op = 2 THEN CONCAT(tpo.tipo, ' ', dpo.detalle_operacion, ' #', m.cod_operacion)
                WHEN m.cod_tipo_op = 3 THEN CONCAT(tpo.tipo, ' ', cp.nombre_condicion, ' #', g.cod_gasto)
                WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 3 THEN CONCAT(tpo.tipo, ' ', dpo.detalle_operacion, ' de venta #', vrc.cod_venta)
                WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 4 THEN CONCAT(tpo.tipo, ' ', dpo.detalle_operacion, ' #', cem.cod_compra)
                WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 5 THEN CONCAT(tpo.tipo, ' ', dpo.detalle_operacion, ' #', geg.cod_gasto)
                WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 6 THEN CONCAT(tpo.tipo, ' por ', dpo.detalle_operacion, ' de inventario #', crg.cod_carga)
                WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 7 THEN CONCAT(tpo.tipo, ' por ', dpo.detalle_operacion, ' de inventario #', dsc.cod_descarga)
                ELSE NULL
            END AS descripcion_operacion,

            CASE 
                WHEN m.cod_tipo_op = 1 THEN v.total
                WHEN m.cod_tipo_op = 2 THEN c.total
                WHEN m.cod_tipo_op = 3 THEN g.monto
                WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 3 THEN prc.monto_total
                WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 4 THEN pem.monto_total
                WHEN m.cod_tipo_op = 4 AND m.cod_detalle_op = 5 THEN peg.monto_total
                WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 6 THEN crg.costo
                WHEN m.cod_tipo_op = 5 AND m.cod_detalle_op = 7 THEN dsc.costo
                ELSE NULL
            END AS monto

        FROM movimientos m
        JOIN tipo_operacion tpo ON m.cod_tipo_op = tpo.cod_tipo_op
        JOIN detalle_operacion dpo ON m.cod_detalle_op = dpo.cod_detalle_op
        LEFT JOIN ventas v ON m.cod_tipo_op = 1 AND m.cod_operacion = v.cod_venta
        LEFT JOIN clientes cl ON v.cod_cliente = cl.cod_cliente
        LEFT JOIN compras c ON m.cod_tipo_op = 2 AND m.cod_operacion = c.cod_compra
        LEFT JOIN proveedores pr ON c.cod_prov = pr.cod_prov
        LEFT JOIN gasto g ON m.cod_tipo_op = 3 AND m.cod_operacion = g.cod_gasto
        LEFT JOIN condicion_pagoe cp ON g.cod_condicion = cp.cod_condicion
        LEFT JOIN pago_recibido prc ON m.cod_tipo_op = 4 AND m.cod_detalle_op = 3 AND m.cod_operacion = prc.cod_pago
        LEFT JOIN ventas vrc ON prc.cod_venta = vrc.cod_venta
        LEFT JOIN pago_emitido pem ON m.cod_tipo_op = 4 AND m.cod_detalle_op = 4 AND m.cod_operacion = pem.cod_pago_emitido
        LEFT JOIN compras cem ON pem.cod_compra = cem.cod_compra
        LEFT JOIN pago_emitido peg ON m.cod_tipo_op = 4 AND m.cod_detalle_op = 5 AND m.cod_operacion = peg.cod_pago_emitido
        LEFT JOIN gasto geg ON peg.cod_gasto = geg.cod_gasto
        LEFT JOIN carga crg ON m.cod_tipo_op = 5 AND m.cod_detalle_op = 6 AND m.cod_operacion = crg.cod_carga
        LEFT JOIN descarga dsc ON m.cod_tipo_op = 5 AND m.cod_detalle_op = 7 AND m.cod_operacion = dsc.cod_descarga
        WHERE m.status=2

        UNION ALL

        SELECT 
            NULL AS cod_mov,
            a.cod_asiento,
            a.fecha,
            a.status,
            NULL AS tipo_operacion,
            NULL AS detalle_operacion,
            NULL AS cod_operacion,
            CAST(a.descripcion AS CHAR CHARACTER SET utf8) AS descripcion_operacion,
            a.total AS monto
        FROM asientos_contables a
        WHERE a.cod_mov IS NULL

        ORDER BY fecha DESC;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($result) {
            return $result;
        } else {
            return [];
        }
    }

    public function get_c_asientos2($valor)
    {
        return $this->c_asientos2($valor);
    }

    private function c_asientos2($cod_asiento)
    {
        $sql = "SELECT 
                ac.cod_asiento,
                ac.cod_mov,
                ac.fecha,
                ac.descripcion,
                ac.total,
                ac.status,

                da.cod_det_asiento,
                da.cod_cuenta,
                da.monto,
                da.tipo, -- 1 = debe, 2 = haber

                pc.nombre_cuenta AS nombre_cuenta,
                pc.codigo_contable AS codigo_cuenta,
                pc.naturaleza

            FROM asientos_contables ac
            JOIN detalle_asientos da ON da.cod_asiento = ac.cod_asiento
            JOIN cuentas_contables pc ON da.cod_cuenta = pc.cod_cuenta

            WHERE ac.cod_asiento = :cod_asiento
            ORDER BY ac.fecha, ac.cod_asiento, da.tipo;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod_asiento', $cod_asiento);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($result) {
            return $result;
        } else {
            return [];
        }
    }
}
