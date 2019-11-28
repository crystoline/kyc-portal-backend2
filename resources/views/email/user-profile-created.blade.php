@component('mail::message')
# Hello {{ $user->first_name }},

Your profile has been created on {{ config('app.name') }}  as {{ in_array(substr($user->group->name, 0,1), ['a', 'e', 'i', 'o', 'u'])? 'an': 'a' }} {{ $user->group->name }}.

Kindly login on the dashboard  with the following credential.
    Username: {{ $user->email }}
    Password: {{ \App\Models\User::getStringPassword() }}

@if(!empty($host))
@component('mail::button', ['url' => $host])
Goto Dashboard
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
