/* global app */

app.service("cuentaService", function (service, Url, MAPPING) {
    return {
        toList: function (_filter) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.CUENTA),
                params: _filter
            });
        },
        obtainTo: function (_id) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.CUENTA) + "/" + _id
            });
        },
        create: function (_data) {
            return service.rest({
                method: 'POST',
                url: Url.getService(MAPPING.CUENTA),
                data: _data
            });
        },
        edit: function (_id, _data) {
            return service.rest({
                method: 'PUT',
                url: Url.getService(MAPPING.CUENTA) + "/" + _id,
                data: _data
            });
        },
        autenticar: function (_data) {
            return service.rest({
                method: 'POST',
                url: Url.getService(MAPPING.LOGIN),
                data: _data
            });
        }
    };
});