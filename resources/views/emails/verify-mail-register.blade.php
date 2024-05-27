<x-mail::message>
    # Introduction
    <p>Hello {{ $user->name }}</p>
    <x-mail::button :url="$url" color="success">
        Verify Email
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
