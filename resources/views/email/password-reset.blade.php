@component('mail::message')
# Hello {{ $user->first_name }}

Your request for reset on {{ config('app.name') }}.

Here is your password reset code

    {{$code}}

kindly enter the code on the app to proceed
@if(isset($return_url))
Or press the button below to proceed
@component('mail::button', ['url' => $return_url.'?reset_code='.$code])
Reset Now
@endcomponent
@endif

Thank you,<br>
{{ config('app.name') }}
@endcomponent
