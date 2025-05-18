@component('mail::message')
# Your Turn Coming Up Soon

Hi {{ $booking->client->name }},

Your turn is coming up soon at the barber shop. You're second in line.

**Booking Details:**
- Reference: {{ $booking->reference }}
- Service: {{ $booking->service->name }}

Please make sure you're at the shop and ready.

Thanks,<br>
{{ config('app.name') }}
@endcomponent