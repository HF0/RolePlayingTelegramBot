


// translate
app.config(function ($translateProvider) {
    $translateProvider.useSanitizeValueStrategy('escape');
    $translateProvider.useCookieStorage();
    $translateProvider.translations('en', en);
    $translateProvider.translations('es', es);
    $translateProvider.preferredLanguage('en');
});

app.controller('TranslateController', function ($scope, $translate) {
    $scope.changeLanguage = function (key) {
        $translate.use(key);
    };

    $scope.isCurrentLanguage = function (language) {
        return $translate.use() === language;
    }
});