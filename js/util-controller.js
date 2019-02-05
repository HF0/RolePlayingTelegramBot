app.controller('UtilController', ['$scope', 'DiceService', function ($scope, DiceService) {
  $scope.d20Result = '';
  $scope.d6Result = '';

  $scope.useD20 = function () {
    DiceService.dice(20).then(function (data) {
      $scope.d20Result = data;
    },
      function (error) {
        $scope.d20Result = error;
      });
  };

  $scope.useD6 = function () {
    DiceService.dice(6).then(function (data) {
      $scope.d6Result = data;
    },
      function (error) {
        $scope.d6Result = error;
      });
  };

}]);

app.factory('DiceService', ['$http', '$q', function ($http, $q) {
  return {
    dice: function (sides) {
      var url = "../api/dice/" + sides;
      return $http.get(url).then(
        function (response) {
          return response.data;
        }).catch(
          function () {
            return $q.reject("Dice error");
          });
    }
  };
}]);
