<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Software Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('software-profiles.store') }}" class="p-6">
                    @csrf
                    <div class="space-y-6">
                        <!-- Profile Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Profile Name*</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block font-medium text-sm text-gray-700">Description (Optional)</label>
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        </div>
                        
                        <!-- Software Checklist -->
                        <div class="pt-2">
                            <label class="block font-medium text-sm text-gray-700">Select Software*</label>
                            <div class="mt-2 border rounded-md p-4 space-y-2 max-h-60 overflow-y-auto">
                                @forelse ($softwareItems as $item)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="software[]" value="{{ $item->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            @if(is_array(old('software')) && in_array($item->id, old('software'))) checked @endif
                                        >
                                        <span class="ml-2 text-sm text-gray-600">{{ $item->name }} {{ $item->version }}</span>
                                    </label>
                                @empty
                                     <p class="text-sm text-gray-500">No software found. Please add software to the master list first.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 pt-6 border-t">
                        <a href="{{ route('software-profiles.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>