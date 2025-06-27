@component('mail::message')
# Invoice Approval Request

You have received multiple invoices for approval.

@foreach ($invoices as $item)
---

### Invoice #{{ $item['invoice']->id }}

Amount: ₹{{ number_format($item['invoice']->amount, 2) }}
Date: {{ $item['invoice']->created_at->format('d M Y') }}

@component('mail::button', ['url' => $item['approveUrl']])
Approve
@endcomponent

@component('mail::button', ['url' => $item['rejectUrl'], 'color' => 'red'])
Reject
@endcomponent

@endforeach

Thanks,
{{ config('app.name') }}
@endcomponent
