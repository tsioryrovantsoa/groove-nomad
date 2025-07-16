<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DiscordService
{
    public static function sendErrorToDiscord(array $context = [])
    {
        $webhookUrl = config('services.discord.webhook_url');
        if (!$webhookUrl) {
            return; // Pas de webhook configuré
        }

        $application = config('app.name'); // Nom de l'application
        $url = config('app.url');         // URL de l'application
        $dateTime = Carbon::now()->format('Y-m-d H:i:s'); // Date et heure actuelles

        $contextFormatted = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $payload = [
            'username' => "Error in {$application}", // Nom du bot : "Error in Application Name"
            'content' => "**URL** : <$url>\n**Date & Time** : {$dateTime}", // URL et date/heure
            'embeds' => [
                [
                    'title' => 'Error Details', // Titre en anglais
                    'description' => "```json\n{$contextFormatted}\n```", // Bloc de code JSON formaté
                    'color' => hexdec('C82333'), // Couleur personnalisée (#C82333)
                ]
            ]
        ];

        Http::post($webhookUrl, $payload);
    }
}