document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on the correct page
    const planContainer = document.getElementById('travelPlanContainer');
    const calculateBtn = document.getElementById('calculatePlanBtn');
    const savePlanBtn = document.getElementById('savePlanBtn');
    
    if (!planContainer || !calculateBtn) return;

    const selectedDestinations = [];
    
    // Add destination to selection
    document.querySelectorAll('.add-destination-button').forEach(btn => {
        btn.addEventListener('click', function() {
            const destId = this.getAttribute('data-id');
            const destName = this.getAttribute('data-name');
            
            if (!selectedDestinations.some(d => d.id === destId)) {
                selectedDestinations.push({
                    id: destId,
                    name: destName
                });
                updateSelectedDestinations();
            }
        });
    });
    
    // Calculate plan
    calculateBtn.addEventListener('click', async function() {
        if (selectedDestinations.length === 0) {
            alert('Please select at least one destination');
            return;
        }
        
        try {
            calculateBtn.disabled = true;
            calculateBtn.textContent = 'Calculating...';
    
            const response = await fetch('http://localhost/Medceylon/travelplan/calculate-travel-dates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    destination_ids: selectedDestinations.map(d => d.id)
                })
            });
    
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || 'Failed to calculate plan');
            }
            
            const data = await response.json();

            if (data.accommodation.name === 'Default Location') {
                alert('No accommodation booking found. Using default starting location.');
            }
            
            displayTravelPlan(data.plan, data.accommodation);
            
        } catch (error) {
            console.error('Error:', error);
            alert(`Error: ${error.message}`);
        } finally {
            calculateBtn.disabled = false;
            calculateBtn.textContent = 'Calculate Travel Plan';
        }
    }
    
    );

    function showBookingRequiredModal(message) {
        // Implement UI to guide user to book a room
        const modal = `
            <div class="booking-required-modal">
                <h3>Room Booking Required</h3>
                <p>${message}</p>
                <a href="/room-booking" class="btn">Book a Room Now</a>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modal);
    }
    
    // Save plan
    savePlanBtn.addEventListener('click', async function calculatePlan() {
        const calculateBtn = document.getElementById('calculatePlanBtn');
        if (!calculateBtn) return;
    
        try {
            // UI feedback
            calculateBtn.disabled = true;
            calculateBtn.textContent = 'Calculating...';
            
            const response = await fetch('http://localhost/Medceylon/travelplan/calculate-travel-dates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    destination_ids: selectedDestinations.map(d => d.id)
                }),
                credentials: 'include'
            });
    
            // Handle empty responses
            if (response.status === 500) {
                throw new Error('Server error occurred. Please try again later.');
            }
    
            // Check content type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                throw new Error('Invalid server response');
            }
    
            const data = await response.json();
            
            if (!response.ok || !data.success) {
                throw new Error(data.error || 'Calculation failed');
            }
            
            // Success case
            displayTravelPlan(data.plan, data.accommodation);
            
        } catch (error) {
            console.error('Error:', error);
            // Replace showErrorAlert with standard alert
            alert(`Error: ${error.message}`);
            
            // Optional: Show more details in console for debugging
            console.debug('Error details:', {
                error: error,
                selectedDestinations: selectedDestinations
            });
        } finally {
            // Reset UI
            calculateBtn.disabled = false;
            calculateBtn.textContent = 'Calculate Travel Plan';
        }
    });
    
    function updateSelectedDestinations() {
        const selectedList = document.getElementById('selectedDestinations');
        if (selectedList) {
            selectedList.innerHTML = selectedDestinations.map(dest => 
                `<li>${dest.name} <button class="remove-dest" data-id="${dest.id}">×</button></li>`
            ).join('');
            
            document.querySelectorAll('.remove-dest').forEach(btn => {
                btn.addEventListener('click', function() {
                    const destId = this.getAttribute('data-id');
                    const index = selectedDestinations.findIndex(d => d.id === destId);
                    if (index !== -1) {
                        selectedDestinations.splice(index, 1);
                        updateSelectedDestinations();
                    }
                });
            });
        }
    }
    
    function displayTravelPlan(plan, accommodation) {
        let currentDate = new Date(accommodation.check_out);
        let remainingHoursToday = 8; // Start with 8 hours available in the first day
        
        const planHTML = `
            <div class="plan-summary">
                <h3>Travel Plan Summary</h3>
                <p>Starting from: ${accommodation.name} (check-out: ${accommodation.check_out})</p>
                <p>Total trip time: ${plan.total_trip_time_hours} hours</p>
            </div>
            <div class="plan-details">
                ${plan.items.map((item, index) => {
                    const travelPlusVisit = item.travel_time_hours + item.time_spent_hours;
                    let startDate = new Date(currentDate);
                    let endDate = new Date(currentDate);
                    
                    // Check if we need to move to next day
                    if (travelPlusVisit > remainingHoursToday) {
                        // Move to next day
                        currentDate.setDate(currentDate.getDate() + 1);
                        remainingHoursToday = 8; // Reset to 8 hours for new day
                        startDate = new Date(currentDate);
                        endDate = new Date(currentDate);
                    }
                    
                    // Calculate end time
                    endDate.setHours(endDate.getHours() + travelPlusVisit);
                    remainingHoursToday -= travelPlusVisit;
                    
                    // Format dates for display (YYYY-MM-DD)
                    const formatDate = (date) => date.toISOString().split('T')[0];
                    
                    return `
                        <div class="plan-item">
                            <h4>${index + 1}. ${item.destination_name}</h4>
                            <p>Travel time: ${item.travel_time_hours} hours</p>
                            <p>Visit time: ${item.time_spent_hours} hours</p>
                            <p>Dates: ${formatDate(startDate)} to ${formatDate(endDate)}</p>
                        </div>
                    `;
                }).join('')}
            </div>
            <input type="hidden" id="planData" value='${JSON.stringify(plan)}'>
        `;
        
        planContainer.innerHTML = planHTML;
    }
});