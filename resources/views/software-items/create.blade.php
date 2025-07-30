<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Software') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('software-items.store') }}" class="p-6">
                    @csrf
                    <div class="space-y-6">
                        <!-- Software Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Software Name*</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Version -->
                        <div>
                            <label for="version" class="block font-medium text-sm text-gray-700">Version (Optional)</label>
                            <input type="text" id="version" name="version" value="{{ old('version') }}" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        
                        <!-- License Details -->
                        <div>
                            <label for="license_details" class="block font-medium text-sm text-gray-700">License Details (Optional)</label>
                            <textarea id="license_details" name="license_details" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('license_details') }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 pt-6 border-t">
                        <a href="{{ route('software-items.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Save Software
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>