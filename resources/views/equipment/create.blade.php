<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Equipment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('equipment.store') }}">
                @csrf
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Main Equipment Details -->
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold">Main Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label for="tag_number">Tag Number (e.g., PC #1)</label>
                                <input type="text" name="tag_number" id="tag_number" class="block mt-1 w-full" required>
                            </div>
                            <div>
                                <label for="lab_id">Location</label>
                                <select name="lab_id" id="lab_id" class="block mt-1 w-full" required>
                                    <option value="">-- Select a Lab --</option>
                                    @foreach ($labs as $lab)
                                        <option value="{{ $lab->id }}">{{ $lab->lab_name }} - {{ $lab->building_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status">Status</label>
                                <select name="status" id="status" class="block mt-1 w-full" required>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="block mt-1 w-full"></textarea>
                        </div>
                    </div>

                    <!-- Components Section -->
                    <div class="p-6 text-gray-900 border-t">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold">Components</h3>
                            <button type="button" id="add-component-btn" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">Add Component</button>
                        </div>
                        <div id="components-container" class="mt-4 space-y-4">
                            <!-- JS will add component rows here -->
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="p-6 bg-gray-50 text-right">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Save Equipment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- This script will handle adding/removing component rows -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('components-container');
            const addBtn = document.getElementById('add-component-btn');
            let componentIndex = 0;

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
                    e.target.closest('.component-row').remove();
                }
            });

            // Create a few initial rows to start with
            createComponentRow();
            createComponentRow();
        });
    </script>
</x-app-layout>