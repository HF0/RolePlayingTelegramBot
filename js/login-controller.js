app.controller('LoginController', ['$scope', 'LoginService', '$window', function ($scope, LoginService, $window) {

    var clearMsgs = function () {
        $scope.errorMsg = undefined;
        $scope.successMsg = undefined;
    }

    $scope.login = function (logindata) {
        clearMsgs();
        LoginService.login(logindata).then(
            function (response) {
                var result = response.data;
                if (!result.ok) {
                    $scope.errorMsg = "Login error";
                } else {
                    $window.location.href = './admin/';
                }
            },
            function () {
                $scope.errorMsg = "Login error";
            });
    };

}]);

app.factory('LoginService', ['$http', function ($http) {
    return {
        login: function (data) {
            var url = "./api/login";
            return $http.post(url, data);
        }
    };
}]);
