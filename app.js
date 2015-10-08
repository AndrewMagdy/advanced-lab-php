$('#cart').click(function(e){
  viewCart();
});

$('#index').click(function(e){
  checkLoggedIn();
  loadProducts();
});

$('#home').click(function(e){
  checkLoggedIn();
  loadProducts();
});

$('#register').click(function(e){
  register();
});
$('#login').click(function(e){
  login();
});
$('#history').click(function(e){
  loadHistory();
});

$(document).on('click', '#products .buy', function() {
  loadProduct($(this).data('product'));
});
$(document).on('click', '#products .remove', function() {
  deleteFromCart(JSON.parse($(this).data('product')));
});
$(document).on('click', '#products .buyCart', function() {
  buyCart();
});

$(document).on('click', '#products .addToCart', function() {
  addToCart(JSON.parse($(this).data('product')),parseInt($('#qty').val()));
});
$(document).on('submit', '#loginForm', function(event) {
  email =$("#email").val();
  pass =$("#pass").val();
  $.ajax({
    url: "/api/login/",
    type: "POST",
    dataType: 'json',
    data: "user_email="+email+"&user_password="+pass,
    success: function( data ) {
      if(data.success){
        checkLoggedIn();
        loadProducts();
      }
      else {
        $("#errorMsg").text(data.error.message);
        $("#errorBanner").removeClass('hidden');

      }
    }
  });
  event.preventDefault();
});

$(document).on('submit', '#registerForm', function(e) {
  e.preventDefault();
  url = $(this).attr('action');
  var formData = new FormData(this);
  $.ajax({
    type: "POST",
    url: url,
    data: formData,
    contentType: false,
    processData: false,
    success: function(data)
    {
      if(data.success){
        window.location.replace("http://localhost");
      }
      else {
        $("#errorMsg").text(data.error);
        $("#errorBanner").removeClass('hidden');
      }
    }
  });
});

$( document ).ready(function() {

  checkLoggedIn();
  loadProducts();


  $("#logoutButton").click(function(){
    $.ajax({
      url: "/api/logout/",
      type: "POST",
      dataType: 'json',
      success: function( data ) {
        if(data.success){
          checkLoggedIn();
        }
      }
    });
  });
});


function viewCart (){
  var x = localStorage.getItem('logged');
  x = (x=='1');

  if(x)

  {
    $("#errorBanner").addClass('hidden');
    $('#cart').attr('class','active');
    var cartValue = localStorage.getItem( "cart" );
    var cartObj = JSON.parse( cartValue );

    $("#products").empty();

    $(function() {
      $('#products').load('/templates/cart.html',function(){
        $.each(cartObj, function(index,value) {
          $("#zift").append('<tr>'
          +' <td>'+value.name+  '</td>'
          +'<td>$ '+value.price+'</td>'
          +'<td><a data-product =\''+JSON.stringify(value)+' \' class="btn btn-primary remove"  role="button"></i>Remove &raquo;</a></td></tr>');
        })
        $("#zift").append(
          '</tbody></table><p><a class="btn btn-primary buyCart"   role="button" >Buy </a></p>'
        )
      });
    });

    $.each(cartObj, function(index,value) {
      $("#zift").append('<tr>'
      +' <td>'+value.name+  '</td>'
      +'<td>$ '+value.price+'</td>'
      +'<td><a data-product =\''+JSON.stringify(value)+' \' class="btn btn-primary remove"  role="button"></i>Remove &raquo;</a></td></tr>');
    })
    $("#zift").append(
      '</tbody></table><p><a class="btn btn-primary buyCart"   role="button" >Buy </a></p>'
    )
  }
  else
  {
    $("#errorMsg").text('pls login');
    $("#errorBanner").removeClass('hidden');
  }

}

function deleteFromCart (product){
  var obj = localStorage.getItem('cart');
  var jsonObj = JSON.parse(obj);

  for(var i= 0;i<jsonObj.length;i++) {
    if(jsonObj[i]['id'] === product['id']){
      jsonObj.splice(i,1);
    }
  }
  localStorage.setItem('cart',JSON.stringify((jsonObj)));
  viewCart();
}

function buyCart ()
{
  var cartValue = localStorage.getItem( "cart" );
  var cartObj = JSON.parse( cartValue );
  $.each(cartObj, function(index,value) {
    $.ajax({
      url: "/api/buy/",
      type: "get",
      data: {id:value.id,qty:value.quantity},
      success: function( data ) {
        if(data.success){
          localStorage.removeItem('cart');
          viewCart();
        }
        else {


        }
      }
    });
  });
}
function addToCart (product,qty){

  var obj = localStorage.getItem('cart');
  var available = parseInt(product['quantity']);

  if(isNaN(qty) || qty==null || qty<1 || qty >available){
    alert("Not Enough Stock");
    return;
  }
  else  {
    product['quantity'] = qty;
    if(( obj === null || obj.length< 1)){
      product = [(product)];
    }
    else {
      var jsonObj = JSON.parse(obj);
      var b = false;
      for(var i= 0;i<jsonObj.length;i++) {
        if(jsonObj[i]['id'] === product['id']){
          b = true;
          var tmp =parseInt(jsonObj[i]['quantity'])+qty;
          jsonObj[i]['quantity'] = tmp;
          if(tmp>available){
            alert("Not Enough Stock2");
            return;
          }
        }
      }
      if(!b){
        product = jsonObj.concat((product));
      }
      else {
        product = jsonObj;
      }
    }
    localStorage.setItem('cart',JSON.stringify((product)));
    loadProducts();
  }
}


function loadProduct (product){

  var x = localStorage.getItem('logged');
  x = (x=='1');
  if(x)
  {
    $("#products").empty();

    $("#products").append('	<div class="col-md-4">'
    +' <img src='+product.image+' class="img-responsive" alt="Responsive image">'
    +' <h2>'+product.name+'</h2>'
    +' <h4>$ '+product.price+ '</h4>'
    +'<input id = "qty" type="text" class="input-sm" placeholder="">'
    +'<a data-product =\''+JSON.stringify(product)+' \'class="btn btn-primary addToCart"></i>Buy &raquo;</a>'
    +'</p></div>');
  }
  else
  {
    $("#errorMsg").text('pls login');
    $("#errorBanner").removeClass('hidden');
  }


}

function register()
{
  $("#products").empty();
  $(function() {
    $('#products').load('/templates/registertemplate.html');
  });
}
function login(){
  $("#products").empty();
  $(function() {
    $('#products').load('/templates/logintemplate.html');
  });

}

function loadProducts (){
  $("#errorBanner").addClass('hidden');
  $.ajax({
    url: "/api/product/",
    type: "get",
    dataType: 'json',
    success: function( data ) {

      if(data.success){
        $("#products").empty();
        $.each(data.success, function(index,value) {
          var x='Sold Out';
          var t = false;
          if(value.quantity>0){

            var x='Buy';
            var t = true;
          }

          if(t){
            $("#products").append('	<div class="col-md-4">'
            +' <img src='+value.image+' class="img-responsive" alt="Responsive image">'
            +' <h2>'+value.name+'</h2>'
            +' <h4>$ '+value.price+ '</h4>'
            +'<a data-product =\''+JSON.stringify(value)
            +'\'class="btn btn-primary buy" ></i>'+x+' &raquo;</a>'
            +'</p></div>');
          }
          else {
            $("#products").append('	<div class="col-md-4">'
            +' <img src='+value.image+' class="img-responsive" alt="Responsive image">'
            +' <h2>'+value.name+'</h2>'
            +' <h4>$ '+value.price+ '</h4>'
            +'<a data-product =\''+JSON.stringify(value)
            +'\'class="btn btn-primary" disabled="true" ></i>'+x+' &raquo;</a>'
            +'</p></div>');
          }
        })


      }
      else {

      }
    }
  });
}

function loadHistory(){
  $.ajax({
    url: "/api/history/",
    type: "get",
    dataType: 'json',
    success: function( data ) {

      if(data.success){
        $("#products").empty();
        $.each(data.success, function(index,value) {
          $("#products").append('	<div class="col-md-4">'
          +' <h2>'+value.quantity+'</h2>'
          +' <h4>$ '+value.price+ '</h4>'
          +' <h4> '+value.date+ '</h4>'
          +'<a data-product =\''+JSON.stringify(value)
          +'</p></div>');
        })
        $("#errorBanner").addClass('hidden');
      }
      else {
        $("#errorMsg").text('pls login');
        $("#errorBanner").removeClass('hidden');
      }
    }
  });
}

function checkLoggedIn() {
  $.ajax({
    url: "/api/user/",
    type: "get",
    dataType: 'json',
    success: function( data ) {
      if(data.success){
        $("#loginRegiser").addClass('hidden');
        $("#userDrop").removeClass('hidden');
        $("#userButton").html('<i class="fa fa-user fa-fw"></i>'+data.success.user);
        localStorage.setItem('logged',1);
        return true ;

      }
      else {

        $("#userDrop").addClass('hidden');
        $("#loginRegiser").removeClass('hidden');
        localStorage.setItem('logged',0);
        return false;
      }
    }
  });
}
