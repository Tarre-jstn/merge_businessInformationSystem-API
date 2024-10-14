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
            $emailBody = "<html><body style='font-family: Arial, sans-serif; color: #333;'>";
            $emailBody .= "<div style='padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-width: 600px; margin: auto; background-color: #f9f9f9;'>";
            
            // Header Section with Logo and Title
            $emailBody .= "<div style='display: flex; align-items: center; margin-bottom: 20px;'>";
            $emailBody .= "<img src='{$businessLogoUrl}' alt='Business Logo' style='width: 80px; height: 80px; border-radius: 50%; margin-right: 20px;'>";
            $emailBody .= "<h2 style='color: #0056b3;'>{$event->newName} Information Updated</h2>";
            $emailBody .= "</div>";
            
            // Business info updated message
            $emailBody .= "<p style='font-size: 16px;'><strong>{$event->newName}</strong> has updated their business information. Please see the details below:</p>";

            // Track changes and build the email body
            $imageChange = ''; // Temporary variable to hold image change HTML

            foreach ($event->changes as $field => $change) {
                if ($field === 'business_Address') {
                    $emailBody .= "<p style='font-size: 14px;'><strong>Business Address</strong> changed from <span style='color: #d9534f;'>{$change['old']}</span> to <span style='color: #5cb85c;'>{$change['new']}</span>.</p>";
                } elseif ($field === 'business_image') {
                    // Store image change information separately to append it later
                    $oldImageUrl = isset($change['old']) ? asset(Storage::url('business_logos/archive/' . $change['old'])) : 'No previous logo';
                    $newImageUrl = asset(Storage::url('business_logos/' . $change['new']));
                    $imageChange = "<p style='font-size: 14px;'><strong>Logo has been updated:</strong></p>";
                    $imageChange .= "<div style='display: flex; align-items: center;'>";
                    $imageChange .= "<div style='margin-right: 20px; text-align: center;'><strong>Old Logo</strong><br><img src='{$oldImageUrl}' alt='Old Logo' style='width: 96px; height: 96px; border-radius: 50%; border: 1px solid #ddd;'></div>";
                    $imageChange .= "<div style='text-align: center;'><strong>New Logo</strong><br><img src='{$newImageUrl}' alt='New Logo' style='width: 96px; height: 96px; border-radius: 50%; border: 1px solid #ddd;'></div>";
                    $imageChange .= "</div>";
                } else {
                    // Handle all other changes
                    $fieldLabel = ucfirst(str_replace('_', ' ', $field));
                    $emailBody .= "<p style='font-size: 14px;'><strong>{$fieldLabel}</strong> changed from <span style='color: #d9534f;'>{$change['old']}</span> to <span style='color: #5cb85c;'>{$change['new']}</span>.</p>";
                }
            }

            // Append the image change details to the bottom of the email body, if there was an image change
            if (!empty($imageChange)) {
                $emailBody .= "<hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>";
                $emailBody .= $imageChange;
            }

            $emailBody .= "</div>"; // Close main email div
            $emailBody .= "</body></html>";

            // Send the email
            Mail::html($emailBody, function ($message) use ($customer, $event) {
                $message->to($customer->email)
                    ->subject("Business Information Change Notification for {$event->newName}");
            });
        }
    }
}
