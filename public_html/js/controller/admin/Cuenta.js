/* global app */

app.controller('cuentaController', function ($scope, $log, loading, cuentaService, swal, CODE, Table) {
    $scope.arreglo = angular.copy(Table);
    $scope.cuenta = {};
    $scope.formCuenta = {};
    $scope.defaultCuenta = {
        id: null,
        nombre: null,
        correo: null,
        usuario: null,
        contrasena: null,
        activo: null,
        rol: null,
        token: null
    };
    const CONTROL = "Cuenta";
    $scope.opcion = null;
    $scope.bloquear = null;
    $scope.agregarLog = function (_accion) {
        $log.info("Controlador:", CONTROL + ',', "accion", _accion);
    };
    $scope.iniciar = function () {
        $scope.agregarLog('iniciar');
        $scope.opcion = CODE.REVISAR;
        $scope.bloquear = true;
        $scope.formCuenta = angular.copy($scope.defaultCuenta);
        $scope.actualizar();
    };
    $scope.actualizar = function () {
        $scope.agregarLog('actualizar');
        loading.start();
        cuentaService.toList(null).then(function (response) {
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
        cuentaService.obtainTo(_id).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                $scope.cuenta = response.data;
                $scope.convetirCuenta($scope.cuenta);
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
        $scope.formCuenta = angular.copy($scope.defaultCuenta);
        $scope.cuenta = {};
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
        $scope.convetirFormCuenta($scope.formCuenta);
        switch ($scope.opcion) {
            case CODE.AGREGAR:
                cuentaService.create($scope.cuenta).then(function (response) {
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
                cuentaService.edit($scope.formCuenta.id, $scope.cuenta).then(function (response) {
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
    $scope.convetirCuenta = function (_data) {
        $scope.agregarLog('convetirCuenta');
        $scope.formCuenta = {
            id: _data.id,
            nombre: _data.nombre,
            correo: _data.correo,
            usuario: _data.usuario,
            contrasena: _data.contrasena,
            activo: _data.activo,
            rol: _data.rol,
            token: _data.token
        };
    };
    $scope.convetirFormCuenta = function (_data) {
        $scope.agregarLog('convetirFormCuenta');
        $scope.cuenta = {
            id: _data.id,
            nombre: _data.nombre,
            correo: _data.correo,
            usuario: _data.usuario,
            contrasena: _data.contrasena,
            activo: _data.activo,
            rol: _data.rol,
            token: _data.token
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