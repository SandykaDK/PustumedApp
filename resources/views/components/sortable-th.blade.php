@props(['route' => null])

@php
    $isActive = request('sort') === $column;
    $direction = $isActive && request('direction') === 'asc' ? 'desc' : 'asc';

    $params = array_merge(request()->all(), [
        'sort' => $column,
        'direction' => $direction
    ]);

    $url = $route ? route($route, $params) : url()->current() . '?' . http_build_query($params);
@endphp

<th>
    <a href="{{ $url }}" class="sort-link" aria-sort="{{ $isActive ? request('direction') : 'none' }}">
        <span>{{ $label }}</span>

        @if ($isActive)
            @if (request('direction') === 'asc')
                {{-- UP --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="sort-icon"
                     fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                </svg>
            @else
                {{-- DOWN --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="sort-icon"
                     fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                </svg>
            @endif
        @endif
    </a>
</th>
