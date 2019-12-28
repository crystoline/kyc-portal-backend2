@component('mail::message')
# Hello
@php
    /** @var \App\Models\VerificationPeriod $verificationPeriod */

@endphp
Please be informed that a new verification period {{ $verificationPeriod->title }} is starting  on {{ $verificationPeriod->date_start }}.

@if(empty($verificationPeriod->state_id) && empty($verificationPeriod->lga_id) && empty($verificationPeriod->territory_id))
    Taking place In all states, lgas and territories
@else
    Taking place in
    @if($verificationPeriod->state_id !== null)
        State: {{ $verificationPeriod->state->name }}
    @endif
    @if($verificationPeriod->lga_id !== null)
        Lga: {{ $verificationPeriod->lga->name }}
    @endif
    @if($verificationPeriod->territory_id !== null)
        Territory: {{ $verificationPeriod->territory->name }}
    @endif
@endif
Thanks,<br>
{{ config('app.name') }}
@endcomponent
