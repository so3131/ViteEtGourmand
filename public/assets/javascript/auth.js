// Toggle password visibility
document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.querySelector('.toggle-password-btn');

    if (toggleButton) {
        toggleButton.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);

            if (passwordInput) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.textContent = '🙈';
                } else {
                    passwordInput.type = 'password';
                    this.textContent = '👁️';
                }
            }
        });
    }
});