<x-app-layout>
    {{-- (Your existing Blade content for header, form, main details, components section) --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('equipment.update', $equipment->id) }}">
                @csrf
                @method('PUT')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold">Main Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label for="tag_number">Tag Number</label>
                                <input type="text" name="tag_number" id="tag_number" class="block mt-1 w-full" value="{{ old('tag_number', $equipment->tag_number) }}" required>
                            </div>
                            <div>
                                <label for="lab_id">Location</label>
                                <select name="lab_id" id="lab_id" class="block mt-1 w-full" required>
                                    @foreach ($labs as $lab)
                                        <option value="{{ $lab->id }}" @selected(old('lab_id', $equipment->lab_id) == $lab->id)>
                                            {{ $lab->lab_name }} - {{ $lab->building_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status">Status</label>
                                <select name="status" id="status" class="block mt-1 w-full" required>
                                    @foreach ($statuses as $status)
                                        @if ($status !== 'In Use') {{-- This line conditionally removes 'In Use' --}}
                                            <option value="{{ $status }}" @selected(old('status', $equipment->status) == $status)>{{ $status }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="block mt-1 w-full">{{ old('notes', $equipment->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="p-6 text-gray-900 border-t">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold">Components</h3>
                            <button type="button" id="add-component-btn" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">Add Component</button>
                        </div>
                        <div id="components-container" class="mt-4 space-y-4">
                            @foreach ($equipment->components as $index => $component)
                                <div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-end component-row">
                                    <div class="md:col-span-2">
                                        <label>Type</label>
                                        <select name="components[{{ $index }}][type]" class="block mt-1 w-full" required>
                                            @foreach ($componentTypes as $type)
                                                <option value="{{ $type }}" @selected($component->type == $type)>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label>Description / Model</label>
                                        <input type="text" name="components[{{ $index }}][description]" value="{{ $component->description }}" class="block mt-1 w-full" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label>Serial Number</label>
                                        <input type="text" name="components[{{ $index }}][serial_number]" value="{{ $component->serial_number }}" class="block mt-1 w-full">
                                    </div>
                                    <div>
                                        <button type="button" class="remove-component-btn w-full px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 text-right">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs
                        text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500
                        focus:ring-offset-2 transition ease-in-out duration-150"> Update Equipment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- CUSTOM CONFIRMATION MODAL --}}
    <div x-data="{ showModal: false, targetRow: null }"
         x-on:open-remove-modal.window="showModal = true; targetRow = $event.detail.row;"
         x-cloak
         x-show="showModal"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-on:click="showModal = false" {{-- Close when clicking outside --}}
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            {{-- This is to horizontally center the modal contents. --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal panel --}}
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            {{-- Heroicon for warning --}}
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Confirm Removal
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to remove this component? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            x-on:click="targetRow.remove(); showModal = false;" {{-- Remove the stored row and close modal --}}
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Remove
                    </button>
                    <button type="button"
                            x-on:click="showModal = false" {{-- Just close the modal --}}
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- END CUSTOM CONFIRMATION MODAL --}}


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('components-container');
            const addBtn = document.getElementById('add-component-btn');
            let componentIndex = {{ $equipment->components->count() }};

            function createComponentRow() {
                const row = document.createElement('div');
                row.classList.add('grid', 'grid-cols-1', 'md:grid-cols-8', 'gap-4', 'items-end', 'component-row');
                row.innerHTML = `
                    <div class="md:col-span-2">
                        <label>Type</label>
                        <select name="components[${componentIndex}][type]" class="block mt-1 w-full" required>
                            <option value="">-- Select Type --</option>
                            @foreach ($componentTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label>Description / Model</label>
                        <input type="text" name="components[${componentIndex}][description]" class="block mt-1 w-full" required>
                    </div>
                    <div class="md:col-span-2">
                        <label>Serial Number</label>
                        <input type="text" name="components[${componentIndex}][serial_number]" class="block mt-1 w-full">
                    </div>
                    <div>
                        <button type="button" class="remove-component-btn w-full px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">Remove</button>
                    </div>
                `;
                container.appendChild(row);
                componentIndex++;
            }

            addBtn.addEventListener('click', createComponentRow);

            container.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-component-btn')) {
                    // Dispatch a custom event to open the modal
                    window.dispatchEvent(new CustomEvent('open-remove-modal', {
                        detail: { row: e.target.closest('.component-row') }
                    }));
                }
            });
        });
    </script>
</x-app-layout>