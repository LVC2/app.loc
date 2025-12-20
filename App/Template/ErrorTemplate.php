<?php
// Файл: App/Template/ErrorTemplate.php (Обновленный Темный Дизайн)

/**
 * Переменные, которые должны быть доступны в этой области видимости:
 * $errorTitle, $errorMessage, $errorDetails
 */
?>
<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($errorTitle ?? 'Критическая ошибка') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        /* Темный фон, соответствующий основной теме MasterAM */
        body { background-color: #1c1f21; }

        .error-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            /* Акцентная красная тень */
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.7);
            border-radius: 8px;
        }

        /* Переопределение цвета danger для темного фона (если нужно сделать его менее ярким) */
        .card-header.bg-danger {
            background-color: #8f0e1c !important; /* Более глубокий красный */
        }

        /* Стилизация для блока трассировки в темной теме */
        .collapse pre {
            background-color: #2b3035 !important;
            color: #ffc107 !important; /* Желтый/янтарный текст для читаемости кода */
            border: 1px solid #495057 !important;
            word-wrap: break-word;
            white-space: pre-wrap;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card error-container border-danger">
        <div class="card-header bg-danger text-white d-flex align-items-center">
            <i class="bi bi-x-octagon-fill me-3 h3 m-0"></i>
            <h1 class="h4 m-0"><?= htmlspecialchars($errorTitle ?? 'Критический сбой приложения') ?></h1>
        </div>
        <div class="card-body bg-dark text-light">
            <p class="lead text-warning">Не могу продолжить выполнение. Причина:</p>
            <div class="alert alert-danger border-danger" role="alert">
                <i class="bi bi-bug-fill me-2"></i>
                <strong>Сообщение:</strong> <?= nl2br(htmlspecialchars($errorMessage)) ?>
            </div>

            <?php if (!empty($errorDetails)): ?>
                <h5 class="mt-4 text-primary">Детали сбоя</h5>
                <p>
                    <i class="bi bi-file-earmark-code me-2"></i>
                    <strong>Файл:</strong> <?= htmlspecialchars($errorDetails['file']) ?>
                    (Строка: <?= $errorDetails['line'] ?>)
                </p>

                <button class="btn btn-sm btn-outline-warning mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#stackTrace" aria-expanded="false" aria-controls="stackTrace">
                    <i class="bi bi-list-nested me-2"></i>
                    Показать трассировку стека
                </button>

                <div class="collapse" id="stackTrace">
                    <pre class="p-3 mt-3 small"><?= htmlspecialchars($errorDetails['trace']) ?></pre>
                </div>
            <?php endif; ?>

            <hr class="border-secondary">
            <p class="text-muted small">
                <i class="bi bi-headset me-1"></i>
                Пожалуйста, сообщите администратору о данной ошибке.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>
</html>