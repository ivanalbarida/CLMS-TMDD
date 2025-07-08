<!-- File: resources/views/pm-tasks/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit PM Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('pm-tasks.update', $pmTask->id) }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Category -->
                        <div>
                            <label for="category" class="block font-medium text-sm text-gray-700">Category</label>
                            <select id="category" name="category" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category', $pmTask->category) == $category)>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Task Description -->
                        <div>
                            <label for="task_description" class="block font-medium text-sm text-gray-700">Task Description</label>
                            <textarea id="task_description" name="task_description" rows="3" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('task_description', $pmTask->task_description) }}</textarea>
                        </div>

                        <!-- Frequency -->
                        <div>
                            <label for="frequency" class="block font-medium text-sm text-gray-700">Frequency</label>
                            <select id="frequency" name="frequency" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @foreach($frequencies as $frequency)
                                    <option value="{{ $frequency }}" @selected(old('frequency', $pmTask->frequency) == $frequency)>
                                        {{ $frequency }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('pm-tasks.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Update Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>