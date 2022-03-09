<?php

/*
 * LibroImpl
 */

class LibroImpl {

    private $dbcon;

    function __construct() {
        $this->dbcon = new Database();
    }

    public function libroLogic($metodo, $variable, $parametro, $datos) {
        $response = '';
        switch ($metodo) {
            case METODO_GET:
                if (isset($variable)) {
                    $response = $this->getLibro($variable);
                } else {
                    $response = $this->getLibros($parametro);
                }
                break;
            case METODO_POST:
                $response = $this->postLibro($datos);
                break;
            case METODO_PUT:
                $datos->id = $variable;
                $response = $this->putLibro($datos);
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
        return $response;
    }

    public function getLibro(int $id) {
        $conn = $this->dbcon->dbConnection();
        $libro = array();
        $get_stmt = $conn->prepare("SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.precio AS precio, "
                . "a.descripcion AS descripcion, "
                . "a.archivo AS archivo, "
                . "a.activo AS activo, "
                . "a.id_categoria AS idCategoria, "
                . "a.id_cuenta AS idCuenta, "
                . "a.descuento AS descuento "
                . "FROM libro a "
                . "WHERE 1=1 AND id = :id");
        $get_stmt->bindParam(":id", $id);
        $get_stmt->execute();
        if ($get_stmt->rowCount() > 0) {
            if ($row = $get_stmt->fetchObject()) {
                $libro = new Libro();
                $libro->id = $row->id;
                $libro->nombre = utf8_encode($row->nombre);
                $libro->precio = $row->precio;
                $libro->descripcion = utf8_encode($row->descripcion);
                $libro->archivo = $row->archivo;
                $libro->archivoBase64 = $this->openFileCoverPage($row->archivo);
                $libro->activo = ($row->activo === "1");
                $libro->categoria = array('id' => $row->idCategoria);
                $libro->cuenta = array('id' => $row->idCuenta);
                $libro->descuento = $row->descuento;
                $libro->caracteristicas = $this->getCaracteristicasLibro($row->id);
            }
        }
        $get_stmt = null;
        $conn = null;
        return $libro;
    }

    public function getDescripcionLibro(int $id) {
        $conn = $this->dbcon->dbConnection();
        $texto = '';
        $get_stmt = $conn->prepare("SELECT "
                . "a.descripcion AS descripcion "
                . "FROM libro a "
                . "WHERE 1=1 AND id = :id");
        $get_stmt->bindParam(":id", $id);
        $get_stmt->execute();
        if ($get_stmt->rowCount() > 0) {
            if ($row = $get_stmt->fetchObject()) {
                $texto = utf8_encode($row->descripcion);
            }
        }
        $get_stmt = null;
        $conn = null;
        return $texto;
    }

    public function getCaracteristicasLibro(int $id) {
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre "
                . "FROM caracteristica a "
                . "WHERE 1=1 AND id_libro = :id";
        $get_stmt = $conn->prepare($sql);
        $get_stmt->bindParam(":id", $id);
        $get_stmt->execute();
        $arreglo = array();
        if ($get_stmt->rowCount() > 0) {
            while ($row = $get_stmt->fetchObject()) {
                $caracteristica = new Caracteristica();
                $caracteristica->id = $row->id;
                $caracteristica->nombre = utf8_encode($row->nombre);
                $arreglo[] = $caracteristica;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $arreglo;
    }

    public function getLibros(array $filters = null) {
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.precio AS precio, "
                . "a.descripcion AS descripcion, "
                . "a.archivo AS archivo, "
                . "a.activo AS activo, "
                . "a.id_categoria AS idCategoria, "
                . "a.id_cuenta AS idCuenta, "
                . "a.descuento AS descuento "
                . "FROM libro a "
                . "WHERE 1=1";
        if (isset($filters)) {
            if (isset($filters['activo'])) {
                $sql .= " AND activo = :activo";
            }
        }
        $get_stmt = $conn->prepare($sql);
        if (isset($filters)) {
            if (isset($filters['activo'])) {
                $get_stmt->bindParam(":activo", $filters['activo'], PDO::PARAM_BOOL);
            }
        }
        $get_stmt->execute();
        $arreglo = array();
        if ($get_stmt->rowCount() > 0) {
            while ($row = $get_stmt->fetchObject()) {
                $libro = new Libro();
                $libro->id = $row->id;
                $libro->nombre = utf8_encode($row->nombre);
                $libro->precio = $row->precio;
                $libro->descripcion = utf8_encode($row->descripcion);
                $libro->archivo = $row->archivo;
                $libro->activo = ($row->activo === "1");
                $libro->categoria = array('id' => $row->idCategoria);
                $libro->cuenta = array('id' => $row->idCuenta);
                $libro->descuento = $row->descuento;
                $arreglo[] = $libro;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $arreglo;
    }

    public function postLibro($data) {
        $this->validateData($data);
        $conn = $this->dbcon->dbConnection();
        try {
            $post_stmt = $conn->prepare("INSERT INTO libro ("
                    . "nombre, "
                    . "precio, "
                    . "descripcion, "
                    . "activo, "
                    . "id_categoria, "
                    . "id_cuenta, "
                    . "descuento"
                    . ") VALUES ("
                    . ":nombre, "
                    . ":precio, "
                    . ":descripcion, "
                    . ":activo, "
                    . ":idCategoria, "
                    . ":idCuenta, "
                    . ":descuento)");
            $conn->beginTransaction();
            $id = "1";
            $post_stmt->bindParam(":nombre", utf8_decode($data->nombre), PDO::PARAM_STR);
            $post_stmt->bindParam(":precio", $data->precio, PDO::PARAM_STR);
            $post_stmt->bindParam(":descripcion", utf8_decode($data->descripcion), PDO::PARAM_STR);
            $post_stmt->bindParam(":activo", $data->activo, PDO::PARAM_BOOL);
            $post_stmt->bindParam(":idCategoria", $data->categoria->id, PDO::PARAM_INT);
            $post_stmt->bindParam(":idCuenta", $id, PDO::PARAM_INT);
            $post_stmt->bindParam(":descuento", $data->descuento, PDO::PARAM_INT);
            $post_stmt->execute();
            $data->id = $conn->lastInsertId();
            $post_stmt = null;

            $this->postListCaracteristicas($conn, $data);
            $this->uploadFileCoverPage($conn, $data);

            $conn->commit();
        } catch (mysqli_sql_exception $ex) {
            $conn->rollBack();
            throw new AppException(AppCode::DATABASE_ERROR);
        }
        $conn = null;
        return "Se creo correctamente";
    }

    public function putLibro($data) {
        $this->validateData($data);
        $conn = $this->dbcon->dbConnection();
        try {
            $put_stmt = $conn->prepare("UPDATE libro SET "
                    . "nombre = :nombre, "
                    . "precio = :precio, "
                    . "descripcion = :descripcion, "
                    . "activo = :activo, "
                    . "id_categoria = :idCategoria, "
                    . "descuento = :descuento "
                    . "WHERE 1=1 AND id = :id");
            $conn->beginTransaction();
            $put_stmt->bindParam(":nombre", utf8_decode($data->nombre), PDO::PARAM_STR);
            $put_stmt->bindParam(":precio", $data->precio, PDO::PARAM_STR);
            $put_stmt->bindParam(":descripcion", utf8_decode($data->descripcion), PDO::PARAM_STR);
            $put_stmt->bindParam(":activo", $data->activo, PDO::PARAM_BOOL);
            $put_stmt->bindParam(":idCategoria", $data->categoria->id, PDO::PARAM_INT);
            $put_stmt->bindParam(":descuento", $data->descuento, PDO::PARAM_INT);
            $put_stmt->bindParam(":id", $data->id, PDO::PARAM_INT);
            $put_stmt->execute();
            $put_stmt = null;

            $this->postListCaracteristicas($conn, $data);
            if (isset($data->archivoBase64)) {
                $this->uploadFileCoverPage($conn, $data);
            }

            $conn->commit();
        } catch (mysqli_sql_exception $ex) {
            $conn->rollBack();
            throw new AppException(AppCode::DATABASE_ERROR);
        }
        $conn = null;
        return "Se modifico correctamente";
    }

    public function uploadFileCoverPage($conn, $data) {
        $size1 = getImageSizeFromString(base64_decode($data->archivoBase64));
        if ($size1[0] !== 400 || $size1[1] !== 400) {
            $conn->rollBack();
            throw new ValidationException("Las dimesiones de la imagen deben ser 300x450");
        }

        $size2 = getFileSizeInKb($data->archivoBase64);
        if ($size2 > 1024) {
            $conn->rollBack();
            throw new ValidationException("El peso de la imagen no debe ser mayor a 1 MB");
        }

        $f = finfo_open();
        $mime_type = finfo_buffer($f, base64_decode($data->archivoBase64), FILEINFO_MIME_TYPE);

        $formato = '';
        if ($mime_type === "image/png") {
            $formato = 'png';
        } else {
            $conn->rollBack();
            throw new ValidationException("La imagen debe ser png");
        }
        $data->archivo = sprintf('%s.%s', $data->id, $formato);
        $directory = sprintf('%s/%s', PRODUCT_IMG, $data->archivo);

        $status = file_put_contents($directory, base64_decode($data->archivoBase64));
        if (!$status) {
            $conn->rollBack();
            throw new Exception('Error al subir archivo.');
        }

        $patch_stmt = $conn->prepare("UPDATE libro SET "
                . "archivo = :archivo "
                . "WHERE 1=1 AND id = :id");
        $patch_stmt->bindParam(":archivo", $data->archivo, PDO::PARAM_STR);
        $patch_stmt->bindParam(":id", $data->id, PDO::PARAM_INT);
        $patch_stmt->execute();
        $patch_stmt = null;
    }

    public function postListCaracteristicas($conn, $data) {
        $delete_stmt = $conn->prepare("DELETE FROM caracteristica WHERE id_libro = :idLibro");
        $delete_stmt->bindParam(":idLibro", $data->id, PDO::PARAM_INT);
        $delete_stmt->execute();
        $delete_stmt = null;

        foreach ($data->caracteristicas as $value) {
            $post_stmt = $conn->prepare("INSERT INTO caracteristica ("
                    . "nombre, "
                    . "id_libro"
                    . ") VALUES ("
                    . ":nombre, "
                    . ":idLibro)");
            $post_stmt->bindParam(":nombre", utf8_decode($value->nombre), PDO::PARAM_STR);
            $post_stmt->bindParam(":idLibro", $data->id, PDO::PARAM_INT);
            $post_stmt->execute();
        }
        $post_stmt = null;
    }

    public function openFileCoverPage($name) {
        $directory = sprintf('%s/%s', PRODUCT_IMG, $name);
        $img = file_get_contents($directory);
        return base64_encode($img);
    }

    public function validateData($data) {
        if (!isset($data->nombre)) {
            throw new ValidationException("Debe ingresar el nombre del libro");
        } else if (strlen($data->nombre) > 250) {
            throw new ValidationException("El nombre del libro no debe superar los 250 caracteres");
        }
        if (!isset($data->precio)) {
            throw new ValidationException("Debe ingresar el precio del libro");
        }
        if (!isset($data->categoria) || !isset($data->categoria->id)) {
            throw new ValidationException("Debe seleccionar la categoria del libro");
        } else {
            $serviceCategoria = new CategoriaImpl();
            $categoria = $serviceCategoria->getCategoria($data->categoria->id);
            if (!isset($categoria)) {
                throw new ValidationException("Categoria seleccionada no existe");
            }
        }
        if (!isset($data->archivoBase64)) {
            throw new ValidationException("Debe adjuntar la portada del libro");
        }
        if (!isset($data->activo)) {
            $data->activo = false;
        }
        if (isset($data->precio) && isset($data->descuento) && ($data->precio - $data->descuento < 0)) {
            throw new ValidationException("El descuento es mayor al precio");
        }
        if (isset($data->caracteristicas)) {
            foreach ($data->caracteristicas as $value) {
                if (!isset($value->nombre) || $value->nombre === '') {
                    throw new ValidationException("Debe ingresar la caracteristica del libro");
                }
            }
        }
    }

}

?>