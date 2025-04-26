document.addEventListener('DOMContentLoaded', function () {
    const basePath = window.location.origin + '/Medceylon'; // Define basePath
    const detailsModal = document.getElementById('detailsModal');
    const bookingModal = document.getElementById('bookingModal');
    const closeButtons = document.querySelectorAll('.close-btn');
    const roomOptions = document.getElementById('roomOptions');
    const totalNights = document.getElementById('totalNights');
    const pricePerNight = document.getElementById('pricePerNight');
    const totalPrice = document.getElementById('totalPrice');
    const checkInDate = document.getElementById('checkInDate');
    const checkOutDate = document.getElementById('checkOutDate');
    const bookNowBtn = document.getElementById('bookNowBtn');
    let selectedRoomPrice = 0;
    let currentProviderId = null;


    // Open Details Modal
    document.querySelectorAll('.view-details-button').forEach(button => {
        button.addEventListener('click', async function () {

            const providerId = this.dataset.id;
            currentProviderId = providerId;

            if (!providerId) {
                alert('Provider ID is missing.');
                return;
            }

            try {
                console.log('Fetching accommodation details for provider ID:', providerId);
                const response = await fetch(`${basePath}/accommodation/get-accommodation-details?provider_id=${providerId}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const data = await response.json();

                if (data.error) {
                    alert(data.error);
                    return;
                }

                const accommodationData = data.data || data;

                document.getElementById('detailsImage').src = `${basePath}/public/assets/${accommodationData.image_path || 'default.jpg'}`;
                document.getElementById('detailsName').textContent = accommodationData.name;
                document.getElementById('detailsAddress').textContent = `${accommodationData.address_line1}, ${accommodationData.address_line2}`;
                document.getElementById('detailsContact').textContent = `Contact: ${accommodationData.contact_info}`;

                const roomTypesList = document.getElementById('roomTypesList');
                roomTypesList.innerHTML = '';

                if (accommodationData.rooms && accommodationData.rooms.length > 0) {
                    bookNowBtn.disabled = false;
                    accommodationData.rooms.forEach((room, index) => {
                        const div = document.createElement('div');
                        div.className = 'room-option';
                        div.innerHTML = `
                            <input type="radio" id="room_${index}" name="room_selection" 
                                value="${room.room_type}" 
                                data-price="${room.cost_per_night}"
                                ${index === 0 ? 'checked' : ''}>
                            <label for="room_${index}">
                                <strong>${room.room_type}</strong><br>
                                Price: LKR ${room.cost_per_night}<br>
                                Services: ${room.services_offered || 'None'}
                            </label>
                        `;
                        roomTypesList.appendChild(div);
                    });

                    selectedRoomPrice = parseFloat(accommodationData.rooms[0].cost_per_night);

                } else {
                    roomTypesList.innerHTML = '<p>No room information available</p>';
                    bookNowBtn.disabled = true;
                }

                roomTypesList.addEventListener('change', function (e) {
                    if (e.target.name === 'room_selection') {
                        selectedRoomPrice = parseFloat(e.target.dataset.price);
                        updateTotalPrice();
                        pricePerNight.textContent = selectedRoomPrice.toFixed(2);
                    }
                });

                detailsModal.classList.add('show');

            } catch (error) {
                console.error('Error fetching accommodation details:', error);
                alert('An error occurred while fetching accommodation details.');
            }
        });
    });

    // Open Booking Modal
    bookNowBtn.addEventListener('click', function () {
        const detailsName = document.getElementById('detailsName').textContent;
        const selectedRoom = document.querySelector('input[name="room_selection"]:checked');

        if (!selectedRoom) {
            alert('Please select a room type.');
            return;
        }

        const roomType = selectedRoom.value;
        selectedRoomPrice = parseFloat(selectedRoom.dataset.price);

        document.getElementById('accommodationName').value = detailsName;
        document.getElementById('accommodationProviderId').value = currentProviderId;
        document.getElementById('accommodationType').value = roomType;

        pricePerNight.textContent = selectedRoomPrice.toFixed(2);

        // Set dates with timezone safety
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        
        // Format dates for input fields (YYYY-MM-DD)
        const todayFormatted = today.toISOString().split('T')[0];
        const tomorrowFormatted = tomorrow.toISOString().split('T')[0];

        checkInDate.min = todayFormatted;
        checkOutDate.min = tomorrowFormatted;

        // Set default values to tomorrow and day after tomorrow for safety
        checkInDate.value = tomorrowFormatted;
        const dayAfterTomorrow = new Date(today);
        dayAfterTomorrow.setDate(today.getDate() + 2);
        checkOutDate.value = dayAfterTomorrow.toISOString().split('T')[0];

        updateTotalPrice();

        bookingModal.classList.add('show');
    });

    // Close Modals
    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modal = this.closest('.modal');
            modal.classList.remove('show');
        });
    });

    window.addEventListener('click', function (event) {
        if (event.target === detailsModal) detailsModal.classList.remove('show');
        if (event.target === bookingModal) bookingModal.classList.remove('show');
    });

    // Update Total Price
    function updateTotalPrice() {
        const checkIn = new Date(checkInDate.value);
        const checkOut = new Date(checkOutDate.value);

        if (checkOut <= checkIn) {
            alert("Check-out date must be after check-in date.");
            const newCheckOut = new Date(checkIn);
            newCheckOut.setDate(checkIn.getDate() + 1);
            checkOutDate.value = newCheckOut.toISOString().split('T')[0];
            return;
        }

        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));

        totalNights.textContent = nights;
        totalPrice.textContent = (nights * selectedRoomPrice).toFixed(2);
    }

    checkInDate.addEventListener('change', updateTotalPrice);
    checkOutDate.addEventListener('change', updateTotalPrice);

    // Add form submission handler
    const bookingForm = document.getElementById('bookingForm');
    bookingForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!isLoggedIn) {
            alert('You must be logged in to book accommodation');
            return;
        }
        
        // Create FormData object from the form
        const formData = new FormData(bookingForm);
        
        // Fix the naming of fields to match what the controller expects
        formData.set('provider_id', document.getElementById('accommodationProviderId').value);
        formData.set('room_type', document.getElementById('accommodationType').value);
        formData.set('total_price', parseFloat(totalPrice.textContent));
        
        // Debug: Log all form data being sent
        console.log("Form data being submitted:");
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        try {
            const response = await fetch(`${basePath}/accommodation/process-booking`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Booking successful!');
                bookingModal.classList.remove('show');
                // Redirect to booking details page on success
                console.log("Redirecting to:", `${basePath}/accommodation/accommodation-providers`);
                window.location.href = `${basePath}/accommodation/accommodation-providers`;
            } else {
                alert('Booking failed: ' + (result.error || 'Unknown error'));
            }
            
        } catch (error) {
            console.error('Error submitting booking:', error);
            alert('An error occurred while processing your booking');
        }
    });

    document.querySelectorAll('.delete-booking').forEach(button => {
        button.addEventListener('click', async function() {
            const bookingId = this.dataset.bookingId;
            const confirmed = confirm('Are you sure you want to cancel this booking?');
            
            if (!confirmed) return;
            
            try {
                const response = await fetch(`${basePath}/accommodation/delete-booking`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `booking_id=${bookingId}&csrf_token=${document.querySelector('input[name="csrf_token"]').value}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Booking cancelled successfully');
                    location.reload(); // Refresh the page to show updated list
                } else {
                    alert('Failed to cancel booking: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error cancelling booking:', error);
                alert('An error occurred while cancelling the booking');
            }
        });
    });
});