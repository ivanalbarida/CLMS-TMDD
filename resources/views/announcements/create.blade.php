<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Announcement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('announcements.store') }}" class="p-6">
                    @csrf
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block font-medium text-sm text-gray-700">Title</label>
                            <input type="text" id="title" name="title" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required autofocus>
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="content" class="block font-medium text-sm text-gray-700">Content</label>
                            <textarea id="content" name="content" rows="6" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Post Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>