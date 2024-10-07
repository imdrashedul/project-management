@props(['class' => '', 'itemName' => 'this item', 'actionUrl'])

<div x-data="{ open: false }" x-init="open = false">
    <!-- Delete Button -->
    <button @click.prevent="open = true" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 {{ $class }}">
        {{ $value ?? $slot }}
    </button>

    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display:none" @click.away="open = false">
        <div class="bg-white rounded-lg p-6 w-96">
            <h2 class="text-lg font-bold">Confirm Deletion</h2>
            <p>Are you sure you want to delete {{ $itemName }}?</p>
            <div class="mt-4 flex justify-end">
                <button @click="open = false" class="px-4 py-2 mr-2 text-gray-600 bg-gray-200 rounded hover:bg-gray-300">
                    Cancel
                </button>
                <form action="{{ $actionUrl }}" method="POST" id="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
