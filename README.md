# Piattaforma di Onboarding

Una piattaforma moderna per la gestione degli eventi e del processo di onboarding, costruita con Laravel e Bootstrap.

## 🚀 Funzionalità Principali

- **Gestione Eventi**
  - Creazione e gestione eventi
  - Tracciamento partecipanti
  - Gestione presenze
  - Eventi obbligatori/facoltativi

- **Gestione Utenti**
  - Sistema di autenticazione
  - Ruoli e permessi
  - Profili personalizzabili

- **Dashboard Admin**
  - Statistiche in tempo reale
  - Gestione partecipanti
  - Report e analisi

- **Sistema Notifiche**
  - Notifiche in tempo reale
  - Comunicazione admin-dipendenti
  - Gestione notifiche lette/non lette

## 📋 Requisiti

- PHP >= 8.4
- Composer
- Node.js e NPM
- MySQL/SQLite
- Estensioni PHP standard

## 🛠️ Installazione Rapida

```bash
# Clona il repository
git clone [URL_REPOSITORY]
cd onboarding-platform

# Installa dipendenze
composer install
npm install

# Configura
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate

# Avvia
php artisan serve
npm run dev
```

## 🔧 Configurazione

Configura il file `.env` con:
- Database (MySQL/SQLite)
- Credenziali
- Altre impostazioni necessarie

## 📝 Licenza

MIT License

## 👥 Contribuire

1. Fork
2. Branch feature
3. Commit
4. Push
5. Pull Request

## 📞 Supporto

Per supporto: [INSERIRE_EMAIL] o apri un issue
