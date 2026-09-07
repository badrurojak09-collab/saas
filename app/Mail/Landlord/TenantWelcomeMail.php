<?php

namespace App\Mail\Landlord;

use App\Models\Landlord\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public string $adminEmail
    ) {}

    public function build()
    {
        return $this
            ->subject('Selamat Datang di '.config('app.name'))
            ->markdown('emails.landlord.welcome');
    }
}
