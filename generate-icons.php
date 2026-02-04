<?php
/**
 * Script de génération des icônes PWA pour BHDM
 * Exécution : php generate-icons.php
 */

class IconGenerator {
    private $sizes = [72, 96, 128, 144, 152, 192, 384, 512];
    private $shortcutSizes = [96];
    private $badgeSize = 72;
    private $screenshotSizes = [
        'mobile' => [375, 812],
        'tablet' => [768, 1024],
        'desktop' => [1280, 720]
    ];

    private $colors = [
        'primary' => [27, 90, 141],    // #1b5a8d
        'secondary' => [255, 90, 88],  // #ff5a58
        'accent' => [74, 175, 255],    // #4aafff
        'dark' => [10, 31, 68],        // #0a1f44
        'light' => [248, 249, 250]     // #f8f9fa
    ];

    public function __construct() {
        // Vérifier si GD est installé
        if (!extension_loaded('gd')) {
            die("❌ L'extension GD n'est pas installée. Installez-la avec : sudo apt-get install php-gd\n");
        }
    }

    public function generateAll() {
        echo "🚀 Démarrage de la génération des icônes PWA...\n\n";

        // Créer les dossiers nécessaires
        $this->createDirectories();

        // Générer les icônes principales
        $this->generateMainIcons();

        // Générer les icônes de raccourci
        $this->generateShortcutIcons();

        // Générer le badge
        $this->generateBadge();

        // Générer les favicons
        $this->generateFavicons();

        // Générer les captures d'écran de démonstration
        $this->generateScreenshots();

        // Générer le manifest.json
        $this->generateManifest();

        echo "\n✅ Tous les assets PWA ont été générés avec succès !\n";
        echo "📍 Emplacement : public/images/\n";
    }

    private function createDirectories() {
        $dirs = [
            'public/images',
            'public/images/screenshots',
            'public/images/shortcuts'
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                echo "📁 Création du dossier : $dir\n";
            }
        }
    }

    private function generateMainIcons() {
        echo "\n🎨 Génération des icônes principales :\n";

        foreach ($this->sizes as $size) {
            $image = imagecreatetruecolor($size, $size);
            imagesavealpha($image, true);
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);

            // Dessiner le fond arrondi
            $margin = $size * 0.1;
            $radius = $size * 0.1667;
            $this->drawRoundedRect($image, $margin, $margin, $size - $margin, $size - $margin, $radius, $this->colors['primary']);

            // Dessiner le symbole BHDM (lettre B stylisée)
            $symbolSize = $size * 0.5;
            $x = ($size - $symbolSize) / 2;
            $y = ($size - $symbolSize) / 2;

            // Créer un cercle pour le symbole
            $this->drawCircle($image, $size / 2, $size / 2, $symbolSize / 2, $this->colors['secondary']);

            // Ajouter la lettre B au centre
            $fontSize = $symbolSize * 0.6;
            $fontPath = __DIR__ . '/public/fonts/roboto-bold.ttf';

            if (file_exists($fontPath)) {
                $textColor = imagecolorallocate($image, 255, 255, 255);
                $bbox = imagettfbbox($fontSize, 0, $fontPath, 'B');
                $textWidth = $bbox[2] - $bbox[0];
                $textHeight = $bbox[1] - $bbox[7];
                imagettftext($image, $fontSize, 0, ($size - $textWidth) / 2, ($size + $textHeight) / 2, $textColor, $fontPath, 'B');
            } else {
                // Fallback avec GD fonts
                $textColor = imagecolorallocate($image, 255, 255, 255);
                $font = 5; // GD built-in font
                $text = 'B';
                $textWidth = imagefontwidth($font) * strlen($text);
                $textHeight = imagefontheight($font);
                imagestring($image, $font, ($size - $textWidth) / 2, ($size - $textHeight) / 2, $text, $textColor);
            }

            // Sauvegarder l'image
            $filename = "public/images/icon-{$size}.png";
            imagepng($image, $filename, 9);
            imagedestroy($image);

            echo "  ✓ Icone {$size}x{$size} générée\n";
        }
    }

    private function generateShortcutIcons() {
        echo "\n🔗 Génération des icônes de raccourci :\n";

        $shortcuts = [
            'demande' => ['text' => 'D', 'color' => $this->colors['accent']],
            'portefeuille' => ['text' => 'P', 'color' => $this->colors['success']],
            'formation' => ['text' => 'F', 'color' => $this->colors['warning']],
            'support' => ['text' => 'S', 'color' => $this->colors['danger']]
        ];

        foreach ($this->shortcutSizes as $size) {
            foreach ($shortcuts as $name => $config) {
                $image = imagecreatetruecolor($size, $size);
                imagesavealpha($image, true);
                $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefill($image, 0, 0, $transparent);

                // Fond arrondi
                $margin = $size * 0.1;
                $radius = $size * 0.2;
                $this->drawRoundedRect($image, $margin, $margin, $size - $margin, $size - $margin, $radius, $config['color']);

                // Texte
                $textColor = imagecolorallocate($image, 255, 255, 255);
                $fontPath = __DIR__ . '/public/fonts/roboto-bold.ttf';

                if (file_exists($fontPath)) {
                    $fontSize = $size * 0.4;
                    $bbox = imagettfbbox($fontSize, 0, $fontPath, $config['text']);
                    $textWidth = $bbox[2] - $bbox[0];
                    $textHeight = $bbox[1] - $bbox[7];
                    imagettftext($image, $fontSize, 0, ($size - $textWidth) / 2, ($size + $textHeight) / 2, $textColor, $fontPath, $config['text']);
                } else {
                    $font = 5;
                    $textWidth = imagefontwidth($font) * strlen($config['text']);
                    $textHeight = imagefontheight($font);
                    imagestring($image, $font, ($size - $textWidth) / 2, ($size - $textHeight) / 2, $config['text'], $textColor);
                }

                $filename = "public/images/shortcut-{$name}-{$size}.png";
                imagepng($image, $filename, 9);
                imagedestroy($image);

                echo "  ✓ Raccourci {$name} {$size}x{$size} généré\n";
            }
        }
    }

    private function generateBadge() {
        echo "\n🏷️  Génération du badge de notification :\n";

        $size = $this->badgeSize;
        $image = imagecreatetruecolor($size, $size);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);

        // Cercle rouge pour les notifications
        $this->drawCircle($image, $size / 2, $size / 2, $size / 2, $this->colors['secondary']);

        // Ajouter une icône de cloche
        $bellColor = imagecolorallocate($image, 255, 255, 255);

        // Dessiner une cloche simple (points et ligne)
        $centerX = $size / 2;
        $centerY = $size / 2;
        $bellWidth = $size * 0.4;
        $bellHeight = $size * 0.5;

        // Base de la cloche (trapèze)
        $points = [
            $centerX - $bellWidth / 2, $centerY - $bellHeight / 3,
            $centerX + $bellWidth / 2, $centerY - $bellHeight / 3,
            $centerX + $bellWidth / 3, $centerY + $bellHeight / 2,
            $centerX - $bellWidth / 3, $centerY + $bellHeight / 2
        ];

        imagefilledpolygon($image, $points, 4, $bellColor);

        // Battant
        $clapperWidth = $size * 0.1;
        $clapperHeight = $size * 0.15;
        imagefilledrectangle($image,
            $centerX - $clapperWidth / 2,
            $centerY + $bellHeight / 2,
            $centerX + $clapperWidth / 2,
            $centerY + $bellHeight / 2 + $clapperHeight,
            $bellColor
        );

        $filename = "public/images/badge-{$size}.png";
        imagepng($image, $filename, 9);
        imagedestroy($image);

        echo "  ✓ Badge {$size}x{$size} généré\n";
    }

    private function generateFavicons() {
        echo "\n🖼️  Génération des favicons :\n";

        $sizes = [16, 32, 64, 128];

        foreach ($sizes as $size) {
            $image = imagecreatetruecolor($size, $size);
            imagesavealpha($image, true);
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);

            // Carré avec coins arrondis
            $margin = $size * 0.1;
            $radius = $size * 0.2;
            $this->drawRoundedRect($image, $margin, $margin, $size - $margin, $size - $margin, $radius, $this->colors['primary']);

            // Petite lettre B
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $font = 3; // Plus petite police GD
            $text = 'B';
            $textWidth = imagefontwidth($font) * strlen($text);
            $textHeight = imagefontheight($font);
            imagestring($image, $font, ($size - $textWidth) / 2, ($size - $textHeight) / 2, $text, $textColor);

            $filename = "public/images/favicon-{$size}.png";
            imagepng($image, $filename, 9);
            imagedestroy($image);

            echo "  ✓ Favicon {$size}x{$size} généré\n";
        }

        // Générer aussi favicon.ico (multi-format)
        $icoImage = imagecreatetruecolor(32, 32);
        imagesavealpha($icoImage, true);
        $transparent = imagecolorallocatealpha($icoImage, 0, 0, 0, 127);
        imagefill($icoImage, 0, 0, $transparent);

        $margin = 3;
        $radius = 5;
        $this->drawRoundedRect($icoImage, $margin, $margin, 32 - $margin, 32 - $margin, $radius, $this->colors['primary']);

        $textColor = imagecolorallocate($icoImage, 255, 255, 255);
        imagestring($icoImage, 3, 10, 10, 'B', $textColor);

        imagepng($icoImage, 'public/favicon.png', 9);
        imagedestroy($icoImage);

        echo "  ✓ Favicon principal (favicon.png) généré\n";
    }

    private function generateScreenshots() {
        echo "\n📱 Génération des captures d'écran de démonstration :\n";

        $screens = [
            'dashboard' => 'Tableau de bord',
            'portefeuille' => 'Mon portefeuille',
            'demandes' => 'Mes demandes'
        ];

        foreach ($this->screenshotSizes as $device => $dimensions) {
            list($width, $height) = $dimensions;

            foreach ($screens as $screen => $title) {
                $image = imagecreatetruecolor($width, $height);

                // Fond dégradé
                $this->drawGradient($image, $width, $height, $this->colors['dark'], $this->colors['primary']);

                // Barre de titre
                $titleBarHeight = $height * 0.08;
                imagefilledrectangle($image, 0, 0, $width, $titleBarHeight,
                    imagecolorallocate($image,
                        $this->colors['primary'][0],
                        $this->colors['primary'][1],
                        $this->colors['primary'][2]
                    )
                );

                // Texte du titre
                $textColor = imagecolorallocate($image, 255, 255, 255);
                imagestring($image, 4, 20, $titleBarHeight / 4, "BHDM - {$title}", $textColor);

                // Contenu simulé
                $contentColor = imagecolorallocatealpha($image, 255, 255, 255, 60);
                $this->drawSimulatedContent($image, $width, $height, $titleBarHeight, $contentColor);

                // Ajouter un cadre d'appareil pour mobile/tablet
                if ($device !== 'desktop') {
                    $this->drawDeviceFrame($image, $width, $height, $device);
                }

                $filename = "public/images/screenshots/{$device}-{$screen}.png";
                imagepng($image, $filename, 8);
                imagedestroy($image);

                echo "  ✓ Capture {$device} - {$screen} générée\n";
            }
        }
    }

    private function generateManifest() {
        echo "\n📄 Génération du manifest.json :\n";

        $manifest = [
            "name" => "BHDM Espace Client",
            "short_name" => "BHDM Client",
            "description" => "Application BHDM pour la gestion des portefeuilles et demandes de financement",
            "start_url" => "/client/dashboard",
            "display" => "standalone",
            "background_color" => "#0a1f44",
            "theme_color" => "#1b5a8d",
            "orientation" => "portrait-primary",
            "lang" => "fr",
            "icons" => [],
            "shortcuts" => [
                [
                    "name" => "Nouvelle demande",
                    "short_name" => "Demande",
                    "description" => "Créer une nouvelle demande de financement",
                    "url" => "/client/demandes/nouvelle",
                    "icons" => []
                ],
                [
                    "name" => "Mon portefeuille",
                    "short_name" => "Portefeuille",
                    "description" => "Accéder à mon portefeuille",
                    "url" => "/client/portefeuille",
                    "icons" => []
                ]
            ],
            "categories" => ["finance", "business", "productivity"],
            "screenshots" => []
        ];

        // Ajouter les icônes
        foreach ($this->sizes as $size) {
            $manifest["icons"][] = [
                "src" => "/images/icon-{$size}.png",
                "sizes" => "{$size}x{$size}",
                "type" => "image/png",
                "purpose" => "any maskable"
            ];
        }

        // Ajouter les icônes de raccourci
        foreach ($this->shortcutSizes as $size) {
            $manifest["shortcuts"][0]["icons"][] = [
                "src" => "/images/shortcut-demande-{$size}.png",
                "sizes" => "{$size}x{$size}"
            ];
            $manifest["shortcuts"][1]["icons"][] = [
                "src" => "/images/shortcut-portefeuille-{$size}.png",
                "sizes" => "{$size}x{$size}"
            ];
        }

        // Ajouter les captures d'écran
        foreach ($this->screenshotSizes as $device => $dimensions) {
            list($width, $height) = $dimensions;

            $manifest["screenshots"][] = [
                "src" => "/images/screenshots/{$device}-dashboard.png",
                "sizes" => "{$width}x{$height}",
                "type" => "image/png",
                "form_factor" => $device === 'desktop' ? 'wide' : 'narrow',
                "label" => "Tableau de bord BHDM"
            ];
        }

        // Sauvegarder le manifest
        file_put_contents('public/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        echo "  ✓ Manifest.json généré\n";
    }

    private function drawRoundedRect($image, $x1, $y1, $x2, $y2, $radius, $color) {
        $r = $radius;
        $col = imagecolorallocate($image, $color[0], $color[1], $color[2]);

        // Dessiner les 4 coins arrondis
        imagefilledarc($image, $x1 + $r, $y1 + $r, $r * 2, $r * 2, 180, 270, $col, IMG_ARC_PIE);
        imagefilledarc($image, $x2 - $r, $y1 + $r, $r * 2, $r * 2, 270, 360, $col, IMG_ARC_PIE);
        imagefilledarc($image, $x2 - $r, $y2 - $r, $r * 2, $r * 2, 0, 90, $col, IMG_ARC_PIE);
        imagefilledarc($image, $x1 + $r, $y2 - $r, $r * 2, $r * 2, 90, 180, $col, IMG_ARC_PIE);

        // Dessiner les rectangles centraux
        imagefilledrectangle($image, $x1 + $r, $y1, $x2 - $r, $y2, $col);
        imagefilledrectangle($image, $x1, $y1 + $r, $x2, $y2 - $r, $col);
    }

    private function drawCircle($image, $cx, $cy, $radius, $color) {
        $col = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        imagefilledellipse($image, $cx, $cy, $radius * 2, $radius * 2, $col);
    }

    private function drawGradient($image, $width, $height, $color1, $color2) {
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $r = $color1[0] + ($color2[0] - $color1[0]) * $ratio;
            $g = $color1[1] + ($color2[1] - $color1[1]) * $ratio;
            $b = $color1[2] + ($color2[2] - $color1[2]) * $ratio;

            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $i, $width, $i, $color);
        }
    }

    private function drawSimulatedContent($image, $width, $height, $titleBarHeight, $color) {
        // Simuler des éléments d'interface
        $contentY = $titleBarHeight + 20;

        // Cartes statistiques
        $cardWidth = ($width - 60) / 2;
        $cardHeight = 80;

        for ($i = 0; $i < 4; $i++) {
            $x = 20 + ($i % 2) * ($cardWidth + 20);
            $y = $contentY + floor($i / 2) * ($cardHeight + 20);

            // Carte
            imagefilledrectangle($image, $x, $y, $x + $cardWidth, $y + $cardHeight, $color);

            // Texte dans la carte
            $textColor = imagecolorallocate($image, 50, 50, 50);
            imagestring($image, 3, $x + 10, $y + 10, "Statistique " . ($i + 1), $textColor);
        }

        // Tableau
        $tableY = $contentY + 2 * ($cardHeight + 20) + 20;
        $tableHeight = 120;

        imagefilledrectangle($image, 20, $tableY, $width - 20, $tableY + $tableHeight, $color);

        // Lignes du tableau
        $rowHeight = $tableHeight / 4;
        for ($i = 1; $i < 4; $i++) {
            imageline($image, 20, $tableY + $i * $rowHeight, $width - 20, $tableY + $i * $rowHeight,
                imagecolorallocate($image, 100, 100, 100));
        }

        // Colonnes
        $colWidth = ($width - 40) / 5;
        for ($i = 1; $i < 5; $i++) {
            imageline($image, 20 + $i * $colWidth, $tableY, 20 + $i * $colWidth, $tableY + $tableHeight,
                imagecolorallocate($image, 100, 100, 100));
        }
    }

    private function drawDeviceFrame($image, $width, $height, $device) {
        $frameColor = imagecolorallocate($image, 100, 100, 100);
        $bezelColor = imagecolorallocate($image, 50, 50, 50);

        // Barre de statut (pour mobile)
        if ($device === 'mobile') {
            $statusHeight = 30;
            imagefilledrectangle($image, 0, 0, $width, $statusHeight, $bezelColor);

            // Icônes de statut
            $statusColor = imagecolorallocate($image, 200, 200, 200);
            imagestring($image, 2, 5, 5, "9:41", $statusColor);
            imagestring($image, 2, $width - 40, 5, "4G", $statusColor);
        }

        // Encadrement de l'appareil
        $frameWidth = 10;
        imagefilledrectangle($image, 0, 0, $frameWidth, $height, $frameColor); // Gauche
        imagefilledrectangle($image, $width - $frameWidth, 0, $width, $height, $frameColor); // Droite
        imagefilledrectangle($image, 0, 0, $width, $frameWidth, $frameColor); // Haut
        imagefilledrectangle($image, 0, $height - $frameWidth, $width, $height, $frameColor); // Bas

        // Bouton home (pour mobile)
        if ($device === 'mobile') {
            $buttonSize = 40;
            $buttonX = $width / 2 - $buttonSize / 2;
            $buttonY = $height - $frameWidth - 5;

            imagefilledellipse($image, $buttonX, $buttonY, $buttonSize, $buttonSize, $bezelColor);
        }
    }
}

// Exécution du script
echo "========================================\n";
echo "    Générateur d'icônes PWA - BHDM     \n";
echo "========================================\n";

$generator = new IconGenerator();
$generator->generateAll();
