  jQuery = jQuery.noConflict();
  var addrowbtn = document.getElementById("addrowbtn");
  var order = document.getElementById("my-orders-table");
  var updateorder = document.getElementById("updateorder");
  // Find a <table> element with id="myTable":
  var neworder = document.getElementById("neworder");
  var pageurl = '';
  
  var url = jQuery(window.location).attr('href').split( '/' );
  for(i = 0; i < url.length - 6; i++) { 
    pageurl += url[i] + '/';
  }

  function sendupdatedorder() {
    var form = document.getElementById('updateorderform');
    var orderid = document.getElementById('orderid').value;

    var pageurl = '';
    var url = jQuery(window.location).attr('href').split( '/' );
    for(i = 0; i < url.length - 6; i++) { 
      pageurl += url[i] + '/';
    }
    console.log(jQuery('#updateorderform').serialize());
    jQuery.ajax({
              type: 'post',
              url: pageurl+'erporder/index/index',
              data: jQuery('#updateorderform').serialize(),
              success: function (data) {
                //location.reload();                
                if(data !== undefined && data !== null){                  
                  if(data.legth == 8 ){
                    console.log(data);
                    var newordernumber = data;//parseInt(data.replace(/\b0+/g, '')) + 3;
                    var newlocation = '';
                    var url = jQuery(window.location).attr('href').split( '/' );
                    for(i = 0; i < url.length - 2; i++) { 
                      newlocation += url[i] + '/';
                    }
                    newlocation += newordernumber + '/';
                    
                    window.location = newlocation;
                  }else{
                    console.log(data);
                    var newordernumber = data;//parseInt(data.replace(/\b0+/g, '')) + 3;
                    var newlocation = '';
                    var url = jQuery(window.location).attr('href').split( '/' );
                    for(i = 0; i < url.length - 2; i++) { 
                      newlocation += url[i] + '/';
                    }
                    newlocation += newordernumber + '/';
                    
                    window.location = newlocation;
                  }
                  
                }else{
                  console.log(data.order_result);
                }
              },
              error: function(xhr, status, error) {
                  console.log(error);
                  console.log(xhr.responseText);
                  console.log(xhr.responseText);
                  console.log(status);
              }
            });
  };

  jQuery('#delivery_date').change(function() {
      var date = document.getElementById('delivery_date').value;
      document.getElementById('changed_deliverydate').value = date;
  });

  function showTable() {  
    var delivery_date = document.getElementById('delivery_date');  
    if (order.classList.contains('hidden')) {
        // Show Order Items
        updateorder.classList.add('hidden');
        order.classList.remove('hidden');
        neworder.classList.add('hidden');
        addrowbtn.classList.add('hidden');
        document.getElementById("edit").innerHTML = "Edit Order";
        document.getElementById("cancelorder").classList.remove('hidden');  
        delivery_date.disabled = true;
    } else {
        // Hide Order Items
        updateorder.classList.remove('hidden');
        addrowbtn.classList.remove('hidden');
        order.classList.add('hidden');
        neworder.classList.remove('hidden');
        document.getElementById("edit").innerHTML = "Cancel";
        document.getElementById("cancelorder").classList.add('hidden');
        delivery_date.disabled = false;
    }
  }

  function addnewrow(){

    var row = neworder.insertRow((neworder.getElementsByTagName("tbody")[0].getElementsByTagName("tr").length) + 1);
    
    var elementsku  = document.createElement('input'); 
    elementsku.type ="text"; 
    elementsku.name = 'sku'+(neworder.getElementsByTagName("tbody")[0].getElementsByTagName("tr").length);
    elementsku.setAttribute("class", "form-control");
    elementsku.onkeypress = 'return event.charCode >= 48 && event.charCode <= 57';

    var elementqty  = document.createElement('input'); 
    elementqty.type ="text"; 
    elementqty.name = 'qty'+(neworder.getElementsByTagName("tbody")[0].getElementsByTagName("tr").length);
    elementqty.setAttribute("class", "form-control");
    elementqty.onkeypress = 'return event.charCode >= 48 && event.charCode <= 57';

    var elementremove = document.createElement('a');
    elementremove.name = 'remove'+(neworder.getElementsByTagName("tbody")[0].getElementsByTagName("tr").length);
    elementremove.text = "Remove";
    elementremove.onclick = function () {
        var row = this.parentNode.parentNode;
        row.parentNode.removeChild(row);
    };

    row.insertCell(0).appendChild(elementsku);
    row.insertCell(1).appendChild(elementqty); 
    row.insertCell(2).appendChild(elementremove); 
  }

  function removerow(id){
    var link = document.getElementById(id);
    var row = link.parentNode.parentNode;
    row.parentNode.removeChild(row);
  }

  function cancelOrder(){
    var ordernumberform = document.getElementById("cancelorderform");
    ordernumber = document.getElementById("ordernumber").value;
    console.log(ordernumberform);
    jQuery.ajax({
              type: 'post',
              url: pageurl+'erporder/index/index',
              data: {"ordernumber":ordernumber, "cancel":1},//jQuery('#cancelorderform').serialize(),
              success: function (data) {
                console.log(data);
                var newordernumber = data;//parseInt(data.replace(/\b0+/g, '')) + 3;
                var newlocation = '';
                var url = jQuery(window.location).attr('href').split( '/' );
                for(i = 0; i < url.length - 2; i++) { 
                  newlocation += url[i] + '/';
                }
                newlocation += newordernumber + '/';
                
                window.location = newlocation;
              },
              error: function(xhr, status, error) {
                  console.log(xhr.responseText);
                  console.log(xhr);
                  console.log(status);
                  console.log(error);
              }
            });
  }

  jQuery(document).ready(function (){
    var mage_order_number = document.getElementById("mage_order_number").value;

    if(window.location.href.indexOf('print') > -1){
        updateorder.classList.add('hidden');
        order.classList.remove('hidden');
        neworder.classList.add('hidden');
        addrowbtn.classList.add('hidden');
        document.getElementById("edit").classList.add('hidden');
        document.getElementById("cancelorder").classList.add('hidden');  
        delivery_date.disabled = true;

        jQuery(document).children().each(function (){
          var title = document.getElementsByName('title');
          jQuery(this).html( jQuery(this).html().replace('Order # '+mage_order_number.value,'1'));
          console.log('Order # '+mage_order_number);
        });
    }

      console.log("Ajax call on pageload");
      jQuery.ajax({
        type: 'post',
        url: pageurl+'erporder/index/index',
        data: {'order_status':1,'mage_order_number':mage_order_number},
        success: function (data) {
            console.log(data);
            var btnCancelorder = document.getElementById('cancelorder');
            var btnEdit = document.getElementById('edit');
            var delivery_date = document.getElementById('delivery_date');
            if(data == '1'){
                btnCancelorder.classList.remove('hidden');
                btnEdit.classList.remove('hidden');
                //delivery_date.disabled = true;
                console.log(delivery_date);
                console.log(data);
            }            
            else{
              //if(data == 'updated'){
                location.reload();
              //}
              btnCancelorder.classList.add('hidden');
              btnEdit.classList.add('hidden');
              //delivery_date.disabled = true;
              console.log(delivery_date);
              console.log(data);
              //cancelorder 
              
              /*updateorder.classList.remove('hidden');
              addrowbtn.classList.remove('hidden');
              order.classList.add('hidden');*/
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
        }
      });
  });
