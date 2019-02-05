app.controller('FightGroupController', ['$scope', 'FightGroupService', '$window',
	function ($scope, FightGroupService, $window) {

		$scope.fightGroupList = [];

		var clearMsgs = function () {
			$scope.errorMsg = undefined;
			$scope.successMsg = undefined;
		}

		var showError = function (error) {
			$scope.errorMsg = error;
		}

		var loadData = function () {
			FightGroupService.getAll().then(function (data) {
				$scope.fightGroupList = data;
			}, showError);
		};

		var setEnable = function (fightgroup, enable) {
			clearMsgs();
			FightGroupService.setEnable(fightgroup.name, enable)
				.then(function () {
					return loadData();
				}).catch(showError);
		}

		var goToFightgroup = function (fightgroupName) {
			$window.location.href = './fightgroupdetail.html?name=' + fightgroupName;
		}

		var create = function (fightgroup) {
			clearMsgs();
			if (!fightgroup || !fightgroup.name || !fightgroup.description) {
				$scope.errorMsg = "Error creating fight";
				return;
			}
			FightGroupService.add(fightgroup)
				.then(function () {
					return loadData();
				}).then(function () {
					$scope.successMsg = "Fight created";
				})
				.catch(showError);
		}

		var deleteFightGroup = function (name) {
			clearMsgs();
			FightGroupService.delete(name)
				.then(function () {
					$scope.successMsg = "Fight deleted";
				}, showError);
		}

		$scope.deleteFightGroup = deleteFightGroup;
		$scope.create = create;
		$scope.goToFightgroup = goToFightgroup;
		$scope.setEnable = setEnable;

		loadData();
	}]);
