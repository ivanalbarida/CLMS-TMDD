<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Details for: {{ $equipment->tag_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div><strong>Tag Number:</strong> {{ $equipment->tag_number }}</div>
                    <div><strong>Location:</strong> {{ $equipment->lab->lab_name }} - {{ $equipment->lab->building_name }}</div>
                    <div><strong>Status:</strong> {{ $equipment->status }}</div>
                    <div><strong>Notes:</strong> {{ $equipment->notes ?? 'N/A' }}</div>

                    <h3 class="text-lg font-bold mt-6 border-t pt-4">Components</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description / Model</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($equipment->components as $component)
                            <tr>
                                <td class="px-6 py-4">{{ $component->type }}</td>
                                <td class="px-6 py-4">{{ $component->description }}</td>
                                <td class="px-6 py-4">{{ $component->serial_number ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                        <div class="mt-8">
                            <h3 class="text-lg font-bold border-t pt-4">Activity & Maintenance History</h3>
                            
                            <!-- ADD THIS NEW FILTER FORM BLOCK -->
                            <div class="my-4 p-4 bg-gray-50 rounded-lg border">
                                <form method="GET" action="{{ route('equipment.show', $equipment->id) }}">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                        <!-- Start Date -->
                                        <div>
                                            <label for="start_date" class="text-sm font-medium">From:</label>
                                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="block w-full text-sm mt-1 border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <!-- End Date -->
                                        <div>
                                            <label for="end_date" class="text-sm font-medium">To:</label>
                                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="block w-full text-sm mt-1 border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <!-- Action Type -->
                                        <div>
                                            <label for="action_type" class="text-sm font-medium">Action Type:</label>
                                            <select name="action_type" id="action_type" class="block w-full text-sm mt-1 border-gray-300 rounded-md shadow-sm">
                                                <option value="">All</option>
                                                @foreach ($actionTypes as $type)
                                                    <option value="{{ $type }}" @selected(request('action_type') == $type)>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Buttons -->
                                        <div class="flex space-x-2">
                                            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border ... bg-indigo-600 text-white ...">Filter</button>
                                            <a href="{{ route('equipment.show', $equipment->id) }}" class="w-full inline-flex justify-center py-2 px-4 border ...">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- END OF FILTER FORM BLOCK -->

                            <div class="mt-4 bg-white ...">
                                <table class="min-w-full ...">
                                    <!-- ... your table remains the same ... -->
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $history->links() }}
                            </div>
                        </div>
                        
                        <div class="mt-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($history as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->created_at->format('M d, Y - H:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->user->name ?? 'System' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $log->action_type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $log->description }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            No history found for this equipment.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $history->links() }}
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('equipment.showByLab', $equipment->lab_id) }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>