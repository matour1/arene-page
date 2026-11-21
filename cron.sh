#!/bin/bash
# ══════════════════════════════════════════════════════════════════
# ARÈNE DES GAMERS — Planificateur de tâches
#
# Remplace le cron cPanel quand il n'est pas disponible.
# Le seul besoin de ce projet : lancer `artisan schedule:run` chaque
# minute, ce qui déclenche la synchronisation des tournois (horaire).
#
# Aucune queue n'est nécessaire ici : l'application ne dispatche aucun
# job asynchrone (vérifié : pas de ShouldQueue).
#
# ── Installation ─────────────────────────────────────────────────
#   1. chmod +x cron.sh
#   2. crontab -e
#      * * * * * /bin/bash /home/<compte>/htdocs/<domaine>/arene-page/cron.sh > /dev/null 2>&1
#
# ── Test manuel ──────────────────────────────────────────────────
#   ./cron.sh
#   tail -n 20 storage/logs/schedule.log
# ══════════════════════════════════════════════════════════════════

set -uo pipefail

# ── 1. Se placer dans le dossier du projet ───────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="${APP_DIR:-$SCRIPT_DIR}"

cd "$APP_DIR" || { echo "Projet introuvable : $APP_DIR" >&2; exit 1; }

if [ ! -f artisan ]; then
    echo "artisan absent de $APP_DIR" >&2
    exit 1
fi

# ── 2. Trouver le binaire PHP (8.3+ requis par Laravel 13) ───────
PHP="${PHP_BIN:-}"
if [ -z "$PHP" ]; then
    for candidat in php8.4 php84 php8.3 php83 php; do
        if command -v "$candidat" >/dev/null 2>&1; then
            PHP="$(command -v "$candidat")"
            break
        fi
    done
fi

if [ -z "$PHP" ] || [ ! -x "$PHP" ]; then
    echo "Binaire PHP introuvable. Definir PHP_BIN=/chemin/vers/php" >&2
    exit 1
fi

# ── 3. Verrou : empêche deux exécutions simultanées ──────────────
# flock peut être absent sur mutualisé : on continue alors sans verrou.
LOCK_FILE="${TMPDIR:-/tmp}/arene-cron.lock"
exec 200>"$LOCK_FILE" 2>/dev/null
if command -v flock >/dev/null 2>&1; then
    if ! flock -n 200; then
        exit 0   # une exécution est déjà en cours
    fi
fi

# ── 4. Logs (rotation simple à 5 Mo) ─────────────────────────────
LOG_DIR="storage/logs"
mkdir -p "$LOG_DIR"
LOG="$LOG_DIR/schedule.log"

if [ -f "$LOG" ]; then
    taille=$(stat -c%s "$LOG" 2>/dev/null || stat -f%z "$LOG" 2>/dev/null || echo 0)
    if [ "$taille" -gt 5242880 ]; then
        mv "$LOG" "${LOG}.old"
    fi
fi

# ── 5. Planificateur Laravel ─────────────────────────────────────
# C'est ici que se joue la synchronisation des tournois.
# `schedule:run` ne fait rien 59 minutes sur 60, puis lance les tâches dues.
echo "[$(date '+%Y-%m-%d %H:%M:%S')] schedule:run" >> "$LOG"
"$PHP" artisan schedule:run >> "$LOG" 2>&1 || true
