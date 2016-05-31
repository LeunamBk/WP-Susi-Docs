app.factory('Request', ['$http', function ($http) {
    return {
        fetchData : function(params) {

            var response = $http({
                url: ajaxurl,
                method: "GET",
                params: params
            }).then(function (response) {
                return response.data;
            });

            return response;
        }
    }
}]);


