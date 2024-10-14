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

        foreach ($customers as $customer) {
            // Create the email content based on the changed fields
            $emailBody = "<html><body>";
            $emailBody .= "<h2>Business Information Updated</h2>";
            $emailBody .= "<p><strong>{$event->newName}</strong> has updated their business information.</p>";

            foreach ($event->changes as $field => $change) {
                if ($field === 'business_Address') {
                    $emailBody .= "<p>Business Address changed from <strong>{$change['old']}</strong> to <strong>{$change['new']}</strong>.</p>";
                } elseif ($field === 'business_image') {
                    // Handle business image change
                    $oldImageUrl = asset(Storage::url('business_logos/archive/' . $change['old']));
                    $newImageUrl = asset(Storage::url('business_logos/' . $change['new']));
                    $emailBody .= "<p>{$event->newName} has changed their logo:</p>";
                    $emailBody .= "<div style='display:flex; align-items:center;'>";
                    $emailBody .= "<div style='margin-right:20px;'><strong>Old Image:</strong><br><img src='{$oldImageUrl}' alt='Old Logo' style='width:96px; height:96px; border-radius:50%;'></div>";
                    $emailBody .= "<div><strong>New Image:</strong><br><img src='{$newImageUrl}' alt='New Logo' style='width:96px; height:96px; border-radius:50%;'></div>";
                    $emailBody .= "</div>";
                } else {
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
