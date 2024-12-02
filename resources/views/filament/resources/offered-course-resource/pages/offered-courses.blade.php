<x-filament::page>
    <div>
        @if ($this->getTableQuery()->count() === 0)
            <div class="text-center text-gray-500 mt-4">
                No courses available for registration.
            </div>
        @else
            {{ $this->table }}
        @endif
    </div>
</x-filament::page>
