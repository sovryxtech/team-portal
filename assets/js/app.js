/**
 * Frontend Utilities for Employee Management & Verification System
 */

$(document).ready(function() {
    // Enable Bootstrap Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Refresh Captcha Image Trigger
    $('.refresh-captcha').on('click', function(e) {
        e.preventDefault();
        var captchaImg = $(this).closest('.captcha-container').find('.captcha-img');
        if (captchaImg.length) {
            // Append random string to query to bypass cache
            var currentSrc = captchaImg.attr('src').split('?')[0];
            captchaImg.attr('src', currentSrc + '?r=' + Math.random());
        }
    });
});

/**
 * Handle Multi-Step Registration Form Wizard
 */
function initRegistrationWizard(formSelector) {
    var $form = $(formSelector);
    if (!$form.length) return;

    var currentStep = 1;
    var totalSteps = $form.find('.wizard-step-content').length;

    // Show initial step
    showStep(currentStep);

    // Next button click
    $form.find('.btn-next').on('click', function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });

    // Previous button click
    $form.find('.btn-prev').on('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    function showStep(stepNum) {
        // Hide all step contents
        $form.find('.wizard-step-content').addClass('d-none');
        
        // Show current step content
        $form.find('.wizard-step-content[data-step="' + stepNum + '"]').removeClass('d-none');

        // Update Wizard Steps UI
        $('.wizard-step-node').removeClass('active completed');
        for (var i = 1; i <= totalSteps; i++) {
            var $node = $('.wizard-step-node[data-step="' + i + '"]');
            if (i < stepNum) {
                $node.addClass('completed');
            } else if (i === stepNum) {
                $node.addClass('active');
            }
        }

        // Toggle buttons visibility
        if (stepNum === 1) {
            $form.find('.btn-prev').addClass('d-none');
        } else {
            $form.find('.btn-prev').removeClass('d-none');
        }

        if (stepNum === totalSteps) {
            $form.find('.btn-next').addClass('d-none');
            $form.find('.btn-submit').removeClass('d-none');
        } else {
            $form.find('.btn-next').removeClass('d-none');
            $form.find('.btn-submit').addClass('d-none');
        }
    }

    function validateStep(stepNum) {
        var isValid = true;
        var $currentContainer = $form.find('.wizard-step-content[data-step="' + stepNum + '"]');
        
        // Basic required inputs validation
        $currentContainer.find('input[required], select[required], textarea[required]').each(function() {
            var $input = $(this);
            if (!$input.val() || ($input.attr('type') === 'checkbox' && !$input.is(':checked'))) {
                isValid = false;
                $input.addClass('is-invalid');
                
                // Add change/input listener to clear validation
                $input.off('input change').on('input change', function() {
                    if ($(this).val()) {
                        $(this).removeClass('is-invalid');
                    }
                });
            } else {
                $input.removeClass('is-invalid');
            }
        });

        // Specific field validations (e.g. Email layout)
        if (isValid) {
            $currentContainer.find('input[type="email"]').each(function() {
                var emailVal = $(this).val();
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailVal && !emailRegex.test(emailVal)) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                    Swal.fire('Validation Error', 'Please enter a valid email address.', 'warning');
                }
            });
        }

        // Custom validation for Email OTP in Registration (Step 2)
        if (isValid && stepNum === 2 && $('#emailVerified').length && $('#emailVerified').val() !== '1') {
            isValid = false;
            Swal.fire({
                icon: 'warning',
                title: 'Email Verification Required',
                text: 'Please verify your email address using the OTP before proceeding.',
                confirmButtonColor: '#0B2545'
            });
        }

        if (!isValid && stepNum !== 2 || (!isValid && stepNum === 2 && $('#emailVerified').val() === '1')) {
            // Only show generic required fields warning if it's not the OTP error
            var hasInvalidInput = $currentContainer.find('.is-invalid').length > 0;
            if(hasInvalidInput) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Fields',
                    text: 'Please complete all required fields on this step before proceeding.',
                    confirmButtonColor: '#0B2545'
                });
            }
        }

        return isValid;
    }
}

/**
 * Standard AJAX Form Submission wrapper with SweetAlert2 integration
 */
function setupAjaxForm(formSelector, successCallback) {
    $(formSelector).on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var formData = new FormData(this);
        
        // Show loading state
        var $submitBtn = $form.find('button[type="submit"]');
        var originalBtnHtml = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $submitBtn.prop('disabled', false).html(originalBtnHtml);
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonColor: '#0B2545'
                    }).then(function() {
                        if (successCallback) {
                            successCallback(response);
                        } else if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#0B2545'
                    });
                    // Refresh CAPTCHA if applicable
                    $form.find('.refresh-captcha').trigger('click');
                }
            },
            error: function(xhr, status, error) {
                $submitBtn.prop('disabled', false).html(originalBtnHtml);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'An unexpected system error occurred. Please try again later.',
                    confirmButtonColor: '#0B2545'
                });
                $form.find('.refresh-captcha').trigger('click');
            }
        });
    });
}
