<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>A simple, clean, and responsive HTML invoice template</title>

		<style>
			.invoice-box {
				max-width: 1300px;
				margin: auto;
				padding: 10px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #1F2937;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
                color: #ffffff;
                
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}

            tr.item:nth-child(even) {
            background-color: #fcf2f2;
            }
            tr.item:nth-child(odd) {
                background-color: #ffffff;
            }

		</style>
	</head>

    <body>
    <div class="invoice-box">
    <table width="100%" style="border-collapse: collapse;">
        <tr>
            <td class="title" style="text-align: left;">
                <img src="storage/business_logos/{{ $businessImage }}" style="width:140px" />
            </td>
            <td>&nbsp;</td> <!-- Non-breaking space for empty cell -->
            <td style="text-align: right;">
                <p style="margin-bottom:0px; font-weight: bold; font-size: 37px">{{ $invoice->last()->business_Name }}</p>
                Address: {{ $invoice->last()->business_Address }}<br />
                TIN: {{ $invoice->last()->business_TIN ?? 'N/A'}}
            </td>
        </tr>
        

        <tr> <!-- Optional height to create spacing -->
            <table style="margin-bottom:5px">
                <td colspan="3"></td> <!-- Empty row for spacing -->
            </table>    

        </tr>

        <tr>
            <td style="text-align: left; font-weight: 700; font-size: 28px;">
                SALES INVOICE SUMMARY 
            </td>
            <td>&nbsp;</td> <!-- Empty cell placeholder -->
            <td style="text-align: right; font-size: 18px;">
                <span style="font-weight: bold">Date from:</span> {{ $startDate->format('m-d-Y') }} <span style="font-weight: bold">to:</span> {{ $endDate->format('m-d-Y') }}
            </td>
        </tr>

        <tr>
            <td colspan="3"> <!-- Use colspan to span the full width -->
                <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                    <tr style="text-align: center" class="heading">
                        <td style="text-align: center">Invoice ID</td>
                        <td style="text-align: center">Customer Name</td>
                        <td style="text-align: center">Payment Type</td>
                        <td style="text-align: center">Total Amount Due</td>
                        <td style="text-align: center">Status</td>
                        <td style="text-align: center">Date</td>
                        <td style="text-align: center">Terms</td>
                        <td style="text-align: center">Tax</td>
                    </tr>

                    
                    <tbody>
                        @foreach ($invoice as $invoices)
                            @php
                            // Find the corresponding computation for this invoice
                            $computation = $invoice_computations->firstWhere('invoice_system_id', $invoices->invoice_system_id);
                            @endphp
                            <tr class="item" style="border-bottom: 1px solid rgba(211, 211, 211, 0.5); text-align: center;">
                                <td style="align-items:center; vertical-align: middle; text-align: left">{{ $invoices->invoice_id }}</td>
                                <td style="align-items:center; vertical-align: middle;width:200px; text-align: center">{{ $invoices->customer_Name }}</td>
                                <td style="align-items:center; vertical-align: middle;">
                                    <span style="font-size: 13px; border-radius: 30px; color: white; padding: 5px; padding-left: 9px;  padding-right: 10px ;
                                    background-color: 
                                        {{ $invoices->payment_Type == 'cash' ? 'green' : 
                                        ($invoices->payment_Type == 'check' ? 'darkblue' : 
                                        ($invoices->payment_Type == 'online transaction' ? 'darkgoldenrod' : 'inherit')) }};">
                                    {{ ucfirst($invoices->payment_Type) }}</span></td>
                                <td style="align-items:center; vertical-align: middle;">PHP {{ number_format($computation->total_Amount_Due,2) ?? 'N/A' }}</td> <!-- Use the computation's total amount -->
                                
                                <td style="align-items:center; vertical-align: middle;">
                                <span style="font-size: 13px; border-radius: 30px; color: white; padding: 5px; padding-left: 9px;  padding-right: 10px ;
                                    background-color: 
                                        {{ $invoices->status == 'paid' ? 'green' : 
                                        ($invoices->status == 'unpaid' ? 'darkred' : 
                                        ($invoices->status == 'pending refund' ? 'darkorange' : 
                                        ($invoices->status == 'refunded' ? 'green' :
                                        ($invoices->status == 'partially paid' ? 'darkorange' : 'inherit')))) }};">
                                    {{ ucfirst($invoices->status) }}</span></td>

                                <td style="align-items:center; vertical-align: middle;">{{ $invoices->date }}</td>
                                <td style="align-items:center; vertical-align: middle;">{{ $invoices->terms ?? 'N/A' }}</td>
                                <td style="align-items:center; vertical-align: middle;">PHP {{ number_format($computation->tax,2) ?? 'N/A' }}</td> <!-- Use the computation's tax -->
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
</table>




</div>
</body>
</html>