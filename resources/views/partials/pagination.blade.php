@if ($paginator->hasPages())
    <div class="mt-20 flex justify-center items-center gap-4" data-aos="fade-up">
        {{-- Nút Quay lại --}}
        @if ($paginator->onFirstPage())
            <span class="w-10 h-10 rounded-full border border-dark/20 flex items-center justify-center text-dark opacity-30 cursor-not-allowed">
                <i class="fas fa-chevron-left text-xs"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="w-10 h-10 rounded-full border border-dark/20 flex items-center justify-center text-dark hover:bg-gold hover:text-white hover:border-gold transition-all">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
        @endif

        {{-- Các số trang --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="text-gray-400">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-10 h-10 rounded-full bg-dark text-white flex items-center justify-center font-serif shadow-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-10 h-10 rounded-full border border-dark/20 flex items-center justify-center text-dark hover:bg-gold hover:text-white hover:border-gold transition-all font-serif">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Nút Tiếp theo --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="w-10 h-10 rounded-full border border-dark/20 flex items-center justify-center text-dark hover:bg-gold hover:text-white hover:border-gold transition-all">
                <i class="fas fa-chevron-right text-xs"></i>
            </a>
        @else
            <span class="w-10 h-10 rounded-full border border-dark/20 flex items-center justify-center text-dark opacity-30 cursor-not-allowed">
                <i class="fas fa-chevron-right text-xs"></i>
            </span>
        @endif
    </div>
@endif