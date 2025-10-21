@component('mail::message')

# Hello {{ getRoleName($user->role_id) }} {{ ucfirst($user->firstname) }} {{ ucfirst($user->lastname) }},

A new event has just been created! 🎉

**Event Name:** {{ $event->name }}  
**Date:** {{ $event->implement_date ?? 'TBA' }}  
**Location:** {{ $event->location ?? 'TBA' }}  
**Description:**  
{{ $event->description ?? 'No description provided.' }}

{{-- @component('mail::button', ['url' => url('/events/' . $event->id)])
View Event
@endcomponent --}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent

