document.querySelectorAll('.add-destination-button').forEach(button => {
    button.addEventListener('click', function () {
        // Get destination details from button data attributes
        const destinationName = this.getAttribute('data-name');
        const destinationImage = this.getAttribute('data-image');

        // Populate modal fields
        document.getElementById('destinationName').value = destinationName;
        document.getElementById('destinationImagePreview').src = destinationImage;

        // Open modal
        document.getElementById('addToPlanModal').style.display = 'block';
    });
});

// Close modal
document.getElementById('closeModal').addEventListener('click', function () {
    document.getElementById('addToPlanModal').style.display = 'none';
});
