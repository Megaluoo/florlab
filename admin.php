<?php
// admin.php
session_start();

// 1. Proteger la página
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit;
}

// 2. FUNCIÓN PARA OBTENER TASA BCV DESDE EL SERVIDOR
function getBCVRate() {
    $apiUrl = 'https://ve.dolarapi.com/v1/dolares/oficial';
    $options = [
        'http' => [
            'header' => "User-Agent: Florlab-Admin/1.0\r\n",
            'timeout' => 5,
        ],
        'ssl' => [ // Ignorar errores SSL si el hosting no tiene certificados actualizados
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($apiUrl, false, $context);

    if ($response === FALSE) {
        return 0; // Falla al conectar
    }
    
    $data = json_decode($response, true);
    
    if (isset($data['promedio']) && is_numeric($data['promedio'])) {
        return (float)$data['promedio'];
    }
    
    return 0; // Tasa no encontrada
}

// 3. Obtener la tasa y cargar los exámenes
$bcvRate = getBCVRate();
$tests = [];
if (file_exists('tests.json')) {
    $tests = json_decode(file_get_contents('tests.json'), true);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Florlab</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: var(--light-color); padding: 20px; }
        .admin-container { max-width: 1200px; margin: 20px auto; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .admin-card { background: var(--white-color); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 30px; }
        .admin-card h2 { text-align: left; margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; }
        
        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 900px) {
            .admin-grid { grid-template-columns: 1fr; }
        }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #DDD;
            font-family: var(--font-family);
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-group small { color: #777; font-size: 0.9em; }

        .msg { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600; }
        .msg.success { background: #E8F5E9; color: #2E7D32; }
        .msg.error { background: #FFEBEE; color: #C62828; }
        
        .bcv-card {
            background: var(--primary-color);
            color: var(--white-color);
            padding: 20px;
            text-align: center;
        }
        .bcv-card h3 { color: var(--white-color); margin-bottom: 10px; }
        .bcv-card .rate { font-size: 2.5rem; font-weight: 700; }
        .bcv-card .rate small { font-size: 1.5rem; font-weight: 400; }

        /* --- NUEVO ESTILO PARA EL BUSCADOR --- */
        .search-bar-admin {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #DDD;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .search-bar-admin:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 90, 156, 0.1);
        }

        .price-table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        .price-table th, .price-table td {
            padding: 12px;
            border: 1px solid #E0E0E0;
            text-align: left;
            word-wrap: break-word;
        }
        .price-table th { background-color: var(--light-color); font-weight: 600; }
        .price-table th:nth-child(1) { width: 30%; }
        .price-table th:nth-child(2) { width: 35%; }
        .price-table th:nth-child(3), .price-table th:nth-child(4) { width: 12%; }
        .price-table th:nth-child(5) { width: 11%; }

        .price-table .price { font-weight: 700; text-align: right; }
        .price-table .actions { text-align: center; }
        .actions a, .actions button {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 0.9em;
        }
        .action-edit {
            background-color: #E3F2FD;
            color: #0D47A1;
        }
        .action-delete {
            background-color: #FFEBEE;
            color: #C62828;
            margin-left: 5px;
        }
        /* Fila para "No se encontraron resultados" */
        .no-results-row {
            display: none; /* Oculta por defecto */
            text-align: center;
            font-weight: 600;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="admin-container">

        <div class="admin-header">
            <h1>Panel de Admin Florlab</h1>
            <a href="logout.php" class="cta-button secondary">Cerrar Sesión</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="msg <?php echo htmlspecialchars($_GET['type']); ?>">
                <?php echo htmlspecialchars(urldecode($_GET['msg'])); ?>
            </div>
        <?php endif; ?>

        <div class="admin-grid">
            <div>
                <div class="admin-card bcv-card">
                    <h3>Tasa BCV del Día</h3>
                    <div class="rate">
                        <?php echo ($bcvRate > 0) ? number_format($bcvRate, 2, ',', '.') : 'Error'; ?>
                        <small>Bs.</small>
                    </div>
                </div>

                <div class="admin-card">
                    <h2>Agregar Nuevo Exámen</h2>
                    <form action="add_exam.php" method="POST">
                        <div class="form-group">
                            <label for="name">Nombre del Exámen</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="keywords">Palabras Clave (Keywords)</label>
                            <textarea id="keywords" name="keywords" required></textarea>
                            <small>Separar cada palabra o frase con una coma. (Ej: hematologia, sangre, cbc)</small>
                        </div>
                        <div class="form-group">
                            <label for="price_usd">Precio (USD)</label>
                            <input type="text" id="price_usd" name="price_usd" required>
                            <small>Puedes usar punto o coma decimal. (Ej: 10.50 o 10,50)</small>
                        </div>
                        <button type="submit" class="cta-button primary">Agregar Exámen</button>
                    </form>
                </div>
            </div>

            <div class="admin-card">
                <h2>Actualizar Precios por Lote (Excel)</h2>
                <p>Sube un archivo Excel (.xlsx) para **reemplazar toda** la lista de precios actual. El archivo debe tener 3 columnas: <strong>A (Nombre)</strong>, <strong>B (Palabras Clave, separadas por coma)</strong>, <strong>C (Precio USD)</strong>.</p>
                
                <form action="upload_prices.php" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                    <div class="form-group">
                        <label for="price_excel">Archivo Excel (.xlsx)</label>
                        <input type="file" id="price_excel" name="price_excel" accept=".xlsx" required>
                    </div>
                    <button type="submit" class="cta-button primary">Actualizar Precios</button>
                    <a href="download_prices.php" class="cta-button secondary">Descargar Plantilla/Actual</a>
                </form>

                <hr style="margin: 30px 0;">

                <h2>Cargar Resultado de Paciente (PDF)</h2>
                <form action="upload_result.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="order_number">Número de Orden (Ej: 222456)</label>
                        <input type="text" id="order_number" name="order_number" required>
                    </div>
                    <div class="form-group">
                        <label for="result_pdf">Archivo PDF</label>
                        <input type="file" id="result_pdf" name="result_pdf" accept=".pdf" required>
                    </div>
                    <button type="submit" class="cta-button primary">Cargar PDF</button>
                </form>
            </div>
        </div>

        <div class="admin-card">
            <h2>Lista de Precios Actual</h2>

            <input type="text" id="admin-search" class="search-bar-admin" placeholder="Buscar exámenes por nombre...">

            <table class="price-table">
                <thead>
                    <tr>
                        <th>Nombre del Exámen</th>
                        <th>Palabras Clave (Keywords)</th>
                        <th>Precio (USD)</th>
                        <th>Precio (Bs.)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="exam-table-body">
                    <?php if (empty($tests)): ?>
                        <tr><td colspan="5">No hay exámenes cargados. Sube un archivo Excel o agrega uno manualmente.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tests as $index => $test): ?>
                            <?php 
                                $bsvPrice = (float)$test['price_usd'] * $bcvRate;
                            ?>
                            <tr>
                                <td class="exam-name"><?php echo htmlspecialchars($test['name']); ?></td>
                                <td><?php echo htmlspecialchars(implode(', ', $test['keywords'])); ?></td>
                                <td class="price">$<?php echo htmlspecialchars(number_format($test['price_usd'], 2)); ?></td>
                                <td class="price"><?php echo ($bcvRate > 0) ? number_format($bsvPrice, 2, ',', '.') : '---'; ?></td>
                                <td class="actions">
                                    <a href="edit_form.php?index=<?php echo $index; ?>" class="action-edit">Editar</a>
                                    <form action="delete_exam.php" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres borrar este exámen?');">
                                        <input type="hidden" name="index" value="<?php echo $index; ?>">
                                        <button type="submit" class="action-delete">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <tr class="no-results-row">
                        <td colspan="5">No se encontraron exámenes con ese nombre.</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('admin-search');
            const tableBody = document.getElementById('exam-table-body');
            const allRows = tableBody.querySelectorAll('tr:not(.no-results-row)');
            const noResultsRow = tableBody.querySelector('.no-results-row');

            // Función para quitar acentos
            function normalizeText(text) {
                return text
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "");
            }

            searchInput.addEventListener('input', (e) => {
                const query = normalizeText(e.target.value);
                let rowsFound = 0;

                allRows.forEach(row => {
                    // Busca el texto en la primera celda (Nombre del Exámen)
                    const examNameCell = row.querySelector('td.exam-name');
                    if (examNameCell) {
                        const examName = normalizeText(examNameCell.textContent);
                        
                        // Compara si el nombre incluye la búsqueda
                        if (examName.includes(query)) {
                            row.style.display = ''; // Muestra la fila
                            rowsFound++;
                        } else {
                            row.style.display = 'none'; // Oculta la fila
                        }
                    }
                });

                // Muestra o oculta el mensaje de "No resultados"
                if (rowsFound === 0 && allRows.length > 0) {
                    noResultsRow.style.display = 'table-row';
                } else {
                    noResultsRow.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>