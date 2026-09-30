<?php
$maxBytes = 5 * 1024 * 1024;
$allowedTypes = array(
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
    'application/pdf' => 'pdf'
);
$message = 'No se pudo procesar el archivo.';
$success = false;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $message = 'El formulario debe enviarse para subir un archivo.';
} elseif (!isset($_FILES['archivo'])) {
    $message = 'Seleccioná un archivo antes de continuar.';
} else {
    $archivo = $_FILES['archivo'];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = array(
            UPLOAD_ERR_INI_SIZE => 'El archivo supera el tamaño máximo permitido por el servidor.',
            UPLOAD_ERR_FORM_SIZE => 'El archivo supera el tamaño máximo permitido.',
            UPLOAD_ERR_PARTIAL => 'La carga quedó incompleta. Intentá nuevamente.',
            UPLOAD_ERR_NO_FILE => 'Seleccioná un archivo antes de continuar.',
            UPLOAD_ERR_NO_TMP_DIR => 'El servidor no tiene una carpeta temporal disponible.',
            UPLOAD_ERR_CANT_WRITE => 'El servidor no pudo guardar el archivo.',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la carga.'
        );
        $message = isset($uploadErrors[$archivo['error']])
            ? $uploadErrors[$archivo['error']]
            : 'Ocurrió un error durante la carga.';
    } elseif ($archivo['size'] <= 0 || $archivo['size'] > $maxBytes) {
        $message = 'El archivo debe pesar más de 0 bytes y no superar los 5 MB.';
    } elseif (!is_uploaded_file($archivo['tmp_name'])) {
        $message = 'La carga no pudo verificarse. Intentá nuevamente.';
    } else {
        $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
        $mimeType = $finfo ? finfo_file($finfo, $archivo['tmp_name']) : false;
        if ($finfo) {
            finfo_close($finfo);
        }

        if ($mimeType === false || !isset($allowedTypes[$mimeType])) {
            $message = 'Tipo de archivo no permitido. Subí una imagen o un PDF.';
        } else {
            $directory = __DIR__ . DIRECTORY_SEPARATOR . 'archivos';
            if (!is_dir($directory) || !is_writable($directory)) {
                $message = 'La carpeta de destino no existe o no permite guardar archivos.';
            } else {
                try {
                    $storedName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
                    $destination = $directory . DIRECTORY_SEPARATOR . $storedName;
                    if (move_uploaded_file($archivo['tmp_name'], $destination)) {
                        $success = true;
                        $message = 'El archivo se subió correctamente.';
                    } else {
                        $message = 'No se pudo guardar el archivo. Revisá los permisos de la carpeta.';
                    }
                } catch (Exception $exception) {
                    $message = 'No se pudo generar un nombre seguro para el archivo.';
                }
            }
        }
    }
}

$storedFiles = array();
$directory = __DIR__ . DIRECTORY_SEPARATOR . 'archivos';
$listedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf');
if (is_dir($directory)) {
    foreach (scandir($directory) as $file) {
        if ($file === '.' || $file === '..' || !is_file($directory . DIRECTORY_SEPARATOR . $file)) {
            continue;
        }
        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $listedExtensions, true)) {
            $storedFiles[] = $file;
        }
    }
    sort($storedFiles, SORT_NATURAL | SORT_FLAG_CASE);
}

function escapeHtml($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado de la carga | Archivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet">
</head>
<body>
    <main class="container upload-shell py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <header class="page-heading mb-4">
                    <span class="eyebrow">ARCHIVO PERSONAL</span>
                    <h1 class="display-title">Resultado de la carga.</h1>
                </header>

                <section class="upload-panel" aria-labelledby="result-title">
                    <div class="panel-topline"></div>
                    <div class="p-4 p-md-5">
                        <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>" role="status">
                            <h2 id="result-title" class="h5 mb-1"><?= $success ? 'Listo' : 'No se pudo subir' ?></h2>
                            <?= escapeHtml($message) ?>
                        </div>
                        <a class="btn btn-primary upload-button" href="index.html">Volver al formulario</a>
                    </div>
                </section>

                <section class="file-list mt-4" aria-labelledby="files-title">
                    <div class="d-flex justify-content-between align-items-baseline gap-3 mb-3">
                        <h2 id="files-title" class="h5 mb-0">Archivos guardados</h2>
                        <span class="small text-secondary"><?= count($storedFiles) ?> en total</span>
                    </div>
                    <?php if (count($storedFiles) === 0): ?>
                        <p class="text-secondary mb-0">Todavía no hay archivos guardados.</p>
                    <?php else: ?>
                        <ul class="list-group file-list-items">
                            <?php foreach ($storedFiles as $file): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    <span class="text-truncate"><?= escapeHtml($file) ?></span>
                                    <a class="btn btn-sm btn-outline-secondary flex-shrink-0" href="archivos/<?= rawurlencode($file) ?>" target="_blank" rel="noopener">Abrir</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>
</body>
</html>