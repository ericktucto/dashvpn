<?php

namespace App\Adapters\Wireguard;

use App\Domain\Wireguard\Peer;
use App\Helper;

class FileToPeer
{
    /**
     * @param string[] $keys
     * @param string[] $lines
     */
    public function parse(
        array $keys,
        array $lines,
    ): Peer {
        $name = explode('# ', $lines[0])[1];
        $address = '';
        foreach ($lines as $line) {
            $property = Helper::getProperty($line, 'Address');
            if ($property) {
                $address = $property;
            }
        }

        return new Peer(
            $name,
            $address,
            $keys['publicKey'],
            $keys['privateKey'],
            $keys['presharedKey'],
        );
    }
}
