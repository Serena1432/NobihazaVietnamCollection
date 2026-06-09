document.addEventListener("DOMContentLoaded", () => {
    initSidebarScripts();
    const gridBtn = document.getElementById("btn-grid-view");
    const listBtn = document.getElementById("btn-list-view");
    const gameContainer = document.getElementById("game-list-container");
    if (gridBtn && listBtn && gameContainer) {
        gridBtn.addEventListener("click", () => {
            gameContainer.classList.remove("game-list-view");
            gridBtn.classList.add("text-primary");
            gridBtn.classList.remove("text-muted");
            listBtn.classList.remove("text-primary");
            listBtn.classList.add("text-muted");
        });
        listBtn.addEventListener("click", () => {
            gameContainer.classList.add("game-list-view");
            listBtn.classList.add("text-primary");
            listBtn.classList.remove("text-muted");
            gridBtn.classList.remove("text-primary");
            gridBtn.classList.add("text-muted");
        });
    }
});
function initSidebarScripts() {
    const submenuToggles = document.querySelectorAll('.has-submenu > a');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parentLi = this.parentElement;
            parentLi.classList.toggle('open');
            const icon = this.querySelector('.bi-chevron-down, .bi-chevron-up');
            if (icon) {
                if (parentLi.classList.contains('open')) {
                    icon.classList.replace('bi-chevron-down', 'bi-chevron-up');
                } else {
                    icon.classList.replace('bi-chevron-up', 'bi-chevron-down');
                }
            }
        });
    });
}
