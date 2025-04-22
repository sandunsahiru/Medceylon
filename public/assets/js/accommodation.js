document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const bookingModal = document.getElementById('bookingModal');
    const bookingForm = document.getElementById('bookingForm');
    const accommodationProviderId = document.getElementById('accommodationProviderId');
    const accommodationName = document.getElementById('accommodationName');
    const checkInDate = document.getElementById('checkInDate');
    const checkOutDate = document.getElementById('checkOutDate');

    const basePath = "http://localhost/Medceylon";
    
    // Add event listeners to all Book buttons
    document.querySelectorAll('.select-accommodation-button').forEach(button => {
        button.addEventListener('click', function() {
            // Get accommodation details from data attributes
            const providerId = this.dataset.id;
            const name = this.dataset.name;
            
            // Set values in the form
            accommodationProviderId.value = providerId;
            accommodationName.value = name;
            
            // Set minimum dates for check-in and check-out
            const today = new Date();
            const tomorrow = new Date();
            tomorrow.setDate(today.getDate() + 1);
            
            checkInDate.min = today.toISOString().split('T')[0];
            checkOutDate.min = tomorrow.toISOString().split('T')[0];
            
            // Clear previous values
            checkInDate.value = '';
            checkOutDate.value = '';
            document.getElementById('accommodationType').value = '';
            document.getElementById('specialRequests').value = '';
            
            // Show the modal
            bookingModal.style.display = 'flex';
        });
    });
    
    // Ensure check-out date is after check-in date
    checkInDate.addEventListener('change', function() {
        const nextDay = new Date(this.value);
        nextDay.setDate(nextDay.getDate() + 1);
        
        // Format the date to YYYY-MM-DD
        const nextDayFormatted = nextDay.toISOString().split('T')[0];
        
        // Set the min attribute of checkOutDate
        checkOutDate.min = nextDayFormatted;
        
        // If current check-out date is before new check-in date, update it
        if (checkOutDate.value && new Date(checkOutDate.value) <= new Date(this.value)) {
            checkOutDate.value = nextDayFormatted;
        }
    });
    
    // Form submission
    bookingForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate dates
        const checkIn = new Date(checkInDate.value);
        const checkOut = new Date(checkOutDate.value);
        
        if (checkOut <= checkIn) {
            showToast('Check-out date must be after check-in date', 'error');
            return;
        }
        
        // Create form data
        const formData = new FormData(this);
        
        // Add status as 'pending'
        formData.append('status', 'pending');
        
        // Add current timestamp as last_updated
        formData.append('last_updated', new Date().toISOString().slice(0, 19).replace('T', ' '));
        
        const submitBtn = this.querySelector('.submit-btn');
        const originalBtnText = submitBtn.innerHTML;

        try {
            submitBtn.innerHTML = '<i class="ri-loader-4-line"></i> Processing...';
            submitBtn.disabled = true;
            
            // Submit booking data to the server
            const response = await fetch(`${basePath}/accommodation/process-booking`, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('Booking request submitted successfully', 'success');
                setTimeout(() => {
                    closeBookingModal();
                }, 1500);
            } else {
                showToast(data.error || 'An error occurred', 'error');
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('An error occurred while processing your booking', 'error');
            
            const submitBtn = this.querySelector('.submit-btn');
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        }
    });
    
    // Close modal functions
    function closeBookingModal() {
        bookingModal.style.display = 'none';
        bookingForm.reset();
    }
    
    // Close button event listeners
    document.querySelector('#bookingModal .close-btn').addEventListener('click', function() {
        closeBookingModal();
    });
    
    document.querySelector('#bookingModal .cancel-btn').addEventListener('click', function() {
        closeBookingModal();
    });
    
    // Close on outside click
    window.addEventListener('click', function(event) {
        if (event.target === bookingModal) {
            closeBookingModal();
        }
    });
    
    // Toast notification system
    function showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        
        // Add toast to container
        toastContainer.appendChild(toast);
        
        // Show toast with animation
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Auto-hide toast after delay
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
