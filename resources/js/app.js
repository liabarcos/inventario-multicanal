//
import './echo';

/**
 * Echo expone una API expresiva para suscribirse a canales y escuchar
 * los eventos que difunde Laravel (Reverb).
 */

window.Echo.channel('inventario')
    .listen('.producto.actualizado', (evento) => {
        console.log('Cambio recibido:', evento);
        window.dispatchEvent(new CustomEvent('inventario-cambio', { detail: evento }));
    });
