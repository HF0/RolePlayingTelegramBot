app.factory('FightGroupService', ['$http', '$q', function ($http, $q) {
	var baseUrl = '../api/fightgroup/group/';
	var obj = {
		getAll: function () {
			var url = baseUrl + 'getAll';
			return $http.get(url).then(function (response) {
				return response.data;
			}).catch(function () {
				return $q.reject('Error getting fights');
			});
		},
		delete: function (name) {
			var url = baseUrl + 'delete/' + name;
			return $http.get(url).then(function (response) {
				var deleteOk = response.data;
				if (!deleteOk) {
					return $q.reject();
				}
				return;
			}).catch(function () {
				return $q.reject('Error deleting fight');
			});
		},
		getdetails: function (name) {
			var url = baseUrl + 'getdetails/' + name;
			return $http.get(url).then(function (response) {
				var result = response.data;
				if (!result.ok) {
					return $q.reject(result.message);
				}
				return result.data;
			}, function () {
				return $q.reject('Error getting fight details');
			});
		},
		add: function (data) {
			var url = baseUrl + 'add';
			return $http.post(url, data)
				.then(function (response) {
					var result = response.data;
					if (!result.ok) {
						return $q.reject(result.message);
					}
					return;
				}).catch(function () {
					return $q.reject("Error adding fight");
				});
		},
		setEnable: function (name, enable) {
			var url = baseUrl + 'enable/' + name + '/';
			url += enable ? "true" : "false";
			return $http.get(url)
				.then(function (response) {
					var data = response.data;
					if (!data.ok) {
						return $q.reject();
					}
				}).catch(function () {
					return $q.reject("Error enabling fight");
				});
		}
	};
	return obj;
}]);
