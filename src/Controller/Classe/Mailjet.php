<?php

namespace App\Controller\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mailjet
{
    private $api_key = '282d3b80fa9093e259817b61cb0ac263';

    private $api_secret_key = '08b63d688cc2b10c18072f22701ba81f';

    public function send($to_email, $to_name, $subject, $content)
    {
        // Instanciation de l'objet Mailjet
        $mj = new Client($this->api_key, $this->api_secret_key, true, ['version' => 'v3.1']);
        // Corps de l'email
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "lionel.delamare@hotmail.com",
                        'Name' => "Ma Boutique"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    // Numéro du template
                    'TemplateID' => 2209591,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    // Contenu du message
                    'Variables' => [
                        "content" => $content
                    ]
                ]
            ]
        ];
        // Méthode post pour l'envoi de l'email
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        // Réponse renvoyée
        $response->success();
    }
}