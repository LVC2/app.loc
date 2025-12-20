// Sidebar.js
const { defineComponent } = Vue;

const Sidebar = defineComponent({
    name: 'Sidebar',
    setup() {
        const user = Vue.ref(window.APP_INITIAL_STATE.user || { isAuth: false, username: 'Гость', role: 0 });

        // Для имитации раскрывающихся разделов
        const activeSection = Vue.ref(null);

        const toggleSection = (section) => {
            activeSection.value = activeSection.value === section ? null : section;
        };

        return { user, activeSection, toggleSection };
    },
    template: `
    <div class="p-3"> 
        <div class="logo mb-4 text-center">
            <h4 class="text-primary fw-bold">Навигация</h4>
            <p class="text-muted small">Вы здесь: <strong>{{ $root.pageTitle }}</strong></p>
        </div>

        <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item bg-secondary text-white fw-bold">Аккаунт</li>
            
            <li v-if="user.role > 4" class="list-group-item">
                <a href="/admin" class="text-warning d-block">
                    <i class="bi bi-gear me-2"></i> Админка
                </a>
            </li>

            <li v-if="user.isAuth" class="list-group-item">
                <a href="/profile" @click.prevent="$root.navigate('Личный кабинет', 'profile')" class="text-success d-block">
                    <i class="bi bi-person-bounding-box me-2"></i> Личный кабинет
                </a>
            </li>
            
            <li v-else class="list-group-item">
                <a href="/login" class="text-success d-block">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Логин / Регистрация
                </a>
            </li>
        </ul>

        <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item bg-secondary text-white fw-bold">Коммуникации</li>
            
            <li class="list-group-item">
                <a href="/chat" @click.prevent="$root.navigate('Мини-чат', 'chat')" class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-chat-dots me-2"></i> Мини-чат</span>
                    <span class="badge bg-success rounded-pill">Онлайн: 15</span>
                </a>
            </li>

            <li class="list-group-item">
                <a href="#" @click.prevent="toggleSection('forum')" class="d-block fw-bold" :class="{'text-primary': activeSection === 'forum'}">
                    <i class="bi bi-chat-square-text me-2"></i> Форум
                </a>
                <ul v-if="activeSection === 'forum'" class="list-group list-group-flush mt-2 ms-3">
                    <li class="list-group-item sidebar-sub-item"><a href="/forum/main"><i class="bi bi-folder-fill me-2"></i> Форумы</a></li>
                    <li class="list-group-item sidebar-sub-item"><a href="/forum/new"><i class="bi bi-clock-history me-2"></i> Новое</a></li>
                    <li class="list-group-item sidebar-sub-item"><a href="/forum/search"><i class="bi bi-zoom-in me-2"></i> Поиск</a></li>
                </ul>
            </li>

            <li class="list-group-item">
                <a href="/gost/" @click.prevent="$root.navigate('Гостевая книга', 'gost')" class="d-block">
                    <i class="bi bi-people me-2"></i> Гостевая
                </a>
            </li>

        </ul>

        <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item bg-secondary text-white fw-bold">Контент</li>
            
            <li class="list-group-item">
                <a href="#" @click.prevent="toggleSection('fileshare')" class="d-block fw-bold" :class="{'text-primary': activeSection === 'fileshare'}">
                    <i class="bi bi-cloud-arrow-up me-2"></i> FileShare
                </a>
                <ul v-if="activeSection === 'fileshare'" class="list-group list-group-flush mt-2 ms-3">
                    <li class="list-group-item sidebar-sub-item"><a href="/files/upload"><i class="bi bi-upload me-2"></i> Загрузить файл</a></li>
                    <li class="list-group-item sidebar-sub-item"><a href="/files/browse"><i class="bi bi-columns me-2"></i> Каталог</a></li>
                    <li class="list-group-item sidebar-sub-item"><a href="/files/top"><i class="bi bi-graph-up me-2"></i> Популярное</a></li>
                </ul>
            </li>

            <li class="list-group-item">
                <a href="#" @click.prevent="toggleSection('articles')" class="d-block fw-bold" :class="{'text-primary': activeSection === 'articles'}">
                    <i class="bi bi-file-earmark-text me-2"></i> Статьи
                </a>
                <ul v-if="activeSection === 'articles'" class="list-group list-group-flush mt-2 ms-3">
                    <li class="list-group-item sidebar-sub-item"><a href="/articles/main"><i class="bi bi-star me-2"></i> Свежие</a></li>
                    <li class="list-group-item sidebar-sub-item"><a href="/articles/list"><i class="bi bi-tags me-2"></i> По категориям</a></li>
                </ul>
            </li>

            <li class="list-group-item">
                <a href="/codes" @click.prevent="$root.navigate('Куски кодов', 'codes')" class="d-block">
                    <i class="bi bi-code-slash me-2"></i> Куски кодов
                </a>
            </li>
            
            <li class="list-group-item">
                <a href="/services" @click.prevent="$root.navigate('Сервисы', 'services')" class="d-block">
                    <i class="bi bi-tools me-2"></i> Сервисы
                </a>
            </li>

            <li class="list-group-item">
                <a href="/help" @click.prevent="$root.navigate('Помощь', 'help')" class="d-block">
                    <i class="bi bi-question-diamond me-2"></i> Помощь
                </a>
            </li>
        </ul>
    </div>`
});