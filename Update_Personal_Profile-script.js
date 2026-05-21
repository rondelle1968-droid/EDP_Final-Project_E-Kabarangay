document.addEventListener('DOMContentLoaded', () => {
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');
    const sexRadios = document.querySelectorAll('input[name="sex"]');
    const maternalSection = document.getElementById('maternal-health-section');
    const psRadios = document.querySelectorAll('input[name="pregnancy_status"]');
    const breastfeedingGroup = document.getElementById('breastfeeding-group');

    // Auto Calculate Age
    const calculateAge = () => {
        if (dobInput.value) {
            const birthDate = new Date(dobInput.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            ageInput.value = age >= 0 ? age : 0;
        }
    };
    dobInput.addEventListener('change', calculateAge);
    calculateAge();

    // Toggle Maternal Health (Only Female)
    const updateSexVisibility = () => {
        const femaleRadio = document.getElementById('sex_female');
        if (femaleRadio) {
            const isFemale = femaleRadio.checked;
            maternalSection.style.display = isFemale ? 'block' : 'none';
            if (!isFemale) {
                // Clear maternal fields when switching to male
                document.querySelectorAll('input[name="family_planning"]').forEach(radio => radio.checked = false);
                document.querySelectorAll('input[name="pregnancy_status"]').forEach(radio => radio.checked = false);
                document.querySelectorAll('input[name="breastfeeding_type"]').forEach(radio => radio.checked = false);
                breastfeedingGroup.style.display = 'none';
            } else {
                // Re-trigger breastfeeding visibility based on current pregnancy status
                updateBreastfeedingVisibility();
            }
        }
    };
    sexRadios.forEach(radio => radio.addEventListener('change', updateSexVisibility));
    updateSexVisibility();

    // Toggle Breastfeeding field based on pregnancy status instantly
    function updateBreastfeedingVisibility() {
        const breastRadio = document.getElementById('ps_breast');
        const breastfeedingLabel = breastfeedingGroup.querySelector('label');
        
        if (breastRadio && breastRadio.checked && maternalSection.style.display !== 'none') {
            breastfeedingGroup.style.display = 'block';
            
            // Add required asterisk if it doesn't already exist
            if (breastfeedingLabel && !breastfeedingLabel.querySelector('.dynamic-asterisk')) {
                // Remove existing text node trailing spaces or existing raw asterisks if any, safely keeping text
                const asteriskSpan = document.createElement('span');
                asteriskSpan.className = 'dynamic-asterisk';
                asteriskSpan.style.color = 'red';
                asteriskSpan.textContent = ' *';
                breastfeedingLabel.appendChild(asteriskSpan);
            }
        } else {
            breastfeedingGroup.style.display = 'none';
            
            // Clear selections inside the group when hidden
            document.querySelectorAll('input[name="breastfeeding_type"]').forEach(radio => radio.checked = false);
            
            // Remove the dynamic asterisk safely
            if (breastfeedingLabel) {
                const dynamicAsterisk = breastfeedingLabel.querySelector('.dynamic-asterisk');
                if (dynamicAsterisk) {
                    dynamicAsterisk.remove();
                }
            }
        }
    }
    
    psRadios.forEach(radio => radio.addEventListener('change', updateBreastfeedingVisibility));
    // Run once to set initial state (important for edit mode/repopulation)
    updateBreastfeedingVisibility();

    // Contact number validation
    const contactInput = document.getElementById('contact_no');
    if (contactInput) {
        contactInput.addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            this.value = value;
        });
    }
});