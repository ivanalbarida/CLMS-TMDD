<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New PM Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('pm-tasks.store') }}">
                @csrf
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Table-like structure for inputs -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Task Description</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Frequency</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="task-container">
                                    <!-- JS will add task rows here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <button type="button" id="add-task-btn" class="text-sm text-indigo-600 hover:text-indigo-900">+ Add Another Row</button>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-gray-50 flex justify-end">
                        <a href="{{ route('pm-tasks.index') }}" class="text-sm text-gray-600 self-center mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Save All Tasks
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for dynamic table rows -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('task-container');
            const addBtn = document.getElementById('add-task-btn');
            let taskIndex = 0;

            const categories = @json($categories);
            const frequencies = @json($frequencies);

            function createTaskRow() {
                const row = document.createElement('tr');
                row.classList.add('task-row');
                
                let categoryOptions = categories.map(cat => `<option value="${cat}">${cat}</option>`).join('');
                let frequencyOptions = frequencies.map(freq => `<option value="${freq}">${freq}</option>`).join('');

                row.innerHTML = `
                    <td class="px-3 py-2">
                        <select name="tasks[${taskIndex}][category]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                            ${categoryOptions}
                        </select>
                    </td>
                    <td class="px-3 py-2">
                        <textarea name="tasks[${taskIndex}][task_description]" rows="1" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required></textarea>
                    </td>
                    <td class="px-3 py-2">
                        <select name="tasks[${taskIndex}][frequency]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                            ${frequencyOptions}
                        </select>
                    </td>
                    <td class="px-3 py-2">
                        <button type="button" class="remove-task-btn text-red-500 hover:text-red-700">Remove</button>
                    </td>
                `;
                container.appendChild(row);
                taskIndex++;
            }

            addBtn.addEventListener('click', createTaskRow);

            container.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-task-btn')) {
                    e.target.closest('.task-row').remove();
                }
            });

            // Start with 5 rows by default to encourage bulk entry
            for(let i=0; i<5; i++) {
                createTaskRow();
            }
        });
    </script>
</x-app-layout>