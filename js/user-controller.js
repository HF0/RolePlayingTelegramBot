app.controller('UserController', ['$scope', 'UserService', function ($scope, UserService) {

	var showError = function (error) {
		$scope.errorMsg = error;
	}

	var clearMsgs = function () {
		$scope.errorMsg = undefined;
		$scope.successMsg = undefined;
	}

	var loadUsers = function () {
		clearMsgs();
		return UserService.getAll()
			.then(function (response) {
				console.log(response.data);
				$scope.userList = response.data;
			}, function () {
				$scope.errorMsg = "Error getting users";
			});
	}

	$scope.user = { ismaster: false };
	var create = function (user) {
		clearMsgs();
		if (!user || !user.name || !user.description || !user.userid) {
			$scope.errorMsg = "Error creating user";
			return;
		}
		UserService.add(user)
			.then(loadUsers)
			.catch(showError);
	}

	var deleteUser = function (user) {
		UserService.delete(user)
			.then(loadUsers)
			.catch(showError);
	}

	$scope.create = create;
	$scope.deleteUser = deleteUser;

	loadUsers();
}]);

app.factory('UserService', ['$http', '$q', function ($http, $q) {
	var baseUrl = '../api/user/';
	var obj = {
		getAll: function () {
			var url = baseUrl + 'getall';
			return $http.get(url);
		},
		add: function (user) {
			var url = baseUrl + 'add';
			return $http.post(url, user)
				.then(function (response) {
					var result = response.data;
					if (!result.ok) {
						return $q.reject(result.message);
					}
				}, function () {
					return $q.reject('Error creating user');
				});
		},
		delete: function (username) {
			var url = baseUrl + 'delete/' + username;
			return $http.get(url)
				.then(function (response) {
					var result = response.data;
					if (!result.ok) {
						return $q.reject(result.message);
					}
				}, function () {
					return $q.reject('Error deleting user');
				});
		}
	};
	return obj;
}]);
