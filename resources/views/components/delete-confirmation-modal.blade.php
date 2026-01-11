<!-- Delete Confirmation Modal Component -->
<div id="deleteConfirmationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Header -->
        <div class="bg-red-50 px-6 py-4 border-b border-red-200">
            <h3 class="text-lg font-bold text-red-900 flex items-center">
                <svg class="w-6 h-6 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6v-2m0 0V7a2 2 0 012-2h2.586a1 1 0 00.707-.293l2.414-2.414a1 1 0 00-.707-1.707H9.414a1 1 0 00-.707.293L6.293 2.293A1 1 0 005.586 2H3a2 2 0 00-2 2v2.586a1 1 0 00.293.707l2.414 2.414a1 1 0 001.414 0l2.414-2.414a1 1 0 00.293-.707V4h8v2.586a1 1 0 001.414 0l2.414 2.414a1 1 0 00.293.707V12"></path>
                </svg>
                Confirm Delete
            </h3>
        </div>

        <!-- Body -->
        <div class="px-6 py-4">
            <p class="text-gray-700 text-base">
                Are you sure you want to delete this record? This action cannot be undone.
            </p>
            <p id="deleteItemName" class="text-gray-600 text-sm mt-3 font-semibold"></p>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
            <button 
                type="button" 
                id="cancelDeleteBtn" 
                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-semibold transition duration-200">
                No, Cancel
            </button>
            <button 
                type="button" 
                id="confirmDeleteBtn" 
                class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 font-semibold transition duration-200">
                Yes, Delete
            </button>
        </div>
    </div>
</div>

<script>
    let deleteForm = null;
    const modal = document.getElementById('deleteConfirmationModal');
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const itemNameEl = document.getElementById('deleteItemName');

    // Close modal function
    function closeDeleteModal() {
        modal.classList.add('hidden');
        deleteForm = null;
        itemNameEl.textContent = '';
    }

    // Open modal function
    function openDeleteModal(form, itemName = '') {
        deleteForm = form;
        if (itemName) {
            itemNameEl.textContent = `Item: ${itemName}`;
        }
        modal.classList.remove('hidden');
        confirmBtn.focus();
    }

    // Cancel button
    cancelBtn.addEventListener('click', closeDeleteModal);

    // Confirm button
    confirmBtn.addEventListener('click', () => {
        if (deleteForm) {
            deleteForm.submit();
        }
    });

    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeDeleteModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeDeleteModal();
        }
    });

    // Prevent form submission and show modal instead
    document.addEventListener('submit', (e) => {
        if (e.target.classList.contains('delete-form')) {
            e.preventDefault();
            const itemName = e.target.dataset.itemName || '';
            openDeleteModal(e.target, itemName);
        }
    });
</script>

