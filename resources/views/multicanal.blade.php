<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario multicanal</title>
    @vite(['resources/js/app.js'])
    <style>
        :root {
            --bg: #f4f5f7;
            --card: #fff;
            --ink: #1f2430;
            --muted: #6b7280;
            --line: #e5e7eb;
            --green: #2e9e5b;
            --amber: #e0a800;
            --red: #d64545;
            --blue: #3b6fd4;
            --violet: #7b5cd6;
            --teal: #0f9b8e;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            background: var(--bg);
            color: var(--ink);
            font-family: system-ui, sans-serif;
        }
        .wrap { max-width: 1120px; margin: 0 auto; }
        code {
            background: #eef1f4;
            border-radius: 4px;
            padding: 1px 5px;
            font-size: 12.5px;
        }
        h1 { margin: 0 0 4px; font-size: 26px; }
        h2 { font-size: 16px; margin: 0; }
        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .hero p { margin: 0; color: var(--muted); font-size: 14px; }
        .live {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--card);
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 13px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }
        .dot { width: 9px; height: 9px; border-radius: 50%; background: #b9c0c7; }
        .dot.on { background: var(--green); box-shadow: 0 0 0 3px rgba(46,158,91,.15); }
        .dot.off { background: var(--red); box-shadow: 0 0 0 3px rgba(214,69,69,.15); }

        .channels {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }
        .ch-card {
            background: var(--card);
            border-radius: 10px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .ch-top { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
        .tag {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            padding: 3px 8px;
            border-radius: 999px;
            color: #fff;
        }
        .tag.rest { background: var(--green); }
        .tag.graphql { background: var(--violet); }
        .tag.ws { background: var(--teal); }
        .tag.grpc { background: var(--blue); }
        .ch-state { font-size: 12px; color: var(--muted); }
        .ch-desc { margin: 0; font-size: 13px; color: var(--muted); line-height: 1.45; }
        .ch-card button, .btn {
            margin-top: auto;
            border: 1px solid var(--line);
            background: #f9fafb;
            color: var(--ink);
            border-radius: 7px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }
        .ch-card button:hover, .btn:hover { background: #eef1f4; }
        .ch-card button:disabled { opacity: .55; cursor: default; }
        .grpc-box { display: flex; gap: 8px; margin-top: auto; }
        .grpc-box input {
            width: 64px;
            border: 1px solid var(--line);
            border-radius: 7px;
            padding: 8px;
            font-size: 13px;
        }
        .grpc-box button { flex: 1; margin-top: 0; }

        .panel {
            background: var(--card);
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            margin-bottom: 18px;
        }
        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th {
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--muted);
            padding: 8px 10px;
            border-bottom: 1px solid var(--line);
        }
        td { padding: 10px; border-bottom: 1px solid #f1f2f4; }
        tbody tr:last-child td { border-bottom: 0; }
        .num { font-variant-numeric: tabular-nums; }
        .stock {
            display: inline-block;
            min-width: 44px;
            text-align: center;
            font-weight: 600;
            border-radius: 999px;
            padding: 2px 10px;
            background: #eef7f1;
            color: #237a45;
        }
        .stock.low { background: #fdf3dd; color: #916a00; }
        .stock.out { background: #fbeaea; color: #a83232; }
        .row-btn {
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 13px;
            cursor: pointer;
        }
        .row-btn:hover { background: #eef1f4; }
        .empty { color: var(--muted); font-size: 14px; text-align: center; padding: 18px 0; }

        .bottom {
            display: grid;
            grid-template-columns: minmax(260px, 340px) 1fr;
            gap: 18px;
            align-items: start;
        }
        @media (max-width: 760px) {
            .bottom { grid-template-columns: 1fr; }
        }
        form { display: grid; gap: 10px; }
        label { font-size: 12.5px; color: var(--muted); display: grid; gap: 4px; }
        input[type="text"], input[type="number"] {
            border: 1px solid var(--line);
            border-radius: 7px;
            padding: 8px 10px;
            font-size: 14px;
            font-family: inherit;
        }
        form button {
            border: 0;
            background: var(--green);
            color: #fff;
            border-radius: 7px;
            padding: 9px 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        form button:hover { background: #278750; }
        .console { padding: 0; overflow: hidden; }
        .console .panel-head { padding: 14px 18px 0; }
        #log {
            margin: 10px 0 0;
            padding: 14px 18px 18px;
            background: #111827;
            color: #d7dce5;
            font-family: ui-monospace, Consolas, monospace;
            font-size: 12.5px;
            line-height: 1.6;
            min-height: 190px;
            max-height: 320px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }
        #log .t { color: #7d8798; }
        #log .rest { color: #7ee2a8; }
        #log .graphql { color: #c3a8ff; }
        #log .ws { color: #6fe3d4; }
        #log .grpc { color: #8fb8ff; }
        #log .err { color: #ff8b8b; }
        footer { color: var(--muted); font-size: 12.5px; text-align: center; padding: 6px 0 2px; }
        footer a { color: var(--blue); }
    </style>
</head>
<body>
<div class="wrap">
    <header class="hero">
        <div>
            <h1>Inventario multicanal</h1>
            <p>Cuatro canales y una sola lógica de negocio: <code>ProductoService</code>.</p>
        </div>
        <div class="live">
            <span class="dot" id="ws-dot"></span>
            <span id="ws-state">Conectando a Reverb…</span>
        </div>
    </header>

    <section class="channels">
        <article class="ch-card">
            <div class="ch-top">
                <span class="tag rest">REST</span>
                <span class="ch-state" id="rest-state">Listo</span>
            </div>
            <p class="ch-desc">CRUD completo sobre <code>/api/productos</code>.</p>
            <button id="btn-rest">Probar GET /api/productos</button>
        </article>

        <article class="ch-card">
            <div class="ch-top">
                <span class="tag graphql">GraphQL</span>
                <span class="ch-state" id="graphql-state">Listo</span>
            </div>
            <p class="ch-desc">Query <code>productos</code> contra <code>POST /graphql</code>.</p>
            <button id="btn-graphql">Probar query GraphQL</button>
        </article>

        <article class="ch-card">
            <div class="ch-top">
                <span class="tag ws">WebSocket</span>
                <span class="ch-state" id="ws-count">0 eventos</span>
            </div>
            <p class="ch-desc">Eventos <code>producto.actualizado</code> por Reverb.</p>
            <button id="btn-ws">Enviar cambio de prueba</button>
        </article>

        <article class="ch-card">
            <div class="ch-top">
                <span class="tag grpc">gRPC</span>
                <span class="ch-state" id="grpc-state">Listo</span>
            </div>
            <p class="ch-desc">Consulta un producto por su ID vía Node.</p>
            <div class="grpc-box">
                <input type="number" id="grpc-id" value="1" min="1" aria-label="ID de producto">
                <button id="btn-grpc">Consultar</button>
            </div>
        </article>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Productos</h2>
            <button class="btn" id="btn-reload">Actualizar</button>
        </div>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="productos"></tbody>
        </table>
    </section>

    <div class="bottom">
        <section class="panel">
            <div class="panel-head"><h2>Crear producto (REST)</h2></div>
            <form id="form-crear">
                <label>Nombre
                    <input type="text" id="nombre" maxlength="100" required placeholder="Teclado">
                </label>
                <label>Precio
                    <input type="number" id="precio" min="0" step="0.01" required placeholder="25000">
                </label>
                <label>Stock
                    <input type="number" id="stock" min="0" step="1" required value="0">
                </label>
                <button type="submit">Crear producto</button>
            </form>
        </section>

        <section class="panel console">
            <div class="panel-head"><h2>Consola</h2></div>
            <pre id="log"></pre>
        </section>
    </div>

    <footer>
        Vista de la guía: <a href="/inventario">/inventario</a> ·
        API: <a href="/api/productos">/api/productos</a>
    </footer>
</div>

<script>
    const $ = (id) => document.getElementById(id);
    let eventosWs = 0;

    function escapar(texto) {
        return String(texto).replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[c]);
    }

    function log(canal, mensaje, error = false) {
        const hora = new Date().toLocaleTimeString('es-AR');
        const linea = document.createElement('div');
        linea.innerHTML = `<span class="t">[${hora}]</span> <span class="${error ? 'err' : canal}">${canal.toUpperCase()}</span> ${escapar(mensaje)}`;
        $('log').prepend(linea);
    }

    function claseStock(stock) {
        if (stock === 0) return 'stock out';
        if (stock < 5) return 'stock low';
        return 'stock';
    }

    async function cargar() {
        const res = await fetch('/api/productos', { headers: { 'Accept': 'application/json' } });
        const productos = await res.json();
        $('rest-state').textContent = 'HTTP ' + res.status;

        if (!productos.length) {
            $('productos').innerHTML = '<tr><td colspan="5" class="empty">Todavía no hay productos. Creá uno con el formulario.</td></tr>';
            return;
        }

        $('productos').innerHTML = productos.map((p) => `
            <tr>
                <td class="num">${p.id}</td>
                <td>${escapar(p.nombre)}</td>
                <td class="num">$ ${Number(p.precio).toLocaleString('es-AR', { minimumFractionDigits: 2 })}</td>
                <td><span class="${claseStock(Number(p.stock))}">${p.stock}</span></td>
                <td><button class="row-btn" data-id="${p.id}" data-stock="${p.stock}">+1 stock</button></td>
            </tr>
        `).join('');
    }

    document.addEventListener('click', async (e) => {
        if (!e.target.matches('button[data-id]')) return;
        const res = await fetch('/api/productos/' + e.target.dataset.id, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ stock: Number(e.target.dataset.stock) + 1 })
        });
        log('rest', `PATCH /api/productos/${e.target.dataset.id} → HTTP ${res.status}`);
        await cargar();
    });

    $('btn-rest').addEventListener('click', async () => {
        const inicio = performance.now();
        const res = await fetch('/api/productos', { headers: { 'Accept': 'application/json' } });
        const datos = await res.json();
        const ms = Math.round(performance.now() - inicio);
        $('rest-state').textContent = `HTTP ${res.status} · ${ms} ms`;
        log('rest', `GET /api/productos → HTTP ${res.status} (${datos.length} productos, ${ms} ms)`);
    });

    $('btn-graphql').addEventListener('click', async () => {
        $('graphql-state').textContent = 'Consultando…';
        const res = await fetch('/graphql', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ query: '{ productos { id nombre precio stock } }' })
        });
        const datos = await res.json();
        $('graphql-state').textContent = 'HTTP ' + res.status;
        log('graphql', `POST /graphql → ${JSON.stringify(datos)}`);
    });

    $('btn-ws').addEventListener('click', () => {
        const primero = document.querySelector('button[data-id]');
        if (!primero) {
            log('ws', 'No hay productos para modificar', true);
            return;
        }
        const stock = Number(primero.dataset.stock) + 1;
        fetch('/api/productos/' + primero.dataset.id, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ stock })
        }).then((res) => {
            log('ws', `Cambio enviado por REST (stock ${stock}); esperando el evento de Reverb… HTTP ${res.status}`);
            cargar();
        });
    });

    $('btn-grpc').addEventListener('click', async () => {
        const id = Number($('grpc-id').value) || 1;
        $('grpc-state').textContent = 'Consultando…';
        const inicio = performance.now();
        try {
            const res = await fetch('http://127.0.0.1:50052/productos/' + id, { headers: { 'Accept': 'application/json' } });
            const datos = await res.json();
            const ms = Math.round(performance.now() - inicio);
            $('grpc-state').textContent = `HTTP ${res.status} · ${ms} ms`;
            if (res.ok) {
                log('grpc', `Producto ${datos.id}: ${datos.nombre} · $ ${datos.precio} · stock ${datos.stock} (protobuf vía gRPC)`);
            } else {
                log('grpc', datos.error || 'Error al consultar gRPC', true);
            }
        } catch (e) {
            $('grpc-state').textContent = 'Sin conexión';
            log('grpc', 'No se pudo contactar el servidor gRPC (¿está corriendo server.cjs?)', true);
        }
    });

    $('form-crear').addEventListener('submit', async (e) => {
        e.preventDefault();
        const cuerpo = {
            nombre: $('nombre').value.trim(),
            precio: Number($('precio').value),
            stock: Number($('stock').value)
        };
        const res = await fetch('/api/productos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(cuerpo)
        });
        const datos = await res.json();
        if (res.status === 201) {
            log('rest', `POST /api/productos → HTTP 201 · "${datos.nombre}" (id ${datos.id})`);
            e.target.reset();
            $('stock').value = 0;
            await cargar();
        } else {
            log('rest', `POST /api/productos → HTTP ${res.status} · ${JSON.stringify(datos)}`, true);
        }
    });

    $('btn-reload').addEventListener('click', cargar);

    window.addEventListener('inventario-cambio', (e) => {
        eventosWs++;
        $('ws-count').textContent = eventosWs + (eventosWs === 1 ? ' evento' : ' eventos');
        const d = e.detail;
        log('ws', `producto.actualizado → ${d.accion}: #${d.producto.id} ${d.producto.nombre} (stock ${d.producto.stock})`);
        cargar();
    });

    // Estado de la conexión WebSocket (Reverb).
    window.addEventListener('load', () => {
        cargar();
        if (!window.Echo) {
            $('ws-state').textContent = 'Echo no disponible';
            $('ws-dot').className = 'dot off';
            return;
        }
        const conexion = window.Echo.connector?.pusher?.connection;
        if (!conexion) {
            $('ws-state').textContent = 'Sin conexión';
            $('ws-dot').className = 'dot off';
            return;
        }
        const pintarEstado = (estado) => {
            const conectado = estado === 'connected';
            $('ws-state').textContent = conectado ? 'Conectado a Reverb' : 'Estado: ' + estado;
            $('ws-dot').className = 'dot ' + (conectado ? 'on' : 'off');
        };
        pintarEstado(conexion.state);
        conexion.bind('state_change', (cambio) => pintarEstado(cambio.current));
    });
</script>
</body>
</html>
