document.addEventListener('DOMContentLoaded', ()=>{

    const datos = {
        nombre:'',
        email:'',
        contrasena:'',
        confirmar_contrasena:'',
        rol:''
    }

    const formusuario = document.querySelector('.formulario .form-crear-usuario')
    
    const Inputnombre = document.getElementById('nombre')
    const Inputemail = document.getElementById('email')
    const Inputcontraseña = document.getElementById('contrasena')
    const Inputconfcontraseña = document.getElementById('confirmar_contrasena')
    const Selectrol = document.getElementById('rol')
    const btnRegistro = document.querySelector('.btn-registro')
    const btnLimpiar = document.querySelector('.btn-limpiar')

    Inputnombre.addEventListener('blur',Validar)
    Inputemail.addEventListener('blur',Validar)
    Inputcontraseña.addEventListener('blur',Validar)
    Inputconfcontraseña.addEventListener('blur',Validar)
    Selectrol.addEventListener('change', Validar)

    formusuario.addEventListener('submit',UsuarioNuevo)
    

    btnLimpiar.addEventListener('click', (e) => {
        e.preventDefault() 
        Limpiar()
    })

    
    async function UsuarioNuevo(e) {
        e.preventDefault() 
        if(Object.values(datos).includes('')) {
        mostrarError("Por favor, complete y corrija todos los campos.", formusuario);
        return;
    }
    
    const dataToSend = new FormData();
    dataToSend.append('nombre', datos.nombre); 
    dataToSend.append('email', datos.email); 
    dataToSend.append('contrasena', datos.contrasena); 
    dataToSend.append('confirmar_contrasena', datos.confirmar_contrasena);
    dataToSend.append('rol', datos.rol); 

    try {
        const respuesta = await fetch('crearusuario.php', {
            method: 'POST',
            body: dataToSend
        });
        
        if (respuesta.ok) {
            LimpiarUsuario(); 

        } else {
            const errorData = await respuesta.json(); 
            mostrarError(errorData.mensaje || 'Error desconocido al registrar.', formusuario);
        }

        } catch (error) {
            mostrarError('Error de conexión con el servidor.', formusuario);
        }
    }

    function Validar(e) {
        
        if (e.target.value.trim() === "") {
            mostrarError(`El campo ${e.target.id} es obligatorio.`, e.target.parentElement)
            datos[e.target.id] = ''
            VerificarDatos();
            return; 
        }
        if(e.target.id==="email" && !validarEmail(e.target.value)){
            mostrarError(`El email no es válido.`,e.target.parentElement);
            datos[e.target.id] = ''
            VerificarDatos();
            return;
        }
        if (e.target.id==="contrasena" && !validarContraseña(e.target.value)) {
            mostrarError(`La contraseña debe tener al menos 8 caracteres, 1 mayúscula y 1 caracter especial.`,e.target.parentElement);
            datos[e.target.id] = ''
            VerificarDatos();
            return;
        }
        if (e.target.id==="confirmar_contrasena" && e.target.value.trim() !== datos['contrasena']) {
            mostrarError(`La contraseña no coincide.`,e.target.parentElement);
            datos[e.target.id] = ''
            VerificarDatos();
            return;
        }
        if (e.target.id === "rol" && e.target.value === "") {
        mostrarError(`Debe seleccionar un rol.`, e.target.parentElement);
        datos[e.target.id] = '';
        VerificarDatos();
        return;
        }
        limpiarError(e.target.parentElement);
        datos[e.target.id]=e.target.value.trim();
        VerificarDatos();
        // Después de validar contraseña, si la confirmación no está vacía, validar también
        if (e.target.id === "contrasena" && datos['confirmar_contrasena'] !== '') {
            // Ejecutar la validación en el campo de confirmación
            const eventoFake = { target: Inputconfcontraseña };
            Validar(eventoFake);
        }
    }
    function mostrarError(mensaje, referencia) {
        limpiarError(referencia);
        const error = document.createElement('p')
        error.textContent=mensaje
        error.classList.add('text-danger', 'mb-0')
        referencia.appendChild(error)
    }
    function limpiarError(referencia) {
        const alerta = referencia.querySelector('.text-danger');
        if(alerta){
            alerta.remove();
        }
    }
    function validarEmail(email) {
        const regex =  /^\w+([.-_+]?\w+)*@\w+([.-]?\w+)*(\.\w{2,10})+$/;
        return regex.test(email);
    }
    function validarContraseña(contraseña) {
        const cantidad = contraseña.length >= 8;
        const mayuscula = /[A-Z]/.test(contraseña);
        const caracter = /[!@#$%^&*(),.?":{}|<>]/.test(contraseña);
        return cantidad && mayuscula && caracter;
    }
    function VerificarDatos() {
        console.log(datos)
        if(Object.values(datos).includes('')){
            btnRegistro.classList.add('disabled')
        }
        else{
            btnRegistro.classList.remove('disabled')
        }
    }
    function LimpiarUsuario() {
        datos.nombre=''
        datos.email=''
        datos.contrasena=''
        datos.confirmar_contrasena=''
        datos.rol=''
        formusuario.reset()
        const alertas = document.querySelectorAll('.text-danger');
        alertas.forEach( (e) => {
            e.remove();
        });
        VerificarDatos()
    }


})