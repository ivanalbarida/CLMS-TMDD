<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Equipment: ') . $equipment->tag_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('equipment.update', $equipment->id) }}">
                @csrf
                @method('PUT')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Main Equipment Details -->
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
                                        <option value="{{ $status }}" @selected(old('status', $equipment->status) == $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="block mt-1 w-full">{{ old('notes', $equipment->notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Components Section -->
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
    <script>
        // Use the same JS from create.blade.php, but initialize index based on existing components
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('components-container');
            const addBtn = document.getElementById('add-component-btn');
            let componentIndex = {{ $equipment->components->count() }}; // <-- Start index here

            // ... (The rest of the JS is identical to create.blade.php)
            function createComponentRow() { /* ... same code ... */ }
            addBtn.addEventListener('click', createComponentRow);
            container.addEventListener('click', function (e) { /* ... same code ... */ });
        });
    </script>
</x-app-layout>