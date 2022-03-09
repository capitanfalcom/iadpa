<?php

/*
 * PedidoImpl
 */

class PedidoImpl {

    private $dbcon;

    function __construct() {
        $this->dbcon = new Database();
    }

    public function pedidoLogic($metodo, $variable, $parametro, $datos) {
        $response = '';
        switch ($metodo) {
            case METODO_GET:
                if (isset($variable)) {
                    $response = $this->getPedido($variable);
                } else {
                    $response = $this->getPedidos($parametro);
                }
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
        return $response;
    }

    public function getPedido(int $id) {
        $conn = $this->dbcon->dbConnection();
        $pedido = array();
        $get_stmt = $conn->prepare("SELECT "
                . "a.id AS id, "
                . "a.fecha AS fecha, "
                . "a.nombre AS nombre, "
                . "a.apellido AS apellido, "
                . "a.correo AS correo, "
                . "a.telefono AS telefono, "
                . "a.direccion AS direccion, "
                . "a.ciudad AS ciudad, "
                . "a.provincia AS provincia, "
                . "a.codigo AS codigo, "
                . "a.comentario AS comentario, "
                . "a.transaccion AS transaccion, "
                . "a.estado AS estado "
                . "FROM pedido a WHERE 1=1 AND id = :id");
        $get_stmt->bindParam(":id", $id);
        $get_stmt->execute();
        if ($get_stmt->rowCount() > 0) {
            if ($row = $get_stmt->fetchObject()) {
                $pedido = new Pedido();
                $pedido->id = $row->id;
                $pedido->fecha = $row->fecha;
                $pedido->nombre = utf8_encode($row->nombre);
                $pedido->apellido = utf8_encode($row->apellido);
                $pedido->correo = utf8_encode($row->correo);
                $pedido->telefono = utf8_encode($row->telefono);
                $pedido->direccion = utf8_encode($row->direccion);
                $pedido->ciudad = utf8_encode($row->ciudad);
                $pedido->provincia = utf8_encode($row->provincia);
                $pedido->codigo = utf8_encode($row->codigo);
                $pedido->comentario = utf8_encode($row->comentario);
                $pedido->transaccion = $row->transaccion;
                $pedido->detalle = $this->getDetallePedidosById($row->id);
                $pedido->estado = $row->estado;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $pedido;
    }

    public function getPedidos(array $filters = null) {
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id, "
                . "a.fecha AS fecha, "
                . "a.nombre AS nombre, "
                . "a.apellido AS apellido, "
                . "a.correo AS correo, "
                . "a.telefono AS telefono, "
                . "a.direccion AS direccion, "
                . "a.ciudad AS ciudad, "
                . "a.provincia AS provincia, "
                . "a.codigo AS codigo, "
                . "a.comentario AS comentario, "
                . "a.transaccion AS transaccion, "
                . "a.estado AS estado "
                . "FROM pedido a "
                . "WHERE 1=1";
        if (isset($filters)) {
            if (isset($filters['idCuenta'])) {
                $sql .= " AND a.id_cuenta = :idCuenta";
            }
        }
        $get_stmt = $conn->prepare($sql);
        if (isset($filters)) {
            if (isset($filters['idCuenta'])) {
                $get_stmt->bindParam(":idCuenta", $filters['idCuenta'], PDO::PARAM_INT);
            }
        }
        $get_stmt->execute();
        $arreglo = array();
        if ($get_stmt->rowCount() > 0) {
            while ($row = $get_stmt->fetchObject()) {
                $pedido = new Pedido();
                $pedido->id = $row->id;
                $pedido->fecha = $row->fecha;
                $pedido->nombre = utf8_encode($row->nombre);
                $pedido->apellido = utf8_encode($row->apellido);
                $pedido->correo = utf8_encode($row->correo);
                $pedido->telefono = utf8_encode($row->telefono);
                $pedido->direccion = utf8_encode($row->direccion);
                $pedido->ciudad = utf8_encode($row->ciudad);
                $pedido->provincia = utf8_encode($row->provincia);
                $pedido->codigo = utf8_encode($row->codigo);
                $pedido->comentario = utf8_encode($row->comentario);
                $pedido->transaccion = $row->transaccion;
                $pedido->estado = $row->estado;
                $arreglo[] = $pedido;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $arreglo;
    }

    public function getDetallePedidosById(int $id) {
        $conn = $this->dbcon->dbConnection();
        $get_stmt = $conn->prepare("SELECT "
                . "a.id AS id, "
                . "a.id_libro AS id_libro, "
                . "a.cantidad AS cantidad, "
                . "a.precio AS precio, "
                . "a.total AS total "
                . "FROM detalle_pedido a WHERE 1=1 AND id_pedido = :id");
        $get_stmt->bindParam(":id", $id);
        $get_stmt->execute();
        $arreglo = array();
        if ($get_stmt->rowCount() > 0) {
            while ($row = $get_stmt->fetchObject()) {
                $detalle_pedido = new DetallePedido();
                $detalle_pedido->id = $row->id;
                $serviceLibro = new LibroImpl();
                $detalle_pedido->libro = $serviceLibro->getLibro($row->id_libro);
                $detalle_pedido->cantidad = $row->cantidad;
                $detalle_pedido->precio = $row->precio;
                $detalle_pedido->total = $row->total;
                $arreglo[] = $detalle_pedido;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $arreglo;
    }

    public function postPedido($data) {
        $conn = $this->dbcon->dbConnection();
        try {
            $post_stmt = $conn->prepare("INSERT INTO pedido ("
                    . "fecha, "
                    . "nombre, "
                    . "apellido, "
                    . "correo, "
                    . "telefono, "
                    . "direccion, "
                    . "ciudad, "
                    . "provincia, "
                    . "codigo, "
                    . "comentario, "
                    . "transaccion, "
                    . "estado"
                    . ") VALUES ("
                    . "now(), "
                    . ":nombre, "
                    . ":apellido, "
                    . ":correo, "
                    . ":telefono, "
                    . ":direccion, "
                    . ":ciudad, "
                    . ":provincia, "
                    . ":codigo, "
                    . ":comentario, "
                    . ":transaccion, "
                    . ":estado)");
            $conn->beginTransaction();
            $post_stmt->bindParam(":nombre", substr(utf8_decode($data->nombre), 0, 150), PDO::PARAM_STR);
            $post_stmt->bindParam(":apellido", substr(utf8_decode($data->apellido), 0, 150), PDO::PARAM_STR);
            $post_stmt->bindParam(":correo", substr(utf8_decode($data->correo), 0, 30), PDO::PARAM_STR);
            $post_stmt->bindParam(":telefono", substr(utf8_decode($data->telefono), 0, 15), PDO::PARAM_STR);
            $post_stmt->bindParam(":direccion", substr(utf8_decode($data->direccion), 0, 250), PDO::PARAM_STR);
            $post_stmt->bindParam(":ciudad", substr(utf8_decode($data->ciudad), 0, 30), PDO::PARAM_STR);
            $post_stmt->bindParam(":provincia", substr(utf8_decode($data->provincia), 0, 30), PDO::PARAM_STR);
            $post_stmt->bindParam(":codigo", substr(utf8_decode($data->codigo), 0, 50), PDO::PARAM_STR);
            $post_stmt->bindParam(":comentario", utf8_decode($data->comentario), PDO::PARAM_STR);
            $post_stmt->bindParam(":transaccion", $data->transaccion, PDO::PARAM_STR);
            $post_stmt->bindParam(":estado", $data->estado, PDO::PARAM_STR);
            $post_stmt->execute();
            $data->id = $conn->lastInsertId();
            $post_stmt = null;

            $this->postDetallePedido($conn, $data);

            $conn->commit();
        } catch (Exception $ex) {
            $conn->rollBack();
        }
        return $data->transaccion;
    }

    public function postDetallePedido($conn, $data) {
        foreach ($data->detalle as $value) {
            $post_stmt = $conn->prepare("INSERT INTO detalle_pedido ("
                    . "id_pedido, "
                    . "id_libro, "
                    . "cantidad, "
                    . "precio, "
                    . "total"
                    . ") VALUES ("
                    . ":idPedido, "
                    . ":idLibro, "
                    . ":cantidad, "
                    . ":precio, "
                    . ":total)");
            $post_stmt->bindParam(":idPedido", $data->id, PDO::PARAM_INT);
            $post_stmt->bindParam(":idLibro", $value->id, PDO::PARAM_INT);
            $post_stmt->bindParam(":cantidad", $value->cantidad, PDO::PARAM_INT);
            $post_stmt->bindParam(":precio", $value->precio, PDO::PARAM_STR);
            $post_stmt->bindParam(":total", $value->total, PDO::PARAM_STR);
            $post_stmt->execute();
        }
        $post_stmt = null;
    }

}

?>