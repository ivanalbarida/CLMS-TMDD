<x-print-layout>
    <x-slot name="title">
        Service Request Report - {{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }}
    </x-slot>

    <div class="py-12 print-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 print-container">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    
                    <!-- Report Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start mb-6 border-b pb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Service Request Report</h1>
                            <p class="text-gray-600">Period: <span class="font-medium">{{ $startDate->format('M d, Y') }}</span> to <span class="font-medium">{{ $endDate->format('M d, Y') }}</span></p>
                            <p class="text-sm text-gray-500">Generated on: {{ now()->format('M d, Y - h:i A') }}</p>
                        </div>
                        <!-- Buttons are in a "no-print" div -->
                        <div class="no-print mt-4 md:mt-0 flex-shrink-0 flex space-x-2">
                            <button onclick="window.print()" class="no-print inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Print / Save as PDF
                            </button>
                        </div>
                    </div>
                    
                    <!-- Report Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Submitted</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requesting Office</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Tech</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($records as $record)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm font-mono text-gray-600">SR-A-{{ str_pad($record->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $record->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @switch($record->status)
                                                @case('Submitted') bg-yellow-100 text-yellow-800 @break
                                                @case('In Review') bg-blue-100 text-blue-800 @break
                                                @case('In Progress') bg-blue-100 text-blue-800 @break
                                                @case('Completed') bg-green-100 text-green-800 @break
                                                @case('Rejected') bg-red-100 text-red-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ $record->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $record->title }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $record->requesting_office }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $record->requester->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $record->technician->name ?? 'Unassigned' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">No records found for the selected criteria.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-print-layout>