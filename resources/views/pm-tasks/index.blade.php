<!-- File: resources/views/pm-tasks/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Preventive Maintenance Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('pm-tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Add New Task
                        </a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left ...">Category</th>
                                <th class="px-6 py-3 text-left ...">Task</th>
                                <th class="px-6 py-3 text-left ...">Frequency</th>
                                <th class="px-6 py-3 text-right ...">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($tasks as $task)
                            <tr>
                                <td class="px-6 py-4 ...">{{ $task->category }}</td>
                                <td class="px-6 py-4 ...">{{ $task->task_description }}</td>
                                <td class="px-6 py-4 ...">{{ $task->frequency }}</td>
                                <td class="px-6 py-4 text-right ...">
                                    <a href="{{ route('pm-tasks.edit', $task->id) }}" class="text-indigo-600 ...">Edit</a>
                                    <form action="{{ route('pm-tasks.destroy', $task->id) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 ...">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center ...">No PM tasks found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>