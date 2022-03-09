/* global app */

app.service("disponibleService", function (service, Url, MAPPING) {
    return {
        obtenerProductos: function (_filter) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.PRODUCTO),
                params: _filter
            });
        },
        obtenerProducto: function (_id) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.PRODUCTO) + "/" + _id
            });
        },
        obtenerCategoriaProducto: function () {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.CATEGORIA_PRODUCTO)
            });
        },
        obtenerFlipbook: function (_filter) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.FLIPBOOK),
                params: _filter
            });
        },
        realizarCompra: function (_data) {
            return service.rest({
                method: 'POST',
                url: Url.getService(MAPPING.COMPRA),
                data: _data
            });
        },
        enviarCorreo: function (_data) {
            return service.rest({
                method: 'POST',
                url: Url.getService(MAPPING.CORREO),
                data: _data
            });
        }
    };
});