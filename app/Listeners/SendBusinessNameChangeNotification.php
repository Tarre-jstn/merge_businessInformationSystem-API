<?php

namespace App\Listeners;

use App\Events\BusinessNameUpdated;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendBusinessNameChangeNotification
{
    public function handle(BusinessNameUpdated $event)
    {
        // Fetch all customers
        $customers = User::where('user_type', 'customer')->get();

        foreach ($customers as $customer) {
            // Compose the email body using plain HTML
            $emailBody = "
                <html>
                    <body>
                        <h2>Business Name Change Notification</h2>
                        <p>The business <strong>{$event->oldName}</strong> has changed its name to <strong>{$event->newName}</strong>.</p>
                        <p>If you have any questions, feel free to reach out to them.</p>
                    </body>
                </html>";

            // Use Mail::html() instead of Mail::send()
            Mail::html($emailBody, function ($message) use ($customer) {
                $message->to($customer->email)
                        ->subject('Business Name Change Notification');
            });
        }
    }
}
