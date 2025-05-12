const form = document.querySelector('form');
const nombreInput = document.getElementById('nombre');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');

form.addEventListener('submit', (event) => {
    let errores = [];

    if (nombreInput.value.trim() === '') {
        errores.push('El nombre es obligatorio.');
    }

    if (emailInput.value.trim() === '') {
        errores.push('El correo electrónico es obligatorio.');
    } else if (!isValidEmail(emailInput.value)) {
        errores.push('El correo electrónico no es válido.');
    }

    if (passwordInput.value.trim() === '') {
        errores.push('La contraseña es obligatoria.');
    } else if (passwordInput.value.length < 8) {
        errores.push('La contraseña debe tener al menos 8 caracteres.');
    }

    if (errores.length > 0) {
        event.preventDefault();
        mostrarErrores(errores);
    }
});

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function mostrarErrores(errores) {
    const errorContainer = document.getElementById('error-container');
    errorContainer.innerHTML = errores.map(error => `<p class="error-message">${error}</p>`).join('');
}
