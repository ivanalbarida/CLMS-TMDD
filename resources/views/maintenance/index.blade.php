<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Maintenance Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-end items-center space-x-4">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Generate Report
                </a>
                <a href="{{ route('maintenance.create', ['type' => 'Corrective']) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                    Report Issue (Corrective)
                </a>
                <a href="{{ route('maintenance.schedule') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                    Schedule PM (Preventive)
                </a>
            </div>

            @php
                // Helper function to generate links for sortable headers
                function sortableHeader($column, $displayName) {
                    $sortBy = request('sort_by');
                    $sortDirection = request('sort_direction', 'desc');
                    $newDirection = ($sortBy == $column && $sortDirection == 'asc') ? 'desc' : 'asc';
                    $url = request()->fullUrlWithQuery(['sort_by' => $column, 'sort_direction' => $newDirection]);

                    $icon = '';
                    if ($sortBy == $column) {
                        $icon = $sortDirection == 'asc' ? '▲' : '▼'; // Up/Down arrows
                    }
                    
                    return "<a href='{$url}' class='flex items-center'>{$displayName} <span class='ml-1'>{$icon}</span></a>";
                }
            @endphp

            <!-- Corrective Maintenance Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Corrective Maintenance Log</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableHeader('lab_name', 'Lab') !!}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableHeader('category', 'Category') !!}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableHeader('date_reported', 'Date Reported') !!}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableHeader('status', 'Status') !!}</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($correctiveRecords as $record)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">@foreach($record->equipment as $pc){{ $pc->tag_number }}@if(!$loop->last), @endif @endforeach</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->equipment->first()->lab->lab_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->category }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $record->issue_description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->date_reported->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->status }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"><a href="{{ route('maintenance.edit', $record->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a></td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No corrective maintenance logs found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t bg-gray-50">{{ $correctiveRecords->appends(['preventive_page' => $preventiveRecords->currentPage()])->links() }}</div>
                </div>
            </div>

            <!-- Preventive Maintenance Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Preventive Maintenance Log</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableHeader('lab_name', 'Lab') !!}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableHeader('category', 'Category') !!}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableHeader('scheduled_for', 'Date Scheduled') !!}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableHeader('status', 'Status') !!}</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($preventiveRecords as $record)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">@foreach($record->equipment as $pc){{ $pc->tag_number }}@if(!$loop->last), @endif @endforeach</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->equipment->first()->lab->lab_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->category }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $record->issue_description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->scheduled_for ? $record->scheduled_for->format('M d, Y') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->status }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"><a href="{{ route('maintenance.edit', $record->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a></td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No preventive maintenance logs found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t bg-gray-50">{{ $preventiveRecords->appends(['corrective_page' => $correctiveRecords->currentPage()])->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>