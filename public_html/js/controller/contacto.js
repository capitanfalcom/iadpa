/* global app */

app.controller('contactoController', function ($scope, $log, loading, disponibleService, swal, CODE) {
    $scope.formMensaje = {};
    $scope.enviarMensaje = function () {
        loading.start();
        disponibleService.enviarCorreo($scope.formMensaje).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                swal.success(response.data);
                $scope.formMensaje = {};
            } else {
                swal.error(response.description);
            }
            grecaptcha.reset();
        });
    };
});