<div>
    <!-- The Announcements Widget -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold">Announcements</h3>
                @if(Auth::user()->role == 'Admin')
                <a href="{{ route('announcements.create') }}" class="text-xs ...">New</a>
                @endif
            </div>
            <div class="space-y-4">
                @forelse($announcements as $announcement)
                    <!-- Main container for one announcement row -->
                    <div class="flex justify-between items-start group p-2 rounded-md hover:bg-gray-50">
                        
                        <!-- Clickable area for the modal pop-up -->
                        <div wire:click="openAnnouncement({{ $announcement->id }})" class="flex-grow cursor-pointer pr-4">
                            <h4 class="font-semibold text-sm">{{ $announcement->title }}</h4>
                            <p class="text-xs text-gray-500 line-clamp-2 break-words">{{ $announcement->content }}</p>
                        </div>

                        @if(Auth::user()->role == 'Admin')
                            <div class="text-xs flex-shrink-0 space-x-2 pt-1">
                                <a href="{{ route('announcements.edit', $announcement->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                </form>
                            </div>
                        @endif

                    </div>
                @empty
                    <p class="text-sm text-gray-500">No recent announcements.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- The Modal -->
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-500 bg-opacity-75" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-xl font-semibold">{{ $modalTitle }}</h3>
                <button wire:click="$set('showModal', false)" class="...">×</button>
            </div>
            <div class="p-6">
                <p class="text-base ...">{{ $modalContent }}</p>
            </div>
            <div class="flex items-center justify-end p-4 border-t">
                <button wire:click="$set('showModal', false)" type="button" class="...">Close</button>
            </div>
        </div>
    </div>
    @endif
</div>