/*
 * Comportements legers du site : menu mobile, barre de chargement,
 * etat "en cours" des formulaires. Aucune dependance externe.
 */

const bouton = document.getElementById('bouton-menu');
const menu = document.getElementById('menu-mobile');

if (bouton && menu) {
    const basculer = (ouvrir) => {
        menu.classList.toggle('hidden', !ouvrir);
        bouton.setAttribute('aria-expanded', String(ouvrir));
        bouton.setAttribute('aria-label', ouvrir ? 'Fermer le menu' : 'Ouvrir le menu');
    };

    bouton.addEventListener('click', () => {
        basculer(menu.classList.contains('hidden'));
    });

    // Fermer au clic sur un lien, puis fermer si on repasse en bureau.
    menu.querySelectorAll('a').forEach((lien) => {
        lien.addEventListener('click', () => basculer(false));
    });

    window.matchMedia('(min-width: 768px)').addEventListener('change', (e) => {
        if (e.matches) basculer(false);
    });

    // Echap ferme le menu.
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !menu.classList.contains('hidden')) {
            basculer(false);
            bouton.focus();
        }
    });
}

// Barre de progression : demarre a la navigation, se termine au chargement.
const barre = document.getElementById('barre-chargement');

if (barre) {
    let progression = 0;
    let minuteur = null;

    const demarrer = () => {
        progression = 12;
        barre.style.transform = 'scaleX(0.12)';
        clearInterval(minuteur);
        minuteur = setInterval(() => {
            progression = Math.min(progression + Math.random() * 12, 88);
            barre.style.transform = `scaleX(${progression / 100})`;
        }, 220);
    };

    const terminer = () => {
        clearInterval(minuteur);
        barre.style.transform = 'scaleX(1)';
        setTimeout(() => {
            barre.style.opacity = '0';
            barre.style.transform = 'scaleX(0)';
            setTimeout(() => { barre.style.opacity = '1'; }, 250);
        }, 200);
    };

    document.addEventListener('click', (e) => {
        const lien = e.target.closest('a');

        if (!lien) return;
        if (lien.target === '_blank' || lien.hasAttribute('download')) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;

        const url = new URL(lien.href, location.href);

        // Liens internes uniquement, et pas les ancres.
        if (url.origin !== location.origin) return;
        if (url.pathname === location.pathname && url.hash) return;

        demarrer();
    });

    window.addEventListener('pageshow', terminer);
    window.addEventListener('load', terminer);

    // Soumission de formulaire : eviter le double clic.
    document.querySelectorAll('form').forEach((formulaire) => {
        formulaire.addEventListener('submit', () => {
            const boutonForm = formulaire.querySelector('button[type="submit"]');

            if (boutonForm && !boutonForm.disabled) {
                demarrer();
                setTimeout(() => { boutonForm.disabled = true; }, 0);
            }
        });
    });
}