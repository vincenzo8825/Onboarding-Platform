<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Notification extends Model
{
    /**
     * La tabella associata al modello.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * Indica se il modello deve avere timestamp.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Gli attributi che sono mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    /**
     * Gli attributi che devono essere convertiti.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Ottiene l'entità che possiede la notifica.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Determina se la notifica è stata letta.
     *
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Determina se la notifica non è stata letta.
     *
     * @return bool
     */
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Marca la notifica come letta.
     *
     * @return void
     */
    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * Marca la notifica come non letta.
     *
     * @return void
     */
    public function markAsUnread(): void
    {
        if ($this->read_at !== null) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    /**
     * Ottiene il titolo della notifica.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->data['title'] ?? 'Notifica';
    }

    /**
     * Ottiene il messaggio della notifica.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->data['message'] ?? '';
    }

    /**
     * Ottiene l'URL associato alla notifica.
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->data['url'] ?? null;
    }

    /**
     * Ottiene il nome della classe di notifica senza namespace.
     *
     * @return string
     */
    public function getNotificationClass(): string
    {
        return Str::afterLast($this->type, '\\');
    }
}
