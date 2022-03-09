<?php

header("Access-Control-Allow-Methods: GET, POST, PUT");
header("Content-Type: text/html;charset=utf-8");

require_once ('config.php');

require_once ('services/CategoriaImpl.php');
require_once ('services/CuentaImpl.php');
require_once ('services/LibroImpl.php');
require_once ('services/PedidoImpl.php');

require_once ('services/DisponibleImpl.php');

require_once ('models/Categoria.php');
require_once ('models/Cuenta.php');
require_once ('models/DetallePedido.php');
require_once ('models/Libro.php');
require_once ('models/Caracteristica.php');
require_once ('models/VistaLibro.php');
require_once ('models/VistaLibroCategoria.php');
require_once ('models/Pedido.php');

try {
    initApi();
} catch (TypeError $ex) {
    error_log($ex);
    $e = AppCode::COMPONENT_ERROR;
    $result = new AppResponse($e['code'], null, $e['message']);
    $result->imprimir();
} catch (AppException $ex) {
    error_log($ex);
    $result = new AppResponse($ex->getCode(), null, $ex->getMessage());
    $result->imprimir();
} catch (Exception $ex) {
    error_log($ex);
    $e = AppCode::UNKNOWN_ERROR;
    $result = new AppResponse($e['code'], null, $e['message']);
    $result->imprimir();
}

function initApi() {
    $api = new ApiRest();
    $mapping = $api->getMapeo();
    $response = '';

    if (!empty($mapping)) {
        switch ($mapping[0]) {
            case MAPPING_CATEGORIA:
                verificarSesion(array(ROL_ADMINISTRADOR));
                $service = new CategoriaImpl();
                $variable = (isset($mapping[1]) ? (int) $mapping[1] : null);
                $response = $service->categoriaLogic($api->getMetodo(), $variable, $api->getParamatro(), $api->getDatos());
                break;
            case MAPPING_CUENTA:
                verificarSesion(array(ROL_ADMINISTRADOR));
                $service = new CuentaImpl();
                $variable = (isset($mapping[1]) ? (int) $mapping[1] : null);
                $response = $service->cuentaLogic($api->getMetodo(), $variable, $api->getParamatro(), $api->getDatos());
                break;
            case MAPPING_LOGIN:
                if ($api->getMetodo() === METODO_POST) {
                    $service = new CuentaImpl();
                    $response = $service->autenticar($api->getDatos());
                } else {
                    throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
                }
                break;
            case MAPPING_LIBRO:
                verificarSesion(array(ROL_ADMINISTRADOR));
                $service = new LibroImpl();
                $variable = (isset($mapping[1]) ? (int) $mapping[1] : null);
                $response = $service->libroLogic($api->getMetodo(), $variable, $api->getParamatro(), $api->getDatos());
                break;
            case MAPPING_PRODUCTO:
                $service = new DisponibleImpl();
                $variable = (isset($mapping[1]) ? (int) $mapping[1] : null);
                $response = $service->productoLogic($api->getMetodo(), $variable, $api->getParamatro());
                break;
            case MAPPING_CATEGORIA_PRODUCTO:
                $service = new DisponibleImpl();
                $variable = (isset($mapping[1]) ? (int) $mapping[1] : null);
                $response = $service->categoriaProductoLogic($api->getMetodo(), $variable);
                break;
            case MAPPING_CORREO:
                $service = new DisponibleImpl();
                $variable = (isset($mapping[1]) ? (int) $mapping[1] : null);
                $response = $service->correoLogic($api->getMetodo(), $variable, $api->getParamatro(), $api->getDatos());
                break;
            case MAPPING_COMPRA:
                $service = new DisponibleImpl();
                $variable = (isset($mapping[1]) ? (int) $mapping[1] : null);
                $response = $service->compraLogic($api->getMetodo(), $variable, $api->getParamatro(), $api->getDatos());
                break;
            case MAPPING_PEDIDO:
                verificarSesion(array(ROL_ADMINISTRADOR));
                $service = new PedidoImpl();
                $variable = (isset($mapping[1]) ? (int) $mapping[1] : null);
                $response = $service->pedidoLogic($api->getMetodo(), $variable, $api->getParamatro(), $api->getDatos());
                break;
            case MAPPING_FLIPBOOK:
                verificarSesion(array(ROL_ADMINISTRADOR, ROL_COLPORTOR));
                $service = new DisponibleImpl();
                $variable = (isset($mapping[1]) ? (int) $mapping[1] : null);
                $response = $service->flipbookLogic($api->getMetodo(), $variable, $api->getParamatro(), $api->getDatos());
                break;
            default:
                throw new AppException(AppCode::SERVICE_NOT_AVAILABLE);
        }
    }
    $result = new AppResponse(AppCode::OK['code'], $response);
    $result->imprimir();
}

function verificarSesion($roles) {
    $service = new CuentaImpl();
    $service->validarToken($roles);
}

function isInteger($input) {
    return(ctype_digit(strval($input)));
}

function getFileSizeInKb($base64string) {
    $bytes = strlen(base64_decode($base64string));
    $roughsize = (((int) $bytes) / 1024.0);
    return round($roughsize);
}

function claveAleatoria($longitud = 10, $opcLetra = TRUE, $opcNumero = TRUE, $opcMayus = TRUE, $opcEspecial = FALSE) {
    $letras = "abcdefghijklmnopqrstuvwxyz";
    $numeros = "1234567890";
    $letrasMayus = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $especiales = "|@#~$%()=^*+[]{}-_";
    $listado = "";
    $password = "";
    if ($opcLetra == TRUE)
        $listado .= $letras;
    if ($opcNumero == TRUE)
        $listado .= $numeros;
    if ($opcMayus == TRUE)
        $listado .= $letrasMayus;
    if ($opcEspecial == TRUE)
        $listado .= $especiales;

    for ($i = 1; $i <= $longitud; $i++) {
        $caracter = $listado[rand(0, strlen($listado) - 1)];
        $password .= $caracter;
        $listado = str_shuffle($listado);
    }
    return $password;
}

function listarCarpetas($path) {
    $lista = array();
    $dir = opendir($path);
    while ($elemento = readdir($dir)) {
        if ($elemento != "." && $elemento != "..") {
            if (is_dir($path . "/" . $elemento)) {
                $lista[] = $elemento;
            }
        }
    }
    return $lista;
}

function listarArchivos($path) {
    $lista = array();
    $dir = opendir($path);
    while ($elemento = readdir($dir)) {
        if ($elemento != "." && $elemento != "..") {
            if (!is_dir($path . "/" . $elemento)) {
                list($width, $height, $type, $attr) = getimagesize($path . '/' . $elemento);
                $lista[] = array('nombre' => $elemento, 'ancho' => $width, 'alto' => $height);
            }
        }
    }
    return $lista;
}

?>