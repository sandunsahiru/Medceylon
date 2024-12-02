document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addToPlanModal');
    const openModalBtns = document.querySelectorAll('.add-destination-button');
    const closeModal = document.getElementById('closeModal');
    const modalDestinationID = document.getElementById('modalDestinationID');
    const modalDestinationName = document.getElementById('modalDestinationName');
    const modalDestinationImage = document.getElementById('modalDestinationImage').querySelector('img');

    // Open modal
    openModalBtns.forEach(button => {
        button.addEventListener('click', () => {
            // Fetch data attributes from the clicked button
            const destinationName = button.getAttribute('data-name');
            const imagePath = button.getAttribute('data-image');
            const destinationId = button.getAttribute('data-id');

            // Populate modal content
            modalDestinationName.textContent = destinationName; // Set name
            modalDestinationImage.src = imagePath;             // Set image source
            modalDestinationImage.alt = destinationName;       // Set image alt text
            modalDestinationID.value = destinationId;          // Set destination ID

            // Display modal
            modal.classList.add('active');
        });
    });

    // Close modal
    closeModal.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    // Close modal if clicking outside it
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.remove('active');
        }
    });
});
