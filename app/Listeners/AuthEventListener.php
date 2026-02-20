<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use OwenIt\Auditing\Models\Audit;

class AuthEventListener
{
    /**
     * Handle user login event.
     */
    public function handleLogin(Login $event): void
    {
        $this->logAuthEvent($event->user, 'login');
    }

    /**
     * Handle user logout event.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            $this->logAuthEvent($event->user, 'logout');
        }
    }

    /**
     * Log an auth event to the audits table.
     */
    protected function logAuthEvent($user, string $event): void
    {
        Audit::create([
            'user_type' => get_class($user),
            'user_id' => $user->id,
            'event' => $event,
            'auditable_type' => get_class($user),
            'auditable_id' => $user->id,
            'old_values' => [],
            'new_values' => ['action' => $event, 'name' => $user->name, 'email' => $user->email],
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'tags' => 'auth',
        ]);
    }
}
