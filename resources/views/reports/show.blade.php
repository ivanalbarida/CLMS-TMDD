<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Maintenance Report: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
            </h2>

            <!-- Container for the action buttons -->
            <div class="flex space-x-4">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    ← New Report
                </a>

                <a href="{{ route('reports.export', $filters) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                    Export as CSV
                </a>

                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Print / Save as PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Report Header -->
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-bold">Report for Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</h3>
                        <p class="text-sm text-gray-600">Generated on: {{ now()->format('M d, Y - H:i A') }}</p>
                    </div>
                    
                    <!-- Report Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Reported</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Completed</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type / Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment Serviced</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technician</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description / Action Taken</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-g-200">
                                @forelse ($records as $record)
                                <tr>
                                    <td class="px-4 py-4 text-sm">{{ \Carbon\Carbon::parse($record->date_reported)->format('Y-m-d') }}</td>
                                    <td class="px-4 py-4 text-sm">{{ $record->date_completed ? \Carbon\Carbon::parse($record->date_completed)->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="block font-semibold">{{ $record->type }}</span>
                                        <span class="block text-xs">{{ $record->status }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        @foreach ($record->equipment as $pc)
                                            <span class="block">{{ $pc->tag_number }} ({{ $pc->lab->lab_name }})</span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-4 text-sm">{{ $record->user->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        <p><strong>Issue:</strong> {{ $record->issue_description }}</p>
                                        @if($record->action_taken)
                                            <p class="mt-2"><strong>Action:</strong> {{ $record->action_taken }}</p>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">No records found for the selected criteria.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>