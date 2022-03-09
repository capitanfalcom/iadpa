<?php

/*
 * DisponibleImpl
 */

class DisponibleImpl {

    private $dbcon;

    function __construct() {
        $this->dbcon = new Database();
    }

    public function productoLogic($metodo, $variable, $parametro) {
        $response = '';
        switch ($metodo) {
            case METODO_GET:
                if (isset($variable)) {
                    $response = $this->getVistaLibro($variable);
                } else {
                    $response = array(
                        'total' => $this->getCountVistaLibros($parametro),
                        'productos' => $this->getVistaLibros($parametro)
                    );
                }
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
        return $response;
    }

    public function categoriaProductoLogic($metodo, $variable) {
        $response = '';
        switch ($metodo) {
            case METODO_GET:
                if (!isset($variable)) {
                    $response = $this->getVistaLibrosCategoria();
                }
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
        return $response;
    }

    public function correoLogic($metodo, $variable, $parametro, $datos) {
        $response = '';
        switch ($metodo) {
            case METODO_POST:
                $response = $this->postCorreo($datos);
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
        return $response;
    }

    public function compraLogic($metodo, $variable, $parametro, $datos) {
        $response = '';
        switch ($metodo) {
            case METODO_POST:
                $response = $this->pasosCompra($datos);
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
        return $response;
    }

    public function flipbookLogic($metodo, $variable, $parametro) {
        $response = '';
        switch ($metodo) {
            case METODO_GET:
                if (!isset($variable)) {
                    $response = $this->getFlipbook($parametro);
                }
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
        return $response;
    }

    public function getCountVistaLibros(array $filters = null) {
        $this->validateFilter($filters);
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "COUNT(a.id) AS total "
                . "FROM view_libro a "
                . "WHERE 1=1 ";
        if (isset($filters)) {
            if (isset($filters['ids'])) {
                $ids = $filters['ids'];
                $sql .= "AND a.id_categoria IN ($ids) ";
            }
        }
        $get_stmt = $conn->prepare($sql);
        $get_stmt->execute();
        $count = 0;
        if ($get_stmt->rowCount() > 0) {
            if ($row = $get_stmt->fetchObject()) {
                $count = $row->total;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $count;
    }

    public function getVistaLibros(array $filters = null) {
        $this->validateFilter($filters);
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.archivo AS archivo, "
                . "a.precio AS precio, "
                . "a.descuento AS descuento, "
                . "a.total AS total, "
                . "a.cantidad AS cantidad, "
                . "a.id_categoria AS idCategoria, "
                . "a.nombre_categoria AS nombreCategoria "
                . "FROM view_libro a "
                . "WHERE 1=1 ";
        if (isset($filters)) {
            if (isset($filters['ids'])) {
                $ids = $filters['ids'];
                $sql .= "AND a.id_categoria IN ($ids) ";
            }
            if (isset($filters['sort'])) {
                switch ((int) $filters['sort']) {
                    case 1:
                        $sql .= "ORDER BY a.cantidad DESC ";
                        break;
                    case 2:
                        $sql .= "ORDER BY a.precio ASC ";
                        break;
                    case 3:
                        $sql .= "ORDER BY a.precio DESC ";
                        break;
                    default:
                        $sql .= "ORDER BY a.descuento DESC, a.cantidad DESC ";
                        break;
                }
            }
            if (isset($filters['cantidad'])) {
                $sql .= "LIMIT :cantidad ";
                if (isset($filters['pagina'])) {
                    $sql .= "OFFSET :skip ";
                }
            }
        }
        $get_stmt = $conn->prepare($sql);
        if (isset($filters)) {
            if (isset($filters['cantidad'])) {
                $get_stmt->bindParam(":cantidad", $filters['cantidad'], PDO::PARAM_INT);
                if (isset($filters['pagina'])) {
                    $skip = ($filters['pagina'] - 1) * $filters['cantidad'];
                    $get_stmt->bindParam(":skip", $skip, PDO::PARAM_INT);
                }
            }
        }
        $get_stmt->execute();
        $arreglo = array();
        if ($get_stmt->rowCount() > 0) {
            while ($row = $get_stmt->fetchObject()) {
                $libro = new VistaLibro();
                $libro->id = $row->id;
                $libro->nombre = utf8_encode($row->nombre);
                $libro->archivo = $row->archivo;
                $libro->precio = $row->precio;
                $libro->descuento = $row->descuento;
                $libro->total = $row->total;
                $libro->cantidad = $row->cantidad;
                $libro->idCategoria = $row->idCategoria;
                $libro->nombreCategoria = utf8_encode($row->nombreCategoria);
                $arreglo[] = $libro;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $arreglo;
    }

    public function getVistaLibrosCategoria() {
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.cantidad AS cantidad "
                . "FROM view_libro_categoria a "
                . "WHERE 1=1 "
                . "ORDER BY nombre";
        $get_stmt = $conn->prepare($sql);
        $get_stmt->execute();
        $arreglo = array();
        if ($get_stmt->rowCount() > 0) {
            while ($row = $get_stmt->fetchObject()) {
                $libro = new VistaLibroCategoria();
                $libro->id = $row->id;
                $libro->nombre = utf8_encode($row->nombre);
                $libro->cantidad = $row->cantidad;
                $arreglo[] = $libro;
            }
        }
        $get_stmt = null;
        $conn = null;
        return $arreglo;
    }

    public function getVistaLibro(int $id) {
        $conn = $this->dbcon->dbConnection();
        $sql = "SELECT "
                . "a.id AS id, "
                . "a.nombre AS nombre, "
                . "a.archivo AS archivo, "
                . "a.precio AS precio, "
                . "a.descuento AS descuento, "
                . "a.total AS total, "
                . "a.cantidad AS cantidad, "
                . "a.id_categoria AS idCategoria, "
                . "a.nombre_categoria AS nombreCategoria "
                . "FROM view_libro a "
                . "WHERE 1=1 AND id = :id";
        $get_stmt = $conn->prepare($sql);
        $get_stmt->bindParam(":id", $id);
        $get_stmt->execute();
        $libro = array();
        if ($get_stmt->rowCount() > 0) {
            if ($row = $get_stmt->fetchObject()) {
                $libro = new VistaLibro();
                $libro->id = $row->id;
                $libro->nombre = utf8_encode($row->nombre);
                $libro->archivo = $row->archivo;
                $libro->precio = $row->precio;
                $libro->descuento = $row->descuento;
                $libro->total = $row->total;
                $libro->cantidad = $row->cantidad;
                $libro->idCategoria = $row->idCategoria;
                $libro->nombreCategoria = utf8_encode($row->nombreCategoria);

                $serviceLibro = new LibroImpl();
                $libro->descripcion = $serviceLibro->getDescripcionLibro($row->id);
                $libro->caracteristicas = $serviceLibro->getCaracteristicasLibro($row->id);
            }
        }
        $get_stmt = null;
        $conn = null;
        return $libro;
    }

    public function validateFilter($filter) {
        if (isset($filter['ids'])) {
            $ids = explode(",", $filter['ids']);
            foreach ($ids as $value) {
                if (!isInteger($value)) {
                    throw new ValidationException("Los identificadores de las categorias son valores numericos");
                }
            }
        }

        if (isset($filters['sort'])) {
            if (!isInteger($filters['sort'])) {
                throw new ValidationException("El orden debe ser un valor numerico");
            }
        }
    }

    public function postCorreo($data) {
        if (isset($data->nombre)) {
            $data->nombre = strip_tags(trim($data->nombre));
        } else {
            throw new ValidationException("Debe ingresar el nombre");
        }
        if (isset($data->correo)) {
            $data->correo = filter_var(trim($data->correo), FILTER_SANITIZE_EMAIL);
        } else {
            throw new ValidationException("Debe ingresar el correo");
        }
        if (isset($data->mensaje)) {
            $data->mensaje = trim($data->mensaje);
        } else {
            throw new ValidationException("Debe ingresar el mensaje");
        }

        if (empty($data->nombre) OR empty($data->mensaje) OR ! filter_var($data->correo, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Campos invalidos");
        }

        if (!isset($data->captcha)) {
            throw new ValidationException("Debe realizar el captcha");
        } else {
            $reCaptcha = new ReCaptcha(CAPTCHA_SECRET);
            $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $data->captcha);

            if ($response == null || !$response->success) {
                throw new ValidationException("Captcha invalido");
            }
        }

        $this->sendContactoMail($data);
        return "Se envio el mensaje correctamente";
    }

    public function pasosCompra($data) {
        if (isset($data->paso) && $data->paso === 'REGISTRO') {
            $this->validatePasoRegistroCompra($data->formulario);
            $serviceCuenta = new CuentaImpl();
            $serviceCuenta->postCuenta($data->formulario);
            $this->sendRegistroMail($data->formulario);
            return "Se registro correctamente";
        }
        if (isset($data->paso) && $data->paso === 'RECUPERAR') {
            $this->validatePasoRecuperarContrasenaCompra($data->formulario);
            $serviceCuenta = new CuentaImpl();
            $cuentas = $serviceCuenta->getCuentas(array('correo' => $data->formulario->correo));
            if (sizeof($cuentas) === 1) {
                $codigo = claveAleatoria();
                $serviceCuenta->actualizarCodigo($codigo, $cuentas[0]->id);
                $this->sendRecuperarContrasenaMail($codigo, $data->formulario);
            }
            return "Se envio el codigo a tu correo";
        }
        if (isset($data->paso) && $data->paso === 'CAMBIAR') {
            $this->validateCambioContrasenaCompra($data->formulario);
            $serviceCuenta = new CuentaImpl();
            $serviceCuenta->actualizarContrasena($data->formulario);
            return "Se modifico la contraseña";
        }
        $this->validatePasosCompra($data);
        session_start();
        $retorna = null;
        switch ($data->paso) {
            case 0:
                $this->validatePasoCeroCompra($data->login);
                $serviceCuenta = new CuentaImpl();
                $usuario = $serviceCuenta->autenticar($data->login);
                $servicePedido = new PedidoImpl();
                $retorna = $servicePedido->getPedidos(array('idCuenta' => $usuario->id));
                break;
            case 1:
                $this->validatePasoUnoCompra($data->pedido);
                $retorna = $data->pedido->detalle;
                $_SESSION["CART"] = $data->pedido;
                break;
            case 2:
                $this->verificarSesion();
                $this->validatePasoDosCompra($data);
                $retorna = true;
                break;
            case 3:
                $this->verificarSesion();
                $this->validatePasoTresCompra($data);
                $servicePedido = new PedidoImpl();
                $pedido = $_SESSION["CART"];
                $pedido->transaccion = $data->order->id;
                $pedido->estado = 'ACEPTADO';
                $retorna = $servicePedido->postPedido($pedido);
                $this->sendPedidoMail($pedido);
                $this->eliminarSesion();
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }

        return $retorna;
    }

    public function validatePasosCompra($data) {
        if (!isset($data->paso) || !isInteger($data->paso)) {
            throw new ValidationException("Debe ingresar el paso");
        } else if ($data->paso === 1 && !isset($data->pedido)) {
            throw new ValidationException("Debe ingresar el pedido");
        } else if ($data->paso === 1 && !isset($data->pedido->detalle)) {
            throw new ValidationException("Debe ingresar el detalle del pedido");
        }
    }

    public function validatePasoRegistroCompra($data) {
        if (!isset($data->contrasena2)) {
            throw new ValidationException("Debe reingresar la constraseña");
        } else if (strlen($data->contrasena2) > 15) {
            throw new ValidationException("La contraseña no debe superar los 15 caracteres");
        } else if (isset($data->contrasena) && ($data->contrasena !== $data->contrasena2)) {
            throw new ValidationException("Las constraseñas deben coincidir");
        }
        $data->activo = 1;

        if (!isset($data->captcha)) {
            throw new ValidationException("Debe realizar el captcha");
        } else {
            $reCaptcha = new ReCaptcha(CAPTCHA_SECRET);
            $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $data->captcha);

            if ($response == null || !$response->success) {
                throw new ValidationException("Captcha invalido");
            }
        }
    }

    public function validatePasoRecuperarContrasenaCompra($data) {
        if (!isset($data->correo)) {
            throw new ValidationException("Debe ingresar el correo");
        } else if (strlen($data->correo) > 30) {
            throw new ValidationException("El correo no debe superar los 30 caracteres");
        }

        if (!isset($data->captcha)) {
            throw new ValidationException("Debe realizar el captcha");
        } else {
            $reCaptcha = new ReCaptcha(CAPTCHA_SECRET);
            $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $data->captcha);

            if ($response == null || !$response->success) {
                throw new ValidationException("Captcha invalido");
            }
        }
    }

    public function validateCambioContrasenaCompra($data) {
        if (!isset($data->codigo)) {
            throw new ValidationException("Debe ingresar el codigo");
        } else if (strlen($data->codigo) > 10) {
            throw new ValidationException("El codigo no debe superar los 10 caracteres");
        }

        if (!isset($data->contrasena)) {
            throw new ValidationException("Debe ingresar la nueva constraseña");
        } else if (strlen($data->contrasena) > 15) {
            throw new ValidationException("La nueva contraseña no debe superar los 15 caracteres");
        }

        if (!isset($data->contrasena2)) {
            throw new ValidationException("Debe reingresar la nueva constraseña");
        } else if (strlen($data->contrasena2) > 15) {
            throw new ValidationException("La nueva contraseña no debe superar los 15 caracteres");
        }

        if (($data->contrasena !== $data->contrasena2)) {
            throw new ValidationException("Las constraseñas deben coincidir");
        }

        if (!isset($data->captcha)) {
            throw new ValidationException("Debe realizar el captcha");
        } else {
            $reCaptcha = new ReCaptcha(CAPTCHA_SECRET);
            $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $data->captcha);

            if ($response == null || !$response->success) {
                throw new ValidationException("Captcha invalido");
            }
        }
    }

    public function validatePasoCeroCompra($data) {
        if (!isset($data->usuario) || !isset($data->contrasena)) {
            throw new ValidationException("Debe ingresar los datos del login");
        }
    }

    public function validatePasoUnoCompra($data) {
        if (isset($data->nombre)) {
            $data->nombre = strip_tags(trim($data->nombre));
        } else {
            throw new ValidationException("Debe ingresar el nombre");
        }
        if (isset($data->apellido)) {
            $data->apellido = strip_tags(trim($data->apellido));
        } else {
            throw new ValidationException("Debe ingresar el apellido");
        }
        if (isset($data->correo)) {
            $data->correo = filter_var(trim($data->correo), FILTER_SANITIZE_EMAIL);
        } else {
            throw new ValidationException("Debe ingresar el correo");
        }
        if (isset($data->telefono)) {
            $data->telefono = strip_tags(trim($data->telefono));
        } else {
            throw new ValidationException("Debe ingresar el telefono");
        }
        if (isset($data->direccion)) {
            $data->direccion = strip_tags(trim($data->direccion));
        } else {
            throw new ValidationException("Debe ingresar la dirección");
        }
        if (isset($data->ciudad)) {
            $data->ciudad = strip_tags(trim($data->ciudad));
        } else {
            throw new ValidationException("Debe ingresar la ciudad");
        }

        if (empty($data->nombre) OR empty($data->apellido) OR ! filter_var($data->correo, FILTER_VALIDATE_EMAIL) OR empty($data->telefono) OR empty($data->direccion) OR empty($data->ciudad)) {
            throw new ValidationException("Campos invalidos");
        }

        $data->total = 0;
        foreach ($data->detalle as &$detalle) {
            if (!isset($detalle->id) || !isInteger($detalle->id) || !isset($detalle->cantidad) || !isInteger($detalle->cantidad)) {
                throw new ValidationException("El detalle del pedido no contiene los datos necesarios");
            }
            $libro = $this->getVistaLibro($detalle->id);
            if (!isset($libro)) {
                throw new ValidationException("Libro seleccionado no existe");
            }
            $detalle->archivo = $libro->archivo;
            $detalle->nombre = $libro->nombre;
            $detalle->precio = $libro->total;
            $detalle->total = ($detalle->precio * $detalle->cantidad);
            $data->total += $detalle->total;
        }

        if (!isset($data->captcha)) {
            throw new ValidationException("Debe realizar el captcha");
        } else {
            $reCaptcha = new ReCaptcha(CAPTCHA_SECRET);
            $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $data->captcha);

            if ($response == null || !$response->success) {
                throw new ValidationException("Captcha invalido");
            }
        }
    }

    public function validatePasoDosCompra($data) {
        if (!isset($data->total)) {
            throw new ValidationException("Debe ingresar el monto total");
        } else {
            if ($data->total !== $_SESSION["CART"]->total) {
                throw new ValidationException("El monto total no coincide con el registrado");
            }
        }
    }

    public function validatePasoTresCompra($data) {
        if (!isset($data->order) || !isset($data->order->id) || !isset($data->order->status)) {
            throw new ValidationException("No se encuentra la orden del pedido");
        }
        if (!isset($data->order->purchase_units) || !is_array($data->order->purchase_units) || !isset($data->order->purchase_units[0]->amount) || !isset($data->order->purchase_units[0]->amount->value)) {
            throw new ValidationException("No se encuentra los detalles del pedido");
        }
        if ($data->order->status !== "COMPLETED") {
            throw new ValidationException("No se ha completado el pedido");
        }
        if ($data->order->purchase_units[0]->amount->value != $_SESSION["CART"]->total) {
            throw new ValidationException("Los montos no coinciden");
        }
    }

    public function verificarSesion() {
        if (!isset($_SESSION) || !isset($_SESSION['CART'])) {
            throw new ValidationException("Debe iniciar de nuevo el proceso pago");
        }
    }

    public function eliminarSesion() {
        unset($_SESSION['CART']);
    }

    public function sendContactoMail($data) {
        $html = file_get_contents(__DIR__ . "/../correo/contacto.html");

        $html = str_replace("#{nombre}#", $data->nombre, $html);
        $html = str_replace("#{correo}#", $data->correo, $html);
        $html = str_replace("#{mensaje}#", $data->mensaje, $html);

        $enviado = $this->sendHtmlMail($html, "Nuevo ingreso al formulario contactanos", MAIL_CONTACTO);
        if (!$enviado) {
            throw new AppException(AppCode::ENVIO_CORREO_ERROR);
        }
    }

    public function sendPedidoMail($data) {
        $content = "";
        $detallePedido = '<tr>'
                . '<td align="center" class="x_wrapword" width="220px" style="min-width:220px">'
                . '<div class="pedido-detalle-item" data-ogsc="rgb(49, 49, 49)">%s</div>'
                . '</td>'
                . '<td align="center" class="x_wrapword" width="140" style="min-width:140px">'
                . '<div class="pedido-detalle-item" data-ogsc="rgb(49, 49, 49)">%s</div>'
                . '</td>'
                . '<td align="right" class="x_wrapword" width="80" style="min-width:80px">'
                . '<div class="pedido-detalle-item" style="text-align: right;" data-ogsc="rgb(49, 49, 49)">$ %s</div>'
                . '</td>'
                . '</tr>';

        $total = 0;
        foreach ($data->detalle as $detalle) {
            $total += $detalle->total;
            $content .= sprintf($detallePedido, $detalle->nombre, $detalle->cantidad, $detalle->precio);
        }

        $htmlPedidoCliente = file_get_contents(__DIR__ . "/../correo/pedido-cliente.html");
        $htmlPedidoCliente = str_replace("#{id}#", $data->id, $htmlPedidoCliente);
        $htmlPedidoCliente = str_replace("#{orden}#", $data->transaccion, $htmlPedidoCliente);
        $htmlPedidoCliente = str_replace("#{correo}#", $data->correo, $htmlPedidoCliente);
        $htmlPedidoCliente = str_replace("#{detalle}#", $content, $htmlPedidoCliente);
        $htmlPedidoCliente = str_replace("#{total}#", $total, $htmlPedidoCliente);
        $this->sendHtmlMail($htmlPedidoCliente, "Se ingreso su pedido correctamente", $data->correo);

        $htmlPedidoAdministrador = file_get_contents(__DIR__ . "/../correo/pedido-administrador.html");
        $htmlPedidoAdministrador = str_replace("#{id}#", $data->id, $htmlPedidoAdministrador);
        $htmlPedidoAdministrador = str_replace("#{orden}#", $data->transaccion, $htmlPedidoAdministrador);
        $htmlPedidoAdministrador = str_replace("#{correo}#", $data->correo, $htmlPedidoAdministrador);
        $htmlPedidoAdministrador = str_replace("#{detalle}#", $content, $htmlPedidoAdministrador);
        $htmlPedidoAdministrador = str_replace("#{total}#", $total, $htmlPedidoAdministrador);
        $this->sendHtmlMail($htmlPedidoAdministrador, "Se realizo un nuevo pedido", MAIL_CONTACTO);
    }

    public function sendRegistroMail($data) {
        $html = file_get_contents(__DIR__ . "/../correo/registro.html");

        $enviado = $this->sendHtmlMail($html, "Registro en libreria IADPA", $data->correo);
        if (!$enviado) {
            throw new AppException(AppCode::ENVIO_CORREO_ERROR);
        }
    }

    public function sendRecuperarContrasenaMail($codigo, $data) {
        $html = file_get_contents(__DIR__ . "/../correo/cambio-contrasena.html");

        $html = str_replace("#{url}#", URL_HOME . "/#/cambio-contrasena?codigo=" . $codigo, $html);

        $enviado = $this->sendHtmlMail($html, "Solicitud cambio contraseña", $data->correo);
        if (!$enviado) {
            throw new AppException(AppCode::ENVIO_CORREO_ERROR);
        }
    }

    public function sendHtmlMail($html, $subject, $correos) {
        $mail = new phpmailer();

        $mail->IsSMTP();
        $mail->Host = MAIL_HOST;
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USUARIO;
        $mail->Password = MAIL_CONTRASENA;
        $mail->From = MAIL_USUARIO;
        $mail->FromName = "Libreria IADPA Panama";
        $correos_arr = explode('; ', $correos);
        foreach ($correos_arr as $correo) {
            $mail->AddAddress($correo);
        }
        $mail->WordWrap = 75;
        $mail->CharSet = "utf-8";

        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;

        return $mail->Send();
    }

    public function getFlipbook($parametro) {
        if (isset($parametro) && isset($parametro['libro'])) {
            return listarArchivos(DIR_FLIPBOOK . "/" . $parametro['libro']);
        } else {
            return listarCarpetas(DIR_FLIPBOOK);
        }
    }

}

?>