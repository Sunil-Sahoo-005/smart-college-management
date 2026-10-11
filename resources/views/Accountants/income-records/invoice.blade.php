<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Income Receipt #{{ $incomeRecord->id }}</title>
    <style>
        body { font-family: 'Helvetica Neue', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-details { margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .table th { background: #f8f9fa; font-weight: bold; }
        .amount-total { font-size: 18px; font-weight: bold; color: #28a745; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>INCOME RECEIPT</h2>
        <p><strong>Receipt #{{ $incomeRecord->id }}</strong></p>
        <p>Date: {{ $incomeRecord->date->format('d M Y') }}</p>
    </div>

    <div class="invoice-details">
        <table class="table">
            <tr>
                <th>Income Head</th>
                <td>{{ $incomeRecord->incomeHead->name }}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td class="amount-total">₹{{ number_format($incomeRecord->amount, 2) }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $incomeRecord->description ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Transaction Date</th>
                <td>{{ $incomeRecord->date->format('d M Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('d M Y H:i') }} | Authorized by System</p>
    </div>
</body>
</html>
