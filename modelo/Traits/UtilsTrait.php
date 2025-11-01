<?php

namespace Modelo\Traits;

trait UtilsTrait {

    protected function formatearFechaEspanol($fecha, $formato = 'abreviado') {
        if (empty($fecha)) {
            return '';
        }
        
        $timestamp = strtotime($fecha);
        if ($timestamp === false) {
            return $fecha; // fecha original si no se puede parsear
        }
        
        $mes = date('n', $timestamp);
        $año = date('Y', $timestamp);
        
        if ($formato === 'completo') {
            $mesesCompletos = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            return $mesesCompletos[$mes] . ' ' . $año;
        } else {
            $mesesAbreviados = [
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
            ];
            return $mesesAbreviados[$mes] . ' ' . $año;
        }
    }
    
    protected function formatearMesEspanol($fecha, $formato = 'abreviado') {
        if (empty($fecha)) {
            return '';
        }
        
        $timestamp = strtotime($fecha);
        if ($timestamp === false) {
            return $fecha;
        }
        
        $mes = date('n', $timestamp);
        
        if ($formato === 'completo') {
            $mesesCompletos = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            return $mesesCompletos[$mes];
        } else {
            $mesesAbreviados = [
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
            ];
            return $mesesAbreviados[$mes];
        }
    }
} 