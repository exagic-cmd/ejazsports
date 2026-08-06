<script src="{{asset('js/html5-qrcode.min.js')}}"></script>
<style>
  .result{
    background-color: green;
    color:#fff;
    padding:20px;
  }
  .row{
    display:flex;
  }
</style>
<div class="row">
  <div class="col">
    <div style="width:500px;" id="reader"></div>
  </div>
  <div class="col" style="padding:30px;">
    <h4>SCAN RESULT</h4>
   <input style="width:200px;height:100px;" type="text" name="barcode" id="barcode" value=""><br><br>
   
   <input style="width:100px;height:30px;" onclick="searchBarcode()" class="search" type="button" value="Search">
    <!--<div id="result">Result Here</div>-->
    
  </div>
</div>

<div class="row">
    <form  method="post" name="sear_form" id="display-div" style="display:none;">
                    <div class="clear"></div>
                    <img id="image" src="https://www.vegas.pk/btPublic/bt-uploads/large/lifebuoy-bar-turmeric-140g.jpg" height="250px">
                    <div class="pixel_space3"></div>
                    <div class="clear"></div> 
             		
                    <div class="clear"></div>
                     <span style="font-size:24px;" id="title"></span>
                    <div class="pixel_space3"></div>
                    <div class="clear"></div> 
					
                    <div class="clear"></div>
                      <span style="font-size:24px;" id="price"></span>
                    <div class="pixel_space3"></div>
                    <div class="clear"></div> 
					  <span style="font-size:20px;"><label >Brand :  </label></span>
                    <div class="clear"></div>
                      <span style="font-size:24px;" id="brand"></span>                    <div class="pixel_space3"></div>
                    <div class="clear"></div>  
					 
                
                    
            </span>
            </form>
</div>

<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<script type="text/javascript">
function onScanSuccess(qrCodeMessage) {
//   document.getElementById('result').innerHTML = '<span class="result">'+qrCodeMessage+'</span>';
window.navigator.vibrate(200);
   $('#barcode').val(qrCodeMessage);
   searchBarcode();
   
    
}
function onScanError(errorMessage) {
  //handle scan error
}
var html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", { fps: 10, qrbox: 250 });
html5QrcodeScanner.render(onScanSuccess, onScanError);

function searchBarcode(){
    
    barcode = $('#barcode').val();
  $.ajax({

    url : 'https://vegas.pk/api/product-detail-by-barcode/'+barcode,
    type : 'GET',
    headers: { 'Auth-key': 'Pak918is27ta36nm45yl09ve11' },
    data : {},
    dataType:'json',
    success : function(data) {   
        alert(data);
        document.getElementById('title').innerHTML = data.data[0].name;
        document.getElementById('price').innerHTML = data.data[0].discount_price;
        document.getElementById('brand').innerHTML = data.data[0].brand;
        
        document.getElementById('display-div').style.display = 'block;'
        
    },
    error : function(request,error)
    {
        alert("Request: "+JSON.stringify(request));
    }
});
}


</script>