function setBackground(el, url) {
    if (!url) return;
    const overlay =
        "linear-gradient(to right, rgba(0,0,0,.65), rgba(0,0,0,.15))";
    el.style.backgroundImage = `${overlay}, url('${url}')`;
    el.style.backgroundSize = "cover";
    el.style.backgroundPosition = "center";
}

function setActiveDot(dots, index) {
    dots.forEach((btn, i) => {
        if (i === index) {
            btn.classList.add("bg-green-400", "ring-2", "ring-white/70");
            btn.classList.remove("bg-white/60");
        } else {
            btn.classList.remove("bg-green-400", "ring-2", "ring-white/70");
            btn.classList.add("bg-white/60");
        }
    });
}

function parseImages(el) {
    try {
        const raw = el.getAttribute("data-hero-images") || "[]";
        const images = JSON.parse(raw);
        return Array.isArray(images) ? images.filter(Boolean) : [];
    } catch {
        return [];
    }
}

function initHeroSlider() {
    const hero = document.querySelector("[data-hero-slider]");
    if (!hero) return;

    const images = parseImages(hero);
    if (images.length === 0) return;

    const dotsRoot = document.querySelector("[data-hero-dots]");
    const dots = dotsRoot
        ? Array.from(dotsRoot.querySelectorAll("[data-hero-dot]"))
        : [];

    let index = 0;

    const goTo = (i) => {
        index = (i + images.length) % images.length;
        setBackground(hero, images[index]);
        if (dots.length) setActiveDot(dots, index);
    };

    goTo(0);

    dots.forEach((btn) => {
        btn.addEventListener("click", () => {
            const i = Number(btn.getAttribute("data-hero-dot") || "0");
            goTo(i);
        });
    });

    if (images.length > 1) {
        window.setInterval(() => goTo(index + 1), 6500);
    }
}

document.addEventListener("DOMContentLoaded", initHeroSlider);
