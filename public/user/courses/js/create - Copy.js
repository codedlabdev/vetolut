// create.js

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation
    initializeFormValidation();

    // Initialize section navigation
    initializeSectionNavigation();

    // Initialize add section functionality
    initializeAddSection();

    // Handle file previews
    handleFilePreviews();

    // Handle form submission
    handleFormSubmission();
});

// Form validation initialization
function initializeFormValidation() {
    const courseForm = document.getElementById('courseBasicForm');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    // Add input event listeners for validation
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('input', () => validateFields());
    });

    // Handle section navigation
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const currentSection = document.querySelector('.section:not([style*="display: none"])');
            if (currentSection && validateCurrentSection(currentSection)) {
                const nextSection = currentSection.nextElementSibling;
                if (nextSection && nextSection.classList.contains('section')) {
                    currentSection.style.display = 'none';
                    nextSection.style.display = 'block';
                    prevBtn.style.display = 'block';
                    
                    // Show submit button on last section
                    if (!nextSection.nextElementSibling) {
                        nextBtn.style.display = 'none';
                        submitBtn.style.display = 'block';
                    }
                }
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            const currentSection = document.querySelector('.section:not([style*="display: none"])');
            if (currentSection) {
                const prevSection = currentSection.previousElementSibling;
                if (prevSection && prevSection.classList.contains('section')) {
                    currentSection.style.display = 'none';
                    prevSection.style.display = 'block';
                    nextBtn.style.display = 'block';
                    submitBtn.style.display = 'none';
                    
                    if (!prevSection.previousElementSibling) {
                        prevBtn.style.display = 'none';
                    }
                }
            }
        });
    }

    // Handle form submission
    if (courseForm) {
        courseForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            // Validate all required fields
            const requiredFields = courseForm.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields');
                return;
            }

            try {
                const formData = new FormData(courseForm);
                
                // Log form data for debugging
                console.log('Submitting form data:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                const response = await fetch(courseForm.action, {
                    method: 'POST',
                    body: formData
                });

                const responseText = await response.text();
                console.log('Server response:', responseText);

                if (responseText.includes('Success')) {
                    alert('Course created successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + responseText.split('\n')[0]);
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Error submitting form. Please try again.');
            }
        });
    }
}

// Validate current section
function validateCurrentSection(section) {
    const requiredFields = section.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Section navigation initialization
function initializeSectionNavigation() {
    const sections = ['basic-section', 'media-section', 'sections-section', 'price-section', 'publish-section'];
    const progressSteps = document.querySelectorAll('.progress-step');
    let currentSection = 0;

    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');

    if (!nextBtn || !prevBtn) {
        console.error('Navigation buttons not found');
        return;
    }

    function showSection(index) {
        sections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (section) {
                section.style.display = 'none';
            }
        });

        const currentSection = document.getElementById(sections[index]);
        if (currentSection) {
            currentSection.style.display = 'block';
        }

        progressSteps.forEach((step, i) => {
            if (i <= index) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        prevBtn.style.display = index === 0 ? 'none' : 'block';
        nextBtn.textContent = index === sections.length - 1 ? 'Publish' : 'Next';

        // Validate fields after showing the section
        validateFields();
    }

    nextBtn.addEventListener('click', () => {
        if (currentSection < sections.length - 1) {
            currentSection++;
            showSection(currentSection);
        } else {
            submitForm();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentSection > 0) {
            currentSection--;
            showSection(currentSection);
        }
    });

    // Initialize first section
    showSection(0);
}

// Initialize add section functionality
function initializeAddSection() {
    const addSectionBtn = document.getElementById('addSectionBtn');
    const courseSections = document.getElementById('course-sections');

    if (!addSectionBtn || !courseSections) {
        console.error('Add Section button or course sections container not found');
        return;
    }

    addSectionBtn.addEventListener('click', function() {
        const newSection = createSection();
        courseSections.appendChild(newSection);
        // Validate fields after adding a new section
        validateFields();
    });
}

// Create a new section element
function createSection() {
    const sectionContainer = document.createElement('div');
    sectionContainer.classList.add('section-container', 'mb-4', 'p-3', 'border', 'rounded');

    const sectionHTML = `
        <div class="mb-3">
            <label for="sectionTitle" class="form-label">Title</label>
            <input type="text" class="form-control" name="sectionTitle" required>
        </div>
        <div class="mb-3">
            <label for="sectionDescription" class="form-label">Description</label>
            <textarea class="form-control" name="sectionDescription" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="sectionVideo" class="form-label">Video</label>
            <input type="file" class="form-control" name="sectionVideo" accept="video/*" required>
        </div>
        <input type="hidden" class="form-control" name="sectionPosition" value="1" required>
        <button type="button" class="btn btn-danger remove-section-btn">
            <i class="fas fa-trash"></i> Remove Section
        </button>
    `;

    sectionContainer.innerHTML = sectionHTML;

    // Add event listener to remove the section
    const removeBtn = sectionContainer.querySelector('.remove-section-btn');
    removeBtn.addEventListener('click', function() {
        sectionContainer.remove();
        // Validate fields after removing a section
        validateFields();
    });

    // Add event listeners to section fields for validation
    const sectionFields = sectionContainer.querySelectorAll('input[name="sectionTitle"], input[name="sectionVideo"]');
    sectionFields.forEach(field => {
        field.addEventListener('input', validateFields);
        field.addEventListener('change', validateFields);
    });

    return sectionContainer;
}

// Handle file previews
function handleFilePreviews() {
    const previewImage = document.getElementById('previewImage');
    const imagePreview = document.getElementById('imagePreview');

    if (previewImage && imagePreview) {
        previewImage.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    const previewVideo = document.getElementById('previewVideo');
    const videoPreview = document.getElementById('videoPreview');

    if (previewVideo && videoPreview) {
        previewVideo.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const videoElement = document.createElement('video');
                videoElement.classList.add('preview-video');
                videoElement.controls = true;
                videoElement.src = URL.createObjectURL(file);
                videoPreview.innerHTML = '';
                videoPreview.appendChild(videoElement);
            }
        });
    }
}

// Form submission logic
function handleFormSubmission() {
    const courseForm = document.getElementById('courseBasicForm');
    if (courseForm) {
        courseForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            try {
                // Get the form element
                const form = document.getElementById('courseBasicForm');
                
                // Create FormData from the form
                const formData = new FormData(form);
                
                // Log the form data for debugging
                console.log('Form action URL:', form.action);
                console.log('Form data entries:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                // Send request
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                // Get the response text
                const responseText = await response.text();
                console.log('Server response:', responseText);

                if (responseText.includes('Debug Info')) {
                    console.log('Server debug information:', responseText);
                }

                if (responseText.includes('Success')) {
                    alert('Course created successfully!');
                    // Optionally redirect to a success page
                    // window.location.href = 'success.php';
                } else {
                    alert('Error: ' + responseText.split('\n')[0]); // Show only the first line of error
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Error submitting form. Please try again.');
            }
        });
    } else {
        console.error('Course form not found');
    }
}

// Validate fields function
function validateFields() {
    const currentSection = document.querySelector('.section:not([style*="display: none"])');
    const nextBtn = document.getElementById('nextBtn');
    
    if (currentSection.id === 'basic-section') {
        const courseTitle = document.getElementById('courseTitle')?.value.trim() || '';
        const courseDescription = document.getElementById('courseDescription')?.value.trim() || '';
        const courseCategory = document.getElementById('courseCategory')?.value || '';
    
        const isValid = courseTitle !== '' &&
                        courseDescription !== '' &&
                        courseCategory !== '' &&
                        courseCategory !== 'null';
    
        nextBtn.disabled = !isValid;
    } else if (currentSection.id === 'media-section') {
        const previewImage = document.getElementById('previewImage')?.files[0];
        const previewVideo = document.getElementById('previewVideo')?.files[0];
        const durationHours = document.getElementById('duration_hours')?.value || '';
        const durationMinutes = document.getElementById('duration_minutes')?.value || '';

        const isValid = previewImage !== undefined &&
                        previewVideo !== undefined &&
                        durationHours !== '' &&
                        durationMinutes !== '' &&
                        durationMinutes !== '0';

        nextBtn.disabled = !isValid;
    } else if (currentSection.id === 'sections-section') {
        const sections = document.querySelectorAll('.section-container');
        let isValid = sections.length > 0; // Ensure at least one section is added

        sections.forEach(section => {
            const sectionTitle = section.querySelector('input[name="sectionTitle"]')?.value.trim() || '';
            const sectionVideo = section.querySelector('input[name="sectionVideo"]')?.files[0];

            if (sectionTitle === '' || !sectionVideo) {
                isValid = false;
            }
        });

        nextBtn.disabled = !isValid;
    } else if (currentSection.id === 'price-section') {
        const coursePriceOption = document.getElementById('coursePriceOption')?.value || '';
        const coursePrice = document.getElementById('coursePrice')?.value || '';

        const isValid = coursePriceOption === 'free' || (coursePriceOption === 'paid' && parseFloat(coursePrice) > 0);

        nextBtn.disabled = !isValid;
    } else if (currentSection.id === 'publish-section') {
        const publishStatus = document.getElementById('publishStatus')?.value || '';
        const isValid = publishStatus !== '';
        nextBtn.disabled = !isValid;
    }
}

// Submit form function
function submitForm() {
    const courseForm = document.getElementById('courseBasicForm');
    if (courseForm) {
        courseForm.submit();
    }
}
