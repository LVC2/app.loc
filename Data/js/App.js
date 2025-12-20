// App.js — основной Vue компонент
const { createApp, ref } = Vue;

const App = {
    components: { Sidebar },
    data() {
        return {
            user: window.APP_INITIAL_STATE.user,
            pageTitle: ref('Главная страница'),
            currentPage: ref('home')
        };
    },
    methods: {
        updatePageTitle(newTitle) {
            const baseTitle = window.APP_INITIAL_STATE.baseTitle;

            // Установка заголовка в формате "Динамический Title | MasterAM"
            document.title = newTitle ? `${newTitle} | ${baseTitle}` : baseTitle;
            this.pageTitle = newTitle;
        },

        // Имитация роутинга для смены заголовка и контента
        navigate(title, pageKey) {
            this.updatePageTitle(title);
            this.currentPage = pageKey;
            // В реальном приложении здесь должна быть логика Vue Router для смены компонента
        }
    },
    mounted() {
        // Устанавливаем начальный заголовок при загрузке
        this.updatePageTitle(this.pageTitle);
    },
    template: `
    <div class="row g-0 flex-grow-1"> 
        <div class="col-md-4 col-lg-3 sidebar-wrapper">
            <Sidebar />
        </div>

        <div class="col-md-8 col-lg-9 p-4 content-wrapper">
            <h2 class="text-primary">{{ pageTitle }}</h2> 
            <hr class="border-secondary">
            
            <div v-if="currentPage === 'home'">
                <h3>Начало работы</h3>
                <p v-if="user.isAuth" class="text-success">Привет, <strong>{{ user.username }}</strong>! Вы авторизованы. Наслаждайтесь полным доступом к контенту.</p>
                <p v-else class="text-info">Добро пожаловать, Гость! Пожалуйста, <a href="/login">авторизуйтесь</a>, чтобы получить доступ ко всем функциям и отключить рекламу.</p>
            </div>
            
            <div v-if="currentPage === 'codes'">
                <p>Здесь будут представлены готовые куски кодов, скрипты и полезные инструменты для веб-мастеров.</p>
            </div>
            
            <div v-if="currentPage === 'gost'">
                <p>Страница гостевой книги. Обсуждайте, задавайте вопросы и делитесь опытом.</p>
            </div>

            <div v-if="currentPage === 'services'">
                <p>Набор полезных онлайн-сервисов для разработчиков и пользователей.</p>
            </div>
            
            <div v-if="currentPage === 'help'">
                <p>Раздел помощи и часто задаваемых вопросов.</p>
            </div>

            <div v-if="currentPage === 'profile'">
                <p>Это ваш личный кабинет. Здесь вы можете управлять данными и настройками.</p>
            </div>

            <div v-if="currentPage === 'chat'">
                <p>Добро пожаловать в мини-чат!</p>
            </div>

        </div>
    </div>
    `
};

// Монтируем Vue
document.addEventListener('DOMContentLoaded', () => {
    const appContainer = document.getElementById('app');
    if (appContainer) createApp(App).mount('#app');
});