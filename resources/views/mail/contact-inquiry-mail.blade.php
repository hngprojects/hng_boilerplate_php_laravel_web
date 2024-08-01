<x-mail::message>
## Inquiry sent successfully, here is your message

Name: {{ $name }} <br>
Email: {{ $email }} <br>
Message: {{ $message }} <br>

Thanks,<br>
{{-- {{ config('app.name') }} --}}
Hng Boilerplate support team
</x-mail::message>
