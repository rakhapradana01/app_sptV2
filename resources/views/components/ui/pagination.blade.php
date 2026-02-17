@if ($paginator->hasPages())
    <div class="px-6 py-4 mt-6">
        <div class="flex items-center justify-between">

            @if ($paginator->onFirstPage())
                <span
                    class="opacity-50 cursor-not-allowed flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    Previous
                </a>
            @endif


            {{-- Page Numbers --}}
            <ul class="hidden sm:flex items-center gap-1">
                @foreach ($paginator->linkCollection() as $link)
                    @continue(in_array($link['label'], ['&laquo; Previous', 'Next &raquo;']))

                    <li>
                        @if ($link['url'] === null)
                            <span class="flex h-10 w-10 items-center justify-center text-gray-500">
                                {!! $link['label'] !!}
                            </span>
                        @elseif ($link['active'])
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500 text-white text-sm font-medium">
                                {!! $link['label'] !!}
                            </span>
                        @else
                            <a href="{{ $link['url'] }}"
                                class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium text-gray-700 hover:bg-blue-500/10 hover:text-blue-500 dark:text-gray-400">
                                {!! $link['label'] !!}
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>


            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    Next
                </a>
            @else
                <span
                    class="opacity-50 cursor-not-allowed flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    Next
                </span>
            @endif

        </div>
    </div>
@endif
