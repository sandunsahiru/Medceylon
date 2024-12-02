
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('travelModal');  // Modal container
    const openModalBtn = document.getElementById('openModal');  // Button to trigger modal
    const closeModalBtn = document.querySelector('.close-btn');  // Close button inside the modal
    const travelForm = document.getElementById('travelForm');  // Travel preferences form
    
    // Open modal when "Do It For Me" button is clicked
    openModalBtn.addEventListener('click', () => {
        modal.style.display = 'block';  // Show modal
    });

    // Close modal when "X" button is clicked
    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';  // Hide modal
    });

    // Close modal if clicking outside of the modal content
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';  // Hide modal if the user clicks outside of the modal content
        }
    });

    // Handle form submission (optional, add your form handling logic)
    travelForm.addEventListener('submit', (event) => {
        event.preventDefault();  // Prevent default form submission
        const formData = new FormData(travelForm);  // Gather form data

        // Optional: You can send the form data using AJAX or fetch to process it
        fetch('/path-to-handle-form', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Handle successful response
            alert('Your travel plan has been generated!');
            modal.style.display = 'none';  // Close modal after form submission
        })
        .catch(error => {
            // Handle error
            alert('There was an error generating your travel plan. Please try again.');
        });
    });
});
