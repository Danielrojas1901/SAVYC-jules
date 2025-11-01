<?php
namespace Modelo\Traits;
trait ImagenTrait {
    private $imagen;
    private $anchoMax = 1000;
    private $alturaMax = 1000;
    private $tamanoMax = 5242880; // 5MB en bytes
    private $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
    private $directorioBase = 'vista/dist/img/';
    private $subcarpeta = '';
    private $imagenDefault = 'default.png';

    public function setDirectorioBase($directorioBase) {
        $this->directorioBase = rtrim($directorioBase, '/') . '/';
    }

    public function setSubcarpeta($subcarpeta) {
        $this->subcarpeta = $subcarpeta ? rtrim($subcarpeta, '/') . '/' : '';
    }

    public function setImagenDefault($nombreArchivo) {
        $this->imagenDefault = $nombreArchivo;
    }

    public function setDimensionesMaximas($ancho, $alto) {
        $this->anchoMax = $ancho;
        $this->alturaMax = $alto;
    }

    public function setTamanoMaximo($tamano) {
        $this->tamanoMax = $tamano;
    }

    public function setTiposPermitidos($tipos) {
        $this->tiposPermitidos = $tipos;
    }

    public function getImagen() {
        return $this->imagen;
    }

    public function setImagen($ruta) {
        $this->imagen = $ruta;
    }

    public function validar($archivo) {
        // Si no hay archivo, se considera válido y se usará la imagen predeterminada
        if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $tipoImagen = $archivo['type'];
        $tamanoImagen = $archivo['size'];
        $imagenTemp = $archivo['tmp_name'];

        // Verificar si el archivo es una imagen válida
        $infoImagen = getimagesize($imagenTemp);
        if ($infoImagen === false) {
            $this->errores['imagen'] = "El archivo no es una imagen válida";
            return false;
        }

        // Verificar dimensiones
        list($ancho, $alto) = $infoImagen;
        if ($ancho > $this->anchoMax || $alto > $this->alturaMax) {
            $this->errores['imagen'] = "Las dimensiones de la imagen deben ser como máximo {$this->anchoMax}px de ancho y {$this->alturaMax}px de alto";
            return false;
        }

        // Verificar tamaño
        if ($tamanoImagen > $this->tamanoMax) {
            $this->errores['imagen'] = "El tamaño de la imagen es demasiado grande (máximo " . ($this->tamanoMax / 1024 / 1024) . "MB)";
            return false;
        }

        // Verificar tipo
        if (!in_array($tipoImagen, $this->tiposPermitidos)) {
            $this->errores['imagen'] = "El tipo de imagen no es permitido. Tipos aceptados: JPG, PNG, GIF";
            return false;
        }

        return true;
    }

    public function subir($archivo, $nombrePersonalizado = null) {
        // Si no hay archivo o no pasa la validación, usar imagen predeterminada
        if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK || !$this->validar($archivo)) {
            $rutaDefault = $this->directorioBase . $this->subcarpeta . $this->imagenDefault;
            $this->imagen = $rutaDefault;
            return $rutaDefault;
        }

        // Crear directorio si no existe
        $directorioCompleto = $this->directorioBase . $this->subcarpeta;
        if (!is_dir($directorioCompleto)) {
            mkdir($directorioCompleto, 0755, true);
        }

        // Determinar nombre de archivo
        $nombreArchivo = $nombrePersonalizado ? 
            $nombrePersonalizado . '.' . pathinfo($archivo['name'], PATHINFO_EXTENSION) : 
            $archivo['name'];
            
        // Sanitizar nombre de archivo
        $nombreArchivo = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $nombreArchivo);

        $rutaCompleta = $directorioCompleto . $nombreArchivo;
        $rutaDB = $rutaCompleta;
        
        // Subir archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            $this->imagen = $rutaDB;
            return $rutaDB; 
        } else {
            $this->errores['imagen'] = "Error al subir la imagen";
            
            $rutaDefault = $this->directorioBase . $this->subcarpeta . $this->imagenDefault;
            $this->imagen = $rutaDefault;
            return $rutaDefault;
        }
    }

    public function procesar($archivo, $imagenActual = null, $nombrePersonalizado = null) {
        if ((!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) && !empty($imagenActual)) {
            if (file_exists($imagenActual)) {
                $this->imagen = $imagenActual;
                return $imagenActual;
            } else {
                $rutaDefault = $this->directorioBase . $this->subcarpeta . $this->imagenDefault;
                $this->imagen = $rutaDefault;
                return $rutaDefault;
            }
        } 
        
        return $this->subir($archivo, $nombrePersonalizado);
    }

    public function eliminarImagen($ruta) {
        if (basename($ruta) === $this->imagenDefault) {
            return false;
        }

        if (file_exists($ruta) && is_file($ruta)) {
            return unlink($ruta);
        }
        return false;
    }
}