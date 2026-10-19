<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Payment Reminder</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #243042; font-size: 12px; line-height: 1.5; }
        h1 { color: #174b7a; font-size: 22px; margin-bottom: 4px; }
        .muted { color: #667085; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #d7dde5; padding: 9px; text-align: left; }
        th { width: 32%; background: #f1f4f8; }
        .balance { color: #a61b1b; font-weight: bold; font-size: 16px; }
        .footer { margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Customer Payment Reminder</h1>
    <div class="muted">Ejaz Sports ERP</div>
    <table>
        <tr><th>Customer</th><td>{{ $customer ? trim($customer->first_name . ' ' . $customer->last_name) : 'Customer' }}</td></tr>
        <tr><th>Phone</th><td>{{ $customer && $customer->phone_number ? $customer->phone_number : '—' }}</td></tr>
        <tr><th>Email</th><td>{{ $customer && $customer->email ? $customer->email : '—' }}</td></tr>
        <tr><th>City</th><td>{{ $customer && $customer->city ? $customer->city : '—' }}</td></tr>
        <tr><th>Reminder list</th><td>{{ $item->reminderList->title }}</td></tr>
        <tr><th>Reminder date</th><td>{{ \Carbon\Carbon::parse($item->reminderList->reminder_date)->format('d M Y') }}</td></tr>
        <tr><th>Reminder status</th><td>{{ $item->status_label }}</td></tr>
        <tr><th>Balance on this reminder list</th><td class="balance">Rs. {{ number_format($item->snapshot_balance, 2) }}</td></tr>
        @if($customer)
            <tr><th>Current outstanding balance</th><td class="balance">Rs. {{ number_format($customer->remaining_balance, 2) }}</td></tr>
        @endif
        @if($item->remarks)
            <tr><th>Customer note</th><td>{{ $item->remarks }}</td></tr>
        @endif
        @if($item->reminderList->notes)
            <tr><th>List instructions</th><td>{{ $item->reminderList->notes }}</td></tr>
        @endif
    </table>
    <p class="footer">Please contact Ejaz Sports if you have already made this payment or have any questions.</p>
</body>
</html>
