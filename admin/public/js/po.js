$(document).on('change','#po_id',function(e) {

    po_id = $(this).val();
    if(po_id) {

        $.confirm({
            title: 'Purchase Order info update!',
            content: 'Are you sure you want to do this!',
            buttons: {
                confirm: function () {
                    $.ajax({
                        url: config.routes.po,
                        type: 'GET',
                        data: {po_id: po_id},
                        success: function (data) {
                            document.getElementById('PODiv').innerHTML = data;
                        }
                    });

                    $.ajax({
                        url: config.routes.product,
                        type: 'GET',
                        data: {po_id: po_id},
                        success: function (data) {
                            document.getElementById('productDiv').innerHTML = data;
                            $('#myTable').DataTable({
                                'ordering': true,
                                'order': [],
                                'sorting': true,
                                'paging': false,
                                'info': true,
                                'searching': true
                            });
                        }
                    });
                },
                cancel: function () {
                }
            }
        });
    }
});

function updateDiff(id) {

  



    updateTradePrice(id);
   updateGrandTotal();
}

function updateTradePrice(id) {
    
    var rQtyId = '#r_qty' + id;
    var tTPId = '#total_t_price' + id;
    var tNPId = '#total_net_price' + id;
    var tDId = '#discount' + id;
    var tGSTId = '#gst' + id;
    var tPId = '#t_price' + id;

    var receivedQty = parseInt($(rQtyId).val()) || 0;
    var tPrice = parseFloat($(tPId).val()) || 0;
    var discount = parseFloat($(tDId).val()) || 0;
    var gst = parseFloat($(tGSTId).val()) || 0;

    $(tTPId).val(receivedQty * tPrice);
    $(tNPId).val((tPrice * receivedQty) - discount + gst);

    updateGrandTotal();
}

function updateNetPrice(id) {

    var rQtyId = '#r_qty' + id;
    var tTPId = '#total_t_price' + id;
    var tNPId = '#total_net_price' + id;
    var tDId = '#discount' + id;
    var dPId = '#discount_p' + id;
    var tGSTId = '#gst' + id;
    var tPId = '#t_price' + id;

    var receivedQty = parseInt($(rQtyId).val()) || 0;
    var totalTPrice = parseFloat($(tTPId).val()) || 0;
    var discount = parseFloat($(tDId).val()) || 0;
    var gst = parseFloat($(tGSTId).val()) || 0;

    if(receivedQty > 0) {
        $(tPId).val(Math.round(totalTPrice / receivedQty, 1));
    }

    if(totalTPrice > 0) {
        $(dPId).val(Math.round(((discount * 100) / totalTPrice), 1));
    }

    $(tNPId).val(totalTPrice - discount + gst);

    updateGrandTotal();

}

function updateGrandTotal() {

    var x = document.getElementsByClassName("total-trade-price");
    var i;var trade = 0;
    var inputs = $(".total-trade-price");
    for (i = 0; i < x.length; i++) {
        trade += parseFloat($(inputs[i]).val()) || 0;
    }

    var x = document.getElementsByClassName("total-discount");
    var i;var discount = 0;
    var inputs = $(".total-discount");
    for (i = 0; i < x.length; i++) {
        discount += parseFloat($(inputs[i]).val()) || 0;
    }

    var x = document.getElementsByClassName("total-gst");
    var i;var tax = 0;
    var inputs = $(".total-gst");
    for (i = 0; i < x.length; i++) {
        tax += parseFloat($(inputs[i]).val()) || 0;
    }
    
    var x = document.getElementsByClassName("qty");
    var i;var qty = 0;
    var inputs = $(".qty");
    for (i = 0; i < x.length; i++) {
        qty += parseFloat($(inputs[i]).val()) || 0;
    }
    
    $('#total_qty').val(qty);

    $('#gross_amount').val(trade);
    $('#g_discount').val(discount);
    $('#g_tax').val(tax);
    
    var packing_charges = parseFloat($('#packing_charges').val()) || 0;
    $('#net_amount').val(trade - discount + tax + packing_charges);
}

function updateDiscount(id) {

    dId = '#discount' + id;
    dPId = '#discount_p' + id;
    tTPId = '#total_t_price' + id;

    if(parseFloat($(dPId).val()) > 0) {

        discount_amount = Math.round(($(tTPId).val() * (parseFloat($(dPId).val()) / 100)) ,1);

        $(dId).val(discount_amount);
    }

    updateTradePrice(id);
}

function addNewProduct(proId,vId) {
    
    document.getElementById('app').style.opacity = '0.1';
    setFormSubmitting();
    $.ajax({
        url: config.routes.addProduct,
        type: 'GET',
        data: {po_id: 1,proId:proId,vId:vId},
        success: function (data) {
            $('#myTable tr:last').after(data);
            
            document.getElementById('app').style.opacity = '1';
        }
    });
    
    
    document.getElementById('app').style.opacity = '1';

}

function addQuantity(id) {
    addId = 'add_qty' + id;
    rQtyId = '#r_qty' + id;
    tRQtyId = '#t_r_qty' + id;
    textId = 'text_qty' + id;
    rId = 'r'+id;

    //update the added qty
    totalAddedQty = parseInt(document.getElementById(addId).innerHTML);
    scanQty = parseInt($(rQtyId).val());
    document.getElementById(addId).innerHTML = totalAddedQty + scanQty;

    //update the received qty
    $(tRQtyId).val(totalAddedQty + scanQty);

    //update the received qty text
    text = document.getElementById(textId).innerHTML;

    if(text)
        text = text + ' + ' + scanQty;
    else
        text = scanQty;

    document.getElementById(textId).innerHTML = text

    //update the scan qty
    $(rQtyId).val(0);

    //update the difference qty
    dQtyId = 'd_qty' + id;
    oQtyId = '#o_qty' + id;
    orderQty = parseInt($(oQtyId).val());
    document.getElementById(dQtyId).innerHTML = orderQty - (totalAddedQty + scanQty);
    if ((orderQty - (totalAddedQty + scanQty)) != 0) {
            document.getElementById(rId).style.backgroundColor = '#ffdada';
            document.getElementById(dQtyId).style.color = 'red';
    } else {
            document.getElementById(rId).style.backgroundColor = '#fff';
            document.getElementById(dQtyId).style.color = 'green';
    }

    //update the total qty
    var x = document.getElementsByClassName("qty");
    var i;var sum = 0;qty = 0;
    var inputs = $(".qty");
    for (i = 0; i < x.length; i++) {
        sum += parseInt($(inputs[i]).val());
        if(parseInt($(inputs[i]).val()) > 0)
            qty++;
    }
    $('#total_qty').val(sum);
    $('#total_products').val(qty);

}

function applyBatchPrice(productId) {
    var tPriceVal = $('#batch_t_price_' + productId).val();
    var rPriceVal = $('#batch_r_price_' + productId).val();
    var dzPriceVal = $('#batch_dz_price_' + productId).val();

    var hasTPrice = tPriceVal !== '' && !isNaN(parseFloat(tPriceVal));
    var hasRPrice = rPriceVal !== '' && !isNaN(parseFloat(rPriceVal));
    var hasDzPrice = dzPriceVal !== '' && !isNaN(parseFloat(dzPriceVal));

    if (!hasTPrice && !hasRPrice && !hasDzPrice) {
        alert('Please enter at least one price (Purchase, Wholesale, or Dz Price) to apply.');
        return;
    }

    var tPrice = hasTPrice ? parseFloat(tPriceVal) : null;
    var rPrice = hasRPrice ? parseFloat(rPriceVal) : null;
    var dzPrice = hasDzPrice ? parseFloat(dzPriceVal) : null;

    var updatedCount = 0;
    $('tr[data-product-id="' + productId + '"]').each(function () {
        var rowId = $(this).data('row-id');

        if (tPrice !== null) {
            $('#t_price' + rowId).val(tPrice);
            updateTradePrice(rowId);
        }
        if (rPrice !== null) {
            $('#retail_price' + rowId).val(rPrice);
        }
        if (dzPrice !== null) {
            $('#dz_price' + rowId).val(dzPrice);
        }

        var $tr = $(this);
        $tr.css('transition', 'background-color 0.4s ease');
        $tr.css('background-color', '#d4edda');
        setTimeout(function () {
            $tr.css('background-color', '');
        }, 1200);

        updatedCount++;
    });

    updateGrandTotal();

    if (typeof toastr !== 'undefined') {
        toastr.success('Applied prices to ' + updatedCount + ' variants successfully!');
    }
}

function copyRowToAllVariants(rowId, productId) {
    var tPriceVal = $('#t_price' + rowId).val();
    var rPriceVal = $('#retail_price' + rowId).val();
    var dzPriceVal = $('#dz_price' + rowId).val();

    var hasTPrice = tPriceVal !== '' && !isNaN(parseFloat(tPriceVal));
    var hasRPrice = rPriceVal !== '' && !isNaN(parseFloat(rPriceVal));
    var hasDzPrice = dzPriceVal !== '' && !isNaN(parseFloat(dzPriceVal));

    if (!hasTPrice && !hasRPrice && !hasDzPrice) {
        alert('Please enter at least one price in this row first.');
        return;
    }

    var tPrice = hasTPrice ? parseFloat(tPriceVal) : null;
    var rPrice = hasRPrice ? parseFloat(rPriceVal) : null;
    var dzPrice = hasDzPrice ? parseFloat(dzPriceVal) : null;

    if (tPrice !== null) $('#batch_t_price_' + productId).val(tPrice);
    if (rPrice !== null) $('#batch_r_price_' + productId).val(rPrice);
    if (dzPrice !== null) $('#batch_dz_price_' + productId).val(dzPrice);

    var updatedCount = 0;
    $('tr[data-product-id="' + productId + '"]').each(function () {
        var targetRowId = $(this).data('row-id');

        if (tPrice !== null) {
            $('#t_price' + targetRowId).val(tPrice);
            updateTradePrice(targetRowId);
        }
        if (rPrice !== null) {
            $('#retail_price' + targetRowId).val(rPrice);
        }
        if (dzPrice !== null) {
            $('#dz_price' + targetRowId).val(dzPrice);
        }

        var $tr = $(this);
        $tr.css('transition', 'background-color 0.4s ease');
        $tr.css('background-color', '#d4edda');
        setTimeout(function () {
            $tr.css('background-color', '');
        }, 1200);

        updatedCount++;
    });

    updateGrandTotal();

    if (typeof toastr !== 'undefined') {
        toastr.success('Copied prices to all ' + updatedCount + ' variants of this product!');
    }
}
