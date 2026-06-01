<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $inv_data['invoice_id']; ?></title>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #555;
        }
        .btn-print {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        @media print {
            .btn-print { display: none; } /* Hide button on paper */
            .invoice-box { border: none; box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div style="text-align: center; border-bottom: 2px solid #eee; padding-bottom: 20px;">
        <h2>BLOOD BANK MANAGEMENT SYSTEM</h2>
        <p>Official Issuance Receipt</p>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <div>
            <h4>Billed To:</h4>
            <p><strong><?php echo htmlspecialchars($inv_data['fullName']); ?></strong><br>
            Contact: <?php echo htmlspecialchars($inv_data['phone']); ?></p>
        </div>
        <div style="text-align: right;">
            <h4>Invoice Info:</h4>
            <p><strong>Invoice #:</strong> <?php echo $inv_data['invoice_id']; ?><br>
            <strong>Date:</strong> <?php echo date('d-M-Y', strtotime($inv_data['invoice_date'])); ?></p>
        </div>
    </div>

    <table style="width: 100%; margin-top: 40px; border-collapse: collapse;">
        <thead>
            <tr style="background: #f9f9f9; border-bottom: 1px solid #eee;">
                <th style="text-align: left; padding: 10px;">Description</th>
                <th style="text-align: right; padding: 10px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px;">
                    <?php if($inv_data['invoice_type'] == 'Subscription'): ?>
                        <strong>Monthly Subscription Charge</strong><br>
                        <small>Billing Period: <?php echo date('F Y', strtotime($inv_data['billing_period'])); ?></small>
                    <?php else: ?>
                        Blood Group: <strong><?php echo htmlspecialchars($inv_data['bloodGroup']); ?></strong><br>
                        <small>Units: <?php echo $inv_data['units_requested']; ?> | Priority: <?php echo ($inv_data['is_urgent'] ? 'Urgent' : 'Normal'); ?></small>
                    <?php endif; ?>
                </td>
                <td style="text-align: right; padding: 10px;">Rs. <?php echo number_format($subtotal, 2); ?></td>
            </tr>
            
            <?php if($inv_data['invoice_type'] == 'Request'): ?>
            <tr>
                <td style="padding: 10px;">Processing & Lab Fee</td>
                <td style="text-align: right; padding: 10px;">Rs. <?php echo number_format($processing_fee, 2); ?></td>
            </tr>
            <?php endif; ?>

            <tr style="border-top: 2px solid #333; font-weight: bold; font-size: 1.1em;">
                <td style="padding: 10px;">TOTAL DUE</td>
                <td style="text-align: right; padding: 10px;">Rs. <?php echo number_format($final_total, 2); ?></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 60px; text-align: center; font-size: 12px; color: #888;">
        <p>This is a computer-generated receipt and does not require a physical signature.</p>
        <button onclick="window.print()" class="btn-print">Print Invoice</button>           <!-- js -->
    </div>
</div>

</body>
</html>