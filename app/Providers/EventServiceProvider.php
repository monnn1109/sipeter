<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Events
use App\Events\DocumentRequestSubmitted;
use App\Events\DocumentRequestApproved;
use App\Events\DocumentRequestRejected;
use App\Events\DocumentReadyForPickup;
use App\Events\DocumentUploaded;
use App\Events\DocumentDownloaded;
use App\Events\DocumentPickedUp;
use App\Events\DocumentCompleted;
use App\Events\DocumentVerificationRequested;
use App\Events\DocumentVerificationApproved;
use App\Events\DocumentVerificationRejected;

// Listeners
use App\Listeners\SendAdminNotification;
use App\Listeners\SendUserNotification;
use App\Listeners\SendWhatsAppNotification;
use App\Listeners\LogDocumentActivity;
use App\Listeners\SendVerificationRequestNotification;
use App\Listeners\NotifyAdminVerificationApproved;
use App\Listeners\NotifyAdminVerificationRejected;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Document Request Submitted
        DocumentRequestSubmitted::class => [
            SendAdminNotification::class,
            SendWhatsAppNotification::class,
            LogDocumentActivity::class,
        ],

        DocumentRequestApproved::class => [
            SendUserNotification::class,
            SendWhatsAppNotification::class,
            LogDocumentActivity::class,
        ],

        // Document Request Rejected
        DocumentRequestRejected::class => [
            SendUserNotification::class,
            SendWhatsAppNotification::class,
            LogDocumentActivity::class,
        ],

        // Document Ready for Pickup
        DocumentReadyForPickup::class => [
            SendUserNotification::class,
            SendWhatsAppNotification::class,
            LogDocumentActivity::class,
        ],

        // Document Uploaded
        DocumentUploaded::class => [
            SendUserNotification::class,
            SendWhatsAppNotification::class,
            LogDocumentActivity::class,
        ],

        // Document Downloaded
        DocumentDownloaded::class => [
            SendAdminNotification::class,
            SendWhatsAppNotification::class,
            LogDocumentActivity::class,
        ],

        // Document Picked Up
        DocumentPickedUp::class => [
            SendUserNotification::class,
            SendWhatsAppNotification::class,
            LogDocumentActivity::class,
        ],

        // Document Completed
        DocumentCompleted::class => [
            SendUserNotification::class,
            SendWhatsAppNotification::class,
            LogDocumentActivity::class,
        ],

        // Document Verification Requested
        DocumentVerificationRequested::class => [
            SendVerificationRequestNotification::class,
        ],

        // Document Verification Approved
        DocumentVerificationApproved::class => [
            NotifyAdminVerificationApproved::class,
        ],

        // Document Verification Rejected
        DocumentVerificationRejected::class => [
            NotifyAdminVerificationRejected::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
