/* global app */

app.controller('registrateController', function ($scope, $log, loading, disponibleService, swal, CODE, $state) {
    $scope.formRegistro = {};
    $scope.registrar = function () {
        loading.start();
        var objeto = {
            paso: 'REGISTRO',
            formulario: $scope.formRegistro
        };
        disponibleService.realizarCompra(objeto).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                swal.info(response.data);
                $state.go('inicio');
            } else {
                swal.error(response.description);
                grecaptcha.reset();
            }
        });
    };
});