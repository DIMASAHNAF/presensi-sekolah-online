@props([
    'status' => 'hadir',
    'lateMinutes' => 0,
    'label' => null,
])

@php
    $normalized = strtolower(trim((string) $status));
    
    switch ($normalized) {
        case 'hadir':
        case 'ontime':
        case 'present':
            $classes = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
            $defaultLabel = 'Hadir';
            break;
            
        case 'terlambat':
        case 'late':
            $classes = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
            $defaultLabel = $lateMinutes > 0 ? "Terlambat ({$lateMinutes}m)" : 'Terlambat';
            break;
            
        case 'izin':
        case 'permission':
            $classes = 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20';
            $defaultLabel = 'Izin';
            break;

        case 'sakit':
        case 'sick':
            $classes = 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20';
            $defaultLabel = 'Sakit';
            break;

        case 'dispensasi':
        case 'dispensation':
            $classes = 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20';
            $defaultLabel = 'Dispensasi';
            break;
            
        case 'alpa':
        case 'alfa':
        case 'absent':
        default:
            $classes = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
            $defaultLabel = 'Alfa';
            break;
    }

    $displayText = $label ?? $defaultLabel;
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium font-mono tabular-nums {$classes}"]) }}>
    {{ $displayText }}
</span>
