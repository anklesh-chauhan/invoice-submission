@component('mail::message')
# Invoice Approval Request

You have received the following invoices for approval:

@foreach ($invoices as $invoice)
- **Invoice #{{ $invoice->invoice_number}}** — ₹{{ number_format($invoice->amount, 2) }} ({{ $invoice->invoice_date->format('d M Y') }})
@endforeach

---

@component('mail::button', ['url' => $approveUrl])
✅ Approve All
@endcomponent

@component('mail::button', ['url' => $rejectUrl, 'color' => 'red'])
❌ Reject All
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
