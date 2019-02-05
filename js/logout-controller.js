app.controller('LogoutController', ['$scope', 'LogoutService', '$window', function ($scope, LogoutService, $window) {
    $scope.logout = function () {
        LogoutService.logout().then(
            function (response) {
                var result = response.data;
                if (result.ok) {
                    $window.location.href = '../login.html';
                }
            });
    };

}]);

app.factory('LogoutService', ['$http', function ($http) {
    return {
        logout: function () {
            var url = "../api/logout";
            return $http.get(url);
        }
    };
}]);
