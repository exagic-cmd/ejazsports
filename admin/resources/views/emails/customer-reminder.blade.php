<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; color: #243042; line-height: 1.6;">
    <h2>Payment Reminder</h2>
    <p>Dear {{ $customer->first_name }}{{ $customer->last_name ? ' ' . $customer->last_name : '' }},</p>
    <p>This is a reminder about your account with Ejaz Sports. The details recorded for this reminder are below.</p>
    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; border-color: #dddddd;">
        <tr><th align="left">Reminder list</th><td>{{ $item->reminderList->title }}</td></tr>
        <tr><th align="left">Date</th><td>{{ \Carbon\Carbon::parse($item->reminderList->reminder_date)->format('d M Y') }}</td></tr>
        <tr><th align="left">Customer</th><td>{{ $customer->first_name }} {{ $customer->last_name }}</td></tr>
        <tr><th align="left">Phone</th><td>{{ $customer->phone_number ?: '—' }}</td></tr>
        <tr><th align="left">Email</th><td>{{ $customer->email ?: '—' }}</td></tr>
        <tr><th align="left">City</th><td>{{ $customer->city ?: '—' }}</td></tr>
        <tr><th align="left">Reminder status</th><td>{{ $item->status_label }}</td></tr>
        <tr><th align="left">Balance on this reminder list</th><td><strong>Rs. {{ number_format($item->snapshot_balance, 2) }}</strong></td></tr>
        <tr><th align="left">Current outstanding balance</th><td><strong>Rs. {{ number_format($customer->remaining_balance, 2) }}</strong></td></tr>
        @if($item->remarks)
            <tr><th align="left">Reminder note</th><td>{{ $item->remarks }}</td></tr>
        @endif
    </table>
    @if($item->reminderList->notes)
        <p><strong>List instructions:</strong> {{ $item->reminderList->notes }}</p>
    @endif
    <p>Please contact us if you have already made this payment or need to discuss it.</p>
    <p>Regards,<br>Ejaz Sports</p>
</body>
</html>
