<?php
namespace Modelo;

use PDO;
use PDOException;
//require_once "config/config.php";

class Conexion extends PDO{
	protected $conex;
	private $conexionActiva=false;
	private $link;
	private $user;
	private $pass;

	public function __construct($host, $db, $user, $pass){
		$this->link="mysql:host=" .$host. ";dbname=" .$db. ";charset=utf8";
		$this->user=$user;
		$this->pass=$pass;
	}
		
	public function conectarBD() {
		if(!$this->conexionActiva) {
			try {
				$this->conex = new PDO($this->link, $this->user, $this->pass);
				$this->conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->conexionActiva = true;
			} catch (PDOException $e) {
				die("conexión fallida" . $e->getMessage());
			}
		}
	}

	public function desconectarBD() {
		if($this->conexionActiva) {
			$this->conex = null;
			$this->conexionActiva = false;
		}
	}
}