
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js" integrity="sha512-QEAheCz+x/VkKtxeGoDq6nsGyzTx/0LMINTgQjqZ0h3+NjP+bCsPYz3hn0HnBkGmkIFSr7QcEZT+KyEM7lbLPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<div style="text-align:center;margin-top:10px;font-family:serif">

  <span style="font-size:10px;font-weight:700;">  {{$product->title}} </span> <br>
                <canvas style="height:65px" id="barcode"></canvas>
<script>JsBarcode("#barcode", "{{$product->barcode}}");</script>

</div>



<script>
    window.onload = function() { window.print(); }
</script>