<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Maintenance Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <!-- This is a placeholder for a title if you want one, or can be empty -->
                        <div></div> 
                        
                        <!-- The buttons now live inside this container -->
                        <div class="flex space-x-4">
                            <a href="{{ route('maintenance.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 ...">
                                Report Issue (Corrective)
                            </a>
                            <a href="{{ route('maintenance.schedule') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 ...">
                                Schedule PM (Preventive)
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 ...">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tag No.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technician</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Reported</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($records as $record)
                                <tr>
                                    <!-- This is now the FIRST cell, matching the "Type" header -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $record->type == 'Corrective' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $record->type }}
                                        </span>
                                    </td>
                                    
                                    <!-- The rest of the data cells -->
                                    <td class="px-6 py-4">
                                        @foreach($record->equipment as $pc)
                                            <span class="block">{{ $pc->tag_number }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal max-w-xs truncate">{{ $record->issue_description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $record->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($record->date_reported)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $record->status }}</td>

                                    <!-- This is now the LAST cell, matching the "Actions" header -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('maintenance.edit', $record->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        
                                        @if(in_array(Auth::user()->role, ['Admin', 'Technician']))
                                            <form action="{{ route('maintenance.destroy', $record->id) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <!-- ... -->
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>