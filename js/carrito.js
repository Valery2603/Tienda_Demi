document.addEventListener('DOMContentLoaded', function() {
    
    const plusBtns = document.querySelectorAll('.plus-btn');
    const minusBtns = document.querySelectorAll('.minus-btn');

    // Manejador del botón (+)
    plusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Obtener el ID del producto que está en el atributo data-id
            const productId = this.getAttribute('data-id'); 
            // Localizar el campo de cantidad asociado usando el ID
            const input = document.getElementById('cantidad_' + productId);
            
            if (input) {
                // Obtener el valor actual, el máximo permitido y convertir a número
                let actualValue = parseInt(input.value);
                const maxValue = parseInt(input.getAttribute('max')); 
                
                // Si el valor actual es menor al stock máximo, incrementa
                if (actualValue < maxValue) {
                    input.value = actualValue + 1;
                }
            }
        });
    });

    // Manejador del botón (-) 
    minusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Obtener el ID del producto
            const productId = this.getAttribute('data-id');
            // Localizar el campo de cantidad asociado
            const input = document.getElementById('cantidad_' + productId);
            
            if (input) {
                // Obtener el valor actual y el mínimo (que debería ser 1)
                let actualValue = parseInt(input.value);
                const minValue = parseInt(input.getAttribute('min'));
                
                // Si el valor actual es mayor al mínimo (1), decrementa
                if (actualValue > minValue) {
                    input.value = actualValue - 1;
                }
            }
        });
    });

    // Verificar si la URL contiene el parámetro ?agregado=1
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('agregado') && urlParams.get('agregado') === '1') {
        const notificacion = document.getElementById('notificacion-carrito');
        
        if (notificacion) {
            notificacion.style.display = 'block';
            setTimeout(() => {
                notificacion.style.display = 'none';
                //Limpiar la URL para que no se muestre el mensaje al recargar
                window.history.replaceState(null, null, window.location.pathname);
            }, 3000);
        }
    }

});