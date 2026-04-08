<?php

namespace App\Services;

class WireguardWrapper implements WireguardWrapperInterface
{
    /**
     * @return string[]|false
     */
    public function getServer(): array|false
    {
        //$result = exec("sudo /usr/local/bin/wg-safe.sh server", $output);
        $result = exec("cat /home/erick/proyectos/dashvpn/tests/Unit/Fixtures/wg0.conf", $output);
        if (!$result) {
            return false;
        }
        return $output;
    }

    /**
     * @return string[]|false
     */
    public function getServerKeys(): array|false
    {
        //$result = exec("sudo /usr/local/bin/wg-safe.sh server", $output);

        $keys = [
            'publicKey' => '',
            'privateKey' => '',
            'presharedKey' => '',
        ];
        $result = exec("echo C57OCFgebG4EtrWw", $output);
        if (!$result) {
            return false;
        }
        $keys['publicKey'] = $output[0];
        $result = exec("echo z3yCGsiZIu5IDzWX", $output);
        if (!$result) {
            return false;
        }
        $keys['privateKey'] = $output[0];
        $result = exec("echo jDreKQlSPG3MBSHG", $output);
        if (!$result) {
            return false;
        }
        $keys['presharedKey'] = $output[0];
        return $keys;
    }
}
