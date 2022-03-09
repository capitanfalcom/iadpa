/* global app */

app.service("pedidoService", function (service, Url, MAPPING) {
    return {
        toList: function (_filter) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.PEDIDO),
                params: _filter
            });
        },
        obtainTo: function (_id) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.PEDIDO) + "/" + _id
            });
        }
    };
});