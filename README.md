<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Sistema di Notifiche - Piattaforma di Onboarding

Questa documentazione spiega come funziona il sistema di notifiche implementato nella piattaforma di onboarding.

## Panoramica

Il sistema di notifiche utilizza il framework integrato di Laravel per gestire notifiche tra admin e dipendenti. È stato progettato per:

- Notificare agli admin le azioni dei dipendenti
- Notificare ai dipendenti le azioni degli admin
- Visualizzare le notifiche in un dropdown nella navbar
- Filtrare le notifiche per stato (lette/non lette)
- Contrassegnare le notifiche come lette

## Implementazione Tecnica

### Componenti principali:

1. **Modello User con Trait Notifiable**
   - Il trait `Illuminate\Notifications\Notifiable` è incluso nel modello User
   - Questo trait aggiunge funzionalità per inviare e ricevere notifiche

2. **Classi di Notifica**
   - Ogni tipo di notifica ha una propria classe in `app/Notifications/`
   - Esempi: `DocumentUploadedNotification`, `NewTicketReplyNotification`, ecc.

3. **Controller delle Notifiche**
   - `NotificationsController` gestisce visualizzazione e gestione delle notifiche
   - Include metodi per contrassegnare le notifiche come lette

4. **Database**
   - Le notifiche sono memorizzate nella tabella `notifications`
   - Le notifiche non lette sono facilmente identificabili dal campo `read_at` nullo

5. **Frontend**
   - Componente `notifications-dropdown.blade.php` per visualizzare le notifiche
   - JavaScript per azioni come "segna come letta" senza ricaricare la pagina

## Come inviare una notifica

### Passaggio 1: Creare una classe di notifica
```php
php artisan make:notification NomeNotifica
```

### Passaggio 2: Configurare la classe
```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NomeNotifica extends Notification
{
    // Configura costruttore con i dati necessari
    
    // Configura canali di consegna
    public function via($notifiable)
    {
        return ['database', 'mail']; // Possibilità di usare più canali
    }
    
    // Definisci il formato per email
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Titolo della notifica')
            ->line('Contenuto della notifica');
    }
    
    // Definisci il formato per database
    public function toArray($notifiable)
    {
        return [
            'title' => 'Titolo della notifica',
            'message' => 'Messaggio della notifica',
            'url' => '/percorso/rilevante',
        ];
    }
}
```

### Passaggio 3: Inviare la notifica
```php
// Notifica a un singolo utente
$user->notify(new NomeNotifica($data));

// Notifica a più utenti
Notification::send($users, new NomeNotifica($data));
```

## Esempi di Notifiche

### Notifica da Dipendente ad Admin
```php
// Quando un dipendente carica un documento
$admins = User::whereHas('roles', function ($query) {
    $query->where('name', 'admin');
})->get();

Notification::send($admins, new DocumentUploadedNotification($document, $user));
```

### Notifica da Admin a Dipendente
```php
// Quando un admin approva un documento
$employee->notify(new DocumentApprovedNotification($document, Auth::user()));
```

## Visualizzazione Notifiche

Le notifiche vengono visualizzate in un dropdown accessibile dall'icona della campana nella navbar. Le notifiche non lette hanno un design distintivo e includono:

- Icona specifica per il tipo di notifica
- Titolo
- Messaggio breve
- Data/ora relativa
- Pulsante per contrassegnare come letta

## Best Practices

1. **Mantieni le notifiche concise** - Le notifiche dovrebbero essere chiare e contenere informazioni essenziali
2. **Includi sempre un URL** - Ogni notifica dovrebbe portare a una pagina rilevante
3. **Considera l'utilizzo di code** - Per notifiche di massa, usa la funzionalità di code di Laravel implementando l'interfaccia `ShouldQueue`
4. **Mantieni un UX coerente** - Usa icone e colori coerenti per tipi simili di notifiche
