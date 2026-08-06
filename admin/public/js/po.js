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
    
    rQtyId = '#r_qty' + id;
    tTPId = '#total_t_price' + id;
    tNPId = '#total_net_price' + id;
    tDId = '#discount' + id;
    tGSTId = '#gst' + id;
    tPId = '#t_price' + id;

    receivedQty = parseInt($(rQtyId).val());
    if(receivedQty) {
        tPrice = parseFloat($(tPId).val());
        if(tPrice >= 0) {
            $(tTPId).val(receivedQty * tPrice);
        }
    }



    $(tNPId).val((tPrice * receivedQty) - parseFloat($(tDId).val()) + parseFloat($(tGSTId).val()));

    updateGrandTotal();
}

function updateNetPrice(id) {

    rQtyId = '#r_qty' + id;
    tTPId = '#total_t_price' + id;
    tNPId = '#total_net_price' + id;
    tDId = '#discount' + id;
    dPId = '#discount_p' + id;
    tGSTId = '#gst' + id;
    tPId = '#t_price' + id;

    receivedQty = parseInt($(rQtyId).val());
    if(receivedQty) {
        totalTPrice = parseFloat($(tTPId).val());
        if(totalTPrice >= 0) {
            $(tPId).val( Math.round(totalTPrice / receivedQty , 1));
        }
    }

    $(dPId).val(Math.round( ( ( ($(tDId).val() ) * 100) / (totalTPrice)  ),1) );

    $(tNPId).val(totalTPrice - parseFloat($(tDId).val()) + parseFloat($(tGSTId).val()));

    updateGrandTotal();

}

function updateGrandTotal() {

    var x = document.getElementsByClassName("total-trade-price");
    var i;var trade = 0;
    var inputs = $(".total-trade-price");
    for (i = 0; i < x.length; i++) {
        trade += parseInt($(inputs[i]).val());
    }

    var x = document.getElementsByClassName("total-discount");
    var i;var discount = 0;
    var inputs = $(".total-discount");
    for (i = 0; i < x.length; i++) {
        discount += parseInt($(inputs[i]).val());
    }

    var x = document.getElementsByClassName("total-gst");
    var i;var tax = 0;
    var inputs = $(".total-gst");
    for (i = 0; i < x.length; i++) {
        tax += parseInt($(inputs[i]).val());
    }
    
    var x = document.getElementsByClassName("qty");
    var i;var qty = 0;
    var inputs = $(".qty");
    for (i = 0; i < x.length; i++) {
        qty += parseInt($(inputs[i]).val());
    }
    
    $('#total_qty').val(qty);

    $('#gross_amount').val(trade);
    $('#g_discount').val(discount);
    $('#g_tax').val(tax);
    
    packing_charges = parseInt($('#packing_charges').val());
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
