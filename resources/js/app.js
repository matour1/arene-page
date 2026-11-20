/*
 * Comportements du site : theme clair/sombre, menu mobile, barre de chargement.
 * Aucune dependance externe.
 */

/* ---------- Theme clair / sombre ---------- */
{
    const RACINE = document.documentElement;
    const CLE = 'arene-theme';

    const appliquer = (theme) => {
        RACINE.dataset.theme = theme;


        document.querySelectorAll('[data-bascule-theme]').forEach((btn) => {
            btn.setAttribute('aria-pressed', String(theme === 'light'));
        });
    };

    const lire = () => {
        try {
            return localStorage.getItem(CLE);
        } catch {
            return null;
        }
    };

    const enregistrer = (theme) => {
        try {
            localStorage.setItem(CLE, theme);
        } catch {
            /* Navigation privee : on ignore, le theme reste pour la session. */
        }
    };

    const preferencSysteme = () =>
        window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';

    // Applique immediatement (le script inline du <head> a deja pose l'attribut).
    appliquer(RACINE.dataset.theme || lire() || preferencSysteme());

    document.querySelectorAll('[data-bascule-theme]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const suivant = RACINE.dataset.theme === 'light' ? 'dark' : 'light';
            appliquer(suivant);
            enregistrer(suivant);
        });
    });

    // Suit le systeme tant que l'utilisateur n'a pas choisi explicitement.
    window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', (e) => {
        if (lire()) return;
        appliquer(e.matches ? 'light' : 'dark');
    });
}

/* ---------- Menu mobile ---------- */
{
    const bouton = document.getElementById('bouton-menu');
    const menu = document.getElementById('menu-mobile');

    if (bouton && menu) {
        const basculer = (ouvrir) => {
            menu.classList.toggle('hidden', !ouvrir);
            bouton.setAttribute('aria-expanded', String(ouvrir));
            bouton.setAttribute('aria-label', ouvrir ? 'Fermer le menu' : 'Ouvrir le menu');
        };

        bouton.addEventListener('click', () => basculer(menu.classList.contains('hidden')));

        menu.querySelectorAll('a').forEach((lien) => {
            lien.addEventListener('click', () => basculer(false));
        });

        window.matchMedia('(min-width: 768px)').addEventListener('change', (e) => {
            if (e.matches) basculer(false);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !menu.classList.contains('hidden')) {
                basculer(false);
                bouton.focus();
            }
        });
    }
}

/* ---------- Barre de progression ---------- */
{
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

            if (url.origin !== location.origin) return;
            if (url.pathname === location.pathname && url.hash) return;

            demarrer();
        });

        window.addEventListener('pageshow', terminer);
        window.addEventListener('load', terminer);

        document.querySelectorAll('form').forEach((formulaire) => {
            formulaire.addEventListener('submit', () => {
                const bouton = formulaire.querySelector('button[type="submit"]');

                if (bouton && !bouton.disabled) {
                    demarrer();
                    setTimeout(() => { bouton.disabled = true; }, 0);
                }
            });
        });
    }
}