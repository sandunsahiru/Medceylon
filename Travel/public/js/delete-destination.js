<<<<<<< HEAD
document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-button');
    const warningModal = document.getElementById('warningModalContainer');
    const cancelDeleteButton = document.getElementById('cancelDelete');
    const confirmDeleteButton = document.getElementById('confirmDelete');
    const deleteForm = document.getElementById('deleteForm');
    const destinationIdInput = document.getElementById('destination_id');
    const travelIdInput = document.getElementById('travel_id'); // Ensure this is the correct input ID

    // Function to open the modal
    deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const travelId = button.getAttribute('data-plan-travelid');
            console.log('Delete button clicked. Travel ID:', travelId);
            travelIdInput.value = travelId; // Populate the hidden input for travel_id
            warningModal.classList.add('active'); // Show modal by adding 'active' class
        });
    });

    // Function to cancel deletion
    cancelDeleteButton.addEventListener('click', () => {
        warningModal.classList.remove('active'); // Hide modal by removing 'active' class
    });

    // Function to confirm deletion
    confirmDeleteButton.addEventListener('click', () => {
        deleteForm.submit(); // Submit the form
    });

    // Close the modal if clicked outside of the modal content
    window.addEventListener('click', (e) => {
        if (e.target === warningModal) {
            warningModal.classList.remove('active'); // Hide modal if clicked outside
        }
    });
});
=======
document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-button');
    const warningModal = document.getElementById('warningModalContainer');
    const cancelDeleteButton = document.getElementById('cancelDelete');
    const confirmDeleteButton = document.getElementById('confirmDelete');
    const deleteForm = document.getElementById('deleteForm');
    const destinationIdInput = document.getElementById('destination_id');
    const travelIdInput = document.getElementById('travel_id'); // Ensure this is the correct input ID

    // Function to open the modal
    deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const travelId = button.getAttribute('data-plan-travelid');
            console.log('Delete button clicked. Travel ID:', travelId);
            travelIdInput.value = travelId; // Populate the hidden input for travel_id
            warningModal.classList.add('active'); // Show modal by adding 'active' class
        });
    });

    // Function to cancel deletion
    cancelDeleteButton.addEventListener('click', () => {
        warningModal.classList.remove('active'); // Hide modal by removing 'active' class
    });

    // Function to confirm deletion
    confirmDeleteButton.addEventListener('click', () => {
        deleteForm.submit(); // Submit the form
    });

    // Close the modal if clicked outside of the modal content
    window.addEventListener('click', (e) => {
        if (e.target === warningModal) {
            warningModal.classList.remove('active'); // Hide modal if clicked outside
        }
    });
});
>>>>>>> d7fee2e90c0e8b6767e13b75b1ecae8294eab4cf
