/* global app */

app.controller('libroController', function ($scope, $log, loading, libroService, categoriaService, swal, CODE, Table) {
    $scope.arreglo = angular.copy(Table);
    $scope.libro = {};
    $scope.formLibro = {};
    $scope.defaultLibro = {
        id: null,
        nombre: null,
        precio: 0.0,
        descripcion: null,
        archivo: null,
        archivoBase64: null,
        activo: true,
        descuento: 0.0,
        caracteristicas: [],
        categoria: {id: null}
    };
    const CONTROL = "Libro";
    $scope.opcion = null;
    $scope.bloquear = null;
    $scope.parametro = {
        categorias: [itemDefault],
        cargar: function () {
            categoriaService.toList(null).then(function (response) {
                $log.info(response);
                if (response.code === CODE.OK) {
                    angular.forEach(response.data, function (value) {
                        $scope.parametro.categorias.push(value);
                    });
                    $scope.iniciar();
                } else {
                    swal.error(response.description);
                }
            });
        }
    };
    $scope.agregarLog = function (_accion) {
        $log.info("Controlador:", CONTROL + ',', "accion", _accion);
    };
    $scope.iniciar = function () {
        $scope.agregarLog('iniciar');
        $scope.opcion = CODE.REVISAR;
        $scope.bloquear = true;
        $scope.formLibro = angular.copy($scope.defaultLibro);
        $scope.actualizar();
    };
    $scope.actualizar = function () {
        $scope.agregarLog('actualizar');
        loading.start();
        libroService.toList(null).then(function (response) {
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
        libroService.obtainTo(_id).then(function (response) {
            loading.finish();
            $log.info(response);
            if (response.code === CODE.OK) {
                $scope.libro = response.data;
                $scope.convetirLibro($scope.libro);
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
        $scope.formLibro = angular.copy($scope.defaultLibro);
        $scope.formLibro.categoria = $scope.parametro.categorias.findById($scope.defaultLibro.categoria.id);
        $scope.libro = {};
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
        $scope.convetirFormLibro($scope.formLibro);
        switch ($scope.opcion) {
            case CODE.AGREGAR:
                libroService.create($scope.libro).then(function (response) {
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
                libroService.edit($scope.formLibro.id, $scope.libro).then(function (response) {
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
    $scope.convetirLibro = function (_data) {
        $scope.agregarLog('convetirLibro');
        $scope.formLibro = {
            id: _data.id,
            nombre: _data.nombre,
            precio: _data.precio,
            descripcion: _data.descripcion,
            archivo: _data.archivo,
            archivoBase64: _data.archivoBase64,
            activo: _data.activo,
            descuento: _data.descuento,
            caracteristicas: _data.caracteristicas,
            categoria: $scope.parametro.categorias.findById(_data.categoria.id)
        };
    };
    $scope.convetirFormLibro = function (_data) {
        $scope.agregarLog('convetirFormLibro');
        $scope.libro = {
            nombre: _data.nombre,
            precio: _data.precio,
            descripcion: _data.descripcion,
            archivo: _data.archivo,
            archivoBase64: _data.archivoBase64,
            activo: _data.activo,
            descuento: _data.descuento,
            caracteristicas: _data.caracteristicas,
            categoria: _data.categoria
        };
    };
    $scope.mostrarFormulario = function () {
        $('#formulario').modal('show');
    };
    $scope.ocultarFormulario = function () {
        $('#formulario').modal('hide');
    };
    $scope.quitarCaracteristica = function (_index) {
        $scope.formLibro.caracteristicas.splice(_index, 1);
    };
    $scope.agregarCaracteristica = function () {
        if ($scope.formLibro.caracteristicas === null 
                || angular.isUndefined($scope.formLibro.caracteristicas)) {
            $scope.formLibro.caracteristicas = [];
        }
        $scope.formLibro.caracteristicas.push({
            id: null,
            nombre: ''
        });
    };
    $scope.parametro.cargar();
});