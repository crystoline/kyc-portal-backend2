@component('mail::message')
# Hello {{ $user->first_name }}

Your password reset was successful.

Thank you,<br>
{{ config('app.name') }}
@endcomponent
