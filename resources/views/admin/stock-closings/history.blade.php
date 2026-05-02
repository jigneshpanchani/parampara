@extends('layouts.admin')

@section('title', 'Stock Closing History')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Stock Closing History</h2>
            <div class="flex gap-2">
                <button type="button" onclick="openSaveClosingModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">+ Save Closing</button>
                <a href="{{ route('admin.stock-closings.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Verification</a>
            </div>
        </div>

        @if($closings->isEmpty())
            <p class="text-gray-500 text-center py-8">No closings saved yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold">Closing Date</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold">Period</th>
                            <th class="px-4 py-2 text-right text-sm font-semibold">Products</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold">Notes</th>
                            <th class="px-4 py-2 text-center text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($closings as $closing)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-semibold text-gray-800">{{ $closing->closing_date->format('d M Y') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ \Carbon\Carbon::parse($closing->closing_date)->format('F Y') }}</td>
                            <td class="px-4 py-2 text-right text-sm text-gray-600">{{ $closing->items_count }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 max-w-xs truncate">{{ $closing->notes ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center items-center gap-3">
                                    <a href="{{ route('admin.stock-closings.show', $closing) }}"
                                        class="text-blue-600 hover:text-blue-800"
                                        title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('admin.stock-closings.destroy', $closing) }}"
                                        class="delete-form inline-flex"
                                        data-item-name="Closing on {{ $closing->closing_date->format('d M Y') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700"
                                            title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $closings->links() }}
            </div>
        @endif
    </div>
</div>

@include('admin.stock-closings._save_modal')
@endsection
