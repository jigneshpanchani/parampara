@extends('layouts.admin')

@section('title', 'Settings & Profile')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="lg:col-span-3 bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">⚙️ Company Settings & Profile</h2>

        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ $message }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Company Name -->
            <div class="mb-6">
                <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">Company Name</label>
                <input type="text" id="company_name" name="company_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('company_name', $settings->company_name ?? 'Parampara') }}">
                @error('company_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('email', $settings->email ?? '') }}">
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                    <input type="text" id="phone" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('phone', $settings->phone ?? '') }}">
                    @error('phone')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Website URL & GST Number -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="website_url" class="block text-sm font-semibold text-gray-700 mb-2">Website URL</label>
                    <input type="url" id="website_url" name="website_url" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com" value="{{ old('website_url', $settings->website_url ?? '') }}">
                    @error('website_url')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="gst_number" class="block text-sm font-semibold text-gray-700 mb-2">GST Number</label>
                    <input type="text" id="gst_number" name="gst_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 27AABCT1234H1Z0" value="{{ old('gst_number', $settings->gst_number ?? '') }}">
                    @error('gst_number')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div class="mb-6">
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                <textarea id="address" name="address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter company address">{{ old('address', $settings->address ?? '') }}</textarea>
                @error('address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Logo Upload -->
            <div class="mb-6">
                <label for="logo" class="block text-sm font-semibold text-gray-700 mb-2">Logo (for Login & Sidebar)</label>
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <input type="file" id="logo" name="logo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" accept="image/*">
                        <p class="text-gray-500 text-sm mt-1">Recommended: 200x200px, Max 5MB</p>
                        @error('logo')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    @if($settings->logo)
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $settings->logo) }}" alt="Current Logo" class="h-20 w-20 object-contain border border-gray-300 rounded p-2">
                            <p class="text-gray-600 text-xs mt-2">Current Logo</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Favicon 16x16 & 32x32 -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <!-- Favicon 16x16 -->
                <div>
                    <label for="favicon_16" class="block text-sm font-semibold text-gray-700 mb-2">Favicon 16x16</label>
                    <input type="file" id="favicon_16" name="favicon_16" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" accept="image/png,image/x-icon">
                    <p class="text-gray-500 text-sm mt-1">PNG or ICO, 16x16px, Max 1MB</p>
                    @if($settings->favicon_16)
                        <div class="mt-3 text-center">
                            <img src="{{ asset('storage/' . $settings->favicon_16) }}" alt="Favicon 16x16" class="h-6 w-6 border border-gray-300 rounded p-1 mx-auto">
                            <p class="text-gray-600 text-xs mt-1">Current 16x16</p>
                        </div>
                    @endif
                    @error('favicon_16')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Favicon 32x32 -->
                <div>
                    <label for="favicon_32" class="block text-sm font-semibold text-gray-700 mb-2">Favicon 32x32</label>
                    <input type="file" id="favicon_32" name="favicon_32" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" accept="image/png,image/x-icon">
                    <p class="text-gray-500 text-sm mt-1">PNG or ICO, 32x32px, Max 1MB</p>
                    @if($settings->favicon_32)
                        <div class="mt-3 text-center">
                            <img src="{{ asset('storage/' . $settings->favicon_32) }}" alt="Favicon 32x32" class="h-8 w-8 border border-gray-300 rounded p-1 mx-auto">
                            <p class="text-gray-600 text-xs mt-1">Current 32x32</p>
                        </div>
                    @endif
                    @error('favicon_32')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter company description">{{ old('description', $settings->description ?? '') }}</textarea>
                @error('description')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">Save Settings</button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Right column: Password + DB Backup stacked -->
    <div class="lg:col-span-1 space-y-6 self-start">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">🔒 Change Password</h2>

        <form action="{{ route('admin.settings.update-password') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div class="mb-6">
                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('current_password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- New Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p class="text-gray-500 text-sm mt-1">Minimum 8 characters</p>
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm New Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('password_confirmation')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded">Update Password</button>
            </div>
        </form>
    </div>

    <!-- DB Backup Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">💾 Database Backup</h2>
        <p class="text-sm text-gray-600 mb-4">Download a full SQL backup of the database.</p>
        <a href="{{ route('admin.db-backup.download') }}" class="inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Download Backup
        </a>
    </div>
    </div>
    </div>
</div>
@endsection

