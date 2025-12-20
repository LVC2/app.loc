<?php
/**
 * Home.php — основной шаблон SPA с продвинутым header
 * @var \Engine\Start $app
 */

// --- Инициализация переменных ---
$configContent = require $app->getRootPath() . '/Data/config/seo.php';

// Имя сайта (короткое)
$siteName = 'MasterAM';
// Полный заголовок (для SEO и вкладки браузера)
$fullTitle = 'MasterAm.us - Архив рабочих скриптов и учебников';

$description = htmlspecialchars($configContent['description'] ?? '');
$keywords = htmlspecialchars($configContent['keywords'] ?? '');
$copyright = htmlspecialchars($configContent['copyright'] ?? 'masteram.us');

// Проверка логотипа
$logoPath = $app->getRootPath() . '/Data/image/logo.png';
$logoExists = file_exists($logoPath);

// Данные пользователя
$isAuth = $app->getAuth()->isAuthorized();
$userObject = $app->getUser();

if ($userObject) {
    $username = htmlspecialchars($userObject->get('name', 'Гость'));
    $role = (int) ($userObject->get('role') ?? 0);
} else {
    $username = 'Гость';
    $role = 0;
}

$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/';
$appBaseTitle = $siteName; // Базовый титул для Vue остается коротким
?>

<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="RU" />
    <meta http-equiv="Cache-Control" content="private" />
    <meta name="copyright" content="<?= $copyright ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="Reply-to" content="<?= htmlspecialchars($configContent['reply_to'] ?? '') ?>" />
    <meta name="description" content="<?= $description ?>" />
    <meta name="keywords" content="<?= $keywords ?>" />
    <title><?= $fullTitle ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>Data/css/App.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>Data/css/theme-light.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>Data/css/theme-classic.css" rel="stylesheet">

</head>
<body class="d-flex flex-column min-vh-100">

<header id="site-header" class="site-header bg-dark border-bottom border-primary shadow">
    <div class="container-70-percent mx-auto d-flex justify-content-between align-items-center py-2 px-3">

        <div class="header-section header-left text-nowrap d-flex align-items-center">
            <?php if ($logoExists): ?>
                <a href="/" class="d-flex align-items-center">
                    <img src="<?= $baseUrl ?>Data/image/logo.png" alt="<?= $siteName ?>" style="height: 30px;" class="me-2">
                </a>
            <?php endif; ?>
            <h1 class="h5 m-0 me-3 text-primary fw-bold <?= $logoExists ? 'd-none d-sm-inline' : '' ?>"><?= $siteName ?></h1>
        </div>

        <div class="header-section header-center flex-grow-1 mx-4 d-none d-lg-flex justify-content-center">
            <div class="input-group input-group-sm" style="max-width: 400px;">
                <input type="text" class="form-control border-secondary" placeholder="Поиск по сайту (Ctrl+F)">
                <button class="btn btn-outline-primary" type="button" id="button-addon2">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <div class="header-section header-right d-flex align-items-center gap-2">

            <?php if ($isAuth): ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-fill me-1"></i> Личный кабинет
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <li><span class="dropdown-item-text text-white-50 small">Привет, <strong><?= $username ?></strong></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/profile"><i class="bi bi-person-gear me-2"></i> Мой профиль</a></li>
                        <li><a class="dropdown-item" href="/message"><i class="bi bi-envelope-open me-2"></i> Почта <span class="badge bg-danger ms-2">0</span></a></li>

                        <?php if ($role > 4): ?>
                            <li><a class="dropdown-item text-warning" href="/control"><i class="bi bi-lock-fill me-2"></i> Control</a></li>
                        <?php endif; ?>

                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/exit"><i class="bi bi-door-open me-2"></i> Выход</a></li>
                    </ul>
                </div>

            <?php else: ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Войти
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <li><a class="dropdown-item" href="/login"><i class="bi bi-lock me-2"></i> Авторизация</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/reg"><i class="bi bi-person-plus me-2"></i> Регистрация</a></li>
                    </ul>
                </div>
            <?php endif; ?>

            <button id="theme-toggle" class="btn btn-sm btn-outline-light" title="Сменить тему">
                <i class="bi bi-sun d-none"></i>       <i class="bi bi-moon-fill d-none"></i> <i class="bi bi-code d-none"></i>
            </button>
        </div>
    </div>
</header>

<div class="main-wrapper flex-grow-1 site-main">

    <div id="content-area" class="container-70-percent mx-auto flex-grow-1 p-0 d-flex">

        <div class="flex-grow-1 d-flex" id="app">
        </div>

    </div>
</div>

<footer class="bg-dark text-center py-3 border-top border-secondary mt-auto">
    <small class="text-white-50">
        &copy; <?= date('Y') ?> <?= $siteName ?> |
        <a href="/about" class="text-decoration-none text-white-50">О нас</a> |
        <a href="/contact" class="text-decoration-none text-white-50">Обратная связь</a> |
        <a href="/help" class="text-decoration-none text-white-50">Помощь</a>
    </small>
</footer>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="<?= $baseUrl ?>Data/js/Sidebar.js"></script>
<script src="<?= $baseUrl ?>Data/js/App.js"></script>
<script src="<?= $baseUrl ?>Data/js/ThemeToggle.js"></script>

<script>
    window.APP_INITIAL_STATE = {
        baseUrl: '<?= $baseUrl ?>',
        baseTitle: '<?= $appBaseTitle ?>',
        user: {
            isAuth: <?= $isAuth ? 'true' : 'false' ?>,
            username: '<?= $username ?>',
            role: <?= $role ?>
        },
        banData: <?= json_encode($app->getBan()->isBanned(true)) ?>
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>