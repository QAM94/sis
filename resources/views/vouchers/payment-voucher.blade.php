<!DOCTYPE html>
<html>
<head>
    <title>Payment Voucher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .voucher-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .header h3 {
            font-size: 16px;
            margin: 5px 0;
            color: #666;
        }

        .details {
            margin-bottom: 20px;
        }

        .details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .table-container {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .table-container th,
        .table-container td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table-container th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .fee-summary {
            text-align: right;
            margin-top: 20px;
        }

        .fee-summary p {
            margin: 5px 0;
            font-size: 14px;
        }

        .total-amount {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
<div class="voucher-container">
    <div class="header">
        <h1>Payment Voucher</h1>
        <h3>Voucher ID: {{ $voucher->id }}</h3>
    </div>

    <div class="details">
        <p><strong>Student Name:</strong> {{ $voucher->student->first_name }} {{ $voucher->student->last_name }}</p>
        <p><strong>Student ID:</strong> {{ $voucher->student->reg_no }}</p>
        <p><strong>Semester:</strong> {{ $voucher->semester->type }} {{ $voucher->semester->year }}</p>
        <p><strong>Generated On:</strong> {{ $voucher->created_at->format('F d, Y') }}</p>
    </div>

    <table class="table-container">
        <thead>
        <tr>
            <th>Description</th>
            <th>Amount ({{ config('app.currency', '$') }})</th>
        </tr>
        </thead>
        <tbody>
        @php
            $feeBreakdown = json_decode($voucher->fee_breakdown, true);
        @endphp
        @foreach ($feeBreakdown as $key => $value)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                <td>{{ number_format($value, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="fee-summary">
        <p><strong>Total Fee:</strong></p>
        <p class="total-amount">{{ config('app.currency', '$') }} {{ number_format($voucher->total_amount, 2) }}</p>
    </div>

    <div class="footer">
        <p>Thank you for your payment. Please contact the finance department for any queries.</p>
    </div>
</div>
</body>
</html>
