/* global app */

app.controller('gestionContrasenaController', function ($scope, $log, loading, disponibleService, swal, CODE, $state) {
    $scope.formRecuperar = {};
    $scope.formCambiar = {};
    $scope.iniciar = function () {
        if ($state.is('cambio-contrasena')) {
            $scope.formCambiar.codigo = $state.params.codigo;
        }
    };
    $scope.recuperar = function () {
        loading.start();
        var objeto = {
            paso: 'RECUPERAR',
            formulario: $scope.formRecuperar
        };
        disponibleService.realizarCompra(objeto).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                swal.info(response.data);
            } else {
                swal.error(response.description);
                grecaptcha.reset();
            }
        });
    };
    $scope.cambiar = function () {
        loading.start();
        var objeto = {
            paso: 'CAMBIAR',
            formulario: $scope.formCambiar
        };
        disponibleService.realizarCompra(objeto).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                swal.info(response.data);
            } else {
                swal.error(response.description);
                grecaptcha.reset();
            }
        });
    };
    $scope.iniciar();
});