<p>You've been invited to join Apollo Window Cleaning as a {{ $invitation->role }}.</p>

<p>
    <a href="{{ route('register.show', $invitation->token) }}">
        Accept your invitation
    </a>
</p>

<p>This link is tied to {{ $invitation->email }}.</p>
