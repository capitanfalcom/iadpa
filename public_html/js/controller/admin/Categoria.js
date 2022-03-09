/* global app */

app.controller('categoriaController', function ($scope, $log, loading, categoriaService, swal, CODE, Table) {
    $scope.arreglo = angular.copy(Table);
    $scope.categoria = {};
    $scope.formCategoria = {};
    $scope.defaultCategoria = {
        id: null,
        nombre: null,
        descripcion: null,
        activo: null
    };
    const CONTROL = "Categoria";
    $scope.opcion = null;
    $scope.bloquear = null;
    $scope.agregarLog = function (_accion) {
        $log.info("Controlador:", CONTROL + ',', "accion", _accion);
    };
    $scope.iniciar = function () {
        $scope.agregarLog('iniciar');
        $scope.opcion = CODE.REVISAR;
        $scope.bloquear = true;
        $scope.formCategoria = angular.copy($scope.defaultCategoria);
        $scope.actualizar();
    };
    $scope.actualizar = function () {
        $scope.agregarLog('actualizar');
        loading.start();
        categoriaService.toList(null).then(function (response) {
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
        categoriaService.obtainTo(_id).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                $scope.categoria = response.data;
                $scope.convetirCategoria($scope.categoria);
                $scope.mostrarFormulario();
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
    $scope.agregar = function () {
        $scope.agregarLog('agregar');
        $scope.bloquear = false;
        $scope.formCategoria = angular.copy($scope.defaultCategoria);
        $scope.categoria = {};
        $scope.opcion = CODE.AGREGAR;
        $scope.mostrarFormulario();
    };
    $scope.editar = function (_id) {
        $scope.agregarLog('editar');
        $scope.bloquear = false;
        $scope.seleccionar(_id);
        $scope.opcion = CODE.EDITAR;
    };
    $scope.guardar = function () {
        $scope.agregarLog('guardar');
        $scope.convetirFormCategoria($scope.formCategoria);
        switch ($scope.opcion) {
            case CODE.AGREGAR:
                categoriaService.create($scope.categoria).then(function (response) {
                    loading.finish();
                    $log.info(response);
                    if (response.code === CODE.OK) {
                        swal.info("Se agrego correctamente");
                        $scope.ocultarFormulario();
                        $scope.iniciar();
                    } else {
                        swal.error(response.description);
                    }
                });
                break;
            case CODE.EDITAR:
                categoriaService.edit($scope.formCategoria.id, $scope.categoria).then(function (response) {
                    loading.finish();
                    $log.info(response);
                    if (response.code === CODE.OK) {
                        swal.info("Se edito correctamente");
                        $scope.ocultarFormulario();
                        $scope.iniciar();
                    } else {
                        swal.error(response.description);
                    }
                });
                break;
        }
    };
    $scope.convetirCategoria = function (_data) {
        $scope.agregarLog('convetirCategoria');
        $scope.formCategoria = {
            id: _data.id,
            nombre: _data.nombre,
            descripcion: _data.descripcion,
            activo: _data.activo
        };
    };
    $scope.convetirFormCategoria = function (_data) {
        $scope.agregarLog('convetirFormCategoria');
        $scope.categoria = {
            nombre: _data.nombre,
            descripcion: _data.descripcion,
            activo: _data.activo
        };
    };
    $scope.mostrarFormulario = function () {
        $('#formulario').modal('show');
    };
    $scope.ocultarFormulario = function () {
        $('#formulario').modal('hide');
    };
    $scope.iniciar();
});