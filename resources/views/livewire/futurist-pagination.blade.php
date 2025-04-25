<div class="">
    @if ($this->sales->hasPages())
    <div class="mt-4 pagination-container">
        <nav role="navigation">
            <ul class="flex justify-center space-x-1">
                {{-- Previous Page --}}
                <li>
                    @if ($this->sales->onFirstPage())
                        <span class="px-3 py-1 bg-gray-700 text-gray-500 rounded cursor-not-allowed">
                            &laquo;
                        </span>
                    @else
                        <button wire:click="previousPage" 
                                class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-indigo-600">
                            &laquo;
                        </button>
                    @endif
                </li>

                {{-- Pages --}}
                @foreach ($this->sales->getUrlRange(1, $this->sales->lastPage()) as $page => $url)
                    <li>
                        @if ($page == $this->sales->currentPage())
                            <span class="px-3 py-2 bg-indigo-600 text-white rounded">
                                {{ $page }}
                            </span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" 
                                    class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-indigo-600">
                                {{ $page }}
                            </button>
                        @endif
                    </li>
                @endforeach

                {{-- Next Page --}}
                <li>
                    @if ($this->sales->hasMorePages())
                        <button wire:click="nextPage" 
                                class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-indigo-600">
                            &raquo;
                        </button>
                    @else
                        <span class="px-3 py-1 bg-gray-700 text-gray-500 rounded cursor-not-allowed">
                            &raquo;
                        </span>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
    @endif
</div>