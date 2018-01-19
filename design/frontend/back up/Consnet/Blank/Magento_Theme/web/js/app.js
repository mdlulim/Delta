try{
    var app = angular.module('app', []);
    app.controller('checkoutCtrlr', function($scope) {
        $scope.products = ['Thato','Madihlaba'];
        //$http.get("customers.php")
        //.then(function (response) {$scope.names = response.data.records;});
    });
}
catch(e){
    console.log(!!e.message.indexOf('btstrpd'));
}
