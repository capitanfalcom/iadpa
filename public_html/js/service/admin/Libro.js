/* global app */

app.service("libroService", function (service, Url, MAPPING) {
    return {
        toList: function (_filter) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.LIBRO),
                params: _filter
            });
        },
        obtainTo: function (_id) {
            return service.rest({
                method: 'GET',
                url: Url.getService(MAPPING.LIBRO) + "/" + _id
            });
        },
        create: function (_data) {
            return service.rest({
                method: 'POST',
                url: Url.getService(MAPPING.LIBRO),
                data: _data
            });
        },
        edit: function (_id, _data) {
            return service.rest({
                method: 'PUT',
                url: Url.getService(MAPPING.LIBRO) + "/" + _id,
                data: _data
            });
        }
    };
});