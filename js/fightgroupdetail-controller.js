app.controller('FightGroupDetailController', ['$scope', '$window', '$q', 'FightGroupDetailService', 'FightGroupService',
	function ($scope, $window, $q, FightGroupDetailService, FightGroupService) {

		$scope.selectedPj = undefined;

		function getJsonFromUrl() {
			var query = location.search.substr(1);
			var result = {};
			query.split("&").forEach(function (part) {
				var item = part.split("=");
				result[item[0]] = decodeURIComponent(item[1]);
			});
			return result;
		}

		var clearMsgs = function () {
			$scope.errorMsg = undefined;
			$scope.successMsg = undefined;
		}

		var showError = function (error) {
			$scope.errorMsg = error;
		}

		var loadCharacter = function (fightgroupname) {
			FightGroupDetailService.character.getAll(fightgroupname)
				.then(function (data) {
					$scope.fightgrouppjs = data;
				}, showError);
		}

		var edit = function (pj) {
			$scope.selectedPj = angular.copy(pj);
		}

		var cancelEdit = function () {
			$scope.selectedPj = undefined;
		}

		var save = function (pj) {
			console.log(pj);
			cancelEdit();
		}

		var loadNpc = function (fightgroupname) {
			return FightGroupDetailService.npc.getAll(fightgroupname)
				.then(function (data) {
					$scope.fightgrouppnjs = data;
				}, showError);
		}

		var selectTemplate = function (pj) {
			var selected = $scope.selectedPj &&
				$scope.selectedPj.name === pj.name;
			return selected ? 'edit' : 'display';
		}

		var loadFightGroupData = function (fightgroupName) {
			return FightGroupService.getdetails(fightgroupName)
				.then(function (data) {
					$scope.fightgroupdetails = data;
				}, showError);
		}

		var create = function (fightgroupname, characterdata) {
			clearMsgs();
			FightGroupDetailService.npc.add(fightgroupname, characterdata)
				.then(function () {
					return $q.all(
						loadFightGroupData(fightgroupname),
						loadNpc(fightgroupname));
				})
				.then(function () {
					$scope.successMsg = "Character created";
				}, showError);
		}

		var deleteNpc = function (fightgroupname, character) {
			clearMsgs();
			FightGroupDetailService.npc.delete(fightgroupname, character)
				.then(function () {
					return $q.all(
						loadFightGroupData(fightgroupname),
						loadNpc(fightgroupname));
				})
				.then(function () {
					$scope.successMsg = "Character deleted";
				}, showError);
		}

		var goback = function () {
			$window.location.href = './fightgroup.html';
		}

		var iniNpcForm = function () {
			var pj = {};
			pj.dexterity = 1;
			pj.attack = 1;
			pj.life = 100;
			pj.defense = 1;
			$scope.pj = pj;
		}

		var save = function (fightgroupname, npc) {
			clearMsgs();
			FightGroupDetailService.npc.update(fightgroupname, npc)
				.then(loadNpc(fightgroupname))
				.then(loadFightGroupData(fightgroupname))
				.then(cancelEdit)
				.catch(function (error) {
					showError(error);
					cancelEdit();
				});
		}

		var setEnable = function (fightgroupname, character, enable) {
			clearMsgs();
			FightGroupDetailService.character
				.setEnable(fightgroupname, character.name, enable)
				.then(function () {
					character.enabled = enable;
					loadFightGroupData(fightgroupname);
				}, showError);
		}

		$scope.fightGroupList = [];
		var params = getJsonFromUrl();
		$scope.fightgroupname = params.name;
		$scope.goback = goback;
		$scope.deleteNpc = deleteNpc;
		$scope.create = create;
		$scope.setEnable = setEnable;
		$scope.selectTemplate = selectTemplate;
		$scope.edit = edit;
		$scope.cancelEdit = cancelEdit;
		$scope.save = save;

		iniNpcForm();
		loadFightGroupData($scope.fightgroupname);
		loadNpc($scope.fightgroupname);
		loadCharacter($scope.fightgroupname);
	}]);
