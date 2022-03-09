/* global app */

app.service("categoriaService", function (service, Url, MAPPING) {
    return {
        toList: function (_filter) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.CATEGORIA),
                params: _filter
            });
        },
        obtainTo: function (_id) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.CATEGORIA) + "/" + _id
            });
        },
        create: function (_data) {
            return service.rest({
                method: 'POST',
                url: Url.getService(MAPPING.CATEGORIA),
                data: _data
            });
        },
        edit: function (_id, _data) {
            return service.rest({
                method: 'PUT',
                url: Url.getService(MAPPING.CATEGORIA) + "/" + _id,
                data: _data
            });
        }
    };
});