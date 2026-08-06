
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js" integrity="sha512-QEAheCz+x/VkKtxeGoDq6nsGyzTx/0LMINTgQjqZ0h3+NjP+bCsPYz3hn0HnBkGmkIFSr7QcEZT+KyEM7lbLPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<div style="text-align:center;margin-top:10px">
   <span style="font-size:11px;font-weight:700;">  {{$variant->product->title}} - {{$variant->shade}} {{$variant->size}} <br> </span>
                <canvas style="height:60px" id="barcode"></canvas>
<script>JsBarcode("#barcode", "{{$variant->barcode}}");</script>
</div>






<script>
    window.onload = function() { window.print(); }
</script>