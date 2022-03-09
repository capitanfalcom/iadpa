/* global app */

app.controller('loginController', function ($scope, $log, loading, cuentaService, swal, CODE, $state, $cookieStore) {
    $scope.cuenta = {
        usuario: '',
        contrasena: ''
    };
    $scope.ingresar = function () {
        loading.start();
        cuentaService.autenticar($scope.cuenta).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                $cookieStore.put('session', response.data);
                $state.go('inicio');
            } else {
                swal.error(response.description);
            }
        });
    };
});