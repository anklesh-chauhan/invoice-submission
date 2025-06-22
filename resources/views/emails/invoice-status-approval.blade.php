<x-mail::message>
# Invoice Approval Required

You have been requested to approve or reject the following invoice:

- **Invoice Number**: {{ $invoice->invoice_number }}
- **Vendor**: {{ $invoice->vendor->VendorName ?? 'N/A' }}
- **Amount**: ₹{{ number_format($invoice->amount, 2) }}
- **Date**: {{ $invoice->invoice_date->format('d M, Y') }}

<x-mail::button :url="$approveUrl" color="success">
Accept Invoice
</x-mail::button>

<x-mail::button :url="$rejectUrl" color="error">
Reject Invoice
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
