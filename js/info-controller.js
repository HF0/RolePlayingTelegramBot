app.controller('InfoController', ['$scope', 'InfoService', function ($scope, InfoService) {
	$scope.infoList = [];

	var clearMsgs = function () {
		$scope.errorMsg = undefined;
		$scope.successMsg = undefined;
	}

	var showError = function (error) {
		$scope.errorMsg = error;
	}

	var loadInfo = function () {
		InfoService.getAll().then(
			function (data) {
				$scope.infoList = data;
			}, showError);
	};

	var deleteInfo = function (name) {
		clearMsgs();
		InfoService.delete(name).then(
			function () {
				$scope.successMsg = "File deleted";
				loadInfo();
			}, showError);
	}

	$scope.deleteInfo = deleteInfo;
	loadInfo();
}]);


app.factory('InfoService', ['$http', '$q', function ($http, $q) {
	var baseUrl = '../api/info/';
	var obj = {
		getAll: function () {
			var url = baseUrl + 'getAll';
			return $http.get(url)
				.then(function (response) {
					return response.data;
				})
				.catch(function () {
					return $q.reject("Error getting info");
				});
		},
		delete: function (name) {
			var url = baseUrl + 'delete/' + name;
			return $http.get(url)
				.then(function (response) {
					var deleteOK = response.data;
					if (!deleteOK) {
						return $q.reject();
					}
					return deleteOK;
				})
				.catch(function () {
					return $q.reject("Error deleting info");
				});
		}
	};
	return obj;
}]);

