<<<<<<< HEAD
document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById('editDestinationModal');
    const editButtons = document.querySelectorAll('.edit-button');
    const closeEditModal = document.getElementById('closeEditModal');
    const modalEditDestinationName = document.getElementById('modalEditDestinationName');
    const modalEditDestinationImage = document.getElementById('modalEditDestinationImage');
    const editCheckInInput = document.getElementById('edit_check_in');
    const editCheckOutInput = document.getElementById('edit_check_out');
    const destinationIdInput = document.getElementById('destination_id');
    const travelIdInput = document.getElementById('travel_id');

    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Fetch attributes from the button
            const destinationName = button.getAttribute('data-plan-name');
            const checkIn = button.getAttribute('data-plan-checkin');
            const checkOut = button.getAttribute('data-plan-checkout');
            const imagePath = button.getAttribute('data-plan-image');
            const destinationId = button.getAttribute('data-plan-id');
            const travelId = button.getAttribute('data-plan-travelid');
            console.log('Edit button clicked. Travel ID:', travelId); 

            // Populate modal fields
            modalEditDestinationName.textContent = destinationName;
            modalEditDestinationImage.src = imagePath;
            modalEditDestinationImage.alt = destinationName;
            editCheckInInput.value = checkIn;
            editCheckOutInput.value = checkOut;
            destinationIdInput.value = destinationId;
            travelIdInput.value = travelId;

            // Display the modal
            editModal.classList.add('active');
        });
    });

    // Close modal when clicking the close button
    closeEditModal.addEventListener('click', () => {
        editModal.classList.remove('active');
    });

    // Close modal when clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === editModal) {
            editModal.classList.remove('active');
        }
    });
});
=======
document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById('editDestinationModal');
    const editButtons = document.querySelectorAll('.edit-button');
    const closeEditModal = document.getElementById('closeEditModal');
    const modalEditDestinationName = document.getElementById('modalEditDestinationName');
    const modalEditDestinationImage = document.getElementById('modalEditDestinationImage');
    const editCheckInInput = document.getElementById('edit_check_in');
    const editCheckOutInput = document.getElementById('edit_check_out');
    const destinationIdInput = document.getElementById('destination_id');
    const travelIdInput = document.getElementById('travel_id');

    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Fetch attributes from the button
            const destinationName = button.getAttribute('data-plan-name');
            const checkIn = button.getAttribute('data-plan-checkin');
            const checkOut = button.getAttribute('data-plan-checkout');
            const imagePath = button.getAttribute('data-plan-image');
            const destinationId = button.getAttribute('data-plan-id');
            const travelId = button.getAttribute('data-plan-travelid');
            console.log('Edit button clicked. Travel ID:', travelId); 

            // Populate modal fields
            modalEditDestinationName.textContent = destinationName;
            modalEditDestinationImage.src = imagePath;
            modalEditDestinationImage.alt = destinationName;
            editCheckInInput.value = checkIn;
            editCheckOutInput.value = checkOut;
            destinationIdInput.value = destinationId;
            travelIdInput.value = travelId;

            // Display the modal
            editModal.classList.add('active');
        });
    });

    // Close modal when clicking the close button
    closeEditModal.addEventListener('click', () => {
        editModal.classList.remove('active');
    });

    // Close modal when clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === editModal) {
            editModal.classList.remove('active');
        }
    });
});
>>>>>>> d7fee2e90c0e8b6767e13b75b1ecae8294eab4cf
