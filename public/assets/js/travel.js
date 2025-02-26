document.addEventListener('DOMContentLoaded', function () {
    // Get the modal and trigger button
    const modal = document.getElementById('addToPlanModal');
    const openModalBtn = document.getElementById('openModal');
    const closeModalBtn = document.getElementById('closeModal');

    console.log("JS Loaded!");

    if (openModalBtn && modal) {
        openModalBtn.addEventListener('click', function () {
            modal.style.display = 'block';
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function () {
            modal.style.display = 'none';
        });
    }

    // Close modal when clicking outside of it
    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
