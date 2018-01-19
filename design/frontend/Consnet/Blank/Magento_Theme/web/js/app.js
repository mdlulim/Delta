var app = angular.module('appchk', []);
app.controller('checkoutCtrlr', function($scope, $http) {
    console.log('fromcontroler');
    $scope.itemlist = window.checkoutConfig.quoteItemData;//['Thato','Madihlaba'];
    $scope.total = window.checkoutConfig.totalsData.grand_total;
    $scope.itemcount = window.checkoutConfig.quoteItemData.length;
    $scope.lastname = window.checkoutConfig.customerData.lastname;
    $scope.firstname = window.checkoutConfig.customerData.firstname;
    $scope.email = window.checkoutConfig.customerData.email;

    $scope.checkout = function(){
        date = 12;
        //console.log($scope.po_number);
        //console.log($scope.delivery_date);
        $http({
            method:'POST',
            data: {quote_id : window.checkoutConfig.quoteData.entity_id, 
                   delivery_date: $scope.delivery_date,
                   po_number: $scope.po_number},
            url: 'http://10.2.10.93/deltaqa01/placeorder/index/index'
        }).then(function successCallback(response){
                    console.log(response);
                    window.location = "http://10.2.10.93/deltaqa01/sales/order/view/order_id/"+response.data+"/";
                },
                function errorCallback(response){
                    console.log(response);
                });
    };
});
