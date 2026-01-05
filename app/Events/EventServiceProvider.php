<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        \App\Events\DocumentRequestSubmitted::class => [
            \App\Listeners\SendAdminNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentRequestApproved::class => [
            \App\Listeners\SendUserNotification::class,
            \App\Listeners\SendWhatsAppNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentRequestRejected::class => [
            \App\Listeners\SendUserNotification::class,
            \App\Listeners\SendWhatsAppNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentReadyForPickup::class => [
            \App\Listeners\SendUserNotification::class,
            \App\Listeners\SendWhatsAppNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentUploaded::class => [
            \App\Listeners\SendUserNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentPickedUp::class => [
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentDownloaded::class => [
            \App\Listeners\SendAdminNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentCompleted::class => [
            \App\Listeners\SendUserNotification::class,
            \App\Listeners\SendAdminNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentVerificationRequested::class => [
            \App\Listeners\SendVerificationRequestNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentVerificationApproved::class => [
            \App\Listeners\NotifyAdminVerificationApproved::class,
            \App\Listeners\SendUserNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentVerificationRejected::class => [
            \App\Listeners\NotifyAdminVerificationRejected::class,
            \App\Listeners\SendUserNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\SignatureRequested::class => [
            \App\Listeners\SendSignatureRequestNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\SignatureUploaded::class => [
            \App\Listeners\NotifyAdminSignatureUploaded::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\SignatureVerified::class => [
            \App\Listeners\NotifyAuthoritySignatureVerified::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\SignatureRejected::class => [
            \App\Listeners\SendUserNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],

        \App\Events\DocumentMarkedAsTaken::class => [
            \App\Listeners\SendAdminNotification::class,
            \App\Listeners\LogDocumentActivity::class,
        ],
    ];

    public function boot(): void
    {
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
