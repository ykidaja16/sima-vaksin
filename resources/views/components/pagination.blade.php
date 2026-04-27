@if ($paginator->hasPages())
    @php
        // Generate pagination elements array
        $elements = [];
        $window = 2; // Number of pages to show on each side of current page
        
        // Always show first page
        if ($paginator->currentPage() > $window + 1) {
            $elements[] = 1;
            if ($paginator->currentPage() > $window + 2) {
                $elements[] = '...';
            }
        }
        
        // Calculate start and end of the window
        $start = max(1, $paginator->currentPage() - $window);
        $end = min($paginator->lastPage(), $paginator->currentPage() + $window);
        
        // Add pages in the window
        for ($i = $start; $i <= $end; $i++) {
            $elements[] = $i;
        }
        
        // Always show last page
        if ($paginator->currentPage() < $paginator->lastPage() - $window) {
            if ($paginator->currentPage() < $paginator->lastPage() - $window - 1) {
                $elements[] = '...';
            }
            $elements[] = $paginator->lastPage();
        }
    @endphp

    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
        {{-- Mobile view --}}
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Selanjutnya
                </a>
            @else
                <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">
                    Selanjutnya
                </span>
            @endif
        </div>

        {{-- Desktop view --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            {{-- Info text --}}
            <div>
                <p class="text-sm text-gray-700">
                    Menampilkan
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            {{-- Pagination buttons --}}
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    {{-- First Page --}}
                    @if (!$paginator->onFirstPage())
                        <a href="{{ $paginator->url(1) }}" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0" title="Halaman Pertama">
                            <span class="sr-only">Halaman Pertama</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M15.79 14.77a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L11.832 10l3.938 3.71a.75.75 0 011.02 1.06zM7.79 14.77a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L3.832 10l3.938 3.71a.75.75 0 011.02 1.06z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-300 cursor-not-allowed">
                            <span class="sr-only">Halaman Pertama</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M15.79 14.77a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L11.832 10l3.938 3.71a.75.75 0 011.02 1.06zM7.79 14.77a.75.75 0 01-1.06.02l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 111.04 1.08L3.832 10l3.938 3.71a.75.75 0 011.02 1.06z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif

                    {{-- Previous Page --}}
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-300 cursor-not-allowed">
                            <span class="sr-only">Sebelumnya</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                            <span class="sr-only">Sebelumnya</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($elements as $element)
                        @if ($element == '...')
                            <span aria-disabled="true" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">
                                ...
                            </span>
                        @else
                            @if ($element == $paginator->currentPage())
                                <span aria-current="page" class="relative z-10 inline-flex items-center bg-blue-600 px-4 py-2 text-sm font-semibold text-white focus:z-20  focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    {{ $element }}
                                </span>
                            @else
                                <a href="{{ $paginator->url($element) }}" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                    {{ $element }}
                                </a>
                            @endif
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                            <span class="sr-only">Selanjutnya</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="relative inline-flex items-center px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-300 cursor-not-allowed">
                            <span class="sr-only">Selanjutnya</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif

                    {{-- Last Page --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0" title="Halaman Terakhir">
                            <span class="sr-only">Halaman Terakhir</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.21 5.23a.75.75 0 011.06-.02l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 11-1.04-1.08L8.168 10 4.23 6.29a.75.75 0 01-1.02-1.06zM12.21 5.23a.75.75 0 011.06-.02l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 11-1.04-1.08L16.168 10l-3.938-3.71a.75.75 0 01-1.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-300 cursor-not-allowed">
                            <span class="sr-only">Halaman Terakhir</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.21 5.23a.75.75 0 011.06-.02l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 11-1.04-1.08L8.168 10 4.23 6.29a.75.75 0 01-1.02-1.06zM12.21 5.23a.75.75 0 011.06-.02l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 11-1.04-1.08L16.168 10l-3.938-3.71a.75.75 0 01-1.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </nav>
            </div>
        </div>
    </div>
@endif
