<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Terms & Conditions - Hira Rentals</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 40px 20px;
        }
        .doc{
            background: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 50px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }
        .doc-header{
            text-align: center;
            border-bottom: 3px solid #E8622A;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .doc-header h1{
            color: #E8622A;
            margin: 0 0 8px 0;
            font-size: 24px;
        }
        .doc-header p{
            color: #888;
            font-size: 13px;
            margin: 0;
        }
        .doc h2{
            color: #333;
            font-size: 16px;
            margin: 28px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #eee;
        }
        .doc p, .doc li{
            color: #444;
            font-size: 14px;
            line-height: 1.8;
        }
        .doc ol{ padding-left: 20px; }
        .doc li{ margin-bottom: 14px; }
        .doc li strong{ color: #222; }
        .highlight-box{
            background: #fff5f0;
            border: 1px solid #E8622A;
            border-radius: 8px;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .highlight-box strong{ color: #E8622A; }
        .back-link{
            display: inline-block;
            margin-bottom: 20px;
            color: #E8622A;
            text-decoration: none;
            font-size: 14px;
        }
        .print-btn{
            background: #E8622A;
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 30px;
        }
        @media print{
            .back-link, .print-btn{ display: none; }
            body{ background: #fff; }
            .doc{ box-shadow: none; border: none; }
        }
    </style>
</head>
<body>

    <div style="max-width:800px; margin:0 auto;">
        <a href="javascript:window.close();" class="back-link" onclick="if(window.history.length>1){history.back(); return false;}">← Back</a>
    </div>

    <div class="doc">
        <div class="doc-header">
            <h1>🏠 Hira Rentals — Lease Terms &amp; Conditions</h1>
            <p>Please read carefully before signing any lease agreement</p>
        </div>

        <div class="highlight-box">
            <strong>Important:</strong> By signing a lease contract on this platform, the tenant confirms
            they have read, understood, and agree to all terms listed in this document.
        </div>

        <h2>1. Security Deposit</h2>
        <p>The tenant shall pay a security deposit (equal to one month's rent, unless otherwise agreed) before occupying the property. This deposit is refundable at the end of the tenancy, minus any deductions for damages, unpaid rent, or unpaid utility bills.</p>

        <h2>2. Rent Payment</h2>
        <p>Monthly rent must be paid in advance, on or before the 5th of each month. Late payment beyond 7 days may incur a late fee as agreed between tenant and owner, and repeated late payment may be grounds for termination.</p>

        <h2>3. Notice Before Vacating</h2>
        <p>The tenant must inform the manager/owner in writing at least <strong>2 months (60 days) before</strong> vacating the property. Vacating without this notice period may result in forfeiture of part or all of the security deposit.</p>

        <h2>4. Utility Bills</h2>
        <p>The tenant is responsible for paying electricity, gas, water, and internet bills for the duration of their tenancy, unless otherwise stated in the lease. All bills must be cleared before vacating.</p>

        <h2>5. Property Use</h2>
        <p>The property must be used only for residential purposes (unless a commercial lease is separately agreed) and must not be used for any illegal activity.</p>

        <h2>6. Maintenance &amp; Repairs</h2>
        <p>The tenant must maintain the property in good condition and promptly report any damage or maintenance issues. Minor repairs (e.g. light fixtures, taps) are the tenant's responsibility; major structural repairs are the owner's responsibility.</p>

        <h2>7. Alterations</h2>
        <p>No structural changes, renovations, or permanent fixtures may be added to the property without prior written consent from the owner/manager.</p>

        <h2>8. Subletting</h2>
        <p>The property may not be sublet, shared with unrelated parties, or transferred to another tenant without written approval from the owner/manager.</p>

        <h2>9. Right of Inspection</h2>
        <p>The owner/manager reserves the right to inspect the property with at least 24 hours' prior notice, except in emergencies.</p>

        <h2>10. Damages</h2>
        <p>The tenant is liable for any damage to the property, fixtures, or fittings beyond normal wear and tear, and the cost of repair may be deducted from the security deposit.</p>

        <h2>11. Lease Duration &amp; Renewal</h2>
        <p>The lease agreement is valid for the start and end dates specified in the contract. Any renewal or extension must be discussed and agreed in writing before the end date.</p>

        <h2>12. Early Termination</h2>
        <p>If either party wishes to end the agreement before the end date, the 2-month notice period (Clause 3) still applies, and any early-termination terms agreed separately will be honored.</p>

        <h2>13. Termination for Cause</h2>
        <p>The owner/manager may terminate the agreement immediately, without the standard notice period, in cases of non-payment of rent, illegal activity, or serious damage to the property.</p>

        <h2>14. Dispute Resolution</h2>
        <p>Any disputes arising from this agreement will first be addressed through direct communication between tenant and owner/manager, and if unresolved, through the appropriate local rent tribunal/authority.</p>

        <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>

</body>
</html>