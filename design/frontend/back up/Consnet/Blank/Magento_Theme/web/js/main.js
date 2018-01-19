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

    jQuery.ajax({
              type: 'post',
              url: pageurl+'erporder/index/index',
              data: jQuery('#updateorderform').serialize(),
              success: function (data) {
                location.reload();
                
                if(data !== undefined && data !== null){                  
                  if(data.legth == 8 ){
                    console.nlog(data);
                    var newordernumber = data;//parseInt(data.replace(/\b0+/g, '')) + 3;
                    var newlocation = '';
                    var url = jQuery(window.location).attr('href').split( '/' );
                    for(i = 0; i < url.length - 2; i++) { 
                      newlocation += url[i] + '/';
                    }
                    newlocation += newordernumber + '/';
                    
                    //window.location = newlocation;
                  }else{
                    console.log(data);
                    var newordernumber = data;//parseInt(data.replace(/\b0+/g, '')) + 3;
                    var newlocation = '';
                    var url = jQuery(window.location).attr('href').split( '/' );
                    for(i = 0; i < url.length - 2; i++) { 
                      newlocation += url[i] + '/';
                    }
                    newlocation += newordernumber + '/';
                    
                    //window.location = newlocation;
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

  function showTable() {    
    if (order.classList.contains('hidden')) {
        updateorder.classList.add('hidden');
        order.classList.remove('hidden');
        neworder.classList.add('hidden');
        addrowbtn.classList.add('hidden');
        document.getElementById("edit").innerHTML = "Edit Order";
        document.getElementById("cancelorder").classList.remove('hidden');
          
    } else {
        // Hide Order Items
        updateorder.classList.remove('hidden');
        addrowbtn.classList.remove('hidden');
        order.classList.add('hidden');
        neworder.classList.remove('hidden');
        document.getElementById("edit").innerHTML = "Cancel";
        document.getElementById("cancelorder").classList.add('hidden');
    }
  }

  function addnewrow(){

    var row = neworder.insertRow((neworder.getElementsByTagName("tbody")[0].getElementsByTagName("tr").length) + 1);
    
    var elementsku  = document.createElement('input'); 
    elementsku.type ="text"; 
    elementsku.name = 'sku'+(neworder.getElementsByTagName("tbody")[0].getElementsByTagName("tr").length);
    elementsku.setAttribute("class", "form-control");

    var elementqty  = document.createElement('input'); 
    elementqty.type ="text"; 
    elementqty.name = 'qty'+(neworder.getElementsByTagName("tbody")[0].getElementsByTagName("tr").length);
    elementqty.setAttribute("class", "form-control");

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
    console.log(ordernumberform);
    jQuery.ajax({
              type: 'post',
              url: pageurl+'erporder/index/index',
              data: jQuery('#cancelorderform').serialize(),
              success: function (data) {
                location.reload();
              },
              error: function(xhr, status, error) {
                  console.log(xhr.responseText);
              }
            });
  }

  jQuery(document).ready(function (){
    var mage_order_number = document.getElementById("mage_order_number").value;
      console.log("Ajax call on pageload");
      jQuery.ajax({
        type: 'post',
        url: pageurl+'erporder/index/index',
        data: {'order_status':1, 'mage_order_number':mage_order_number},
        success: function (data) {
            console.log(data);
            var btnCancelorder = document.getElementById('cancelorder');
            var btnEdit = document.getElementById('edit');
            if(data == 1){
                btnCancelorder.classList.remove('hidden');
                btnEdit.classList.remove('hidden');
            }
            else{
              btnCancelorder.classList.add('hidden');
              btnEdit.classList.add('hidden');
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
