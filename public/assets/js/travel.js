document.addEventListener('DOMContentLoaded', () => {

    console.log('JS loaded');

    const modal = document.getElementById('addToPlanModal');
    const openModalBtns = document.querySelectorAll('.add-destination-button');
    const closeModal = document.getElementById('closeModal');
    const modalDestinationID = document.getElementById('modalDestinationID');
    const modalDestinationName = document.getElementById('modalDestinationName');
    const modalDestinationImage = document.querySelector('#addToPlanModal .destination-image-modal img');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const modalopening = document.getElementById('OpeningTime');
    const modalclosing =  document.getElementById('ClosingTime');
    const modalentry = document.getElementById('EntryFee');

    const basePath = "http://localhost/Medceylon/public/assets/";

    if (modal && modalDestinationID && checkInInput && checkOutInput) {
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
    }

    const editModal = document.getElementById('editDestinationModal');
    const editButtons = document.querySelectorAll('.edit-button');
    const closeEditModal = document.getElementById('closeEditModal');
    const modalEditDestinationName = document.getElementById('modalEditDestinationName');
    const modalEditDestinationImage = document.getElementById('modalEditDestinationImage');
    const editCheckInInput = document.getElementById('edit_check_in');
    const editCheckOutInput = document.getElementById('edit_check_out');
    const destinationIdInput = document.getElementById('destination_id');
    const travelIdInput = document.getElementById('travel_id');

    // Only run if edit modal exists
    if (editModal && closeEditModal && modalEditDestinationName && modalEditDestinationImage) {
        
        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                const destinationName = button.getAttribute('data-plan-name');
                const checkIn = button.getAttribute('data-plan-checkin');
                const checkOut = button.getAttribute('data-plan-checkout');
                const imagePath = button.getAttribute('data-plan-image');
                const destinationId = button.getAttribute('data-plan-id');
                const travelId = button.getAttribute('data-plan-travelid');

                modalEditDestinationName.textContent = destinationName;
                modalEditDestinationImage.src = basePath + imagePath;
                modalEditDestinationImage.alt = destinationName;
                editCheckInInput.value = checkIn;
                editCheckOutInput.value = checkOut;
                destinationIdInput.value = destinationId;
                travelIdInput.value = travelId;

                const today = new Date().toISOString().split('T')[0];
                editCheckInInput.min = today;

                editModal.classList.add('active');
            });
        });

        closeEditModal.addEventListener('click', () => {
            editModal.classList.remove('active');
        });

        window.addEventListener('click', (event) => {
            if (event.target === editModal) {
                editModal.classList.remove('active');
            }
        });
    }

    const deleteButtons = document.querySelectorAll('.delete-button');
    const deletemodal = document.getElementById('warningModalContainer');
    const cancelBtn = document.getElementById('cancelDelete');
    const confirmBtn = document.getElementById('confirmDelete');
    const form = document.getElementById('deleteForm');

    
        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                console.log('delete');
                const travelId = button.getAttribute('data-travel-id');
                const destinationId = button.getAttribute('data-destination-id');

                document.getElementById('travel_id').value = travelId;
                document.getElementById('destination_id').value = destinationId;

                deletemodal.classList.add('active');
            });
        });

        cancelBtn.addEventListener('click', () => {
            console.log('cancel');
            deletemodal.classList.remove('active');
        });

        confirmBtn.addEventListener('click', () => {
            form.submit();
        });
    
        
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const townSelect = document.getElementById('town');

    provinceSelect.addEventListener('change', function () {
        const provinceId = this.value;
        console.log("Province changed: ", provinceId);

        // Clear the district and town dropdowns
        districtSelect.innerHTML = '<option value="">Select District</option>';
        townSelect.innerHTML = '<option value="">Select Town</option>';

        if (provinceId) {
            fetch('/TravelPlan/districts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `province_id=${provinceId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.districts.forEach(d => {
                        const option = document.createElement('option');
                        option.value = d.district_id;
                        option.textContent = d.district_name;
                        districtSelect.appendChild(option);
                    });
                } else {
                    alert(data.error || 'Failed to load districts');
                }
            });
        }
    });

    districtSelect.addEventListener('change', function () {
        const districtId = this.value;

        townSelect.innerHTML = '<option value="">Select Town</option>';

        if (districtId) {
            fetch('/TravelPlan/towns', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `district_id=${districtId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.towns.forEach(t => {
                        const option = document.createElement('option');
                        option.value = t.town_id;
                        option.textContent = t.town_name;
                        townSelect.appendChild(option);
                    });
                } else {
                    alert(data.error || 'Failed to load towns');
                }
            });
        }
    });
});
