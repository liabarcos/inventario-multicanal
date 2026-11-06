const grpc = require('@grpc/grpc-js');
const loader = require('@grpc/proto-loader');
const path = require('path');

const def = loader.loadSync(path.join(__dirname, 'producto.proto'));
const api = grpc.loadPackageDefinition(def).inventario;

const server = new grpc.Server();

server.addService(api.ProductoService.service, {
    async ObtenerProducto(call, callback) {
        try {
            const url = 'http://127.0.0.1:8002/api/interno/productos/' + call.request.id;
            const r = await fetch(url, {
                headers: {
                    'X-Internal-Key': process.env.GRPC_INTERNAL_KEY || ''
                }
            });

            if (r.status === 404) {
                return callback({ code: grpc.status.NOT_FOUND, message: 'Producto inexistente' });
            }
            if (!r.ok) {
                return callback({ code: grpc.status.INTERNAL, message: 'Fallo del backend Laravel: ' + r.status });
            }

            const p = await r.json();
            callback(null, {
                id: p.id,
                nombre: p.nombre,
                precio: Number(p.precio),
                stock: p.stock
            });
        } catch (err) {
            callback({ code: grpc.status.UNAVAILABLE, message: err.message });
        }
    }
});

server.bindAsync('127.0.0.1:50051', grpc.ServerCredentials.createInsecure(), (err, port) => {
    if (err) throw err;
    console.log('Servidor gRPC en puerto', port);
});

// Puente HTTP opcional para el panel: recibe un GET y llama por gRPC al servicio.
const http = require('http');

const puente = http.createServer((req, res) => {
    const match = req.url.match(/^\/productos\/(\d+)$/);
    const responder = (codigo, cuerpo) => {
        res.writeHead(codigo, {
            'Content-Type': 'application/json',
            'Access-Control-Allow-Origin': '*'
        });
        res.end(JSON.stringify(cuerpo));
    };

    if (!match) return responder(404, { error: 'Ruta no encontrada' });

    const cliente = new api.ProductoService('127.0.0.1:50051', grpc.credentials.createInsecure());

    cliente.ObtenerProducto({ id: Number(match[1]) }, (error, producto) => {
        cliente.close();
        if (error) {
            const codigo = error.code === grpc.status.NOT_FOUND ? 404 : 502;
            return responder(codigo, { error: error.details || error.message });
        }
        responder(200, producto);
    });
});

puente.listen(50052, '127.0.0.1', () => {
    console.log('Puente HTTP del panel en puerto 50052');
});
