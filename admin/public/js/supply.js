function updateQty() {

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
