const grpc = require('@grpc/grpc-js');
const loader = require('@grpc/proto-loader');
const path = require('path');

const def = loader.loadSync(path.join(__dirname, 'producto.proto'));
const api = grpc.loadPackageDefinition(def).inventario;

const cliente = new api.ProductoService('127.0.0.1:50051', grpc.credentials.createInsecure());
const id = Number(process.argv[2] || 1);

cliente.ObtenerProducto({ id }, (err, producto) => {
    if (err) {
        console.error(JSON.stringify({ error: err.details || err.message }));
        process.exitCode = 1;
    } else {
        console.log(JSON.stringify(producto));
    }
    cliente.close();
});
