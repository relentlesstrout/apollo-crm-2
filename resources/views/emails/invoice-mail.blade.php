<p>Hi {{ $invoice->user->name }},</p>

<p>You have a new invoice from Apollo Window Cleaners.</p>

<p><strong>{{ $invoice->description }}</strong> — {{ $invoice->formattedAmount() }}</p>

<p>
    <a href="{{ $checkoutUrl }}">Pay Now</a>
</p>

<p>This link will expire in 30 days. If you have any questions, please get in touch.</p>

<p>Thanks,<br>Apollo Window Cleaners</p>
