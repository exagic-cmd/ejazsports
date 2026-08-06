<div class="table-responsive">
    <h3>{{$customer->first_name}} Ledger</h3>
    <h5>Date Range: {{ date('d-m-Y', strtotime(explode('-', $date_range)[0])) }} to {{ date('d-m-Y', strtotime(explode('-', $date_range)[1])) }}</h5>
    <br>
    <table id="myTable1" class="table table-hover">
    <thead>
    <tr>
        <th>#Sr</th>
        <th scope="col">Date</th>
        <th scope="col">Order # / Voucher #</th>
        <th scope="col">Narration</th>
        <th scope="col">Debit</th>
        <th scope="col">Credit</th>
        <th scope="col">Balance</th>
    </tr>
    </thead>
    <tbody>
    <?php $sr = 1; $balance = $debit = $credit = 0; ?>
    @foreach($ledger as $r)
        <tr>
            <td>{{$sr++}}</td>
             <td>
                            @if($r['date'])
                            {{date('d-m-Y',strtotime($r['date']))}}
                            @endif</td>

            @if($r['link'] == 'order')
            <td><a href="{{route('orders.view.customer', $r['id'])}}" target="_blank">
                <b>{{$r['invoice/voucher']}}</b>
                </a></td>
            @elseif($r['link'] == 'return')
            <td><a href="{{route('orders.return', ['order_id' => $r['id']])}}" target="_blank">
                <b>{{$r['invoice/voucher']}}</b>
                </a></td>
            @elseif($r['link'] == 'payment')
            <td><a href="{{route('customer-payments.show', $r['id'])}}" target="_blank">
                <b>{{$r['invoice/voucher']}}</b>
                </a></td>
            @else
            <td><b>{{$r['invoice/voucher']}}</b></td>
            @endif

            <td>{{$r['narration']}}</td>
            <td>{{$r['debit'] ? number_format($r['debit']) : ''}}
                <?php $debit += $r['debit']; $balance += $r['debit']; ?>
            </td>
            <td>{{number_format($r['credit'])}}
                <?php $credit += $r['credit']; $balance += ($r['credit'] * -1); ?>
            </td>
            @if($balance < 0)
            <td>({{number_format(abs($balance))}})</td>
            @else
            <td>{{number_format($balance)}}</td>
            @endif
        </tr>
    @endforeach

    <tr style="border-top: 1px solid #0a0a18;">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><b style="border-bottom: double;">{{number_format($debit)}}</b></td>
        <td><b style="border-bottom: double;">{{number_format($credit)}}</b></td>
        @if($balance < 0)
        <td><b style="border-bottom: double;">({{number_format(abs($balance))}})</b></td>
        @else
        <td><b style="border-bottom: double;">{{number_format($balance)}}</b></td>
        @endif
    </tr>
    </tbody>
</table>
</div>