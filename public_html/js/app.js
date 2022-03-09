/* global Swal */
$(document).ready(function () {

    // :: Menu Code

    if ($.fn.classyNav) {
        $('#bigshopNav').classyNav();
    }

    // :: Fixed Top Dropdown Code    

    $(".classy-navbar-toggler").on("click", function () {
        $(".top-header-area").toggleClass("z-index-reduce");
    });
    $(".classycloseIcon, .language-currency-dropdown a.btn").on("click", function () {
        $(".top-header-area").removeClass("z-index-reduce");
    });
    $(".language-currency-dropdown a.btn").on("click", function () {
        $(".classy-menu").removeClass("menu-on");
        $(".navbarToggler").removeClass("active");
    });
    $(".nav-item a").on("click", function () {
        $(".classy-menu").removeClass("menu-on");
        $(".top-header-area").removeClass("z-index-reduce");
        $(".navbarToggler").removeClass("active");
    });
    $(".cart-area .cart--btn").on("click", function () {
        $(".cart-area .cart-dropdown-content").toggleClass("cart-show");
    });
    $(".cart-box a").on("click", function () {
        $(".cart-area .cart-dropdown-content").removeClass("cart-show");
    });
    $(".cart-area .cart-dropdown-content").mouseleave(function () {
        $(".cart-area .cart-dropdown-content").removeClass("cart-show");
    });

    // :: Search Form Code    
    $(".search-btn").on("click", function () {
        $(".search-form").toggleClass("active");
    });


    // :: ScrollUp Code

    if ($.fn.scrollUp) {
        $.scrollUp({
            scrollSpeed: 1000,
            easingType: 'easeInOutQuart',
            scrollText: '<i class="icofont-rounded-up"></i>'
        });
    }

    // :: PreventDefault "a" Click

    $("a[href='#']").on('click', function ($) {
        $.preventDefault();
    });


});

// :: Popular Items Slides Code
var carruselUtil = (function () {
    return{
        setupcarousel: function (_id) {
            $(_id).owlCarousel({
                items: 4,
                margin: 30,
                loop: true,
                nav: true,
                navText: ['<i class="icofont-rounded-left"></i>', '<i class="icofont-rounded-right"></i>'],
                dots: false,
                autoplay: true,
                smartSpeed: 1500,
                autoplayTimeout: 7000,
                autoplayHoverPause: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    480: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    992: {
                        items: 4
                    }
                }
            });
        }
    };
})();

const itemDefault = {id: null, nombre: "Seleccionar"};

String.prototype.replaceFormat = function () {
    var formatted = this;
    for (var prop in arguments[0]) {
        var regexp = new RegExp('\\${' + prop + '\\}', 'gi');
        formatted = formatted.replace(regexp, arguments[0][prop]);
    }
    return formatted;
};

Array.prototype.findById = function (id) {
    for (var i = 0; i < this.length; i++) {
        if (this[i].id === id) {
            return this[i];
        }
    }
    return itemDefault;
};


var app = angular.module('appIadpa', ['ui.router', 'ngAnimate', 'ngCookies', 'ngCookies', 'ui.bootstrap', 'grecaptcha']).value('loading', {
    start: function () {
        $('#preloader').css({"display": "block"});
    },
    finish: function () {
        $('#preloader').css({"display": "none"});
    }
}).value('swal', {
    info: function (txt) {
        this.show("info", txt);
    },
    success: function (txt) {
        this.show("success", txt);
    },
    error: function (txt) {
        this.show("error", txt);
    },
    warning: function (txt) {
        this.show("warning", txt);
    },
    show: function (alert, txt) {
        Swal.fire({
            icon: alert,
            text: txt,
            confirmButtonText: 'Continuar'
        });
    },
    manual: function (obj) {
        Swal.fire(obj);
    }
}).config(function ($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/inicio');

    $stateProvider
            .state('inicio', {
                url: '/inicio',
                data: {pageTitle: 'Inicio'},
                templateUrl: 'page/inicio.html',
                controller: 'inicioController',
                authenticate: false
            })
            .state('catalogo', {
                url: '/catalogo?id',
                data: {pageTitle: 'Cátalogo'},
                templateUrl: 'page/grid-productos.html',
                controller: 'listadoProductoController',
                authenticate: false
            })
            .state('libro', {
                url: '/libro?id',
                data: {pageTitle: 'Producto'},
                templateUrl: 'page/producto.html',
                controller: 'productoController',
                authenticate: false
            })
            .state('contacto', {
                url: '/contacto',
                data: {pageTitle: 'Contacto'},
                templateUrl: 'page/contacto.html',
                controller: 'contactoController',
                authenticate: false
            })
            .state('quienes-somos', {
                url: '/quienes-somos',
                data: {pageTitle: 'Quiénes somos'},
                templateUrl: 'page/quienes-somos.html',
                authenticate: false
            })
            .state('sucursales', {
                url: '/sucursales',
                data: {pageTitle: 'Sucursales'},
                templateUrl: 'page/sucursales.html',
                authenticate: false
            })
            .state('checkout', {
                url: '/checkout',
                data: {pageTitle: 'Checkout'},
                templateUrl: 'page/checkout.html',
                controller: 'checkoutController',
                authenticate: false
            })
            .state('registrate', {
                url: '/registrate',
                data: {pageTitle: 'Registrate'},
                templateUrl: 'page/registrate.html',
                controller: 'registrateController',
                authenticate: false
            })
            .state('recuperar-contrasena', {
                url: '/recuperar-contrasena',
                data: {pageTitle: 'Recuperar contraseña'},
                templateUrl: 'page/gestion-contrasena.html',
                controller: 'gestionContrasenaController',
                authenticate: false
            })
            .state('cambio-contrasena', {
                url: '/cambio-contrasena?codigo',
                data: {pageTitle: 'Cambiar contraseña'},
                templateUrl: 'page/gestion-contrasena.html',
                controller: 'gestionContrasenaController',
                authenticate: false
            })
            .state('flipbook', {
                url: '/flipbook?libro',
                data: {pageTitle: 'Flipbook'},
                templateUrl: 'page/admin/flipbook.html',
                controller: 'flipbookController',
                authenticate: false
            })
            .state('login', {
                url: '/login',
                data: {pageTitle: 'Login'},
                templateUrl: 'page/login.html',
                controller: 'loginController',
                authenticate: false
            })
            .state('app', {
                url: '/app',
                template: '<div ui-view></div>',
                abstract: true,
                authenticate: true
            })
            .state('app.libro', {
                url: '/libro',
                data: {pageTitle: 'Libro'},
                templateUrl: 'page/admin/libro.html',
                controller: 'libroController',
                authenticate: true
            })
            .state('app.pedido', {
                url: '/pedido',
                data: {pageTitle: 'Pedido'},
                templateUrl: 'page/admin/pedido.html',
                controller: 'pedidoController',
                authenticate: true
            })
            .state('app.categoria', {
                url: '/categoria',
                data: {pageTitle: 'Categoria'},
                templateUrl: 'page/admin/categoria.html',
                controller: 'categoriaController',
                authenticate: true
            })
            .state('app.cuenta', {
                url: '/cuentas',
                data: {pageTitle: 'Cuentas'},
                templateUrl: 'page/admin/cuenta.html',
                controller: 'cuentaController',
                authenticate: true
            });
}).run(function ($rootScope, $state, $cookies) {
    $rootScope.$state = $state;
    $rootScope.$on('$stateChangeStart', function (event, toState, toParams) {
        $rootScope.loggedUser = $cookies.get('session');
        if (toState.authenticate && !$rootScope.loggedUser) {
            event.preventDefault();
            $state.transitionTo("inicio", null, {notify: false});
            $state.go('inicio');
        }
    });
}).filter('sprintf', function () {
    function parse(str, args) {
        if (angular.isUndefined(str)) {
            return "";
        } else {
            var i = 0;
            return str.replace(/%s/g, function () {
                var result = '';
                if (!angular.isUndefined(args[i]) && args[i] !== null) {
                    result = args[i];
                }
                i++;
                return result;
            });
        }
    }

    return function () {
        return parse(Array.prototype.slice.call(arguments, 0, 1)[0], Array.prototype.slice.call(arguments, 1));
    };
}).filter('strReplace', function () {
    return function (input, from, to) {
        input = input || '';
        from = from || '';
        to = to || '';
        return input.replace(new RegExp(from, 'g'), to);
    };
}).filter('searchObject', function () {
    return function (_id, _list, _attr) {
        if (angular.isDefined(_id) && angular.isArray(_list)) {
            for (var i = 0; i < _list.length; i++) {
                if (_list[i].id === _id) {
                    return _list[i][_attr];
                }
            }
        }
        return '';
    };
}).constant('ROL', {
    ADMINISTRADOR: 'ADMINISTRADOR',
    COLPORTOR: 'COLPORTOR',
    CLIENTE: 'CLIENTE'
}).constant('CODE', {
    AGREGAR: 1,
    EDITAR: 2,
    PARCHAR: 3,
    REVISAR: 4,
    OK: 0
}).constant('MAPPING', {
    LOGIN: "autenticar",
    CATEGORIA: "categoria",
    CUENTA: "cuenta",
    LIBRO: "libro",
    PEDIDO: "pedido",
    FLIPBOOK: "flipbook",
    PRODUCTO: "productos",
    CATEGORIA_PRODUCTO: "categoria-productos",
    COMPRA: "compras",
    CORREO: "correos"
}).factory('Util', function ($filter) {
    var utilidades = {};
    utilidades.formatEndpoint = function (url, _mapping) {
        return $filter('sprintf')('%s/%s%s', url, _mapping);
    };
    utilidades.convertToDate = function (_value) {
        if (_value === null || _value === undefined) {
            return null;
        }
        return new Date(_value);
    };
    utilidades.formatDate = function (_value, format) {
        if (_value === null || _value === undefined) {
            return "";
        } else {
            return $filter('date')(_value, format);
        }
    };
    utilidades.addDays = function (_date, _days) {
        _date.setDate(_date.getDate() + _days);
        return _date;
    };
    utilidades.isValidField = function (_field) {
        return angular.isDefined(_field) && _field !== null;
    };
    utilidades.isNumeric = function (_n) {
        return angular.isNumber(_n) && !isNaN(_n) && !isFinite(_n);
    };
    return utilidades;
}).factory('Url', function (Util) {
    const REST_PATH = "php";
    const IMAGE_PATH = "img/product-img/";
    const FLIPBOOK_PATH = "img/flipbook/";
    var obj = {};
    obj.getService = function (_mapping) {
        return Util.formatEndpoint(REST_PATH, _mapping);
    };
    obj.getFolderImage = function () {
        return IMAGE_PATH;
    };
    obj.getFolderFlipbook = function () {
        return FLIPBOOK_PATH;
    };
    return obj;
}).factory('Session', function ($rootScope) {
    var obj = {};
    obj.getRol = function () {
        var auxSession = angular.fromJson($rootScope.loggedUser);
        if (angular.isObject(auxSession)) {
            return auxSession.rol;
        }
        return "";
    };
    return obj;
}).factory('Table', function ($filter) {
    var table = {
        count: 0,
        options: [10, 20, 30, 40, 50],
        quantity: 10,
        current: 1,
        final: 1,
        search: '',
        orderBy: {column: 'default', asc: true},
        sorting: false,
        data: [],
        page: []
    };
    table.init = function (_array) {
        this.data = _array;
        this.viewFilterArray();
    };
    table.activeSorting = function () {
        this.sorting = true;
    };
    table.viewFilterArray = function () {
        var begin = ((this.current - 1) * this.quantity);
        var end = begin + this.quantity;
        var auxData = $filter('filter')(this.data, this.search);
        this.final = Math.ceil(auxData.length / this.quantity);
        this.page = auxData.slice(begin, end);
        this.count = this.page.length;
    };
    table.quantityPage = function () {
        this.current = 1;
        this.viewFilterArray();
    };
    table.prevPage = function () {
        if (this.current > 1) {
            this.current--;
            this.viewFilterArray();
        }
    };
    table.nextPage = function () {
        if (this.current < this.final) {
            this.current++;
            this.viewFilterArray();
        }
    };
    table.orderByPage = function (_column) {
        if (this.isColumnSorting(_column)) {
            this.orderBy.asc = !this.orderBy.asc;
        } else {
            this.orderBy.column = _column;
            this.orderBy.asc = false;
        }
        this.data = $filter('orderBy')(this.data, this.orderBy.column, this.orderBy.asc);
        this.viewFilterArray();
    };
    table.searchPage = function () {
        this.current = 1;
        this.viewFilterArray();
    };
    table.isColumnSorting = function (_column) {
        return this.orderBy.column === _column;
    };
    table.isColumnSortingAsc = function (_column) {
        return (this.isColumnSorting(_column) ? this.orderBy.asc : false);
    };
    table.isColumnSortingDesc = function (_column) {
        return (this.isColumnSorting(_column) ? !this.orderBy.asc : false);
    };
    return table;
}).factory('Cart', function ($cookies, $cookieStore, Util, disponibleService, CODE, $rootScope) {
    var shopping = {};
    shopping.get = function () {
        var cart = $cookies.get('shoppingCart');
        if (Util.isValidField(cart)) {
            cart = angular.fromJson(cart);
            if (Array.isArray(cart)) {
                return cart;
            }
        }
        return [];
    };
    shopping.addProduct = function (_id, _cantidad) {
        var newProduct = {id: _id, cantidad: _cantidad};
        if (this.isValidProduct(newProduct)) {
            var cart = this.get();
            disponibleService.obtenerProducto(_id).then(function (response) {
                if (response.code === CODE.OK) {
                    if (Util.isValidField(response.data)) {
                        if (shopping.exitsCartById(newProduct.id)) {
                            $rootScope.localCart = [];
                            angular.forEach(cart, function (value) {
                                if (value.id === newProduct.id) {
                                    newProduct.nombre = response.data.nombre;
                                    newProduct.precio = response.data.total;
                                    newProduct.cantidad = (shopping.isValidNumber(newProduct.cantidad) ? newProduct.cantidad : value.cantidad + 1);
                                    newProduct.total = newProduct.precio * newProduct.cantidad;
                                    newProduct.archivo = response.data.archivo;
                                    $rootScope.localCart.push(newProduct);
                                } else {
                                    $rootScope.localCart.push(value);
                                }
                            });
                        } else {
                            newProduct.nombre = response.data.nombre;
                            newProduct.precio = response.data.total;
                            newProduct.cantidad = (shopping.isValidNumber(newProduct.cantidad) ? newProduct.cantidad : 1);
                            newProduct.total = newProduct.precio * newProduct.cantidad;
                            newProduct.archivo = response.data.archivo;
                            $rootScope.localCart.push(newProduct);
                        }
                        $cookieStore.put('shoppingCart', $rootScope.localCart);
                        $rootScope.cambioLocalCart++;
                    }
                }
            });
        }
    };
    shopping.isValidProduct = function (_data) {
        if (!Util.isValidField(_data)) {
            return false;
        }
        if (this.isValidNumber(_data.id)) {
            return true;
        }
        return false;
    };
    shopping.isValidNumber = function (_n) {
        return Util.isValidField(_n)
                && Number.isInteger(_n)
                && _n > 0;
    };
    shopping.exitsCartById = function (_id) {
        var cart = this.get();
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === _id) {
                return true;
            }
        }
        return false;
    };
    shopping.removeCartById = function (_id) {
        if (this.isValidNumber(_id)
                && this.exitsCartById(_id)) {
            var cart = this.get();
            $rootScope.localCart = [];
            angular.forEach(cart, function (value) {
                if (value.id !== _id) {
                    $rootScope.localCart.push(value);
                }
            });
            $cookieStore.put('shoppingCart', $rootScope.localCart);
            $rootScope.cambioLocalCart++;
        }
    };
    shopping.remove = function () {
        $rootScope.localCart = [];
        $cookieStore.put('shoppingCart', $rootScope.localCart);
    };
    $rootScope.localCart = shopping.get();
    $rootScope.cambioLocalCart = 0;
    return shopping;
}).service("service", function ($http, $q) {
    return {
        rest: function (header) {
            var deferred = $q.defer();
            $http(header).then(function (response) {
                deferred.resolve(response.data);
            }, function (error) {
                if (error.data && error.data.codigo) {
                    deferred.resolve(error.data);
                } else {
                    deferred.resolve({code: -1});
                }
            });
            return deferred.promise;
        }
    };
}).directive('sortingColumn', function ($compile, $filter) {
    return {
        restrict: 'AE',
        link: function ($scope, $elem) {
            var paintSorting = "<i style=\"cursor: pointer;\" ng-if=\"${object}.sorting\" ng-click=\"${object}.orderByPage('${column}')\" "
                    + "ng-class=\"{'icofont-sort': !${object}.isColumnSorting('${column}'), "
                    + "'icofont-caret-up': ${object}.isColumnSortingAsc('${column}'), "
                    + "'icofont-caret-down': ${object}.isColumnSortingDesc('${column}')}\"></i>";
            var elemSorting = paintSorting.replaceFormat({
                object: "arreglo",
                column: ($elem[0].getAttribute("sorting-column"))
            });
            var el = $filter('sprintf')("%s%s", $elem.html(), elemSorting);
            $elem.html(el);
            $compile($elem.contents())($scope);
        }
    };
}).directive('fileModel', function ($parse, $timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            element.bind('change', function () {
                scope.$apply(function () {
                    var file = element[0].files[0];
                    var reader = new FileReader();
                    reader.onload = function (readerEvt) {
                        $timeout(function () {
                            var binaryString = readerEvt.target.result;
                            modelSetter(scope, btoa(binaryString));
                        });
                    };
                    reader.readAsBinaryString(file);
                });
            });
        }
    };
}).directive('azsLength', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attrs) {
            if (angular.isDefined(attrs.azsLength)) {
                var total = parseInt(attrs.azsLength);
                if (Number.isInteger(total)) {
                    element.bind('keypress', function (event) {
                        if (element.val().length > total) {
                            event.preventDefault();
                        }
                    });
                }
            }
        }
    };
}).config(function (grecaptchaProvider) {
    grecaptchaProvider.setParameters({
        sitekey: '',
        theme: 'light',
        languageCode: 'es'
    });
}).controller('navController', function ($scope, $state, $cookieStore, $rootScope, Cart, Util, disponibleService, CODE, $state, Session, ROL, Url) {
    $scope.showConfig = false;
    $scope.resumen = {
        total: 0
    };
    $scope.$watch("loggedUser", function () {
        $scope.showConfig = (Util.isValidField($rootScope.loggedUser)
                && Session.getRol() === ROL.ADMINISTRADOR);
        $scope.showFlipbook = (Session.getRol() === ROL.ADMINISTRADOR)
                || (Session.getRol() === ROL.COLPORTOR);
    });
    $scope.$watch("cambioLocalCart", function () {
        $scope.resumen.total = 0;
        for (var i = 0; i < $rootScope.localCart.length; i++) {
            $scope.resumen.total += $rootScope.localCart[i].total;
        }
    });
    $scope.quitarCarro = function (_id) {
        Cart.removeCartById(_id);
    };
    $scope.salir = function () {
        $cookieStore.remove('session');
        $state.go('inicio');
    };
    $scope.search = "";
    $scope.librosOutput = [];
    $scope.libros = [];
    $rootScope.categorias = [];
    $scope.grupoCategoria = [];
    $scope.iniciar = function () {
        disponibleService.obtenerProductos().then(function (response) {
            $scope.libros = [];
            if (response.code === CODE.OK) {
                $scope.libros = response.data.productos;
            }
        });
        disponibleService.obtenerCategoriaProducto().then(function (response) {
            $scope.categorias = [];
            if (response.code === CODE.OK) {
                $rootScope.categorias = response.data;
                var cantidadPorGrupo = Math.ceil($rootScope.categorias.length / 4);
                for (var i = 0; i < $rootScope.categorias.length; i++) {
                    var auxItem = $rootScope.categorias[i];
                    if (i % cantidadPorGrupo === 0) {
                        $scope.grupoCategoria[Math.floor(i / cantidadPorGrupo)] = [auxItem];
                    } else {
                        $scope.grupoCategoria[Math.floor(i / cantidadPorGrupo)].push(auxItem);
                    }
                }
            }
        });
    };
    $scope.complete = function () {
        var output = [];
        angular.forEach($scope.libros, function (value) {
            if (value.nombre.toLowerCase().indexOf($scope.search.toLowerCase()) >= 0) {
                output.push(value);
            }
        });
        $scope.librosOutput = output;
    };
    $scope.selectList = function () {
        $scope.search = "";
        $scope.librosOutput = [];
        $(".search-form").toggleClass("active");
    };
    $scope.mostrarCategorias = function () {
        $(".megamenu").show();
    };
    $scope.selectCategoria = function (_data) {
        $state.transitionTo("catalogo", {id: _data.id});
        $(".classy-menu").removeClass("menu-on");
        $(".megamenu").hide();
    };
    $scope.baseImage = function () {
        return Url.getFolderImage();
    };
});