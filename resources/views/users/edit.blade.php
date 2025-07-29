<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update User: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Display Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="username" class="block font-medium text-sm text-gray-700">Username</label>
                                <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Role -->
                            <div class="md:col-span-2">
                                <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
                                <select id="role" name="role" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" @selected(old('role', $user->role) == $role)>{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2 mt-6">
                                <label class="block font-medium text-sm text-gray-700">Assign Labs</label>
                                <div class="mt-2 border rounded-md p-4 space-y-2 max-h-48 overflow-y-auto">
                                    @foreach ($labs as $lab)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="labs[]" value="{{ $lab->id }}"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                @if(in_array($lab->id, $assignedLabIds)) checked @endif
                                            >
                                            <span class="ml-2 text-sm text-gray-600">{{ $lab->lab_name }} ({{ $lab->building_name }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mt-4 md:col-span-2">
                                <p class="text-sm text-gray-600">Update Password (optional)</p>
                            </div>
                            <div>
                                <label for="password" class="block font-medium text-sm text-gray-700">New Password</label>
                                <input id="password" type="password" name="password" autocomplete="new-password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirm New Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>