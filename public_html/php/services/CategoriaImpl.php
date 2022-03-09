<?php

/*
 * CategoriaImpl
 */

class CategoriaImpl {

    private $dbcon;

    function __construct() {
        $this->dbcon = new Database();
    }

    public function categoriaLogic($metodo, $variable, $parametro, $datos) {
        $response = '';
        switch ($metodo) {
            case METODO_GET:
                if (isset($variable)) {
                    $response = $this->getCategoria($variable);
                } else {
                    $response = $this->getCategorias($parametro);
                }
                break;
            case METODO_POST:
                $response = $this->postCategoria($datos);
                break;
            case METODO_PUT:
                $datos->id = $variable;
                $response = $this->putCategoria($datos);
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
        return $response;
    }

    public function getCategoria(int $id) {
        $conn = $this->dbcon->dbConnection();
        $categoria = array();
        $get_stmt = $conn->prepare("SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.descripcion AS descripcion, "
                . "a.activo AS activo "
                . "FROM categoria a "
                . "WHERE 1=1 AND id = :id");
        $get_stmt->bindParam(":id", $id);
        $get_stmt->execute();
        if ($get_stmt->rowCount() > 0) {
            if ($row = $get_stmt->fetchObject()) {
                $categoria = new Categoria();
                $categoria->id = $row->id;
                $categoria->nombre = utf8_encode($row->nombre);
                $categoria->descripcion = utf8_encode($row->descripcion);
                $categoria->activo = ($row->activo === "1");
            }
        }
        $get_stmt = null;
        $conn = null;
        return $categoria;
    }

    public function getCategorias(array $filters = null) {
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.descripcion AS descripcion, "
                . "a.activo AS activo "
                . "FROM categoria a "
                . "WHERE 1=1";
        if (isset($filters)) {
            if (isset($filters['nombre'])) {
                $sql .= " AND nombre = :nombre";
            }
            if (isset($filters['excluyeId'])) {
                $sql .= " AND id <> :excluyeId";
            }
        }
        $get_stmt = $conn->prepare($sql);
        if (isset($filters)) {
            if (isset($filters['nombre'])) {
                $get_stmt->bindParam(":nombre", utf8_decode($filters['nombre']), PDO::PARAM_STR);
            }
            if (isset($filters['excluyeId'])) {
                $get_stmt->bindParam(":excluyeId", $filters['excluyeId'], PDO::PARAM_INT);
            }
        }
        $get_stmt->execute();
        $arreglo = array();
        if ($get_stmt->rowCount() > 0) {
            while ($row = $get_stmt->fetchObject()) {
                $categoria = new Categoria();
                $categoria->id = $row->id;
                $categoria->nombre = utf8_encode($row->nombre);
                $categoria->descripcion = utf8_encode($row->descripcion);
                $categoria->activo = ($row->activo === "1");
                $arreglo[] = $categoria;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $arreglo;
    }

    public function postCategoria($data) {
        $this->validateData($data);
        $categorias = $this->getCategorias(array('nombre' => $data->nombre));
        if (sizeof($categorias) > 0) {
            throw new ValidationException("Ya existe el nombre de la categoria");
        }
        $conn = $this->dbcon->dbConnection();
        $post_stmt = $conn->prepare("INSERT INTO categoria ("
                . "nombre, "
                . "descripcion, "
                . "activo"
                . ") VALUES ("
                . ":nombre, "
                . ":descripcion, "
                . ":activo)");
        $post_stmt->bindParam(":nombre", utf8_decode($data->nombre), PDO::PARAM_STR);
        $post_stmt->bindParam(":descripcion", utf8_decode($data->descripcion), PDO::PARAM_STR);
        $post_stmt->bindParam(":activo", $data->activo, PDO::PARAM_BOOL);
        $post_stmt->execute();
        $post_stmt = null;
        $conn = null;
        return "Se creo correctamente";
    }

    public function putCategoria($data) {
        $this->validateData($data);
        $categorias = $this->getCategorias(array('nombre' => $data->nombre, 'excluyeId' => $data->id));
        if (sizeof($categorias) > 0) {
            throw new ValidationException("Ya existe el nombre de la categoria");
        }
        $conn = $this->dbcon->dbConnection();
        $put_stmt = $conn->prepare("UPDATE categoria SET "
                . "nombre = :nombre, "
                . "descripcion = :descripcion, "
                . "activo = :activo "
                . "WHERE 1=1 AND id = :id");
        $put_stmt->bindParam(":nombre", utf8_decode($data->nombre), PDO::PARAM_STR);
        $put_stmt->bindParam(":descripcion", utf8_decode($data->descripcion), PDO::PARAM_STR);
        $put_stmt->bindParam(":activo", $data->activo, PDO::PARAM_BOOL);
        $put_stmt->bindParam(":id", $data->id, PDO::PARAM_INT);
        $put_stmt->execute();
        $put_stmt = null;
        $conn = null;
        return "Se modifico correctamente";
    }

    public function validateData($data) {
        if (!isset($data->nombre)) {
            throw new ValidationException("Debe ingresar el nombre de la categoria");
        } else if (strlen($data->nombre) > 150) {
            throw new ValidationException("El nombre de la categoria no debe superar los 150 caracteres");
        }
        if (!isset($data->activo)) {
            $data->activo = false;
        }
    }

}

?>