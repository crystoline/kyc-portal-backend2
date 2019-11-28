@component('mail::message')
# Hello {{ $approval->verification->verifiedBy->first_name }},

The verification for agent "{{ $approval->verification->first_name }}",  Agent CODE : {{ $approval->verification->agent->code }},
has been {{ $approval->status_text }}.

@if($approval->comment)
# Comment
    {{ $approval->comment }}
@endif


Thank you,<br>
{{ config('app.name') }}
@endcomponent
