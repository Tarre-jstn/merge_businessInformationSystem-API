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
				background: #8B0000;
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

            .headingTotal {
				background: #8B0000;
				border-bottom: 13px solid #ddd;
				font-weight: bold;
                color: #ffffff;
                
			}

		</style>
	</head>

    <body>
    <div class="invoice-box">
    <table width="100%" style="border-collapse: collapse;">
        <tr>
            <td class="title" style="text-align: left;">
                <img src="{{ asset('path_to_logo') }}" style="width:140px" />
            </td>
            <td>&nbsp;</td> <!-- Non-breaking space for empty cell -->
            <td style="text-align: right;">

            </td>
        </tr>
        

        <tr> <!-- Optional height to create spacing -->
            <table style="margin-bottom:5px">
                <td colspan="3"></td> <!-- Empty row for spacing -->
            </table>    

        </tr>

        <tr>
            <td style="text-align: left; font-weight: 700; font-size: 28px;">
                FINANCE SUMMARY 
            </td>
            <td>&nbsp;</td> <!-- Empty cell placeholder -->
            <td style="text-align: right; font-size: 18px;">
                <span style="font-weight: bold">Date from:</span> {{ $startDate->format('m-d-Y') }} <span style="font-weight: bold">to:</span> {{ $endDate->format('m-d-Y') }}
            </td>
        </tr>




        @php
            // Group finances by category
            $financesByCategory = $finance->groupBy('category');

            // Reorder the collection to place 'income' and 'expense' categories first
            $orderedCategories = ['income', 'expense'];
            $financesByCategory = $financesByCategory->sortBy(function($group, $category) use ($orderedCategories) {
                // Sort by the position of the category in $orderedCategories, or a high number for other categories
                return array_search($category, $orderedCategories) !== false ? array_search($category, $orderedCategories) : count($orderedCategories) + 1;
            });
        @endphp

        @foreach ($financesByCategory as $category => $financeGroup)
        <tr>
            <td colspan="3"> <!-- Use colspan to span the full width -->
                <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 20px">
                    <!-- Header Row with consistent colspan -->
                    <tr style="text-align: center" class="heading">
                        <td style="width: 15%; text-align: center">Date</td>
                        <td style="width: 40%; text-align: center">Finance Description</td>
                        <td style="width: 20%; text-align: center">Category</td>
                        <td style="width: 25%; text-align: center">Amount</td>
                    </tr>

                    <tbody>
                        @foreach ($financeGroup as $finances)
                        <tr class="item" style="border-bottom: 1px solid rgba(211, 211, 211, 0.5); text-align: center;">
                            <td style="align-items:center; vertical-align: middle; text-align: center">{{ $finances->date }}</td>
                            <td style="align-items:center; vertical-align: middle; text-align: center">{{ $finances->description }}</td>
                            <td style="align-items:center; vertical-align: middle;">
                                <span style="font-size: 13px; border-radius: 30px; color: white; padding: 5px; padding-left: 9px; padding-right: 7px;
                                background-color: {{ $finances->category == 'income' ? 'green' : ($finances->category == 'expense' ? 'darkblue' : 'darkgoldenrod') }};">
                                    {{ ucfirst($finances->category) }}
                                </span>
                            </td>
                            <td style="align-items:center; vertical-align: middle;">
                                PHP {{ number_format($finances->amount, 2) ?? 'N/A' }}
                            </td>
                        </tr>
                        @endforeach


                        @php
                            // Calculate the total amount for this category
                            $totalAmount = $financeGroup->sum('amount');
                        @endphp
                        <!-- Total row for the category -->
                        <tr style="text-align: right" >
                            <td colspan="2"></td>
                            <td style="width: 20%; text-align: center"><strong>Total Amount</strong></td>
                            <td style="width: 20%; text-align: center"><strong>PHP {{ number_format($totalAmount, 2) }}</strong></td>
                        </tr>
                    </tbody>



                </table>
            </td>
        </tr>
        @endforeach

</table>




</div>
</body>
</html>