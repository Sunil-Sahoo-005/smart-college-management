<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $feePayment->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            line-height: 1.4;
            color: #333;
        }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1e3a8a; padding-bottom: 20px; }
        .header h1 { color: #1e3a8a; font-size: 24px; margin-bottom: 5px; }
        .school-info { font-size: 14px; margin-bottom: 10px; }
        .receipt-badge { 
            display: inline-block; 
            background: #10b981; 
            color: white; 
            padding: 12px 30px; 
            font-size: 16px; 
            font-weight: bold; 
            border-radius: 8px; 
            margin: 20px 0; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 30px 0; }
        .detail-box { border: 2px solid #e5e7eb; padding: 20px; border-radius: 8px; }
        .detail-box h4 { color: #1e3a8a; margin-bottom: 15px; border-bottom: 2px solid #1e3a8a; padding-bottom: 8px; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .detail-label { font-weight: 600; }
        .detail-value { text-align: right; }
        .amount-highlight { font-size: 20px; font-weight: bold; color: #10b981; }
        table { width: 100%; border-collapse: collapse; margin: 25px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #d1d5db; }
        th { background: #f8fafc; font-weight: 600; color: #374151; }
        .total-row { background: #dbeafe !important; }
        .total-amount { font-size: 18px !important; font-weight: bold !important; color: #1e3a8a !important; text-align: right !important; }
        .footer { text-align: center; margin-top: 40px; padding-top: 25px; border-top: 3px solid #e5e7eb; }
        .thanks { font-size: 18px; font-weight: bold; color: #1e3a8a; margin-bottom: 10px; }
        .generated { font-size: 11px; color: #6b7280; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name', 'Your School') }}</h1>
            <div class="school-info">{{ Auth::user()->tenant->name ?? 'School Name' }}</div>
            <div class="receipt-badge">{{ strtoupper($feePayment->payment_method) }} RECEIPT</div>
        </div>

        <!-- Details -->
        <div class="details-grid">
            <div class="detail-box">
                <h4>Student Information</h4>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value">{{ $feePayment->student->first_name ?? '' }} {{ $feePayment->student->last_name ?? '' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Roll No:</span>
                    <span class="detail-value">{{ $feePayment->student->roll_no ?? $feePayment->student->roll_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Receipt No:</span>
                    <span class="detail-value text-primary">#{{ $feePayment->receipt_number }}</span>
                </div>
            </div>
            
            <div class="detail-box">
                <h4>Payment Information</h4>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ $feePayment->payment_date->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value amount-highlight">₹{{ number_format($feePayment->amount_paid, 2) }}</span>
                </div>
                @if($feePayment->transaction_id)
                <div class="detail-row">
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value">{{ $feePayment->transaction_id }}</span>
                </div>
                @endif
                @if($feePayment->gateway_name)
                <div class="detail-row">
                    <span class="detail-label">Bank/Gateway:</span>
                    <span class="detail-value">{{ $feePayment->gateway_name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Installment Table -->
        <table>
            <thead>
                <tr>
                    <th>Fee Category</th>
                    <th>Amount Paid</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if($feePayment->installment)
                <tr>
                    <td>{{ $feePayment->installment->feeCategory->name ?? 'General Fee' }}</td>
                    <td class="amount-highlight">₹{{ number_format($feePayment->installment->amount, 2) }}</td>
                    <td>{{ $feePayment->installment->due_date->format('d M Y') }}</td>
                    <td style="background: #d4edda; color: #155724; font-weight: bold;">PAID</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="3">TOTAL AMOUNT:</th>
                    <th class="total-amount">₹{{ number_format($feePayment->amount_paid, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div class="thanks">Thank you for your payment!</div>
            <p>This receipt serves as official payment confirmation</p>
            <div class="generated">
                Computer-generated on {{ now()->format('d M Y H:i:s') }}<br>
                No signature required
            </div>
        </div>
    </div>
</body>
</html>