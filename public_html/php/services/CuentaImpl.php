<?php

/*
 * CuentaImpl
 */

class CuentaImpl {

    private $dbcon;

    function __construct() {
        $this->dbcon = new Database();
    }

    public function cuentaLogic($metodo, $variable, $parametro, $datos) {
        $response = '';
        switch ($metodo) {
            case METODO_GET:
                if (isset($variable)) {
                    $response = $this->getCuenta($variable);
                } else {
                    $response = $this->getCuentas($parametro);
                }
                break;
            case METODO_POST:
                $response = $this->postCuenta($datos);
                break;
            case METODO_PUT:
                $datos->id = $variable;
                $response = $this->putCuenta($datos);
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
        return $response;
    }

    public function getCuenta(int $id) {
        $conn = $this->dbcon->dbConnection();
        $cuenta = array();
        $get_stmt = $conn->prepare("SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.correo AS correo, "
                . "a.usuario AS usuario, "
                . "a.rol AS rol, "
                . "a.activo AS activo "
                . "FROM cuenta a "
                . "WHERE 1=1 AND id = :id");
        $get_stmt->bindParam(":id", $id);
        $get_stmt->execute();
        if ($get_stmt->rowCount() > 0) {
            if ($row = $get_stmt->fetchObject()) {
                $cuenta = new Cuenta();
                $cuenta->id = $row->id;
                $cuenta->nombre = utf8_encode($row->nombre);
                $cuenta->correo = utf8_encode($row->correo);
                $cuenta->usuario = utf8_encode($row->usuario);
                $cuenta->activo = ($row->activo === "1");
                $cuenta->rol = $row->rol;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $cuenta;
    }

    public function getCuentas(array $filters = null) {
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.correo AS correo, "
                . "a.usuario AS usuario, "
                . "a.rol AS rol, "
                . "a.activo AS activo "
                . "FROM cuenta a "
                . "WHERE 1=1";
        if (isset($filters)) {
            if (isset($filters['usuario'])) {
                $sql .= " AND usuario = :usuario";
            }
            if (isset($filters['correo'])) {
                $sql .= " AND correo = :correo";
            }
            if (isset($filters['excluyeId'])) {
                $sql .= " AND id <> :excluyeId";
            }
        }
        $get_stmt = $conn->prepare($sql);
        if (isset($filters)) {
            if (isset($filters['usuario'])) {
                $get_stmt->bindParam(":usuario", utf8_decode($filters['usuario']), PDO::PARAM_STR);
            }
            if (isset($filters['correo'])) {
                $get_stmt->bindParam(":correo", utf8_decode($filters['correo']), PDO::PARAM_STR);
            }
            if (isset($filters['excluyeId'])) {
                $get_stmt->bindParam(":excluyeId", $filters['excluyeId'], PDO::PARAM_INT);
            }
        }
        $get_stmt->execute();
        $arreglo = array();
        if ($get_stmt->rowCount() > 0) {
            while ($row = $get_stmt->fetchObject()) {
                $cuenta = new Cuenta();
                $cuenta->id = $row->id;
                $cuenta->nombre = utf8_encode($row->nombre);
                $cuenta->correo = utf8_encode($row->correo);
                $cuenta->usuario = utf8_encode($row->usuario);
                $cuenta->activo = ($row->activo === "1");
                $cuenta->rol = $row->rol;
                $arreglo[] = $cuenta;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $arreglo;
    }

    public function postCuenta($data) {
        $this->validateData($data);
        if (!isset($data->contrasena)) {
            throw new ValidationException("Debe ingresar la constraseña");
        } else if (strlen($data->contrasena) > 15) {
            throw new ValidationException("La contraseña no debe superar los 15 caracteres");
        }
        $cuentas = $this->getCuentas(array('usuario' => $data->usuario));
        if (sizeof($cuentas) > 0) {
            throw new ValidationException("Ya existe el nombre del usuario");
        }
        $cuentas = $this->getCuentas(array('correo' => $data->correo));
        if (sizeof($cuentas) > 0) {
            throw new ValidationException("Ya existe el correo asociado a un usuario");
        }
        $conn = $this->dbcon->dbConnection();
        $post_stmt = $conn->prepare("INSERT INTO cuenta ("
                . "nombre, "
                . "correo, "
                . "usuario, "
                . "contrasena, "
                . "activo, "
                . "rol"
                . ") VALUES ("
                . ":nombre, "
                . ":correo, "
                . ":usuario, "
                . ":contrasena, "
                . ":activo, "
                . ":rol)");
        $post_stmt->bindParam(":nombre", utf8_decode($data->nombre), PDO::PARAM_STR);
        $post_stmt->bindParam(":correo", utf8_decode($data->correo), PDO::PARAM_STR);
        $post_stmt->bindParam(":usuario", utf8_decode($data->usuario), PDO::PARAM_STR);
        $post_stmt->bindParam(":contrasena", hash('sha256', $data->contrasena), PDO::PARAM_STR);
        $post_stmt->bindParam(":activo", $data->activo, PDO::PARAM_BOOL);
        $post_stmt->bindParam(":rol", $data->rol, PDO::PARAM_STR);
        $post_stmt->execute();
        $post_stmt = null;
        $conn = null;
        return "Se creo correctamente";
    }

    public function putCuenta($data) {
        $this->validateData($data);
        $cuentas = $this->getCuentas(array('usuario' => $data->usuario, 'excluyeId' => $data->id));
        if (sizeof($cuentas) > 0) {
            throw new ValidationException("Ya existe el nombre del usuario");
        }
        $cuentas = $this->getCuentas(array('correo' => $data->correo, 'excluyeId' => $data->id));
        if (sizeof($cuentas) > 0) {
            throw new ValidationException("Ya existe el correo asociado a un usuario");
        }
        if (isset($data->contrasena) && strlen($data->contrasena) > 15) {
            throw new ValidationException("La contraseña no debe superar los 15 caracteres");
        }
        $conn = $this->dbcon->dbConnection();
        $put_stmt = $conn->prepare("UPDATE cuenta SET "
                . "nombre = :nombre, "
                . "correo = :correo, "
                . "usuario = :usuario, "
                . "activo = :activo, "
                . (isset($data->contrasena) ? "contrasena = :contrasena, " : "")
                . "rol = :rol "
                . "WHERE 1=1 AND id = :id");
        $put_stmt->bindParam(":nombre", utf8_decode($data->nombre), PDO::PARAM_STR);
        $put_stmt->bindParam(":correo", utf8_decode($data->correo), PDO::PARAM_STR);
        $put_stmt->bindParam(":usuario", utf8_decode($data->usuario), PDO::PARAM_STR);
        if (isset($data->contrasena)) {
            $put_stmt->bindParam(":contrasena", hash('sha256', $data->contrasena), PDO::PARAM_STR);
        }
        $put_stmt->bindParam(":activo", $data->activo, PDO::PARAM_BOOL);
        $put_stmt->bindParam(":rol", $data->rol, PDO::PARAM_STR);
        $put_stmt->bindParam(":id", $data->id, PDO::PARAM_INT);
        $put_stmt->execute();
        $put_stmt = null;
        $conn = null;
        return "Se modifico correctamente";
    }

    public function validateData($data) {
        if (!isset($data->nombre)) {
            throw new ValidationException("Debe ingresar el nombre");
        } else if (strlen($data->nombre) > 150) {
            throw new ValidationException("El nombre no debe superar los 150 caracteres");
        }
        if (!isset($data->correo)) {
            throw new ValidationException("Debe ingresar el correo");
        } else if (strlen($data->correo) > 30) {
            throw new ValidationException("El correo no debe superar los 30 caracteres");
        }
        if (!isset($data->usuario)) {
            throw new ValidationException("Debe ingresar el nombre de usuario");
        } else if (strlen($data->usuario) > 15) {
            throw new ValidationException("El nombre del usuario no debe superar los 15 caracteres");
        }
        if (!isset($data->rol)) {
            $data->rol = 'CLIENTE';
        } else if ($data->rol !== 'ADMINISTRADOR' && $data->rol !== 'COLPORTOR' && $data->rol !== 'CLIENTE') {
            throw new ValidationException("El rol seleccionado no se encuentra");
        }
        if (!isset($data->activo)) {
            $data->activo = false;
        }
    }

    public function autenticar($data) {
        $this->validateLoginData($data);
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.rol AS rol, "
                . "a.usuario AS usuario "
                . "FROM cuenta a "
                . "WHERE 1=1 "
                . "AND activo = 1 "
                . "AND usuario = :usuario "
                . "AND contrasena = :contrasena";
        $get_stmt = $conn->prepare($sql);
        $get_stmt->bindParam(":usuario", $data->usuario, PDO::PARAM_STR);
        $get_stmt->bindParam(":contrasena", hash('sha256', $data->contrasena), PDO::PARAM_STR);
        $get_stmt->execute();
        $session = null;
        $token = null;
        if ($get_stmt->rowCount() > 0) {
            if ($row = $get_stmt->fetchObject()) {
                $session = (object) array();
                $session->id = $row->id;
                $session->nombre = utf8_encode($row->nombre);
                $session->usuario = utf8_encode($row->usuario);
                $session->rol = utf8_encode($row->rol);
                $token = hash("sha512", md5($row->id . Time()));
            }
        }
        $get_stmt = null;
        $conn = null;

        if (isset($session)) {
            $this->actualizarToken($session, $token);
            setcookie("AZS_SESSION", $token, strtotime('+1 day'));

            session_start();
            $_SESSION["ID"] = $session;
            $_SESSION["TOKEN"] = $token;
        } else {
            throw new AppException(AppCode::CREDENTIALS_ERROR);
        }

        return $session;
    }

    public function actualizarCodigo($codigo, $id) {
        $conn = $this->dbcon->dbConnection();
        $patch_stmt = $conn->prepare("UPDATE cuenta SET "
                . "codigo_temporal = :codigoTemporal "
                . "WHERE 1=1 AND id = :id");
        $patch_stmt->bindParam(":codigoTemporal", $codigo, PDO::PARAM_STR);
        $patch_stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $patch_stmt->execute();
        $patch_stmt = null;
        $conn = null;
    }
    
    public function actualizarContrasena($data) {
        $conn = $this->dbcon->dbConnection();
        $patch_stmt = $conn->prepare("UPDATE cuenta SET "
                . "contrasena = :contrasena, "
                . "codigo_temporal = null "
                . "WHERE 1=1 AND codigo_temporal = :codigoTemporal");
        $patch_stmt->bindParam(":contrasena", hash('sha256', $data->contrasena), PDO::PARAM_STR);
        $patch_stmt->bindParam(":codigoTemporal", $data->codigo, PDO::PARAM_STR);
        $patch_stmt->execute();
        $patch_stmt = null;
        $conn = null;
    }
    
    public function actualizarToken($d, $t) {
        $conn = $this->dbcon->dbConnection();
        $patch_stmt = $conn->prepare("UPDATE cuenta SET "
                . "token = :token "
                . "WHERE 1=1 AND id = :id");
        $patch_stmt->bindParam(":token", $t, PDO::PARAM_STR);
        $patch_stmt->bindParam(":id", $d->id, PDO::PARAM_INT);
        $patch_stmt->execute();
        $patch_stmt = null;
        $conn = null;
    }

    public function validateLoginData($data) {
        if (!isset($data->usuario)) {
            throw new ValidationException("Debe ingresar el nombre del usuario");
        }
        if (!isset($data->contrasena)) {
            throw new ValidationException("Debe ingresar la contraseña");
        }
    }

    public function validarToken($roles) {
        session_start();
        if (!isset($_COOKIE) || !isset($_COOKIE['AZS_SESSION']) || !isset($_COOKIE['session']) || !isset($_SESSION) || !isset($_SESSION['ID']) || !isset($_SESSION['TOKEN']) || $_COOKIE['AZS_SESSION'] !== $_SESSION['TOKEN']) {
            $this->quitarCookie('AZS_SESSION');
            $this->quitarCookie('session', '/iadpa');
            unset($_SESSION['ID']);
            unset($_SESSION['TOKEN']);
            throw new AppException(AppCode::CREDENTIALS_ERROR);
        }
        $textRoles = implode(",", $roles);
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id "
                . "FROM cuenta a "
                . "WHERE 1=1 "
                . "AND rol in ($textRoles) "
                . "AND token = :token";
        $get_stmt = $conn->prepare($sql);
        $get_stmt->bindParam(":token", $_SESSION['TOKEN'], PDO::PARAM_STR);
        $get_stmt->execute();
        $isValido = false;
        if ($get_stmt->rowCount() > 0) {
            if ($row = $get_stmt->fetchObject()) {
                $isValido = ($row->id === $_SESSION['ID']->id);
            }
        }
        $get_stmt = null;
        $conn = null;

        if (!$isValido) {
            $this->quitarCookie('AZS_SESSION');
            $this->quitarCookie('session', '/iadpa');
            unset($_SESSION['ID']);
            unset($_SESSION['TOKEN']);
            throw new AppException(AppCode::CREDENTIALS_ERROR);
        }
    }

    public function quitarCookie($name, $path = "/") {
        unset($_COOKIE[$name]);
        setcookie($name, null, -1, $path);
    }

}

?>