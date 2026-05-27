const botonVer = document.getElementById('ver');
const inputPassword = document.getElementById('password');
const formulario = document.getElementById('formularioLogin');

// 1. LÓGICA DEL BOTÓN: Ver / Ocultar Contraseña
if (botonVer && inputPassword) {
    botonVer.addEventListener('click', function() {
        if (inputPassword.type === 'password'){
            // Mostrar contraseña
            inputPassword.type = 'text';
            botonVer.textContent = 'Ocultar';
            botonVer.style.backgroundColor = '#dc3545'; // Se pone rojo al mostrar
        } else {
            // Ocultar contraseña
            inputPassword.type = 'password';
            botonVer.textContent = 'Ver';
            botonVer.style.backgroundColor = '#666'; // Vuelve a gris
        }
    });
}

// 2. LÓGICA DE CONEXIÓN A BASE DE DATOS (AJAX)
if (formulario) {
    formulario.addEventListener('submit', function(event) {
        event.preventDefault(); 

        const datos = new FormData(formulario);

        fetch('../Controlador/controlador.php', {
            method: 'POST',
            body: datos
        })
        .then(respuesta => respuesta.json())
        .then(datosPHP => {
            if (datosPHP.status === 'success') {
                // Si la clave es correcta, vamos al panel
                window.location.href = 'panel.php';
            } else {
                // Si es incorrecta, mostramos la alerta de Windows/Navegador
                alert(datosPHP.mensaje);
            }
        })
        .catch(error => {
            console.error('Error de red:', error);
            alert("Error crítico al conectar con el servidor.");
        });
    });
}