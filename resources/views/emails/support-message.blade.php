@component('mail::message')
# New Support Request

**From:** {{ $name }} ({{ $email }})

**Subject:** {{ $subject }}

**Message:**
{{ $userMessage }}

@component('mail::button', ['url' => 'mailto:'.$email])
    Reply to {{ $name }}
@endcomponent

@endcomponent
