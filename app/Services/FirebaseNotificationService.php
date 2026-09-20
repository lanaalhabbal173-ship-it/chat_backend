<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
      $credentials = json_decode(
    env('FIREBASE_CREDENTIALS_JSON'),
    true
);

$factory = (new Factory)
    ->withServiceAccount($credentials);

        $this->messaging = $factory->createMessaging();
    }


    public function sendNotification(
        string $token,
        string $title,
        string $body
    ) {
        $message = CloudMessage::withTarget(
            'token',
            $token
        )
        ->withNotification(
            Notification::create(
                $title,
                $body
            )
        );

        return $this->messaging->send($message);
    }
}