<div data-table-node
    data-table-id="{{ $table->id }}"
    data-move-url="{{ route('pos.tables.move', $table) }}"
    data-open-url="{{ route('pos.open', $table) }}"
    data-reserve-url="{{ route('pos.tables.reserve', $table) }}"
    data-cancel-reserve-url="{{ route('pos.tables.reservations.cancel', $table) }}"
    data-table-name="{{ $table->name }}"
    data-status="{{ $table->status }}"
    data-has-reservation="{{ $table->activeReservation ? 1 : 0 }}"
    class="group absolute flex flex-col items-center gap-1 cursor-grab touch-none select-none active:cursor-grabbing"
    style="left: {{ $table->pos_x ?? 40 }}px; top: {{ $table->pos_y ?? 40 }}px;"
    title="Arrastrar para mover · clic para opciones">
    @php
        $shapeWrap = match ($table->shape) {
            'round' => 'h-16 w-16 rounded-full',
            'rectangular' => 'h-12 w-24 rounded-lg',
            default => 'h-16 w-16 rounded-lg',
        };
        $status = match ($table->status) {
            'occupied' => ['label' => 'Ocupada', 'border' => 'border-amber-500', 'text' => 'text-amber-600', 'dot' => 'bg-amber-500'],
            'reserved' => ['label' => 'Reservada', 'border' => 'border-sky-500', 'text' => 'text-sky-600', 'dot' => 'bg-sky-500'],
            default => ['label' => 'Disponible', 'border' => 'border-emerald-500', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-500'],
        };
        $reservation = $table->activeReservation;
    @endphp

    {{-- Líneas de la mesa (contorno) --}}
    <div
        class="{{ $shapeWrap }} flex items-center justify-center border-2 {{ $status['border'] }} border-dashed bg-white/60 transition group-hover:border-solid">
        <span class="px-1 text-center text-sm font-semibold {{ $status['text'] }}">{{ $table->name }}</span>
    </div>

    @if ($reservation)
        <div class="max-w-[10rem] rounded-md bg-sky-50 px-2 py-0.5 text-center text-[11px] leading-tight text-sky-700 ring-1 ring-sky-200">
            {{ $reservation->customer_name }} · {{ $reservation->people_count }} pers.
        </div>
    @endif
</div>