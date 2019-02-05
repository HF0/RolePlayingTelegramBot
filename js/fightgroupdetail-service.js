app.factory('FightGroupDetailService', ['$http', '$q', function ($http, $q) {
	var npcBaseUrl = '../api/fightgroup/npc/';
	var characterBaseUrl = '../api/fightgroup/character/';

	var getDataIfOkOrReject = function (response) {
		var result = response.data;
		return result.ok ? result.data : $q.reject();
	};

	var npc = {
		getAll: function (fightgroupname) {
			var url = npcBaseUrl + 'getAll/' + fightgroupname;
			return $http.get(url).then(getDataIfOkOrReject)
				.catch(function () {
					return $q.reject("Error getting npc list");
				});
		},
		delete: function (fightgroupname, character) {
			var url = npcBaseUrl + 'delete/' + fightgroupname + '/' + character;
			return $http.get(url).then(function (response) {
				var deleteOk = response.data;
				if (!deleteOk) {
					return $q.reject();
				}
			}).catch(function () {
				return $q.reject('Error deleting npc');
			});
		},
		add: function (fightgroupname, characterdata) {
			var url = npcBaseUrl + 'add/' + fightgroupname;
			return $http.post(url, characterdata)
				.then(function (response) {
					var result = response.data;
					if (!result.ok) {
						return $q.reject(result.message);
					}
				}, function () {
					return $q.reject('Error creating npc');
				});
		},
		update: function (fightgroupname, pj) {
			var url = npcBaseUrl + 'update/' + fightgroupname;
			return $http.post(url, pj)
				.then(function (response) {
					var result = response.data;
					if (!result.ok) {
						return $q.reject(result.message);
					}
					return
				}, function () {
					return $q.reject('Error updating npc');
				});
		}
	};
	var character = {
		getAll: function (fightgroupname) {
			var url = characterBaseUrl + 'getAll/' + fightgroupname;
			return $http.get(url).then(getDataIfOkOrReject)
				.catch(function () {
					return $q.reject("Error getting character");
				});
		},
		setEnable: function (fightgroupname, charactername, enable) {
			var url = characterBaseUrl + 'enable/' + fightgroupname;
			url += '/' + charactername + '/' + (enable ? "true" : "false");
			return $http.get(url).then(function (response) {
				var result = response.data;
				if (!result.ok) {
					return $q.reject();
				}
			}).catch(function () {
				return $q.reject('Error enabling character');
			});
		},
	};
	return { 'character': character, 'npc': npc };
}]);
