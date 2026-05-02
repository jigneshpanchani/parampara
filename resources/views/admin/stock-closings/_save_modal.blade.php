{{-- Save Closing Modal --}}
<div id="saveClosingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Save Stock Closing</h3>
            <button type="button" onclick="closeSaveClosingModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <form action="{{ route('admin.stock-closings.store') }}" method="POST" class="flex-1 flex flex-col overflow-hidden">
            @csrf

            <div class="px-6 py-4 overflow-y-auto flex-1">
                <div class="mb-4 max-w-xs">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Closing Date *</label>
                    <input type="date" name="closing_date" value="{{ now()->format('Y-m-d') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <table class="w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100 sticky top-0">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Product</th>
                            <th class="px-3 py-2 text-right font-semibold text-purple-700">Closing Qty *</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modalProducts as $product)
                        <tr class="border-b">
                            <td class="px-3 py-2 font-medium text-gray-800">
                                {{ $product->product_name }}
                                <input type="hidden" name="product_id[]" value="{{ $product->id }}">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" name="actual_stock[]" step="0.01" min="0" required
                                    value="{{ $suggestedClosingQty[$product->id] ?? 0 }}"
                                    class="w-32 ml-auto block px-2 py-1 border border-gray-300 rounded text-right focus:outline-none focus:ring-2 focus:ring-purple-400">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t flex justify-end gap-2 bg-gray-50">
                <button type="button" onclick="closeSaveClosingModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold">Save Closing</button>
            </div>
        </form>
    </div>
</div>

<script>
function openSaveClosingModal() {
    document.getElementById('saveClosingModal').classList.remove('hidden');
}
function closeSaveClosingModal() {
    document.getElementById('saveClosingModal').classList.add('hidden');
}
document.getElementById('saveClosingModal').addEventListener('click', (e) => {
    if (e.target.id === 'saveClosingModal') closeSaveClosingModal();
});
</script>
