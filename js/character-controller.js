app.controller('CharacterController', ['$scope', 'CharacterService', 'UserService',
	function ($scope, CharacterService, UserService) {

		$scope.characterList = [];
		$scope.selectedCharacter = undefined;

		var selectTemplate = function (character) {
			var selected = $scope.selectedCharacter &&
				$scope.selectedCharacter.name === character.name;
			return selected ? 'edit' : 'display';
		}

		var edit = function (character) {
			$scope.selectedCharacter = angular.copy(character);
		}

		var cancelEdit = function () {
			$scope.selectedCharacter = undefined;
		}

		var save = function (character) {
			CharacterService.update(character).then(function (response) {
			}).then(loadData).then(cancelEdit)
				.catch(function (error) {
					showError(error);
					cancelEdit();
				});
		}

		var showError = function (error) {
			$scope.errorMsg = error;
		}

		var clearMsgs = function () {
			$scope.errorMsg = undefined;
			$scope.successMsg = undefined;
		}

		var loadUsers = function () {
			clearMsgs();
			UserService.getAll().then(function (response) {
				var users = [];
				angular.forEach(response.data, function (user) {
					this.push(user.name)
				}, users);
				$scope.users = users;
			},
				function () {
					$scope.errorMsg = "Error loading users";
				});
		}

		var loadData = function () {
			clearMsgs();
			return CharacterService.getAll().then(function (response) {
				$scope.characterList = response.data;
			},
				function () {
					$scope.errorMsg = "Error loading characters";
				});
		};

		var deleteCharacter = function (name) {
			clearMsgs();
			CharacterService.delete(name)
				.then(function () {
					return loadData();
				}).then(function () {
					$scope.successMsg = "Character deleted";
				}).catch(showError);
		}

		var create = function (character) {
			clearMsgs();
			if (!character || !character.name || !character.life
				|| !character.description || !character.control) {
				$scope.errorMsg = "Error creating character";
				return;
			}
			if (!angular.isNumber(character.life)) {
				$scope.errorMsg = "Life must be a number";
				return;
			}

			CharacterService.add(character).then(function () {
				return loadData();
			}).then(function () {
				$scope.successMsg = "Character created";
			}).catch(showError);
		}

		$scope.deleteCharacter = deleteCharacter;
		$scope.create = create;
		$scope.selectTemplate = selectTemplate;
		$scope.edit = edit;
		$scope.cancelEdit = cancelEdit;
		$scope.save = save;

		var iniCharacterForm = function () {
			var pj = {}
			pj.life = 100;
			pj.attack = 1;
			pj.defense = 1;
			pj.dexterity = 1;
			pj.level = 1;
			$scope.pj = pj;
		}

		iniCharacterForm();
		loadUsers();
		loadData();
	}]);


app.factory('CharacterService', ['$http', '$q', function ($http, $q) {
	var baseUrl = '../api/character/';
	var obj = {
		getAll: function () {
			var url = baseUrl + 'getAll';
			return $http.get(url);
		},
		delete: function (name) {
			var url = baseUrl + 'delete/' + name;
			return $http.get(url)
				.then(function (response) {
					var deleteOp = response.data;
					if (!deleteOp.ok) {
						return $q.reject(deleteOp.message);
					}
				}).catch(function (error) {
					return $q.reject(error);
				});
		},
		add: function (data) {
			var url = baseUrl + 'add';
			return $http.post(url, data)
				.then(function (response) {
					var data = response.data
					if (!data.ok) {
						return $q.reject(data.message);
					}
				})
				.catch(function () {
					return $q.reject("Error in add");
				});;
		},
		update: function (data) {
			var url = baseUrl + 'update';
			return $http.post(url, data)
				.then(function (response) {
					var data = response.data
					if (!data.ok) {
						return $q.reject(data.message);
					}
					return data;
				});
		}
	};
	return obj;
}]);
