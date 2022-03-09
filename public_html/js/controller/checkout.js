/* global app, paypal, grecaptcha */

app.controller('checkoutController', function ($scope, $rootScope, loading, disponibleService, swal, CODE, $timeout, Cart, $state, Url) {
    $scope.paso = 0;
    $scope.registrarse = false;
    $scope.lista = [];
    $scope.total = 0;
    $scope.selectRadio = "";
    $scope.listadoPedido = [];
    $scope.selectPedido = {};
    $scope.formLogin = {};
    $scope.formPedido = {};
    $scope.mostrar = false;
    $scope.identificadorPedido = '';
    $scope.calcularTotal = function () {
        $scope.total = 0;
        for (var i = 0; i < $scope.lista.length; i++) {
            $scope.total += $scope.lista[i].total;
        }
    };
    $scope.comenzar = function () {
        $scope.mostrar = true;
        if ($scope.myFormPedido.$valid) {
            loading.start();
            var objeto = {};
            objeto.paso = 1;
            objeto.pedido = angular.copy($scope.formPedido);
            objeto.pedido.detalle = [];
            for (var i = 0; i < $rootScope.localCart.length; i++) {
                var carro = $rootScope.localCart[i];
                var item = {
                    id: carro.id,
                    cantidad: carro.cantidad
                };
                objeto.pedido.detalle.push(item);
            }
            disponibleService.realizarCompra(objeto).then(function (response) {
                loading.finish();
                if (response.code === CODE.OK) {
                    $scope.lista = response.data;
                    $scope.paso = 2;
                    $scope.calcularTotal();
                } else {
                    grecaptcha.reset();
                    swal.error(response.description);
                }
            });
        } else {
            grecaptcha.reset();
            swal.error("Corregir los campos en rojo.");
        }
    };
    $scope.confirmar = function () {
        var objeto = {
            paso: 2,
            total: $scope.total
        };
        disponibleService.realizarCompra(objeto).then(function (response) {
            loading.finish();
            if (response.code === CODE.OK) {
                $scope.paso = 3;

                $timeout(function () {
                    paypal.Buttons({
                        style: {
                            shape: 'pill',
                            label: 'pay',
                            color: 'blue',
                            height: 40
                        },
                        createOrder: function (data, actions) {
                            return actions.order.create({
                                purchase_units: [{
                                        amount: {
                                            value: $scope.total
                                        }
                                    }]
                            });
                        },
                        onApprove: function (data, actions) {
                            return actions.order.capture().then(function (details) {
                                $scope.finalizar(details);
                            });
                        }
                    }).render('#paypal-button-container');
                });
            } else {
                swal.error(response.description);
            }
        });
    };
    $scope.finalizar = function (_data) {
        var objeto = {
            paso: 3,
            order: _data
        };
        disponibleService.realizarCompra(objeto).then(function (response) {
            loading.finish();
            if (response.code === CODE.OK) {
                $scope.identificadorPedido = response.data;
                $scope.paso = 4;
                Cart.remove();
            } else {
                swal.error(response.description);
            }
        });
    };
    $scope.seguir = function () {
        if (angular.equals($scope.selectRadio, "INVITADO")) {
            $scope.paso = 1;
        } else {
            $state.go('registrate');
        }
    };
    $scope.ingresar = function () {
        var objeto = {
            paso: 0,
            login: $scope.formLogin
        };
        disponibleService.realizarCompra(objeto).then(function (response) {
            loading.finish();
            $scope.listadoPedido = [];
            if (response.code === CODE.OK) {
                $scope.paso = 1;
                $scope.listadoPedido = response.data;
            } else {
                swal.error(response.description);
            }
        });
    };
    $scope.abrirPedidosAnteriores = function () {
        $scope.selectPedido = {};
        $('#pedidosAnteriores').modal('show');
    };
    $scope.cambiarPedido = function (_data) {
        $scope.selectPedido = _data;
    };
    $scope.selecionarPedidoAnterior = function () {
        $scope.formPedido.nombre = $scope.selectPedido.nombre;
        $scope.formPedido.apellido = $scope.selectPedido.apellido;
        $scope.formPedido.correo = $scope.selectPedido.correo;
        $scope.formPedido.telefono = $scope.selectPedido.telefono;
        $scope.formPedido.direccion = $scope.selectPedido.direccion;
        $scope.formPedido.ciudad = $scope.selectPedido.ciudad;
        $scope.formPedido.provincia = $scope.selectPedido.provincia;
        $scope.formPedido.codigo = $scope.selectPedido.codigo;
        $('#pedidosAnteriores').modal('hide');
    };
    $scope.baseImage = function () {
        return Url.getFolderImage();
    };
});