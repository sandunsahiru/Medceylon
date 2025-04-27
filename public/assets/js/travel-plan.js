document.addEventListener('DOMContentLoaded', () => {

    const BASE_URL = window.location.origin + '/Medceylon';
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
    
            const response = await fetch('travelplan/calculate-travel-dates', {
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
    });

    function showBookingRequiredModal(message) {
        const modal = document.createElement('div');
        modal.className = 'booking-required-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h3>Room Booking Required</h3>
                <p>${message}</p>
                <div class="modal-actions">
                    <a href="/room-booking" class="btn primary">Book a Room Now</a>
                    <button class="btn secondary close-modal">Cancel</button>
                </div>
            </div>
        `;
        
        modal.querySelector('.close-modal').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        document.body.appendChild(modal);
    }
    
    // Save plan
    savePlanBtn?.addEventListener('click', async function() {
        try {
            console.log("Save button clicked - starting save process");
            console.log("Raw plan items:", 
                Array.from(document.querySelectorAll('.plan-item')).map(item => ({
                    id: item.querySelector('.edit-dates-btn').getAttribute('data-id'),
                    start: item.querySelector('.edit-dates-btn').getAttribute('data-min-start'),
                    end: item.querySelector('.edit-dates-btn').getAttribute('data-min-end'),
                    travel: item.querySelector('.edit-dates-btn').getAttribute('data-travel-hours'),
                    hours: item.querySelector('.edit-dates-btn').getAttribute('data-min-hours')
                }))
            );
    
            // Process items
            const planItems = Array.from(document.querySelectorAll('.plan-item')).map((item, index) => {
                const btn = item.querySelector('.edit-dates-btn');
                
                // Simple date validation
                const validateDate = (dateStr) => {
                    if (!dateStr) return null;
                    // Basic check for YYYY-MM-DD format
                    if (!/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                        console.warn("Invalid date format:", dateStr);
                        return null;
                    }
                    return dateStr;
                };
    
                return {
                    destination_id: btn.getAttribute('data-id'),
                    start_date: btn.getAttribute('data-min-start'), // Changed from check_in
                    end_date: btn.getAttribute('data-min-end'),    // Changed from check_out
                    travel_time_hours: parseFloat(btn.getAttribute('data-travel-hours')) || 0,
                    time_spent_hours: parseFloat(btn.getAttribute('data-min-hours')) || 2,
                    sequence: index + 1
                };
            });
    
            // Debug: Show processed data
            console.log("Processed data to send:", planItems);
    
            const csrfToken = document.getElementById('csrf_token')?.value;
            
            const response = await fetch('/Medceylon/travelplan/save-plan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken || ''
                },
                body: JSON.stringify({
                    items: planItems,
                    csrf_token: csrfToken
                })
            });
    
            // Debug: Show full response
            console.log("Response status:", response.status);
            const responseBody = await response.text();
            console.log("Response body:", responseBody);
            
            if (!response.ok) {
                throw new Error(`Server error: ${response.status} - ${responseBody}`);
            }
    
            const result = JSON.parse(responseBody);
            if (result.success) {
                alert('Saved successfully!');
                window.location.reload();
            } else {
                throw new Error(result.message || 'Unknown error');
            }
        } catch (error) {
            console.error('Full error:', error);
            alert(`Error: ${error.message}`);
        }
    });

    function updateSelectedDestinations() {
        const selectedList = document.getElementById('selectedDestinations');
        if (selectedList) {
            selectedList.innerHTML = selectedDestinations.map(dest => 
                `<li class="selected-destination">
                    <span>${dest.name}</span>
                    <button class="remove-dest" data-id="${dest.id}" aria-label="Remove destination">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </li>`
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

        const formatDate = (date) => {
            if (!(date instanceof Date)) date = new Date(date);
            return date.toISOString().split('T')[0];
        };

        const planHTML = `
            <div class="plan-summary">
                <h3>Travel Plan Summary</h3>
                <p><strong>Starting from:</strong> ${accommodation.name} (check-out: ${formatDate(accommodation.check_out)})</p>
                <p><strong>Total trip time:</strong> ${plan.total_trip_time_hours} hours</p>
                <p><strong>Estimated travel days:</strong> ${Math.ceil(plan.total_trip_time_hours / 8)} days</p>
            </div>
            <div class="plan-details">
                ${plan.items.map((item, index) => {
                    const dates = calculatedDates.find(d => d.id === item.destination_id);
                    
                    return `
                        <div class="plan-item" data-id="${item.destination_id}">
                            <div class="plan-item-header">
                                <h4>${index + 1}. ${item.destination_name}</h4>
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
                            <div class="plan-item-details">
                                <p><strong>Travel time:</strong> ${item.travel_time_hours} hours</p>
                                <p><strong>Visit time:</strong> ${item.time_spent_hours} hours (minimum)</p>
                                <p><strong>Scheduled dates:</strong> ${formatDate(dates.minStartDate)} to ${formatDate(dates.minEndDate)}</p>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
        
        planContainer.innerHTML = planHTML;
        if (savePlanBtn) savePlanBtn.style.display = 'block';
        
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

                openEditDatesModal(destId, destName, minStart, minEnd, minHours, travelHours, travelId);
            });
        });
    }

    function openEditDatesModal(destId, destName, minStart, minEnd, minHours, travelHours, travelId) {
        // 1. First check if modal exists
        const modal = document.getElementById('editPlanModal');
        if (!modal) {
          console.error('Error: Modal element (#editPlanModal) not found in DOM');
          return;
        }
      
        // 2. Safely get all elements with null checks
        const elements = {
          travelId: document.getElementById('modalTravelID'),
          destId: document.getElementById('modalDestinationID'),
          destName: document.getElementById('modalDestinationName'),
          checkIn: document.getElementById('check_in'),
          checkOut: document.getElementById('check_out'),
          travelTime: document.getElementById('travel_time'),
          minHours: document.getElementById('min_hours')
        };
      
        // 3. Verify all required elements exist
        for (const [key, element] of Object.entries(elements)) {
          if (!element && key !== 'travelTime' && key !== 'minHours') { // Some might be optional
            console.error(`Error: Element #${key} not found in DOM`);
            return;
          }
        }
      
        // 4. Now safely set values
        elements.travelId.value = travelId || '';
        elements.destId.value = destId;
        elements.destName.textContent = destName;
        elements.checkIn.value = minStart;
        elements.checkOut.value = minEnd;
        
        if (elements.travelTime) elements.travelTime.value = travelHours;
        if (elements.minHours) elements.minHours.value = minHours;
      
        // 5. Show modal
        modal.classList.add('active');
      }

      document.getElementById('editPlanForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
    
            const formData = new FormData(form);

            const jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });
            const response = await fetch('/Medceylon/travelplan/save-plan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    items: [jsonData], // Wrap in items array to match savePlan format
                    csrf_token: document.getElementById('csrf_token')?.value
                })
            });
    
            // ... rest of your existing error handling ...
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

    // Escape key to close modal
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.getElementById('editPlanModal')?.classList.remove('active');
        }
    });
});