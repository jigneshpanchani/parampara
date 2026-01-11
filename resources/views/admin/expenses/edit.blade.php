@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Expense</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.expenses.update', $expense) }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <!-- Row 1: Expense Date & Category -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="expense_date" class="block text-sm font-semibold text-gray-700 mb-2">Expense Date</label>
                    <input type="date" id="expense_date" name="expense_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ $expense->expense_date->format('Y-m-d') }}">
                    @error('expense_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                    <div class="flex gap-2">
                        <select id="category_id" name="category_id" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $expense->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="addCategoryBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            + Add
                        </button>
                    </div>
                    @error('category_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Row 2: Description & Amount -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description <span class="text-gray-500 text-xs">(Optional)</span></label>
                    <input type="text" id="description" name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $expense->description }}">
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">Amount (₹)</label>
                    <input type="number" id="amount" name="amount" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ $expense->amount }}">
                    @error('amount')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Row 3: Payment Method & Notes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Select Payment Method --</option>
                        <option value="Cash" {{ $expense->payment_method == 'Cash' ? 'selected' : '' }}>💵 Cash</option>
                        <option value="G-Pay" {{ $expense->payment_method == 'G-Pay' ? 'selected' : '' }}>📱 G-Pay</option>
                        <option value="Online Transfer" {{ $expense->payment_method == 'Online Transfer' ? 'selected' : '' }}>🏦 Online Transfer</option>
                    </select>
                    @error('payment_method')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $expense->notes }}</textarea>
                    @error('notes')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    Update Expense
                </button>
                <a href="{{ route('admin.expenses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Add New Category</h3>
            <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="addCategoryForm" class="space-y-4">
            @csrf
            <div>
                <label for="categoryName" class="block text-sm font-semibold text-gray-700 mb-2">Category Name</label>
                <input type="text" id="categoryName" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="e.g., Office Supplies" required>
                <span id="categoryNameError" class="text-red-500 text-sm hidden"></span>
            </div>

            <div>
                <label for="categoryDescription" class="block text-sm font-semibold text-gray-700 mb-2">Description (Optional)</label>
                <textarea id="categoryDescription" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Brief description of this category"></textarea>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Add Category
                </button>
                <button type="button" id="cancelModalBtn" class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
            </div>

            <div id="categorySuccessMessage" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4">
                Category added successfully!
            </div>

            <div id="categoryErrorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4"></div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('addCategoryModal');
    const addCategoryBtn = document.getElementById('addCategoryBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const addCategoryForm = document.getElementById('addCategoryForm');
    const categorySelect = document.getElementById('category_id');

    // Open modal
    addCategoryBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        document.getElementById('categoryName').focus();
    });

    // Close modal
    const closeModal = () => {
        modal.classList.add('hidden');
        addCategoryForm.reset();
        document.getElementById('categorySuccessMessage').classList.add('hidden');
        document.getElementById('categoryErrorMessage').classList.add('hidden');
        document.getElementById('categoryNameError').classList.add('hidden');
    };

    closeModalBtn.addEventListener('click', closeModal);
    cancelModalBtn.addEventListener('click', closeModal);

    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Handle form submission
    addCategoryForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(addCategoryForm);
        const categoryName = document.getElementById('categoryName').value.trim();

        if (!categoryName) {
            document.getElementById('categoryNameError').textContent = 'Category name is required';
            document.getElementById('categoryNameError').classList.remove('hidden');
            return;
        }

        try {
            const response = await fetch('{{ route("admin.expense-categories.store") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Add new category to select
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                option.selected = true;
                categorySelect.appendChild(option);

                // Show success message
                document.getElementById('categorySuccessMessage').classList.remove('hidden');
                document.getElementById('categoryErrorMessage').classList.add('hidden');

                // Close modal after 1.5 seconds
                setTimeout(() => {
                    closeModal();
                }, 1500);
            } else {
                document.getElementById('categoryErrorMessage').textContent = data.message || 'Error adding category';
                document.getElementById('categoryErrorMessage').classList.remove('hidden');
            }
        } catch (error) {
            document.getElementById('categoryErrorMessage').textContent = 'Error: ' + error.message;
            document.getElementById('categoryErrorMessage').classList.remove('hidden');
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
</script>
@endsection

