<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Equipment from CSV') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Displaying validation errors or success messages -->
                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="space-y-6">
                        <!-- Lab Selection -->
                        <div>
                            <label for="lab_id" class="block font-medium text-sm text-gray-700">Select Lab for this Import</label>
                            <select id="lab_id" name="lab_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Select a Lab --</option>
                                @foreach($labs as $lab)
                                    <option value="{{ $lab->id }}">{{ $lab->lab_name }} - {{ $lab->building_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <label for="csv_file" class="block font-medium text-sm text-gray-700">CSV File</label>
                            <input type="file" id="csv_file" name="csv_file" class="block mt-1 w-full" required accept=".csv">
                            <p class="mt-2 text-sm text-gray-500">
                                Required Columns: PC#, Monitor, Monitor Serial Num, OS, Processor, CPU Serial Num, Motherboard, Memory, Storage, Video Card, PSU, Status
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('equipment.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Start Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>