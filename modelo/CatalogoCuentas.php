<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;

class CatalogoCuentas extends Conexion{
    use ValidadorTrait;
    private $codigo_contable;
    private $nombre;
    private $naturaleza;
    private $cuenta_padreid;
    private $nivel;
    private $saldo;
    private $status;

    private $errores = [];

    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function setDatos($datos){

        //Cuenta hija
        if(isset($datos['cuentaPadre'])){
            $rp = $this->validarCodigoSelect($datos['cuentaPadre'], 'cuentaPadre');
            if($rp === true) {
                $this->cuenta_padreid = $datos['cuentaPadre'];
            } else {
                $this->errores['cuentaPadre'] = $rp;
            }
        }

        if(isset($datos['naturaleza'])){
            //Naturaleza cuenta padre
            $rs = $this->validarSelect($datos['naturaleza'], ['deudora', 'acreedora'], 'naturaleza');
            if($rs === true) {
                $this->naturaleza = $datos['naturaleza'];
            } else {
                $this->errores['naturaleza'] = $rs;
            }
        }else if(isset($datos['naturalezaHidden'])){
            //Naturaleza cuenta padre
            $rs = $this->validarSelect($datos['naturalezaHidden'], ['deudora', 'acreedora'], 'naturaleza');
            if($rs === true) {
                $this->naturaleza = $datos['naturalezaHidden'];
            } else {
                $this->errores['naturalezaHidden'] = $rs;
            }
        }

        if(isset($datos['naturalezae'])){
            //Naturaleza cuenta padre
            $rs = $this->validarSelect($datos['naturalezae'], ['deudora', 'acreedora'], 'naturaleza');
            if($rs === true) {
                $this->naturaleza = $datos['naturalezae'];
            } else {
                $this->errores['naturalezae'] = $rs;
            }
        }

        if(isset($datos['naturalezah'])){
            //Naturaleza cuenta padre
            $rs = $this->validarSelect($datos['naturalezah'], ['deudora', 'acreedora'], 'naturaleza');
            if($rs === true) {
                $this->naturaleza = $datos['naturalezah'];
            } else {
                $this->errores['naturalezah'] = $rs;
            }
        }

        if(isset($datos['nivel'])){
            $r = $this->validarCodigoSelect($datos['nivel'], 'nivel');
            if ($r === true) {
                $this->nivel = $datos['nivel'];
            } else {
                $this->errores['nivel'] = $r;
            }
        }

        if(isset($datos['nombreCuenta'])){
            $resul = $this->validarTextoNumero($datos['nombreCuenta'], 'Nombre de la Cuenta', 2, 50);
            if ($resul === true) {
                $this->nombre = $datos['nombreCuenta'];
            } else {
                $this->errores['nombreCuenta'] = $resul;
            }
        }

        if(isset($datos['saldo'])){
            $result = $this->validarDecimal($datos['saldo'], 'saldo', 1, 20);
            if ($result === true) {
                $this->saldo = $datos['saldo'];
            } else {
                $this->errores['saldo'] = $result;
            }
        }

        // Validar código contable
        if(isset($datos['codigoContable'])){
            if(!empty('codigoContable')){
                $this->codigo_contable = $datos['codigoContable'];
            } else {
            $this->errores['codigoContable'] = 'El código contable no puede estar vacío';
        }
    }

    }

    public function getCodigoContable(){
        return $this->codigo_contable;
    }
    public function getSaldo(){
        return $this->saldo;
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function getNaturaleza(){
        return $this->naturaleza;
    }
    public function getCuentaPadreid(){
        return $this->cuenta_padreid;
    }
    public function getNivel(){
        return $this->nivel;
    }

    public function check(){
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }
    
    public function getErrores() {
        return $this->errores;
    }
    /*==============================
    BUSCAR
    ================================*/
    private function buscar($valor){
    $this->nombre=$valor;
    $registro = "SELECT * FROM cuentas_contables WHERE nombre_cuenta='".$this->nombre."'";
    $resultado= "";
    parent::conectarBD();
        $dato=$this->conex->prepare($registro);
        $resul=$dato->execute();
        $resultado=$dato->fetch(PDO::FETCH_ASSOC); 
    parent::desconectarBD();
        if ($resul) {
            return $resultado;
        }else{
            return false;
        }
    }

    public function getbuscar($valor){
        return $this->buscar($valor);
    }

    
    /*==============================
    REGISTRAR CUENTA CONTABLE
    ================================*/
    private function registrar() {
    try {
        parent::conectarBD();

        // Verificar si hay cuenta padre
        if (!empty($this->cuenta_padreid)) {
            $sql = "SELECT nivel, naturaleza FROM cuentas_contables WHERE cod_cuenta = :cuenta_padreid";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cuenta_padreid', $this->cuenta_padreid, PDO::PARAM_INT);
            $stmt->execute();
            $padre = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$padre) {
                throw new Exception("Cuenta padre no encontrada.");
            }

            // Validar nivel
            if ($this->nivel <= $padre['nivel']) {
                throw new Exception("El nivel de la cuenta debe ser mayor que el de su cuenta padre.");
            }

            // Validar naturaleza
            if ($this->naturaleza !== $padre['naturaleza']) {
                throw new Exception("La naturaleza de la cuenta hija debe ser igual a la de su cuenta padre.");
            }
        }

        // Insertar cuenta contable
        $sqlInsert = "INSERT INTO cuentas_contables (codigo_contable, nombre_cuenta, naturaleza, cuenta_padreid, nivel, status)
        VALUES (:codigo_contable, :nombre, :naturaleza, :cuenta_padreid, :nivel, :status)";

        $stmtInsert = $this->conex->prepare($sqlInsert);
        $stmtInsert->bindParam(':codigo_contable', $this->codigo_contable);
        $stmtInsert->bindParam(':nombre', $this->nombre);
        $stmtInsert->bindParam(':naturaleza', $this->naturaleza);
        $stmtInsert->bindParam(':cuenta_padreid', $this->cuenta_padreid, PDO::PARAM_INT);
        $stmtInsert->bindParam(':nivel', $this->nivel, PDO::PARAM_INT);
        //$stmtInsert->bindParam(':saldo', $this->saldo);
        $stmtInsert->bindValue(':status', 2, PDO::PARAM_INT);
        $stmtInsert->execute();
        return 1;
    } catch (Exception $e) {
        $this->errores[] = $e->getMessage();
        return 0;
    } finally {
        parent::desconectarBD();
    }
}


    public function getregistrar(){
        return $this->registrar();
    }

/* CONSULTAR (TABLA) CON STORED PROCEDURE  */
    private function consultar_cuentas(){
        $sql = "CALL consultar_cuentas_contables()";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->execute();
        $resul=$strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $resul;
    }

    public function getconsultar_cuentas(){
        return $this->consultar_cuentas();
    }


/* LISTAR CUENTAS PADRES POR NIVEL */
    public function get_listarcuentaspadrespornivel($nivel){
        return $this->listarcuentaspadrespornivel($nivel);
    }

    private function listarcuentaspadrespornivel($nivel) {
    try {
        parent::conectarBD();
        $nivelPadre = $nivel - 1;
        $sql = "SELECT cod_cuenta, codigo_contable, nombre_cuenta, naturaleza 
                FROM cuentas_contables 
                WHERE nivel = :nivel 
                ORDER BY codigo_contable ASC";
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':nivel', $nivelPadre, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $resultado;
    } catch (Exception $e) {
        parent::desconectarBD();
        return [];
    }
}


/* GENERAR CÓDIGO CONTABLE POR NIVEL */
    private function generarCodigo($nivel, $codPadre = null) {
        try {
            parent::conectarBD();

            // NIVEL 1: Código raíz
            if ($nivel == 1) {
                $sql = "SELECT MAX(CAST(codigo_contable AS UNSIGNED)) AS ultimo 
                        FROM cuentas_contables 
                        WHERE nivel = 1 AND codigo_contable NOT LIKE '%.%'";
                $stmt = $this->conex->prepare($sql);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if(!$stmt){
                    throw new Exception("No se pudo consultar");
                }

                $nuevo = $row && $row['ultimo'] ? ((int)$row['ultimo'] + 1) : 1;
                return $nuevo;
            } else{

                // NIVEL > 1: Buscar código del padre y generar en base a él
                if ($codPadre === null) {
                    throw new Exception("La cuenta hija no tiene cuenta padre"); 
                }

                // Obtener código del padre
                $sql = "SELECT codigo_contable FROM cuentas_contables WHERE cod_cuenta = :codPadre";
                $stmt = $this->conex->prepare($sql);
                $stmt->bindParam(':codPadre', $codPadre);
                $stmt->execute();
                $padre = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$padre) {
                    throw new Exception("La cuenta no tiene cuenta padre");;
                }

                $codigoPadre = $padre['codigo_contable'];

                // Buscar último hijo directo
                $sqlHijos = "SELECT MAX(codigo_contable) AS ultimo 
                            FROM cuentas_contables 
                            WHERE cuenta_padreid = :codPadre";
                $stmtHijos = $this->conex->prepare($sqlHijos);
                $stmtHijos->bindParam(':codPadre', $codPadre);
                $stmtHijos->execute();
                $hijo = $stmtHijos->fetch(PDO::FETCH_ASSOC);

                if ($hijo && $hijo['ultimo']) {
                    $partes = explode('.', $hijo['ultimo']);
                    $ultimoSegmento = (int) end($partes) + 1;

                    if ($nivel <= 3) {
                        $nuevoCodigo = $codigoPadre . '.' . $ultimoSegmento; // sin ceros para nivel 2 y 3
                    } else {
                        $nuevoCodigo = $codigoPadre . '.' . str_pad($ultimoSegmento, 2, '0', STR_PAD_LEFT); // con ceros desde nivel 4
                    }
                    return $nuevoCodigo;

                } else {
                    // Primer hijo
                    if ($nivel <= 3) {
                        return $codigoPadre . '.1'; // sin ceros
                    } else {
                        return $codigoPadre . '.01'; // con ceros desde nivel 4
                    }
                }
        }

        } catch (Exception $e) {
            $this->errores[] = 'Error al generar el codigo '.$e->getMessage();
            return 0;
        } finally {
            parent::desconectarBD();
        }
    }

    public function get_generarCodigo($nivel, $codPadre = null){
        return $this->generarCodigo($nivel, $codPadre);
    }

    // EDITAR 
private function editar($datos){
    try {
        parent::conectarBD();

        // 1. Buscar la cuenta
        $sql = "SELECT * FROM cuentas_contables WHERE cod_cuenta = :id";
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':id', $datos['codigocuenta'], PDO::PARAM_INT);
        $stmt->execute();
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cuenta) {
            throw new Exception("La cuenta contable no existe.");
        }

        // 2. Verificar permisos de usuario
        if ($_SESSION['cod_usuario'] != 1 && $cuenta['status'] == 1) {
            throw new Exception("No tienes permiso para editar cuentas predefinidas del sistema.");
        }

        $naturaleza = !empty($datos['naturaleza']) ? $datos['naturaleza'] : $datos['naturalezah'];
        $esPadre = $this->tieneHijas($cuenta['cod_cuenta']);

        // 3. Si es cuenta padre y cambia la naturaleza
        if ($esPadre && $cuenta['naturaleza'] !== $naturaleza) {
            $resultado = $this->propagarNaturaleza($cuenta['cod_cuenta'], $naturaleza);
            if ($resultado === "error3") {// ??????????
                throw new Exception("No se puede cambiar la naturaleza porque tiene cuentas hijas con movimientos.");
            }
        }

        // 4. Actualizar solo nombre y naturaleza (si es nivel 1)
        $sqlUpdate = "UPDATE cuentas_contables SET nombre_cuenta = :nombre, naturaleza = :naturaleza WHERE cod_cuenta = :id";
        $stmt = $this->conex->prepare($sqlUpdate);
        $stmt->bindParam(':nombre', $datos['nombreCuenta']);
        $stmt->bindParam(':naturaleza', $naturaleza);
        $stmt->bindParam(':id', $datos['codigocuenta'], PDO::PARAM_INT);
        $stmt->execute();
        return 1;
        
    } catch (Exception $e) {
        throw new Exception("Error al editar la cuenta contable: " . $e->getMessage());
    } finally {
        parent::desconectarBD();
    }
}

    private function tieneHijas($id) {
        $sql = "SELECT COUNT(*) FROM cuentas_contables WHERE cuenta_padreid = :cuenta_padreid";
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cuenta_padreid', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    private function tieneMovimientos($id) {
        $sql = "SELECT COUNT(*) FROM detalle_asientos WHERE cod_cuenta = :cod_cuenta";
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod_cuenta', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }


    private function propagarNaturaleza($idPadre, $nuevaNaturaleza) {
    try {
        // 1. Obtener todas las subcuentas descendientes (hijas, nietas, etc.)
        $sql = "
            WITH RECURSIVE Hijas AS (
                SELECT cod_cuenta FROM cuentas_contables WHERE cuenta_padreid = :cuenta_padreid
                UNION ALL
                SELECT c.cod_cuenta
                FROM cuentas_contables c
                INNER JOIN Hijas h ON c.cuenta_padreid = h.cod_cuenta
            )
            SELECT cod_cuenta FROM Hijas
        ";
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cuenta_padreid', $idPadre, PDO::PARAM_INT);
        $stmt->execute();
        $hijas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($hijas)) {
            return; // No hay hijas, nada que propagar
        }

        // Validar que ninguna subcuenta tenga movimientos
        $inClause = implode(',', array_map('intval', $hijas));
        $sqlMov = "SELECT COUNT(*) FROM detalle_asientos WHERE cod_cuenta IN ($inClause)";
        $stmtMov = $this->conex->prepare($sqlMov);
        $stmtMov->execute();
        $tieneMovimientos = $stmtMov->fetchColumn();

        if ($tieneMovimientos > 0) {
            throw new Exception("No se puede cambiar la naturaleza porque una o más cuentas hijas tienen movimientos contables.");
        }

        // 3. Hacer el UPDATE a todas las hijas
        $sqlUpdate = "UPDATE cuentas_contables SET naturaleza = :naturaleza WHERE cod_cuenta IN ($inClause)";
        $stmtUpdate = $this->conex->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':naturaleza', $nuevaNaturaleza);
        $stmtUpdate->execute();

    } catch (Exception $e) {
        throw new Exception("Error al propagar la naturaleza: " . $e->getMessage()); // ??????????
    }
}

    public function geteditar($datos){
        return $this->editar($datos);
    }


/* ELIMINAR */
    private function eliminar($codigo) {
        try {
            parent::conectarBD();
            // 1. Obtener información de la cuenta
            $sql1 = "SELECT * FROM cuentas_contables WHERE cod_cuenta = :cod_cuenta";
            $stmt1 = $this->conex->prepare($sql1);
            $stmt1->bindParam(':cod_cuenta', $codigo, PDO::PARAM_INT);
            $stmt1->execute();
            $cuenta = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$cuenta) {
                throw new Exception("No existe la cuenta contable.");
            }

            // 2. Validar permiso (solo admin puede eliminar predefinidas)
            if ($_SESSION['cod_usuario'] != 1 && $cuenta['status'] == 1) {
                throw new Exception("No tiene permisos para eliminar una cuenta contable predefinida.");
            }

            // 3. Si no es nivel 5: verificar que no tenga subcuentas
            if ($cuenta['nivel'] != 5) {
                $sqlHijas = "SELECT COUNT(*) FROM cuentas_contables WHERE cuenta_padreid = :cod_cuenta";
                $stmtHijas = $this->conex->prepare($sqlHijas);
                $stmtHijas->bindParam(':cod_cuenta', $codigo, PDO::PARAM_INT);
                $stmtHijas->execute();
                $totalHijas = $stmtHijas->fetchColumn();

                if ($totalHijas > 0) {
                    throw new Exception("No se puede eliminar esta cuenta porque tiene subcuentas asociadas.");
                }
            }

            // 4. Si es nivel 5: verificar que no tenga movimientos contables
            if ($cuenta['nivel'] === 5) {
                $sqlMovs = "SELECT COUNT(*) FROM detalle_asientos WHERE cod_cuenta = :cod_cuenta";
                $stmtMovs = $this->conex->prepare($sqlMovs);
                $stmtMovs->bindParam(':cod_cuenta', $codigo, PDO::PARAM_INT);
                $stmtMovs->execute(); 
                $movimientos = $stmtMovs->fetchColumn();

                if ($movimientos > 0) {
                    throw new Exception("No se puede eliminar esta cuenta porque tiene movimientos contables asociados.");
                }
            }

            // 5. Eliminar cuenta
            $sql = "DELETE FROM cuentas_contables WHERE cod_cuenta = :cod_cuenta";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod_cuenta', $codigo, PDO::PARAM_INT);
            $stmt->execute();

            return 1;

        } catch (Exception $e) {
            return $e->getMessage();
        } finally {
            parent::desconectarBD();
        }
    }

    public function geteliminar($codigo){
        return $this->eliminar($codigo);
    }

    
}
