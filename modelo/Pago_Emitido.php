<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class Pago_Emitido extends Conexion
{
  use ValidadorTrait;
  private $errores = [];
  private $datos = [];
  private $cod_pago_emitido;
  private $cod_gasto;
  private $cod_compra;
  private $cod_tipo_pago;
  private $cod_vuelto_r;
  private $montototal;
  private $montopagado;
  private $vuelto;
  private $fecha;
  private $status;
  private $tipo_pago;
  private $cod_caja;
  private $cod_cuenta_bancaria;
  private $monto_pagar;
  private $montopagadoV;
  private $pago = [];
  private $pagoV = [];

  public function __construct()
  {
    global $_ENV;
    parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
  }


  public function setDatos(array $datos)
  {
    foreach ($datos as $key => $value) {
      switch ($key) {
        case 'cod_pago_emitido':
          if (is_numeric($value)) {
            $this->cod_pago_emitido = $value;
          } else {
            $this->errores[] = "El campo $key debe ser numérico.";
          }
          break;
        case 'cod_gasto':
          if (is_numeric($value)) {
            $this->cod_gasto = $value;
          } else {
            $this->errores[] = "El campo $key debe ser numérico.";
          }
          break;
        case 'cod_compra':
          if (is_numeric($value)) {
            $this->cod_compra = $value;
          } else {
            $this->errores[] = "El campo $key debe ser numérico.";
          }
          break;

        case 'cod_vuelto_r':
          if (is_numeric($value)) {
            $this->cod_vuelto_r = $value;
          }
          break;
        case 'montototal':
          if (!is_numeric($value) || $value >= 0) {
            $this->montototal = $value;
          } else {
            $this->errores[] = "El campo $key debe ser un número mayor o igual a 0.";
          }
          break;
        case 'monto_pagar':
          if (!is_numeric($value) || $value > 0) {
            $this->monto_pagar = $value;
          } else {
            $this->errores[] = "El campo $key debe ser un número mayor o igual a 0.";
          }
          break;
        case 'montopagado':
          if (!is_numeric($value) || $value > 0) {
            $this->montopagado = $value;
          } else {
            $this->errores[] = "El campo $key debe ser un número mayor o igual a 0.";
          }
          break;

        case 'montopagadoV':
          if (!is_numeric($value) || $value >= 0) {
            $this->montopagadoV = $value;
          } else {
            $this->errores[] = "El campo $key debe ser un número mayor o igu11al a 0.";
          }
          break;

        case 'vuelto':
          if (!is_numeric($value) || $value >= 0) {
            $this->vuelto = $value;
          } else {
            $this->errores[] = "El campo $key debe ser un número mayor o igual a 0.";
          }
          break;
        case 'fecha':
          if (!empty($value)) {
            $this->fecha = $value;
          } else {
            $this->errores[] = "El campo $key no puede estar vacío.";
          }
          break;

        case 'tipo_pago':
          $res = $this->validarTexto($value, $key, 2, 10);
          if ($res === true) {
            $this->tipo_pago = $value;
          } else {
            $this->errores[] = $res;
          }
          break;

        case 'pago':
          if (is_array($value)) {
            $this->pago = $value;
            foreach ($this->pago as $index => $pago) {
              if (!isset($pago['monto']) && $pago['monto'] >= 0 || !isset($pago['cod_tipo_pago']) && $pago['cod_tipo_pago'] >= 0) {
                $this->errores[] = "El elemento $index en 'pago' debe contener 'monto' y 'cod_tipo_pago'.";
                continue;
              }
              $res = $this->validarDecimal($pago['monto'], 'monto', 2, 10);
              if ($res === false) {
                $this->errores['monto'] = "El campo 'monto' en el elemento $index debe ser un número mayor o igual a 0.";
                break;
              }
              $rres = $this->validarNumerico($pago['cod_tipo_pago'], 'cod_tipo_pago', 1, 10);
              if ($rres === false) {
                $this->errores['cod_tipo_pago'] = "El campo 'cod_tipo_pago' en el elemento $index debe ser un número mayor o igual a 0.";
                break;
              }
            }
          } else {
            $this->errores[] = "El campo $key debe ser un arreglo.";
          }
          break;
        case 'pagoV':
          if (is_string($value)) {
            parse_str($value, $this->pagoV);
          } else {
            $this->errores[] = "El campo de los vueltos no puede estar vació.";
          }
          break;


        default:

          break;
      }
      $this->datos[$key] = $value;
    }
  }


  public function check()
  {
    if (!empty($this->errores)) {
      $mensajes = implode(" | ", $this->errores);
      throw new Exception("Errores de validación: $mensajes");
    }
  }

  public function getErrores()
  {
    return $this->errores;
  }

  public function consultar()
  {
    $registro = "SELECT
  tp.cod_tipo_pago,
  tp.cod_metodo,
  tp.tipo_moneda,
  t.medio_pago,
  cam2.tasa,
  cam1.tasa,
  d1.abreviatura,
  c.cod_cuenta_bancaria AS cod_cuenta_bancaria,
  COALESCE(c.cod_divisa, '') AS cod_divisa,
  COALESCE(cam1.cod_cambio, '') AS cod_cambio,
  COALESCE(d1.cod_divisa, '') AS divisa_cod,
  COALESCE(dc.cod_divisas, '') AS detcaja_cod,
  COALESCE(dc.cod_caja, '') AS detcaja_cod_caja,
  COALESCE(cam2.cod_cambio, '') AS cod_cambio_dtcaja,
  COALESCE(d2.cod_divisa, '') AS divisa_cod_dtcaja
FROM
  detalle_tipo_pago tp
LEFT JOIN
  tipo_pago t ON tp.cod_metodo = t.cod_metodo
LEFT JOIN
  cuenta_bancaria c ON tp.cod_cuenta_bancaria = c.cod_cuenta_bancaria
LEFT JOIN
  cambio_divisa cam1 ON c.cod_divisa = cam1.cod_cambio
LEFT JOIN
  divisas d1 ON cam1.cod_divisa = d1.cod_divisa
LEFT JOIN
  (
    SELECT
      dtcaja.cod_divisas,
      dtcaja.cod_caja
    FROM
      caja dtcaja
    GROUP BY
      dtcaja.cod_divisas,
      dtcaja.cod_caja
  ) dc ON tp.cod_caja = dc.cod_caja
LEFT JOIN
  cambio_divisa cam2 ON dc.cod_divisas = cam2.cod_cambio
LEFT JOIN
  divisas d2 ON cam2.cod_divisa = d2.cod_divisa
";
    parent::conectarBD();
    $consulta = $this->conex->prepare($registro);
    $resul = $consulta->execute();
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if ($resul) {
      return $datos;
    } else {
      return [];
    }
  }


  private function registrarPG()
  {
    $cod_pago_emitido = 0;

    try {
      parent::conectarBD();
      $this->conex->beginTransaction();
      if ($this->montopagado <= 0) {
        throw new Exception("El monto pagado debe ser mayor a 0.");
      }
      if ($this->tipo_pago == 'gasto') {
        $sql = "INSERT INTO pago_emitido(tipo_pago,fecha,cod_gasto, monto_total) VALUES(:tipo_pago,:fecha,:cod_gasto,:monto_total)";
        $gasto = $this->conex->prepare($sql);
        $gasto->bindParam(':tipo_pago', $this->tipo_pago);
        $gasto->bindParam(':fecha', $this->fecha);
        $gasto->bindParam(':cod_gasto', $this->cod_gasto);
        $gasto->bindParam(':monto_total', $this->montopagado);
        if (!$gasto->execute()) {
          throw new Exception("Error al insertar en pago_emitido.");
        }
        $cod_pago_emitido = $this->conex->lastInsertId();
        $actual = "SELECT SUM(monto_total) AS montopago FROM pago_emitido WHERE cod_gasto = :cod_gasto";
        $actual = $this->conex->prepare($actual);
        $actual->bindParam(':cod_gasto', $this->cod_gasto);
        $res = $actual->execute();
        $resultado = $actual->fetch(PDO::FETCH_ASSOC);
        $montopg = $resultado['montopago'];
        $montopc = 0;
      } else {
        $montopg = 0;
        $sql = "INSERT INTO pago_emitido(tipo_pago,fecha,cod_compra, monto_total) VALUES(:tipo_pago,:fecha,:cod_compra,:monto_total)";
        $compra = $this->conex->prepare($sql);
        $compra->bindParam(':tipo_pago', $this->tipo_pago);
        $compra->bindParam(':fecha', $this->fecha);
        $compra->bindParam(':cod_compra', $this->cod_compra);
        $compra->bindParam(':monto_total', $this->montopagado);
        if (!$compra->execute()) {
          throw new Exception("Error al insertar en pago_emitido.");
        }
        $cod_pago_emitido = $this->conex->lastInsertId();
        $actualcom = "SELECT SUM(monto_total) AS montopago FROM pago_emitido WHERE cod_compra = :cod_compra";
        $actualcom = $this->conex->prepare($actualcom);
        $actualcom->bindParam(':cod_compra', $this->cod_compra);
        $res = $actualcom->execute();
        $resultado = $actualcom->fetch(PDO::FETCH_ASSOC);
        $montopc = $resultado['montopago'];
      }

      $this->cod_pago_emitido = $cod_pago_emitido;

      foreach ($this->pago as $pagos) {
        if (!empty($pagos['monto']) && $pagos['monto'] > 0) {
          $this->cod_tipo_pago = $pagos['cod_tipo_pago'];

          $registro = "INSERT INTO detalle_pago_emitido(cod_pago_emitido, cod_tipo_pagoe,monto) VALUES(:cod_pago_emitido,:cod_tipo_pago,:monto)";
          $strExec = $this->conex->prepare($registro);
          $strExec->bindParam(':cod_pago_emitido', $cod_pago_emitido);
          $strExec->bindParam(':cod_tipo_pago', $this->cod_tipo_pago);
          $strExec->bindParam(':monto', $pagos['monto']);

          if (!$strExec->execute()) {
            throw new Exception("Error al insertar en detalle_pago_emitido.");
          }

          $consultaRelacion = "SELECT cod_cuenta_bancaria, cod_caja FROM detalle_tipo_pago WHERE cod_tipo_pago = :cod_tipo_pago";
          $consulta = $this->conex->prepare($consultaRelacion);
          $consulta->bindParam(':cod_tipo_pago', $this->cod_tipo_pago);
          if (!$consulta->execute()) {
            throw new Exception("Error al obtener la relación de tipo de pago.");
          }
          $relacion = $consulta->fetch(PDO::FETCH_ASSOC);
          $divisa = $this->divisa($pagos['cod_tipo_pago']);
          if ($relacion) {
            $descuento = $divisa['cod_divisa'] != 1 ? $pagos['monto'] / $divisa['ultima_tasa'] : $pagos['monto'];
            if (!empty($relacion['cod_cuenta_bancaria'])) {

              $this->cod_cuenta_bancaria = $relacion['cod_cuenta_bancaria'];
              $saldodisponible = $this->saldoBanco();
              if ($saldodisponible < $descuento) {
                throw new Exception("Saldo insuficiente en la cuenta bancaria.");
              }
              $actualizarSaldoCuenta = "UPDATE cuenta_bancaria SET saldo = saldo - :monto WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
              $banco = $this->conex->prepare($actualizarSaldoCuenta);
              $banco->bindParam(':monto', $descuento);
              $banco->bindParam(':cod_cuenta_bancaria', $relacion['cod_cuenta_bancaria']);
              $banco->execute();
            } else if (!empty($relacion['cod_caja'])) {
              $this->cod_caja = $relacion['cod_caja'];

              $saldodisponible = abs($this->saldoCaja());
              if ((float)$saldodisponible < (float)$descuento) {
                throw new Exception("Saldo insuficiente en la caja.");
              }

              $actualizarSaldoCaja = "UPDATE caja SET saldo = saldo - :monto WHERE cod_caja = :cod_caja";
              $caja = $this->conex->prepare($actualizarSaldoCaja);
              $caja->bindParam(':monto', $descuento);
              $caja->bindParam(':cod_caja', $relacion['cod_caja']);
              $caja->execute();
            }
          }
        }
      }

      if ($this->tipo_pago == 'compra') {
        $monto_pagarc = (float)$this->monto_pagar;

        if ($this->montototal > $montopc) {
          $status = "UPDATE compras SET status = 2 WHERE cod_compra=:cod_compra";
          $editgasto = $this->conex->prepare($status);
          $editgasto->bindParam(':cod_compra', $this->cod_compra);
          if (!$editgasto->execute()) {
            throw new Exception("Error al actualizar el estado de la compra.");
          }

          $r = abs($this->montototal - $montopc);
        } else if ($this->montototal < $montopc) {
          if ($this->vuelto > 0) {

            $sql = "INSERT INTO vuelto_recibido(vuelto_total) VALUES(:vuelto_total)";
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':vuelto_total', $this->vuelto);
            if (!$strExec->execute()) {
              throw new Exception("Error al insertar en vuelto_recibido.");
            }
            $vueltocod = $this->conex->lastInsertId();

            foreach ($this->pagoV['vuelto'] as $pago) {
              if (!empty($pago['monto']) && $pago['monto'] > 0) {

                $detvuelto = "INSERT INTO detalle_vueltor(cod_vuelto_r,cod_tipo_pago, monto) VALUES(:cod_vuelto_r,:cod_tipo_pago,:monto)";
                $strExec = $this->conex->prepare($detvuelto);
                $strExec->bindParam(':cod_vuelto_r', $vueltocod);
                $strExec->bindParam(':cod_tipo_pago', $pago['cod_tipo_pago']);
                $strExec->bindParam(':monto', $pago['monto']);
                if (!$strExec->execute()) {
                  throw new Exception("Error al registrar un vuelto en la transacción.");
                }

                $consultaRelacion1 = "SELECT cod_cuenta_bancaria, cod_caja FROM detalle_tipo_pago WHERE cod_tipo_pago = :cod_tipo_pago";
                $consulta = $this->conex->prepare($consultaRelacion1);
                $consulta->bindParam(':cod_tipo_pago', $pago['cod_tipo_pago']);
                if (!$consulta->execute()) {
                  throw new Exception("Error al obtener la relación de tipo de pago.");
                }
                $relacion1 = $consulta->fetch(PDO::FETCH_ASSOC);
                $divisaV = $this->divisa($pago['cod_tipo_pago']);
                $descuentoV = $divisaV['cod_divisa'] != 1 ? $pago['monto'] / $divisaV['ultima_tasa'] : $pago['monto'];
                if ($relacion1) {
                  if (!empty($relacion1['cod_cuenta_bancaria'])) {
                    $actualizarSaldoCuenta = "UPDATE cuenta_bancaria SET saldo = saldo + :monto WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
                    $banco = $this->conex->prepare($actualizarSaldoCuenta);
                    $banco->bindParam(':monto', $descuentoV);
                    $banco->bindParam(':cod_cuenta_bancaria', $relacion1['cod_cuenta_bancaria']);
                    $banco->execute();
                  } else if (!empty($relacion1['cod_caja'])) {
                    $actualizarSaldoCaja = "UPDATE caja SET saldo = saldo + :monto WHERE cod_caja = :cod_caja";
                    $caja = $this->conex->prepare($actualizarSaldoCaja);
                    $caja->bindParam(':monto', $descuentoV);
                    $caja->bindParam(':cod_caja', $relacion1['cod_caja']);
                    $caja->execute();
                  }
                }
              }
            }
            $actualizargasto = "UPDATE pago_emitido SET cod_vuelto_r = :cod_vuelto_r WHERE cod_pago_emitido= :cod_pago_emitido";
            $insert = $this->conex->prepare($actualizargasto);
            $insert->bindParam(':cod_vuelto_r', $vueltocod);
            $insert->bindParam(':cod_pago_emitido', $cod_pago_emitido);
            if (!$insert->execute()) {
              throw new Exception("Error al actualizar el gasto con el vuelto.");
            }
          } else {
            throw new Exception("No se pudo insertar el vuelto.");
          }

          $status = "UPDATE compras SET status= 3 WHERE cod_compra=:cod_compra";
          $gastoxvuelto = $this->conex->prepare($status);
          $gastoxvuelto->bindParam(':cod_compra', $this->cod_compra);
          if (!$gastoxvuelto->execute()) {
            throw new Exception("Error al actualizar el estado de la compra.");
          }

          $r = 0;
        } else if ($this->montototal == $montopc) {
          $status = "UPDATE compras SET status = 3 WHERE cod_compra=:cod_compra";
          $detgasto = $this->conex->prepare($status);
          $detgasto->bindParam(':cod_compra', $this->cod_compra);
          if (!$detgasto->execute()) {
            throw new Exception("Error al actualizar el estado del gasto.");
          }

          $r = 0;
        }
      } else {

        $monto_pagar = (float)$this->monto_pagar;

        if ($this->montototal > $montopg) {
          $status = "UPDATE gasto SET status = 2 WHERE cod_gasto=:cod_gasto";
          $editgasto = $this->conex->prepare($status);
          $editgasto->bindParam(':cod_gasto', $this->cod_gasto);
          if (!$editgasto->execute()) {
            throw new Exception("Error al actualizar el estado del gasto.");
          }

          $r = abs($this->montototal - $montopg);
        } else if ($this->montototal < $montopg) {
          if ($this->vuelto > 0) {

            $sql = "INSERT INTO vuelto_recibido(vuelto_total) VALUES(:vuelto_total)";
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':vuelto_total', $this->vuelto);
            if (!$strExec->execute()) {
              throw new Exception("Error al insertar en vuelto_recibido.");
            }
            $vueltocod = $this->conex->lastInsertId();

            foreach ($this->pagoV['vuelto'] as $pago) {
              if (!empty($pago['monto']) && $pago['monto'] > 0) {

                $detvuelto = "INSERT INTO detalle_vueltor(cod_vuelto_r,cod_tipo_pago, monto) VALUES(:cod_vuelto_r,:cod_tipo_pago,:monto)";
                $strExec = $this->conex->prepare($detvuelto);
                $strExec->bindParam(':cod_vuelto_r', $vueltocod);
                $strExec->bindParam(':cod_tipo_pago', $pago['cod_tipo_pago']);
                $strExec->bindParam(':monto', $pago['monto']);
                if (!$strExec->execute()) {
                  throw new Exception("Error al registrar un vuelto en la transacción.");
                }

                $consultaRelacion1 = "SELECT cod_cuenta_bancaria, cod_caja FROM detalle_tipo_pago WHERE cod_tipo_pago = :cod_tipo_pago";
                $consulta = $this->conex->prepare($consultaRelacion1);
                $consulta->bindParam(':cod_tipo_pago', $pago['cod_tipo_pago']);
                if (!$consulta->execute()) {
                  throw new Exception("Error al obtener la relación de tipo de pago.");
                }

                $relacion1 = $consulta->fetch(PDO::FETCH_ASSOC);
                $divisaVG = $this->divisa($pago['cod_tipo_pago']);
                $descuentoG = $divisaVG['cod_divisa'] != 1 ? $pago['monto'] / $divisaVG['ultima_tasa'] : $pago['monto'];

                if ($relacion1) {
                  if (!empty($relacion1['cod_cuenta_bancaria'])) {

                    $this->cod_cuenta_bancaria = $relacion1['cod_cuenta_bancaria'];

                    $actualizarSaldoCuenta = "UPDATE cuenta_bancaria SET saldo = saldo + :monto WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
                    $banco = $this->conex->prepare($actualizarSaldoCuenta);
                    $banco->bindParam(':monto', $descuentoG);
                    $banco->bindParam(':cod_cuenta_bancaria', $relacion1['cod_cuenta_bancaria']);
                    $banco->execute();
                  } else if (!empty($relacion1['cod_caja'])) {
                    $this->cod_caja = $relacion1['cod_caja'];

                    $actualizarSaldoCaja = "UPDATE caja SET saldo = saldo + :monto WHERE cod_caja = :cod_caja";
                    $caja = $this->conex->prepare($actualizarSaldoCaja);
                    $caja->bindParam(':monto', $descuentoG);
                    $caja->bindParam(':cod_caja', $relacion1['cod_caja']);
                    $caja->execute();
                  }
                }
              }
            }
            $actualizargasto = "UPDATE pago_emitido SET cod_vuelto_r = :cod_vuelto_r WHERE cod_pago_emitido= :cod_pago_emitido";
            $insert = $this->conex->prepare($actualizargasto);
            $insert->bindParam(':cod_vuelto_r', $vueltocod);
            $insert->bindParam(':cod_pago_emitido', $cod_pago_emitido);
            if (!$insert->execute()) {
              throw new Exception("Error al actualizar el gasto con el vuelto.");
            }
          } else {
            throw new Exception("No se pudo insertar el vuelto.");
          }

          $status = "UPDATE gasto SET status= 3 WHERE cod_gasto=:cod_gasto";
          $gastoxvuelto = $this->conex->prepare($status);
          $gastoxvuelto->bindParam(':cod_gasto', $this->cod_gasto);
          if (!$gastoxvuelto->execute()) {
            throw new Exception("Error al actualizar el estado del gasto.");
          }
          $r = 0;
        } else if ($this->montototal == $montopg) {
          $status = "UPDATE gasto SET status = 3 WHERE cod_gasto=:cod_gasto";
          $detgasto = $this->conex->prepare($status);
          $detgasto->bindParam(':cod_gasto', $this->cod_gasto);
          if (!$detgasto->execute()) {
            throw new Exception("Error al actualizar el estado del gasto.");
          }
          $r = 0;
        }
      }

      $this->conex->commit();
      return $r;
    } catch (Exception $e) {
      $this->conex->rollBack();
      error_log("Error en la consulta: " . $e->getMessage());
      $this->errores[] = $e->getMessage();
      return false;
    } finally {

      parent::desconectarBD();
    }
  }
  public function registrarPgasto()
  {
    return $this->registrarPG();
  }

  private function divisa($recibo)
  {
    $calculo = "SELECT dtp.cod_tipo_pago, dtp.cod_cuenta_bancaria, dtp.cod_caja,

      CASE 
        WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN 'banco'
         WHEN dtp.cod_caja IS NOT NULL THEN 'caja'
        ELSE 'indefinido'
        END AS origen,

   
       CASE 
       WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN dcb.nombre
       WHEN dtp.cod_caja IS NOT NULL THEN dcj.nombre
        ELSE 'Sin divisa'
        END AS nombre_divisa,

        CASE 
        WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN dcb.abreviatura
        WHEN dtp.cod_caja IS NOT NULL THEN dcj.abreviatura
        ELSE '-'
        END AS abreviatura_divisa,

        CASE 
        WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN dcb.cod_divisa
        WHEN dtp.cod_caja IS NOT NULL THEN dcj.cod_divisa
        ELSE NULL
        END AS cod_divisa,

    
        CASE 
        WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN (
        SELECT cd.tasa
        FROM cambio_divisa cd
        WHERE cd.cod_divisa = cb.cod_divisa
        ORDER BY cd.fecha DESC
        LIMIT 1
        )
        WHEN dtp.cod_caja IS NOT NULL THEN (
        SELECT cd.tasa
        FROM cambio_divisa cd
        WHERE cd.cod_divisa = cj.cod_divisas
        ORDER BY cd.fecha DESC
        LIMIT 1
        )
        ELSE NULL
        END AS ultima_tasa

        FROM detalle_tipo_pago dtp
        LEFT JOIN cuenta_bancaria cb ON dtp.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
        LEFT JOIN divisas dcb ON cb.cod_divisa = dcb.cod_divisa

        LEFT JOIN caja cj ON dtp.cod_caja = cj.cod_caja
        LEFT JOIN divisas dcj ON cj.cod_divisas = dcj.cod_divisa

      WHERE dtp.cod_tipo_pago = :cod_tipo_pago;";
    $strExec = $this->conex->prepare($calculo);
    $strExec->bindParam(':cod_tipo_pago', $recibo);
    $strExec->execute();
    $result = $strExec->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  private function saldoBanco()
  {
    $sql = "SELECT saldo FROM cuenta_bancaria WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria FOR UPDATE";

    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam(':cod_cuenta_bancaria', $this->cod_cuenta_bancaria);
    $res = $strExec->execute();
    $resultado = $strExec->fetch(PDO::FETCH_ASSOC);

    if ($res) {
      return $resultado['saldo'];
    } else {
      return [];
    }
  }
  private function saldoCaja()
  {
    $sql = "SELECT saldo FROM caja WHERE cod_caja = :cod_caja FOR UPDATE";

    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam(':cod_caja', $this->cod_caja);
    $res = $strExec->execute();
    $resultado = $strExec->fetch(PDO::FETCH_ASSOC);

    if ($res && $resultado) {
      return (float) $resultado['saldo'];
    } else {
      return [];
    }
  }
  public function Scaja()
  {
    return $this->saldoCaja();
  }
  public function Sbanco()
  {
    return $this->saldoBanco();
  }

  private function verifSaldo($cod_tipo_pago)
  {
    $n = null; 
    try {
      parent::conectarBD();
      $this->conex->beginTransaction();
      $buscar = "SELECT cod_cuenta_bancaria, cod_caja FROM detalle_tipo_pago WHERE cod_tipo_pago = :cod_tipo_pago";
      $strExec = $this->conex->prepare($buscar);
      $strExec->bindParam(':cod_tipo_pago', $cod_tipo_pago);
      $res = $strExec->execute();
      $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
      if ($res) {
        if ($resultado['cod_cuenta_bancaria']) {
          $valor = $resultado['cod_cuenta_bancaria'];
          $sql = "SELECT saldo FROM cuenta_bancaria WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
          $strExec = $this->conex->prepare($sql);
          $strExec->bindParam(':cod_cuenta_bancaria', $valor);
          $res = $strExec->execute();
          $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
          if ($res) {
            $n = $resultado['saldo'];
          }
        } else if ($resultado['cod_caja']) {
          $valor = $resultado['cod_caja'];
          $sql = "SELECT saldo FROM caja WHERE cod_caja = :cod_caja";
          $strExec = $this->conex->prepare($sql);
          $strExec->bindParam(':cod_caja', $valor);
          $res = $strExec->execute();
          $resultado = $strExec->fetch(PDO::FETCH_ASSOC);

          if ($res) {
            $n = $resultado['saldo'];
          }
        }
      } else {
        throw new Exception("No se encontró el tipo de pago especificado.");
      }
      $this->conex->commit();
      if($n == null){
        return $n = 0;
      }else if($n != null){
        return $n;
      }
      
    } catch (Exception $e) {
      $this->conex->rollBack();
      error_log("Error en la consulta: " . $e->getMessage());
      $this->errores[] = $e->getMessage();
      return false;
    } finally {
      parent::desconectarBD();
    }
  }

  public function saldo($cod_tipo_pago)
  {
    return $this->verifSaldo($cod_tipo_pago);
  }

  private function gastos()
  {
    $sql = "SELECT monto_total, cod_pago_emitido, cod_gasto FROM pago_emitido WHERE cod_gasto = :cod_gasto";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam(':cod_gasto', $this->cod_gasto);
    $res = $strExec->execute();
    $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if ($res) {
      return $resultado;
    } else {
      return [];
    }
  }
  public function getGastos()
  {
    return $this->gastos();
  }

  private function compras()
  {
    $sql = "SELECT monto_total, cod_pago_emitido FROM pago_emitido WHERE cod_compra = :cod_compra";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam(':cod_compra', $this->cod_compra);
    $res = $strExec->execute();
    $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if ($res) {
      return $resultado;
    } else {
      return [];
    }
  }
  public function getCompras()
  {
    return $this->compras();
  }
  public function getDatos()
  {
    return $this->datos;
  }
  public function getcod_pago()
  {
    return $this->cod_pago_emitido;
  }
}
