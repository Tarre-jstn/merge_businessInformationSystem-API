<?php

namespace App\Listeners;

use App\Events\BusinessNameUpdated;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendBusinessNameChangeNotification
{
    public function handle(BusinessNameUpdated $event)
    {
        // Fetch all customers
        $customers = User::where('user_type', 'customer')->get();

        // Determine whether to use the existing logo or a new logo
        $currentImage = $event->changes['business_image']['new'] ?? $event->oldImage; // Use new image if available, otherwise fallback to old image
        $businessLogoUrl = asset(Storage::url('business_logos/' . ($currentImage ?? 'default.png')));

        foreach ($customers as $customer) {
            // Create the email content based on the changed fields
            $emailBody = "<html><body>";
            $emailBody .= "<div style='display: flex; align-items: center;'>";
            $emailBody .= "<img src='{$businessLogoUrl}' alt='Business Logo' style='width: 80px; height: 80px; border-radius: 50%; margin-right: 20px;'>";
            $emailBody .= "<h2>{$event->newName} Information Updated</h2>";
            $emailBody .= "</div>";
            $emailBody .= "<p><strong>{$event->newName}</strong> has updated their business information.</p>";

            // Track changes and build the email body
            foreach ($event->changes as $field => $change) {
                if ($field === 'business_Address') {
                    $emailBody .= "<p>Business Address changed from <strong>{$change['old']}</strong> to <strong>{$change['new']}</strong>.</p>";
                } elseif ($field === 'business_image') {
                    // Handle the business image change separately
                    $oldImageUrl = isset($change['old']) ? asset(Storage::url('business_logos/archive/' . $change['old'])) : 'No previous logo';
                    $newImageUrl = asset(Storage::url('business_logos/' . $change['new']));
                    $emailBody .= "<p>{$event->newName} has changed their logo:</p>";
                    $emailBody .= "<div style='display: flex; align-items: center;'>";
                    $emailBody .= "<div style='margin-right: 20px;'><strong>Old Image:</strong><br><img src='{$oldImageUrl}' alt='Old Logo' style='width: 96px; height: 96px; border-radius: 50%;'></div>";
                    $emailBody .= "<div><strong>New Image:</strong><br><img src='{$newImageUrl}' alt='New Logo' style='width: 96px; height: 96px; border-radius: 50%;'></div>";
                    $emailBody .= "</div>";
                } else {
                    // Handle all other changes
                    $fieldLabel = ucfirst(str_replace('_', ' ', $field));
                    $emailBody .= "<p>{$fieldLabel} changed from <strong>{$change['old']}</strong> to <strong>{$change['new']}</strong>.</p>";
                }
            }

            $emailBody .= "</body></html>";

            // Send the email
            Mail::html($emailBody, function ($message) use ($customer, $event) {
                $message->to($customer->email)
                    ->subject("Business Information Change Notification for {$event->newName}");
            });
        }
    }
}
