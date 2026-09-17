// Auto-remover notificações após 3s
document.addEventListener('DOMContentLoaded', () => {
    const notif = document.querySelector('.notificacao');
    if (notif) setTimeout(() => notif.remove(), 3000);
});