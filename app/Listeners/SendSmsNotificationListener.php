<?php

namespace App\Listeners;

use App\Events\SendSmsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSmsNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SendSmsEvent $event)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('SEND_SMS_API', 'https://api.payvand.tj/messaging/SendMessage'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                [
                    "source-address" =>  env('SOURCE_ADDRESS', 'Livo Go'),
                    "destination-address" => $event->phoneNumber,
                    "data-encoding" =>  1,
                    "txn-id" =>  rand(1000000000, 9999999999999),
                    "message" =>  $event->message
                ]
            ]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Api-Key: ' . env('SEND_SMS_API_KEY', 'E187C22D-A253-4BC4-B5CA-680E8447B700'),
                'Locale:EN'
            ),
        ));

        curl_close($curl);
    }
}
