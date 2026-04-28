<?php

namespace App\Adapters\Wireguard;

use App\Domain\Wireguard\Peer;
use App\Helper;

final class FileToPeer
{
    /**
     * @param array{
     *  publicKey: string,
     *  privateKey: string,
     *  presharedKey: string
     * } $keys
     * @param string[] $lines
     */
    public function parse(
        array $keys,
        array $lines,
    ): Peer {
        $name = explode('# ', $lines[0])[1];
        $address = '';
        $allowedIps = [];
        foreach ($lines as $line) {
            $property = Helper::getProperty($line, 'Address');
            if ($property !== null) {
                $address = preg_replace('/\/32/', '', $property) ?? '';
            }

            $property = Helper::getProperty($line, 'AllowedIPs');
            if ($property !== null) {
                $allowedIps = array_map(
                    'trim',
                    explode(', ', $property),
                );
            }
        }

        return new Peer(
            $name,
            $address,
            $allowedIps,
            $keys['publicKey'],
            $keys['privateKey'],
            $keys['presharedKey'],
        );
    }
}
