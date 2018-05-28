@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
    # {{ $greeting }}
@else
    @if ($level == 'error')
        # 糟糕!
    @else
        # 您好!
    @endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
-金園排骨官方網站
@endif

{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
如果點擊 "{{ $actionText }}" 出現連結錯誤, 請嘗試複製此連結: [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endisset
@endcomponent
