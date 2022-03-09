/* global app, carruselUtil */

app.controller('inicioController', function ($scope, disponibleService, CODE, $timeout, Url) {
    $scope.productos = {
        biblia: [],
        familia: [],
        salud: [],
        nino: []
    };
    $scope.iniciar = function () {
        disponibleService.obtenerProductos({ids: '2', cantidad: 10}).then(function (response) {
            $scope.productos.biblia = [];
            if (response.code === CODE.OK) {
                $scope.productos.biblia = response.data.productos;

                $timeout(function () {
                    carruselUtil.setupcarousel('#itemsBiblia');
                });
            }
        });
        disponibleService.obtenerProductos({ids: '6', cantidad: 10}).then(function (response) {
            $scope.productos.familia = [];
            if (response.code === CODE.OK) {
                $scope.productos.familia = response.data.productos;

                $timeout(function () {
                    carruselUtil.setupcarousel('#itemsFamilia');
                });
            }
        });
        disponibleService.obtenerProductos({ids: '11', cantidad: 10}).then(function (response) {
            $scope.productos.salud = [];
            if (response.code === CODE.OK) {
                $scope.productos.salud = response.data.productos;

                $timeout(function () {
                    carruselUtil.setupcarousel('#itemsSalud');
                });
            }
        });
        disponibleService.obtenerProductos({ids: '9', cantidad: 10}).then(function (response) {
            $scope.productos.nino = [];
            if (response.code === CODE.OK) {
                $scope.productos.nino = response.data.productos;

                $timeout(function () {
                    carruselUtil.setupcarousel('#itemsNino');
                });
            }
        });
    };
    $scope.baseImage = function () {
        return Url.getFolderImage();
    };
    $scope.iniciar();
});