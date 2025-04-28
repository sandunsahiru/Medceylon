document.addEventListener('DOMContentLoaded', () => {

    const BASE_URL = window.location.origin + '/Medceylon';
    const planContainer = document.getElementById('travelPlanContainer');
    const calculateBtn = document.getElementById('calculatePlanBtn');
    const savePlanBtn = document.getElementById('savePlanBtn');
    
    if (!planContainer || !calculateBtn) return;

    const selectedDestinations = [];
    
    
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
    
    
    savePlanBtn?.addEventListener('click', async function () {
        try {
            console.log("Save button clicked - starting save process");
    
            const planItems = Array.from(document.querySelectorAll('.plan-item')).map((item, index) => {
                const btn = item.querySelector('.edit-dates-btn');
                return {
                    destination_id: btn.getAttribute('data-id'),
                    start_date: btn.getAttribute('data-min-start'),
                    end_date: btn.getAttribute('data-min-end'),
                    travel_time_hours: parseFloat(btn.getAttribute('data-travel-hours')) || 0,
                    time_spent_hours: parseFloat(btn.getAttribute('data-min-hours')) || 2,
                    sequence: index + 1
                };
            });
    
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
                </li>`).join('');
            
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
                                        data-travel-hours="${item.travel_time_hours}">
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
        
        document.getElementById('editPlanForm').addEventListener('submit', async function(e) {
            e.preventDefault(); 
            
            const formData = {
                travel_id: document.getElementById('modalTravelID').value,
                destination_id: document.getElementById('modalDestinationID').value,
                check_in: document.getElementById('check_in').value,
                check_out: document.getElementById('check_out').value,
                travel_time: document.getElementById('travel_time').value,
                min_hours: document.getElementById('min_hours').value,
                csrf_token: document.getElementById('csrf_token').value
            };
        
            try {
                const response = await fetch('/Medceylon/travelplan/edit-destination-dates', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': formData.csrf_token
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Changes saved successfully!');
                    closeEditModal(); 
                    if (typeof refreshTravelPlan === 'function') {
                        refreshTravelPlan();
                    }
                } else {
                    throw new Error(result.message || 'Failed to save changes');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error saving changes: ' + error.message);
            }
        });
        
        const editButtons = document.querySelectorAll('.edit-dates-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const travelData = {
                    id: this.getAttribute('data-id'),
                    name: this.getAttribute('data-name'),
                    minStartDate: this.getAttribute('data-min-start'),
                    minEndDate: this.getAttribute('data-min-end'),
                    minHours: this.getAttribute('data-min-hours'),
                    travelTime: this.getAttribute('data-travel-hours')
                };
                openEditModal(travelData);
            });
        });
    }
    
  
    const modal = document.getElementById('editPlanModal');
    const closeModalButton = document.getElementById('closeEditModal');



function openEditModal(travelData) {
    if (!travelData) {
        console.error("No travel data available");
        return;
    }

    console.log("Opening modal with data:", travelData);

    
    document.getElementById('modalTravelID').value = travelData.travel_id || '';
    document.getElementById('modalDestinationID').value = travelData.id;
    document.getElementById('modalDestinationName').textContent = travelData.name;
    document.getElementById('check_in').value = travelData.minStartDate;
    document.getElementById('travel_time').value = travelData.travelTime;
    document.getElementById('min_hours').value = travelData.minHours;
    
    
    document.getElementById('editStartDate').value = travelData.minStartDate;
    document.getElementById('travelTime').value = travelData.travelTime;
    document.getElementById('editStayDuration').value = travelData.minHours;
    document.getElementById('minStayDuration').textContent = travelData.minHours;
    
   
    const endDate = new Date(travelData.minStartDate);
    endDate.setHours(endDate.getHours() + parseInt(travelData.minHours));
    document.getElementById('check_out').value = endDate.toISOString().split('T')[0];
    document.getElementById('editEndDate').value = endDate.toISOString().split('T')[0];

   
    modal.classList.add('active');
    modal.style.display = 'block';
}


function closeEditModal() {
    modal.classList.remove('active');
    modal.style.display = 'none';
}


closeModalButton.addEventListener('click', closeEditModal);
window.addEventListener('click', (event) => {
    if (event.target === modal) closeEditModal();
});


document.getElementById('editPlanForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        travel_id: document.getElementById('modalTravelID').value,
        destination_id: document.getElementById('modalDestinationID').value,
        check_in: document.getElementById('check_in').value,
        check_out: document.getElementById('check_out').value,
        travel_time: document.getElementById('travel_time').value,
        min_hours: document.getElementById('min_hours').value,
        csrf_token: document.getElementById('csrf_token').value
    };

    try {
        const response = await fetch('/Medceylon/travelplan/edit-destination-dates', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': formData.csrf_token
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Changes saved successfully!');
            closeEditModal();
            // Refresh the plan display without full page reload
            if (typeof displayTravelPlan === 'function' && typeof refreshPlanData === 'function') {
                const newPlan = await refreshPlanData();
                displayTravelPlan(newPlan.plan, newPlan.accommodation);
            }
        } else {
            throw new Error(result.message || 'Failed to save changes');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error saving changes: ' + error.message);
    }
});

});
