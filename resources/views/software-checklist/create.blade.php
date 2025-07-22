<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Checklist Items') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('software-checklist.store') }}">
                @csrf
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Main Details Section -->
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold">Group Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Program Name -->
                            <div>
                                <label for="program_name" class="block font-medium text-sm text-gray-700">Program Name (e.g., Civil Engineering)</label>
                                <input type="text" id="program_name" name="program_name" value="{{ old('program_name') }}" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Year and Semester -->
                            <div>
                                <label for="year_and_sem" class="block font-medium text-sm text-gray-700">Year & Semester (e.g., 1st Year, 1st Semester)</label>
                                <input type="text" id="year_and_sem" name="year_and_sem" value="{{ old('year_and_sem') }}" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Software Items Section -->
                    <div class="p-6 text-gray-900 border-t">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold">Software Items</h3>
                            <button type="button" id="add-software-btn" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">Add Software</button>
                        </div>
                        <div id="software-container" class="mt-4 space-y-4">
                            <!-- JavaScript will add software rows here -->
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="p-6 bg-gray-50 text-right">
                        <a href="{{ route('software-checklist.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Add Items to Checklist
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for dynamic rows -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('software-container');
            const addBtn = document.getElementById('add-software-btn');
            let softwareIndex = 0;

            function createSoftwareRow() {
                const row = document.createElement('div');
                row.classList.add('grid', 'grid-cols-1', 'md:grid-cols-8', 'gap-4', 'items-center', 'software-row');
                row.innerHTML = `
                    <div class="md:col-span-3">
                        <label class="text-sm text-gray-600">Software Name*</label>
                        <input type="text" name="software_items[${softwareIndex}][software_name]" class="block mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-600">Version</label>
                        <input type="text" name="software_items[${softwareIndex}][version]" class="block mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm text-gray-600">Notes</label>
                        <input type="text" name="software_items[${softwareIndex}][notes]" class="block mt-1 w-full text-sm border-gray-300 rounded-md shadow-sm">
                    </div>

                    <!-- CORRECTED REMOVE BUTTON SECTION -->
                    <div class="flex items-end">
                        <button type="button" class="remove-software-btn w-full px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm">Remove</button>
                    </div>
                `;
                container.appendChild(row);
                softwareIndex++;
            }

            addBtn.addEventListener('click', createSoftwareRow);

            container.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-software-btn')) {
                    e.target.closest('.software-row').remove();
                }
            });

            createSoftwareRow();
        });
    </script>
</x-app-layout>