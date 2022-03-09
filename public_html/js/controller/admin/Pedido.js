/* global app */

app.controller('pedidoController', function ($scope, $log, loading, pedidoService, swal, CODE, Table) {
    $scope.arreglo = angular.copy(Table);
    $scope.pedido = {};
    $scope.formPedido = {};
    $scope.defaultPedido = {
        id: null,
        fecha: null,
        nombre: null,
        apellido: null,
        correo: null,
        telefono: null,
        direccion: null,
        ciudad: null,
        provincia: null,
        codigo: null,
        comentario: null,
        transaccion: null,
        detalle: null,
        estado: null
    };
    const CONTROL = "Pedido";
    $scope.agregarLog = function (_accion) {
        $log.info("Controlador:", CONTROL + ',', "accion", _accion);
    };
    $scope.iniciar = function () {
        $scope.agregarLog('iniciar');
        $scope.opcion = CODE.REVISAR;
        $scope.formPedido = angular.copy($scope.defaultPedido);
        $scope.actualizar();
    };
    $scope.actualizar = function () {
        $scope.agregarLog('actualizar');
        loading.start();
        pedidoService.toList(null).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                $scope.arreglo.init(response.data);
                if ($scope.arreglo.data.length > 0) {
                    $scope.arreglo.activeSorting();
                }
            } else {
                swal.error(response.description);
            }
        });
    };
    $scope.seleccionar = function (_id) {
        $scope.agregarLog('seleccionar');
        loading.start();
        pedidoService.obtainTo(_id).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                $scope.pedido = response.data;
                $scope.convetirPedido($scope.pedido);
            } else {
                swal.error(response.description);
            }
        });
    };
    $scope.revisar = function (_id) {
        $scope.agregarLog('revisar');
        $scope.bloquear = true;
        $scope.seleccionar(_id);
        $scope.opcion = CODE.REVISAR;
    };
    $scope.convetirPedido = function (_data) {
        $scope.agregarLog('convetirPedido');
        $scope.formPedido = {
            id: _data.id,
            fecha: _data.fecha,
            nombre: _data.nombre,
            apellido: _data.apellido,
            correo: _data.nombre,
            telefono: _data.telefono,
            direccion: _data.direccion,
            ciudad: _data.ciudad,
            provincia: _data.provincia,
            codigo: _data.codigo,
            comentario: _data.comentario,
            transaccion: _data.transaccion,
            detalle: _data.detalle,
            estado: _data.estado
        };
    };
    $scope.iniciar();
});