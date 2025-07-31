<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Add x-data to the form -->
                <form method="POST" action="{{ route('users.store') }}" class="p-6" 
                      x-data="{ password: '', password_confirmation: '' }">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="font-medium text-red-600">{{ __('Something went wrong.') }}</div>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                                    
                    <!-- Display Validation Errors if any -->
                    @if ($errors->any()) ... @endif

                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block font-medium text-sm text-gray-700">Username</label>
                            <input id="username" type="text" name="username" value="{{ old('username') }}" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
                            <select id="role" name="role" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Select a Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                            <!-- Add x-model to this input -->
                            <input id="password" type="password" name="password" required autocomplete="new-password" 
                                   x-model="password"
                                   class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirm Password</label>
                            <!-- Add :disabled attribute here -->
                            <input id="password_confirmation" type="password" name="password_confirmation" required 
                                   x-model="password_confirmation"
                                   :disabled="password === ''"
                                   class="block mt-1 w-full border-gray-300 rounded-md shadow-sm disabled:bg-gray-100">
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Create User
                            </button>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>