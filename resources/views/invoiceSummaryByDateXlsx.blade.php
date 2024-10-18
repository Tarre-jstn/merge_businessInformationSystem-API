
<table>
    <tr style="text-align: center" class="heading">
        <td bgcolor="black">Invoice ID</td>
        <td bgcolor="black">Customer Name</td>
        <td bgcolor="black">Payment Type</td>
        <td bgcolor="black">Total Amount Due</td>
        <td bgcolor="black">Status</td>
        <td bgcolor="black">Date</td>
        <td bgcolor="black">Terms</td>
        <td bgcolor="black">Tax</td>
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
                <td style="align-items:center; vertical-align: middle;">PHP{{ number_format($computation->tax,2) ?? 'N/A' }}</td> <!-- Use the computation's tax -->
                
            </tr>
        @endforeach
    </tbody>
</table>



