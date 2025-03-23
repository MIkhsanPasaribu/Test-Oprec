document.addEventListener('DOMContentLoaded', function() {
    // Get form elements
    const form = document.getElementById('recruitmentForm');
    const section1 = document.getElementById('section1');
    const section2 = document.getElementById('section2');
    const nextButton = document.getElementById('nextToSection2');
    const backButton = document.getElementById('backToSection1');
    const progressBar = document.querySelector('.progress-bar');
    const otherSoftwareCheckbox = document.getElementById('otherSoftware');
    const otherSoftwareField = document.getElementById('otherSoftwareField');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');

    // Handle "Other Software" checkbox
    otherSoftwareCheckbox.addEventListener('change', function() {
        if (this.checked) {
            otherSoftwareField.classList.remove('d-none');
            document.getElementById('otherSoftwareText').setAttribute('required', '');
        } else {
            otherSoftwareField.classList.add('d-none');
            document.getElementById('otherSoftwareText').removeAttribute('required');
        }
    });

    // Handle next button click
    nextButton.addEventListener('click', function() {
        // Validate section 1
        const email = document.getElementById('email');
        const paymentProof = document.getElementById('paymentProof');
        
        if (!email.checkValidity()) {
            email.classList.add('is-invalid');
            email.focus();
            return;
        }
        
        if (!paymentProof.files.length) {
            alert('Please upload your payment proof');
            paymentProof.focus();
            return;
        }
        
        // Move to section 2
        section1.classList.add('d-none');
        section2.classList.remove('d-none');
        
        // Update progress bar
        progressBar.style.width = '50%';
        progressBar.setAttribute('aria-valuenow', '50');
        
        // Update progress steps
        step1.classList.add('completed');
        step2.classList.add('active');
    });

    // Handle back button click
    backButton.addEventListener('click', function() {
        section2.classList.add('d-none');
        section1.classList.remove('d-none');
        
        // Update progress bar
        progressBar.style.width = '0%';
        progressBar.setAttribute('aria-valuenow', '0');
        
        // Update progress steps
        step1.classList.remove('completed');
        step2.classList.remove('active');
    });

    // Autosave functionality
    function saveFormData() {
        const formData = {
            email: document.getElementById('email').value,
            fullName: document.getElementById('fullName').value,
            nickname: document.getElementById('nickname').value,
            gender: document.querySelector('input[name="gender"]:checked')?.value,
            birthDate: document.getElementById('birthDate').value,
            faculty: document.getElementById('faculty').value,
            department: document.getElementById('department').value,
            studyProgram: document.getElementById('studyProgram').value,
            previousSchool: document.getElementById('previousSchool').value,
            addressInPadang: document.getElementById('addressInPadang').value,
            phoneNumber: document.getElementById('phoneNumber').value,
            motivation: document.getElementById('motivation').value,
            futurePlans: document.getElementById('futurePlans').value,
            reasonToJoin: document.getElementById('reasonToJoin').value,
        };
        
        localStorage.setItem('recruitmentFormData', JSON.stringify(formData));
    }

    function loadFormData() {
        const savedData = localStorage.getItem('recruitmentFormData');
        if (savedData) {
            const formData = JSON.parse(savedData);
            document.getElementById('email').value = formData.email || '';
            document.getElementById('fullName').value = formData.fullName || '';
            document.getElementById('nickname').value = formData.nickname || '';
            
            if (formData.gender) {
                document.querySelector(`input[name="gender"][value="${formData.gender}"]`).checked = true;
            }
            
            document.getElementById('birthDate').value = formData.birthDate || '';
            document.getElementById('faculty').value = formData.faculty || '';
            document.getElementById('department').value = formData.department || '';
            document.getElementById('studyProgram').value = formData.studyProgram || '';
            document.getElementById('previousSchool').value = formData.previousSchool || '';
            document.getElementById('addressInPadang').value = formData.addressInPadang || '';
            document.getElementById('phoneNumber').value = formData.phoneNumber || '';
            document.getElementById('motivation').value = formData.motivation || '';
            document.getElementById('futurePlans').value = formData.futurePlans || '';
            document.getElementById('reasonToJoin').value = formData.reasonToJoin || '';
        }
    }

    // Add event listeners for autosave
    document.querySelectorAll('input, textarea, select').forEach(element => {
        if (element.type !== 'file') {  // Don't try to save file inputs
            element.addEventListener('change', saveFormData);
        }
    });

    // Load saved form data when page loads
    loadFormData();

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all required fields
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        
        // Create FormData object to handle file uploads
        const formData = new FormData(form);
        
        // Add checkbox values for software
        const softwareChecked = document.querySelectorAll('input[name="software"]:checked');
        if (softwareChecked.length > 0) {
            const softwareValues = Array.from(softwareChecked).map(cb => cb.value);
            formData.delete('software'); // Remove individual entries
            softwareValues.forEach(value => {
                formData.append('software[]', value);
            });
        }
        
        // Add other software text if checked
        if (otherSoftwareCheckbox.checked) {
            formData.append('otherSoftwareText', document.getElementById('otherSoftwareText').value);
        }
        
        // Add send copy checkbox value
        formData.append('sendCopy', document.getElementById('sendCopy').checked ? 'on' : 'off');
        
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
        
        // Send data to server
        fetch('submit.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Clear localStorage after successful submission
                localStorage.removeItem('recruitmentFormData');
                
                // Hide the form
                form.classList.add('d-none');
                
                // Show success message
                document.getElementById('successMessage').classList.remove('d-none');
                
                // Update progress bar
                progressBar.style.width = '100%';
                progressBar.setAttribute('aria-valuenow', '100');
                
                // Update progress steps
                step1.classList.add('completed');
                step2.classList.add('completed');
                step3.classList.add('active');
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                alert(data.message || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            // Reset submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });

    // Add conditional logic for faculty and department
    document.getElementById('faculty').addEventListener('change', function() {
        const selectedFaculty = this.value;
        const departmentField = document.getElementById('department');
        const studyProgramField = document.getElementById('studyProgram');
        
        // Reset department field
        departmentField.value = '';
        studyProgramField.value = '';
        
        // Suggest departments based on faculty
        if (selectedFaculty === 'Teknik') {
            departmentField.setAttribute('list', 'teknikDepartments');
        } else if (selectedFaculty === 'Teknologi Informasi') {
            departmentField.setAttribute('list', 'tiDepartments');
        } else {
            departmentField.removeAttribute('list');
        }
    });
});

// Tambahkan kode untuk pertanyaan bersyarat
document.getElementById('faculty').addEventListener('change', function() {
    const selectedFaculty = this.value;
    const departmentField = document.getElementById('department');
    
    // Reset opsi departemen
    departmentField.innerHTML = '<option value="" selected disabled>Pilih Departemen</option>';
    
    // Tambahkan opsi departemen berdasarkan fakultas yang dipilih
    if (selectedFaculty === 'Teknik') {
        const departments = ['Teknik Sipil', 'Teknik Mesin', 'Teknik Elektro', 'Teknik Industri', 'Teknik Lingkungan'];
        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            departmentField.appendChild(option);
        });
    } else if (selectedFaculty === 'Teknologi Informasi') {
        const departments = ['Sistem Informasi', 'Teknik Komputer', 'Informatika'];
        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            departmentField.appendChild(option);
        });
    }
    // Tambahkan kondisi untuk fakultas lainnya
});

// Tambahkan kode untuk batas waktu pendaftaran
document.addEventListener('DOMContentLoaded', function() {
    const registrationEndDate = new Date('2025-12-31T23:59:59');
    const currentDate = new Date();
    
    if (currentDate > registrationEndDate) {
        // Pendaftaran sudah ditutup
        document.getElementById('recruitmentForm').classList.add('d-none');
        
        const closedMessage = document.createElement('div');
        closedMessage.className = 'alert alert-warning';
        closedMessage.innerHTML = '<h4>Pendaftaran Ditutup</h4><p>Maaf, periode pendaftaran telah berakhir.</p>';
        
        document.querySelector('.container').insertBefore(closedMessage, document.getElementById('recruitmentForm'));
    }
});