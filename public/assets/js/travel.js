document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addToPlanModal');
    const openModalBtns = document.querySelectorAll('.add-destination-button');
    const closeModal = document.getElementById('closeModal');
    const modalDestinationID = document.getElementById('modalDestinationID');
    const modalDestinationName = document.getElementById('modalDestinationName');
    const modalDestinationImage = document.querySelector('#addToPlanModal .destination-image img');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const modalopening = document.getElementById('OpeningTime');
    const modalclosing =  document.getElementById('ClosingTime');
    const modalentry = document.getElementById('EntryFee');

    const basePath = "http://localhost/Medceylon/public/assets/";

    // Open modal
    openModalBtns.forEach(button => {
        button.addEventListener('click', () => {
            const destinationName = button.getAttribute('data-name');
            const imagePath = button.getAttribute('data-image');
            const destinationId = button.getAttribute('data-id');
            const openingTime = button.getAttribute('data-opening');
            const closingTime = button.getAttribute('data-closing');
            const entryFee = button.getAttribute('data-entry');

            modalDestinationName.textContent = destinationName;
            modalDestinationImage.src = basePath + imagePath;
            modalDestinationImage.alt = destinationName;
            modalDestinationID.value = destinationId;
            modalopening.textContent = openingTime;
            modalclosing.textContent = closingTime;
            modalentry.textContent = entryFee;


            // Set minimum for check-in as today
            const today = new Date().toISOString().split('T')[0];
            checkInInput.min = today;
            checkInInput.value = today;

            // Reset checkout
            checkOutInput.value = '';
            checkOutInput.min = today;

            modal.classList.add('active');
        });
    });

    // Update checkout's min when check-in changes
    checkInInput.addEventListener('change', () => {
        const checkInDate = checkInInput.value;
        checkOutInput.min = checkInDate;

        // Clear checkout if it's before check-in
        if (checkOutInput.value && checkOutInput.value <= checkInDate) {
            checkOutInput.value = '';
        }
    });

    // Close modal
    closeModal.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.remove('active');
        }
    });

    document.getElementById('addToPlanForm').addEventListener('submit', () => {
        console.log('Submitting travel plan!');
    });
    
});
