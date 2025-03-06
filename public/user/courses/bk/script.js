$(document).ready(function() {
    // Initialize Summernote
    $('#description').summernote({
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']]
        ],
        callbacks: {
            onChange: function(contents) {
                $('input[name="description"]').val(contents);
                updateProgress();
            }
        }
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Initialize Hammer.js for swipe gestures
    const tabContent = document.querySelector('.tab-content');
    const hammer = new Hammer(tabContent);
    
    hammer.on('swipeleft swiperight', function(ev) {
        const currentTab = $('.nav-link.active');
        const tabs = $('.nav-link');
        const currentIndex = tabs.index(currentTab);
        
        if (ev.type === 'swipeleft' && currentIndex < tabs.length - 1) {
            tabs.eq(currentIndex + 1).tab('show');
        } else if (ev.type === 'swiperight' && currentIndex > 0) {
            tabs.eq(currentIndex - 1).tab('show');
        }
    });

    // Handle collapsible sections
    $('.collapsible-header').click(function() {
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
        $(this).next('.collapsible-content').slideToggle(300);
    });

    // Show first collapsible section by default
    $('.collapsible-content').first().show();

    // Handle banner image preview
    $('#bannerImage').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#bannerPreview').html(`<img src="${e.target.result}" class="preview-image">`);
            };
            reader.readAsDataURL(file);
            updateProgress();
        }
    });

    // Handle video preview
    $('#introVideo').change(function() {
        const file = this.files[0];
        if (file) {
            const video = document.createElement('video');
            video.classList.add('preview-image');
            video.controls = true;
            video.src = URL.createObjectURL(file);
            $('#videoPreview').html(video);
            updateProgress();
        }
    });

    // Function to show toast message
    function showToast(message, type = 'error') {
        const toast = $('.toast');
        const toastBody = toast.find('.toast-body');
        
        toast.removeClass('bg-success bg-danger')
             .addClass(type === 'error' ? 'bg-danger' : 'bg-success');
        toastBody.html(message);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }

    // Handle form submission and validation
    $('#courseForm').on('submit', function(e) {
        e.preventDefault();
        
        // Remove any existing error messages
        $('.invalid-feedback').remove();
        $('.is-invalid').removeClass('is-invalid');
        
        // Get all required fields
        const requiredFields = $(this).find('[required]');
        let missingFields = [];
        let firstInvalidField = null;
        
        // Check each required field
        requiredFields.each(function() {
            const field = $(this);
            const fieldName = field.closest('.mb-3').find('label').text().replace(' *', '') || field.attr('name');
            
            if (!field.val()) {
                field.addClass('is-invalid');
                field.after('<div class="invalid-feedback">This field is required</div>');
                missingFields.push(fieldName);
                
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            }
        });
        
        // If there are validation errors
        if (missingFields.length > 0) {
            // Build error message for toast
            let errorMessage = '<strong>Please fill in the following required fields:</strong><ul class="mb-0 ps-3">';
            missingFields.forEach(field => {
                errorMessage += `<li>${field}</li>`;
            });
            errorMessage += '</ul>';
            
            // Show toast with missing fields
            showToast(errorMessage);
            
            // Find and open the collapsible section containing the first invalid field
            const collapsibleContent = firstInvalidField.closest('.collapsible-content');
            if (collapsibleContent.length) {
                // Close all sections first
                $('.collapsible-content').slideUp(300);
                $('.collapsible-header i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                
                // Open the section with the invalid field
                collapsibleContent.slideDown(300);
                collapsibleContent.prev('.collapsible-header').find('i')
                    .removeClass('fa-chevron-down')
                    .addClass('fa-chevron-up');
                
                // Scroll to and focus on the first invalid field
                $('html, body').animate({
                    scrollTop: firstInvalidField.offset().top - 100
                }, 500, function() {
                    firstInvalidField.focus();
                });
            }
        } else {
            // If all validation passes, submit the form
            this.submit();
        }
    });

    let topicCount = 0;

});