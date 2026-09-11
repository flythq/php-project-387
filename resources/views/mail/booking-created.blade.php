<x-mail::message>
# Запись на звонок создана

@unless ($forHost)
Здравствуйте, {{ $booking->invitee_name }}!
Вы записались на 30-минутный звонок.
@else
Новая запись на звонок.
Гость: {{ $booking->invitee_name }} ({{ $booking->invitee_email }}).
@endunless

**Когда:** {{ $booking->slot_start_at->format('d.m.Y H:i') }} (30 минут)

Запись создана и зафиксирована. Подтверждения организатором не требуется.

<x-mail::button :url="config('app.url')">
Перейти в календарь
</x-mail::button>

С уважением,<br>
Календарь звонков
</x-mail::message>
