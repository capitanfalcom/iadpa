/* global app */

app.controller('flipbookController', function ($scope, $log, loading, disponibleService, swal, CODE, $state, $sce, $timeout, Url) {
    $scope.libros = [];
    $scope.paginas = [];
    $scope.iniciar = function () {
        loading.start();
        disponibleService.obtenerFlipbook({}).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                $scope.libros = response.data;
                var params = $state.params;
                if (params !== null && angular.isDefined(params.libro)) {
                    disponibleService.obtenerFlipbook(params).then(function (response) {
                        $log.info(response);
                        if (response.code === CODE.OK) {
                            $scope.paginas = response.data;
                            $timeout(function () {
                                $('.flipbook').turn({
                                    width: 1110,
                                    height: 780,
                                    gradients: true,
                                    autoCenter: true
                                });
                            });
                        }
                    });
                }
            } else {
                swal.error(response.description);
            }
        });
    };
    $scope.obtenerUrl = function (item) {
        return $sce.getTrustedResourceUrl(Url.getFolderFlipbook() + $state.params.libro + "/" + item);
    };
    $scope.iniciar();
});