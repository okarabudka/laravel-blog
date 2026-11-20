@props(['name', 'show' => false])

<div
    x-data="{ show: @js($show) }"
    x-init="$watch('show', value => document.body.classList.toggle('modal-open', value))"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="modal"
    style="display: {{ $show ? 'flex' : 'none' }};"
>
    <div class="modal-backdrop" x-on:click="show = false"></div>

    <div class="modal-panel" role="dialog" aria-modal="true">
        {{ $slot }}
    </div>
</div>
