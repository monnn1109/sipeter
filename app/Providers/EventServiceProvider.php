<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Events
use App\Events\{
    DocumentRequestSubmitted,
    DocumentRequestApproved,
    DocumentRequestRejected,
    DocumentReadyForPickup,
    DocumentUploaded,
    DocumentDownloaded,
    DocumentPickedUp,
    DocumentCompleted,
    DocumentVerificationRequested,
    DocumentVerificationApproved,
    DocumentVerificationRejected,
    SignatureVerified 
};

// Listeners
use App\Listeners\{
    SendAdminNotification,
    SendUserNotification,
    SendWhatsAppNotification,
    LogDocumentActivity,
    SendVerificationRequestNotification,
    NotifyAdminVerificationApproved,
    NotifyAdminVerificationRejected,
    NotifyAuthoritySignatureVerified,
    NotifyAllSignaturesVerified
};

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

        // Document Request Approved
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

        DocumentVerificationRejected::class => [
            NotifyAdminVerificationRejected::class,
        ],

        SignatureVerified::class => [
            NotifyAuthoritySignatureVerified::class,
            NotifyAllSignaturesVerified::class,
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
