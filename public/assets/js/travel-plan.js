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
    savePlanBtn.addEventListener('click', async function() {
        try {
            // Get all the plan items from the displayed plan
            const planItems = Array.from(document.querySelectorAll('.plan-item')).map(item => {
                return {
                    destination_id: item.querySelector('.edit-dates-btn').getAttribute('data-id'),
                    start_date: item.querySelector('.edit-dates-btn').getAttribute('data-min-start'),
                    end_date: item.querySelector('.edit-dates-btn').getAttribute('data-min-end'),
                    travel_time_hours: parseFloat(item.querySelector('.edit-dates-btn').getAttribute('data-travel-hours')),
                    time_spent_hours: parseFloat(item.querySelector('.edit-dates-btn').getAttribute('data-min-hours')),
                    sequence: Array.from(document.querySelectorAll('.plan-item')).indexOf(item) + 1
                };
            });
    
            const response = await fetch('http://localhost/Medceylon/travelplan/save-complete-plan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `plan_data=${encodeURIComponent(JSON.stringify({
                    items: planItems
                }))}`
            });
    
            if (!response.ok) {
                throw new Error('Failed to save plan');
            }
    
            const result = await response.json();
            if (result.success) {
                alert('Travel plan saved successfully!');
                window.location.reload();
            } else {
                throw new Error(result.error || 'Failed to save plan');
            }
        } catch (error) {
            console.error('Error saving plan:', error);
            alert(`Error: ${error.message}`);
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
        let remainingHoursToday = 8;
        
        const calculatedDates = [];
        
        plan.items.forEach(item => {
            const travelPlusVisit = item.travel_time_hours + item.time_spent_hours;
            let startDate = new Date(currentDate);
            let endDate = new Date(currentDate);
            
            if (travelPlusVisit > remainingHoursToday) {
                currentDate.setDate(currentDate.getDate() + 1);
                remainingHoursToday = 8;
                startDate = new Date(currentDate);
                endDate = new Date(currentDate);
            }
            
            endDate.setHours(endDate.getHours() + travelPlusVisit);
            remainingHoursToday -= travelPlusVisit;
            
            calculatedDates.push({
                id: item.destination_id,
                name: item.destination_name,
                minStartDate: startDate,
                minEndDate: endDate,
                minHours: item.time_spent_hours,
                travelTime: item.travel_time_hours
            });
        });

        const planHTML = `
            <div class="plan-summary">
                <h3>Travel Plan Summary</h3>
                <p>Starting from: ${accommodation.name} (check-out: ${accommodation.check_out})</p>
                <p>Total trip time: ${plan.total_trip_time_hours} hours</p>
                <p>Time in Travel Days: ${plan.travel_days} days</p>
            </div>
            <div class="plan-details">
                ${plan.items.map((item, index) => {
                    const dates = calculatedDates.find(d => d.id === item.destination_id);
                    const formatDate = (date) => date.toISOString().split('T')[0];
                    
                    return `
                        <div class="plan-item">
                            <h4>${index + 1}. ${item.destination_name}</h4>
                            <p>Travel time: ${item.travel_time_hours} hours</p>
                            <p>Visit time: ${item.time_spent_hours} hours (minimum)</p>
                            <p>Calculated dates: ${formatDate(dates.minStartDate)} to ${formatDate(dates.minEndDate)}</p>
                            <button class="edit-dates-btn" 
                                    data-id="${item.destination_id}"
                                    data-name="${item.destination_name}"
                                    data-min-start="${formatDate(dates.minStartDate)}"
                                    data-min-end="${formatDate(dates.minEndDate)}"
                                    data-min-hours="${item.time_spent_hours}"
                                    data-travel-hours="${item.travel_time_hours}"
                                    data-travel-id="${plan.travel_id || 'NEW'}">
                                Edit Dates
                            </button>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
        
        planContainer.innerHTML = planHTML;
        savePlanBtn.style.display = 'block';
        
        // Add event listeners to edit buttons
        document.querySelectorAll('.edit-dates-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const destId = this.getAttribute('data-id');
                const destName = this.getAttribute('data-name');
                const minStart = this.getAttribute('data-min-start');
                const minEnd = this.getAttribute('data-min-end');
                const minHours = this.getAttribute('data-min-hours');
                const travelHours = this.getAttribute('data-travel-hours');
                const travelId = this.getAttribute('data-travel-id');

                console.log('Edit button clicked with:', {
                    travelId, destId, destName, minStart, minEnd
                });
                
                openEditDatesModal(destId, destName, minStart, minEnd, minHours, travelHours, travelId);
            });
        });
    }

    function openEditDatesModal(destId, destName, minStart, minEnd, minHours, travelId) {
        const modal = document.getElementById('editPlanModal');
        if (!modal) return;
    
        // Debug log
        console.log('Opening modal with:', { 
            travelId, 
            destId, 
            minStart, 
            minEnd 
        });
    
        // Set form values
        document.getElementById('modalTravelID').value = travelId;
        document.getElementById('modalDestinationID').value = destId;
        document.getElementById('modalDestinationName').textContent = destName;
        document.getElementById('check_in').value = minStart;
        document.getElementById('check_out').value = minEnd;
        
        modal.classList.add('active');
    }
    // Single form submission handler
    document.getElementById('editPlanForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const travel_id = document.getElementById('travel_id').value;
        console.log("Submitting travel_id:", travel_id);

        
        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
            
            // Validate form before submission
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
    
            // Create FormData and append all required fields
            const formData = new FormData(form);
            
            // Debug: Log what's being sent
            console.log('Submitting:', Object.fromEntries(formData.entries()));
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });
    
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                throw new Error('Server returned an unexpected response');
            }
    
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || `Server error: ${response.status}`);
            }
            
            // Success case
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                alert('Changes saved successfully!');
                document.getElementById('editPlanModal').classList.remove('active');
                // Optional: Refresh the plan display
            }
        } catch (error) {
            console.error('Save failed:', error);
            alert(`Save failed: ${error.message}`);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Changes';
        }
    });

    // Modal close handlers
    document.getElementById('closeEditModal')?.addEventListener('click', () => {
        document.getElementById('editPlanModal')?.classList.remove('active');
    });

    window.addEventListener('click', (event) => {
        if (event.target === document.getElementById('editPlanModal')) {
            document.getElementById('editPlanModal')?.classList.remove('active');
        }
    });

    savePlanBtn.addEventListener('click', async function() {
        try {
            // Generate a temporary ID for new plans
            const tempTravelId = 'temp-' + Date.now();
            
            // Add to each destination
            selectedDestinations.forEach(dest => {
                dest.travel_id = tempTravelId;
            });
    
            const response = await fetch('/travelplan/save-plan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    travel_id: tempTravelId,
                    destinations: selectedDestinations
                })
            });
    
            // Handle response...
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || 'Failed to save plan');
            }
        } catch (error) {
            console.error('Save error:', error);
        }
    });
    

});