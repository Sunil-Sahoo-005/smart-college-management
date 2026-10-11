<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Receipt #{{ $expenseRecord->id }}</title>
    <style>
        body { font-family: 'Helvetica Neue', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-details { margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .table th { background: #f8f9fa; font-weight: bold; }
        .amount-total { font-size: 18px; font-weight: bold; color: #dc3545; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>EXPENSE RECEIPT</h2>
        <p><strong>Receipt #{{ $expenseRecord->id }}</strong></p>
        <p>Date: {{ $expenseRecord->date->format('d M Y') }}</p>
    </div>

    <div class="invoice-details">
        <table class="table">
            <tr>
                <th>Expense Head</th>
                <td>{{ $expenseRecord->expenseHead->name }}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td class="amount-total">₹{{ number_format($expenseRecord->amount, 2) }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $expenseRecord->description ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('d M Y H:i') }} | Authorized by System</p>
    </div>
</body>
</html>
