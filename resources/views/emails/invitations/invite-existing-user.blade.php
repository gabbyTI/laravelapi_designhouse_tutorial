@component('mail::message')
# Hi

You have been invited to join the team
**{{$invitation->team->name}}**.
Because you are a registered member, you can accept or reject the
invitation in your [Team management console]({{$url}}).

@component('mail::button', ['url' => $url])
Go to Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
