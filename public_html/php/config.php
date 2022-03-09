<?php

define("RUTA_LOGS", __DIR__ . "/logs");

ini_set('display_errors', 0);
ini_set("log_errors", 0);
ini_set("error_log", RUTA_LOGS . "/" . date("Y-m-d") . ".log");

// Mapeo
define("MAPPING_LOGIN", "autenticar");

define("MAPPING_CATEGORIA", "categoria");
define("MAPPING_CUENTA", "cuenta");
define("MAPPING_LIBRO", "libro");
define("MAPPING_PEDIDO", "pedido");
define("MAPPING_FLIPBOOK", "flipbook");

define("MAPPING_PRODUCTO", "productos");
define("MAPPING_CATEGORIA_PRODUCTO", "categoria-productos");
define("MAPPING_COMPRA", "compras");
define("MAPPING_CORREO", "correos");

// Parametros
define("METODO_GET", 'GET');
define("METODO_POST", 'POST');
define("METODO_PUT", 'PUT');

// URL
define("PRODUCT_IMG", '../img/product-img');

// Captcha
require_once ('libs/recaptchalib.php');

define("CAPTCHA_SECRET", '');

// Mail
require_once ("libs/phpmailer/class.phpmailer.php");

define("MAIL_HOST", '');
define("MAIL_USUARIO", '');
define("MAIL_CONTRASENA", '');
define("MAIL_CONTACTO", '');

define("URL_HOME", '');

define("DIR_FLIPBOOK", '../img/flipbook');

define("ROL_ADMINISTRADOR", "'ADMINISTRADOR'");
define("ROL_COLPORTOR", "'COLPORTOR'");
define("ROL_CLIENTE", "'CLIENTE'");

/*
 * AppCode
 */

class AppCode {

    const OK = array('code' => 0, 'message' => 'OK');
    const UNKNOWN_ERROR = array('code' => 1, 'message' => 'Error desconocido');
    const DATA_VALIDATION_ERROR = array('code' => 2, 'message' => 'Validación de datos');
    const PARSE_JSON_ERROR = array('code' => 3, 'message' => 'Error al leer JSON mal formado');
    const CREDENTIALS_ERROR = array('code' => 4, 'message' => 'No tiene los permisos necesarios');
    const DATABASE_ERROR = array('code' => 5, 'message' => 'Error conexion a la base de datos');
    const COMPONENT_ERROR = array('code' => 6, 'message' => 'Error en la definicion de parametros');
    const SERVICE_NOT_AVAILABLE = array('code' => 7, 'message' => 'Error servicio no disponible');
    const ENVIO_CORREO_ERROR = array('code' => 8, 'message' => 'Error no se pudo enviar el correo');

}

/*
 * AppException
 */

class AppException extends Exception {

    public function __construct(array $tipo = null) {
        if (isset($tipo) && isset($tipo['code']) && isset($tipo['message'])) {
            parent::__construct($tipo['message'], $tipo['code']);
        } else {
            parent::__construct("Error");
        }
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}

/*
 * ValidationException
 */

class ValidationException extends AppException {

    public function __construct(string $tipo = null) {
        $validate = AppCode::DATA_VALIDATION_ERROR;
        if (isset($tipo)) {
            $validate[message] .= ': ' . $tipo;
            parent::__construct($validate);
        } else {
            parent::__construct($validate);
        }
    }

}

/*
 * AppResponse
 */

class AppResponse {

    public $code;
    public $data;
    public $description;

    public function __construct($code = null, $data = null, $description = null) {
        $this->code = $code;
        $this->data = $data;
        $this->description = $description;
    }

    public function imprimir() {
        echo json_encode($this, JSON_NUMERIC_CHECK);
    }

}

/*
 * AzsConverter
 */

class AzsConverter {

    public static function toObject(object $obj, object $object) {
        $vars = get_object_vars($obj);
        foreach ($vars as $key => $val) {
            if (is_array($val) || is_object($val)) {
                
            } elseif (property_exists($object, $key)) {
                $object->$key = utf8_encode($val);
            }
        }
        return $object;
    }

    public static function toObjectPrefix(string $prefix = 'a', object $obj, object $object) {
        $vars = get_object_vars($obj);
        $auxClass = new stdClass();
        foreach ($vars as $key => $val) {
            $prefix_replace = $prefix . '_';
            $keyObj = substr($key, strlen($prefix_replace));
            if (property_exists($object, $keyObj)) {
                $auxClass->$keyObj = $val;
            }
        }
        AzsConverter::toObject($auxClass, $object);
        return $object;
    }

}

/*
 * Database
 */

class Database {

    private $db_host = '';
    private $db_username = '';
    private $db_password = '';
    private $db_name = '';

    public function dbConnection() {
        try {
            $conn = new PDO('mysql:host=' . $this->db_host . ';dbname=' . $this->db_name, $this->db_username, $this->db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            throw new AppException(AppCode::DATABASE_ERROR);
        }
    }

}

/*
 * 
 */

class ApiRest {

    public function getMetodo() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getParamatro() {
        $result = [];
        parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $result);
        return $result;
    }

    public function getDatos() {
        return json_decode(file_get_contents("php://input"));
    }

    public function getMapeo() {
        $result = [];
        if (isset($_REQUEST['mapping'])) {
            $result = explode('/', trim($_REQUEST['mapping']));
        }
        return $result;
    }

}

?>