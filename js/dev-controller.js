app.filter('safe', function ($sce) { return $sce.trustAsHtml; });

app.controller('DevController', ['$scope', 'DevService',
	function ($scope, DevService) {

		var clearMsgs = function () {
			$scope.errorMsg = undefined;
			$scope.successMsg = undefined;
		}

		var loadErrors = function () {
			clearMsgs();
			DevService.getErrors().then(function (errors) {
				$scope.errorInfo = errors;
			}, function () {
				$scope.errorMsg = "Error getting data";
			});
		}
		$scope.errorInfo = { hasErrors: false };
		$scope.loadErrors = loadErrors;
		loadErrors();
	}]);

app.factory('DevService', ['$http', '$q', function ($http, $q) {
	var baseUrl = '../api/dev/';
	var obj = {
		getErrors: function () {
			var url = baseUrl + 'getphperror';
			return $http.get(url)
				.then(function (response) {
					if (!response.data) {
						return $q.reject();
					}
					return response.data;
				});;
		}
	};
	return obj;
}]);
