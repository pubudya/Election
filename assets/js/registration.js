// Registration page logic
const regForm = document.getElementById('voterRegistrationForm');
if (regForm) {
    regForm.addEventListener('submit', function(e) {
        const pin = document.getElementById('pin').value;
        const confirmPin = document.getElementById('confirmPin').value;
        if (pin !== confirmPin) {
            e.preventDefault();
            document.getElementById('confirmPin').setCustomValidity('PINs do not match');
        } else {
            document.getElementById('confirmPin').setCustomValidity('');
        }
        // NIC format check
        const nic = document.getElementById('nic').value;
        if (!/^[0-9]{9}[vVxX]?$/.test(nic)) {
            e.preventDefault();
            document.getElementById('nic').setCustomValidity('Invalid NIC format');
        } else {
            document.getElementById('nic').setCustomValidity('');
        }
    });
} 