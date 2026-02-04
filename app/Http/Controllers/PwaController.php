<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PwaController extends Controller
{
    public function manifest()
    {
        $manifest = [
            'name' => 'BHDM Client',
            'short_name' => 'BHDM',
            'description' => 'Espace client BHDM - Gestion de portefeuille et demandes de financement',
            'start_url' => '/client/dashboard',
            'display' => 'standalone',
            'background_color' => '#1b5a8d',
            'theme_color' => '#1b5a8d',
            'orientation' => 'portrait-primary',
            'scope' => '/',
            'lang' => 'fr',
            'categories' => ['finance', 'business', 'productivity'],
            
            'icons' => $this->getIcons(),
            
            'screenshots' => [
                [
                    'src' => '/screenshots/mobile-dashboard.png',
                    'sizes' => '720x1280',
                    'type' => 'image/png',
                    'form_factor' => 'narrow'
                ],
                [
                    'src' => '/screenshots/tablet-dashboard.png',
                    'sizes' => '1536x2048',
                    'type' => 'image/png',
                    'form_factor' => 'wide'
                ]
            ],
            
            'shortcuts' => [
                [
                    'name' => 'Nouvelle demande',
                    'short_name' => 'Demande',
                    'description' => 'Créer une nouvelle demande de financement',
                    'url' => '/client/demandes/nouvelle',
                    'icons' => [
                        [
                            'src' => '/images/shortcut-request.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ],
                [
                    'name' => 'Mon portefeuille',
                    'short_name' => 'Portefeuille',
                    'description' => 'Consulter mon solde et transactions',
                    'url' => '/client/portefeuille',
                    'icons' => [
                        [
                            'src' => '/images/shortcut-wallet.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ]
            ]
        ];

        return response()->json($manifest);
    }

    private function getIcons()
    {
        $sizes = [72, 96, 128, 144, 152, 192, 384, 512];
        $icons = [];
        
        foreach ($sizes as $size) {
            $icons[] = [
                'src' => "/images/icon-{$size}x{$size}.png",
                'sizes' => "{$size}x{$size}",
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ];
        }
        
        return $icons;
    }
}