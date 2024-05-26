document.addEventListener("DOMContentLoaded", function() {
    // Ocultar todos los mensajes de error al cargar la página
    var mensajesError = document.querySelectorAll(".noVisible");
    mensajesError.forEach(function(mensaje) {
        mensaje.style.display = "none";
    });

    document.getElementById("registrar").addEventListener("click", comprueba);
});

function comprueba(event) {
    event.preventDefault();
    var nombre = document.getElementById("nombre").value;
    var email = document.getElementById("email").value;
    var contrasena = document.getElementById("contrasena").value;
    var confirmarContrasena = document.getElementById("confirmar_contrasena").value;
    var telefono = document.getElementById("telefono").value;
    var fechaNacimiento = document.getElementById("fecha_nacimiento").value;
    var direccion = document.getElementById("direccion").value;

    var hayError = false;

    // Validación de nombre
    if (nombre.trim() === "") {
        mostrarError("mnombre", "Campo obligatorio");
        hayError = true;
    } else {
        ocultarError("mnombre");
    }

    // Validación de correo electrónico
    if (email.trim() === "") {
        mostrarError("memail", "Campo obligatorio");
        hayError = true;
    } else if (!validarCorreo(email)) {
        mostrarError("memail", "Formato de correo electrónico no válido");
        hayError = true;
    } else {
        ocultarError("memail");
    }

    // Validación de contraseña
    if (contrasena.trim() === "") {
        mostrarError("mcontrasena", "Campo obligatorio");
        hayError = true;
    } else if (contrasena.length < 8) {
        mostrarError("mcontrasena", "La contraseña debe tener al menos 8 caracteres");
        hayError = true;
    } else {
        ocultarError("mcontrasena");
    }

    // Validación de confirmación de contraseña
    if (confirmarContrasena.trim() === "") {
        mostrarError("mconfirmar_contrasena", "Campo obligatorio");
        hayError = true;
    } else if (contrasena !== confirmarContrasena) {
        mostrarError("mconfirmar_contrasena", "Las contraseñas no coinciden");
        hayError = true;
    } else {
        ocultarError("mconfirmar_contrasena");
    }

    // Validación de teléfono
    if (telefono.trim() === "") {
        mostrarError("mtelefono", "Campo obligatorio");
        hayError = true;
    } else if (!validarTelefono(telefono)) {
        mostrarError("mtelefono", "Formato de teléfono no válido");
        hayError = true;
    } else {
        ocultarError("mtelefono");
    }

    // Validación de fecha de nacimiento
    if (fechaNacimiento.trim() === "") {
        mostrarError("mfecha", "Campo obligatorio");
        hayError = true;
    } else if (!mayorDe16(fechaNacimiento)) {
        mostrarError("mfecha", "Debes ser mayor de 16 años para registrarte");
        hayError = true;
    } else {
        ocultarError("mfecha");
    }

    // Validación de dirección
    if (direccion.trim() === "") {
        mostrarError("mdireccion", "Campo obligatorio");
        hayError = true;
    } else {
        ocultarError("mdireccion");
    }

    // Si hay algún error, no se envía el formulario
    if (hayError) {
        return;
    }

    // Si todas las validaciones son exitosas, puedes enviar el formulario
    document.getElementById("formulario").submit();
}

function mostrarError(idElemento, mensaje) {
    var elemento = document.getElementById(idElemento);
    elemento.innerText = mensaje;
    elemento.style.display = "block"; // Mostrar el mensaje de error
}

function ocultarError(idElemento) {
    var elemento = document.getElementById(idElemento);
    elemento.style.display = "none"; // Ocultar el mensaje de error
}

function validarCorreo(correo) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
}

function validarTelefono(telefono) {
    return /^\d{9}$/.test(telefono);
}

function mayorDe16(fecha) {
    const fechaActual = new Date();
    const fechaHace16Anios = new Date(fechaActual.getFullYear() - 16, fechaActual.getMonth(), fechaActual.getDate());
    const fechaNacimiento = new Date(fecha);
    return fechaNacimiento <= fechaHace16Anios || (fechaNacimiento.getMonth() === fechaHace16Anios.getMonth() && fechaNacimiento.getDate() === fechaHace16Anios.getDate());
}
