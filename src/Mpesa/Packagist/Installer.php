<?php

namespace Kabangi\Mpesa\Support;

use Composer\Script\Event;

class Installer
{
    public static function install(Event $event)
    {
        $config    = __DIR__ . '/../../config/mpesa.php';
        $cert    = __DIR__ . '/../../config/mpesa_public_cert.cer';
        $configDir = self::getConfigDirectory($event);

        if (! \is_file($configDir . '/mpesa.php')) {
            \copy($config, $configDir . '/mpesa.php');
        }

        // Copy mpesa config file
        if (! \is_file($configDir . '/mpesa.php')) {
            \copy($config, $configDir . '/mpesa.php');
        }

        // Copy certificate
        if (! \is_file($configDir . '/mpesa_public_cert.cer')) {
            \copy($cert, $configDir . '/mpesa_public_cert.cer');
        }
    }

    public static function getConfigDirectory(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $configDir = $vendorDir . '/../src/config';

        if (! \is_dir($configDir)) {
            \mkdir($configDir, 0755, true);
        }

        return $configDir;
    }
}
