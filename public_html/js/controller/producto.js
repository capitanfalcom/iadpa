/* global app, carruselUtil */

app.controller('listadoProductoController', function ($scope, $rootScope, $log, loading, disponibleService, swal, CODE, Cart, $state, Url) {
    $scope.productos = [];
    $scope.grid = true;
    $scope.filtro = {
        pagina: 1,
        cantidad: 20,
        sort: "1"
    };
    $scope.total = 0;
    $scope.totalPagina = 0;
    $scope.maximo = 5;
    const CONTROL = "Productos";
    $scope.agregarLog = function (_accion) {
        $log.info("Controlador:", CONTROL + ',', "accion", _accion);
    };
    $scope.nombreCategoria = "";
    $scope.actualizar = function () {
        $scope.agregarLog('actualizar');
        var params = $state.params;
        if (params !== null && angular.isDefined(params.id)) {
            $scope.filtro.ids = angular.copy(params.id);
            for (var i = 0; i < $rootScope.categorias.length; i++) {
                if (parseInt(params.id) === $rootScope.categorias[i].id) {
                    $scope.nombreCategoria = $rootScope.categorias[i].nombre;
                    break;
                }
            }
        }
        loading.start();
        disponibleService.obtenerProductos($scope.filtro).then(function (response) {
            loading.finish();
            $log.info(response);
            $scope.productos = [];
            if (response.code === CODE.OK) {
                $scope.productos = response.data.productos;
                $scope.total = 0;
                $scope.total = response.data.total;

                $scope.totalPagina = Math.ceil(response.data.total / $scope.filtro.cantidad);
            } else {
                swal.error(response.description);
            }
        });
    };
    $scope.paginaCambia = function () {
        if (($scope.filtro.pagina >= 1) && ($scope.filtro.pagina <= $scope.totalPagina)) {
            $scope.actualizar();
        }
    };
    $scope.cambiarOrden = function () {
        $scope.filtro.pagina = 1;
        $scope.actualizar();
    };
    $scope.agregarCarro = function (_id) {
        Cart.addProduct(_id);
    };
    $scope.baseImage = function () {
        return Url.getFolderImage();
    };
    $scope.actualizar();
});


app.controller('productoController', function ($scope, $log, loading, disponibleService, swal, CODE, $state, Cart, $timeout, Url) {
    $scope.producto = null;
    $scope.relacionados = [];
    $scope.cantidad = 1;
    const CONTROL = "Producto";
    $scope.agregarLog = function (_accion) {
        $log.info("Controlador:", CONTROL + ',', "accion", _accion);
    };
    $scope.buscar = function () {
        $scope.agregarLog('buscar');
        var params = $state.params;
        var id = 0;
        if (params !== null && angular.isDefined(params.id)) {
            id = angular.copy(params.id);
        }
        loading.start();
        disponibleService.obtenerProducto(id).then(function (response) {
            loading.finish();
            $log.info(response);
            $scope.producto = {};
            if (response.code === CODE.OK) {
                $scope.producto = response.data;
                $scope.buscarCantidadCart();
                disponibleService.obtenerProductos({ids: $scope.producto.idCategoria, cantidad: 10}).then(function (response) {
                    $scope.relacionados = [];
                    if (response.code === CODE.OK) {
                        $scope.relacionados = response.data.productos;
                        $timeout(function () {
                            carruselUtil.setupcarousel('#itemsRelacioando');
                        });
                    }
                });
            } else {
                $scope.producto = null;
                swal.error(response.description);
            }
        });
    };
    $scope.agregarCarro = function () {
        Cart.addProduct($scope.producto.id, $scope.cantidad);
    };
    $scope.buscarCantidadCart = function () {
        var cart = Cart.get();
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === $scope.producto.id) {
                $scope.cantidad = cart[i].cantidad;
            }
        }
    };
    $scope.baseImage = function () {
        return Url.getFolderImage();
    };
    $scope.buscar();
});