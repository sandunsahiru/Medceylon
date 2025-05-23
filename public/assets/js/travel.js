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


                
                const today = new Date().toISOString().split('T')[0];
                checkInInput.min = today;
                checkInInput.value = today;

               
                checkOutInput.value = '';
                checkOutInput.min = today;

                modal.classList.add('active');
            });

        });

        
        checkInInput.addEventListener('change', () => {
            const checkInDate = checkInInput.value;
            checkOutInput.min = checkInDate;

            
            if (checkOutInput.value && checkOutInput.value <= checkInDate) {
                checkOutInput.value = '';
            }
        });

        
        closeModal.addEventListener('click', () => {
            modal.classList.remove('active');
        });

       
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
                editCheckInInput.value = today;
               
                editCheckOutInput.value = '';
                editCheckOutInput.min = today;

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

    const markCompletedModal = document.getElementById('markCompletedModal');
    const markCompletedButtons = document.querySelectorAll('.mark-completed-button');
    const closeMarkCompletedModal = document.getElementById('closeMarkCompletedModal');
    const cancelMarkCompleted = document.getElementById('cancelMarkCompleted');
    const completeTraveId = document.getElementById('complete_travel_id');

    if (markCompletedModal && markCompletedButtons.length) {
        markCompletedButtons.forEach(button => {
            button.addEventListener('click', () => {
                const travelId = button.getAttribute('data-plan-travelid');
                completeTraveId.value = travelId;
                markCompletedModal.classList.add('active');
            });
        });

        closeMarkCompletedModal.addEventListener('click', () => {
            markCompletedModal.classList.remove('active');
        });

        cancelMarkCompleted.addEventListener('click', () => {
            markCompletedModal.classList.remove('active');
        });

        window.addEventListener('click', (event) => {
            if (event.target === markCompletedModal) {
                markCompletedModal.classList.remove('active');
            }
        });
    }

    const addMemoriesModal = document.getElementById('addMemoriesModal');
    const addMemoriesButtons = document.querySelectorAll('.add-memories-button');
    const closeAddMemoriesModal = document.getElementById('closeAddMemoriesModal');
    const cancelAddMemories = document.getElementById('cancelAddMemories');
    const memoriesTraveId = document.getElementById('memories_travel_id');
    const memoriesDestinationName = document.getElementById('memoriesDestinationName');
    const photoInput = document.getElementById('memory_photos');
    const photoPreviewContainer = document.getElementById('photoPreviewContainer');

    if (addMemoriesModal && addMemoriesButtons.length) {
        addMemoriesButtons.forEach(button => {
            button.addEventListener('click', () => {
                const travelId = button.getAttribute('data-plan-travelid');
                const destinationName = button.getAttribute('data-plan-name');
                
                memoriesTraveId.value = travelId;
                memoriesDestinationName.textContent = destinationName;
                
                if (photoPreviewContainer) {
                    photoPreviewContainer.innerHTML = '';
                }
                
                addMemoriesModal.classList.add('active');
            });
        });


        if (photoInput && photoPreviewContainer) {
            photoInput.addEventListener('change', () => {
                photoPreviewContainer.innerHTML = '';
                
                if (photoInput.files.length > 5) {
                    alert('You can upload a maximum of 5 photos');
                    photoInput.value = '';
                    return;
                }
                
                for (let i = 0; i < photoInput.files.length; i++) {
                    const file = photoInput.files[i];
                    
                    if (!file.type.match('image.*')) {
                        continue;
                    }
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'photo-preview';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.title = file.name;
                        
                        preview.appendChild(img);
                        photoPreviewContainer.appendChild(preview);
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        }

        closeAddMemoriesModal.addEventListener('click', () => {
            addMemoriesModal.classList.remove('active');
        });

        cancelAddMemories.addEventListener('click', () => {
            addMemoriesModal.classList.remove('active');
        });

        window.addEventListener('click', (event) => {
            if (event.target === addMemoriesModal) {
                addMemoriesModal.classList.remove('active');
            }
        });
    }

    
    const viewMemoriesModal = document.getElementById('viewMemoriesModal');
    const viewMemoriesButtons = document.querySelectorAll('.view-memories-button');
    const closeViewMemoriesModal = document.getElementById('closeViewMemoriesModal');
    const viewMemoriesContent = document.getElementById('viewMemoriesContent');
    const viewMemoriesDestinationName = document.getElementById('viewMemoriesDestinationName');

    if (viewMemoriesModal && viewMemoriesButtons.length) {
        viewMemoriesButtons.forEach(button => {
            button.addEventListener('click', () => {
                const travelId = button.getAttribute('data-plan-travelid');
                
               
                viewMemoriesContent.innerHTML = '<div class="memory-loading">Loading memories...</div>';
                viewMemoriesModal.classList.add('active');
                
                
                fetch(`/travelplan/getMemories?travel_id=${travelId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.memories) {
                            const memories = data.memories;
                            viewMemoriesDestinationName.textContent = memories.destination_name;
                            
                            let memoryHTML = `
                                <div class="memory-content">
                                    <div class="memory-rating">
                                        <p>Rating: ${generateStars(memories.rating)}</p>
                                    </div>
                                    <div class="memory-note">
                                        <p>${memories.note || 'No notes added'}</p>
                                    </div>
                                    <div class="memory-date">
                                        <p>Added on: ${new Date(memories.created_at).toLocaleDateString()}</p>
                                    </div>
                                </div>
                            `;
                            
                            if (memories.photos && memories.photos.length > 0) {
                                memoryHTML += '<div class="memory-photos">';
                                memories.photos.forEach(photo => {
                                    memoryHTML += `
                                        <div class="memory-photo">
                                            <img src="${photo.photo_path}" alt="Travel Memory">
                                        </div>
                                    `;
                                });
                                memoryHTML += '</div>';
                            } else {
                                memoryHTML += '<p class="no-photos">No photos added</p>';
                            }
                            
                            viewMemoriesContent.innerHTML = memoryHTML;
                        } else {
                            viewMemoriesContent.innerHTML = '<p class="error-message">Failed to load memories</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching memories:', error);
                        viewMemoriesContent.innerHTML = '<p class="error-message">Failed to load memories</p>';
                    });
            });
        });

        closeViewMemoriesModal.addEventListener('click', () => {
            viewMemoriesModal.classList.remove('active');
        });

        window.addEventListener('click', (event) => {
            if (event.target === viewMemoriesModal) {
                viewMemoriesModal.classList.remove('active');
            }
        });
    }

    
    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<span class="star ${i <= rating ? 'filled' : ''}">★</span>`;
        }
        return stars;
    }
});
