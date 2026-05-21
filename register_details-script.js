document.addEventListener('DOMContentLoaded', () => {
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');
    const sexRadios = document.querySelectorAll('input[name="sex"]');
    const maternalSection = document.getElementById('maternal-health-section');
    const psRadios = document.querySelectorAll('input[name="pregnancy_status"]');
    const breastfeedingGroup = document.getElementById('breastfeeding-group');
    const idFileInput = document.getElementById('id_picture');
    const uploadLabel = document.getElementById('upload-label');
    const uploadStatus = document.getElementById('upload-status');
    const uploadContainer = document.getElementById('upload-container');
    const uploadText = document.getElementById('upload-text');

    // 1. Auto Calculate Age
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

    // 2. Toggle Maternal Health (Only Female)
    const updateSexVisibility = () => {
        const femaleRadio = document.getElementById('sex_female');
        if (femaleRadio) {
            maternalSection.style.display = femaleRadio.checked ? 'block' : 'none';
        }
    };
    sexRadios.forEach(radio => radio.addEventListener('change', updateSexVisibility));
    updateSexVisibility();

    // 3. Toggle Breastfeeding field
    const updateBreastfeedingVisibility = () => {
        const breastRadio = document.getElementById('ps_breast');
        if (breastRadio) {
            breastfeedingGroup.style.display = breastRadio.checked ? 'block' : 'none';
        }
    };
    psRadios.forEach(radio => radio.addEventListener('change', updateBreastfeedingVisibility));
    updateBreastfeedingVisibility();

    // 4. ID Upload Success Indication
    idFileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const fileName = this.files[0].name;
            uploadText.textContent = "File Selected";
            uploadStatus.textContent = `"${fileName}" ready!`;
            uploadStatus.style.color = "#2F855A";
            uploadLabel.style.borderColor = "#2F855A";
            uploadContainer.style.backgroundColor = "#F0FFF4";
            uploadContainer.classList.remove('error-border');
        }
    });
});