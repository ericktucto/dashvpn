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
        foreach ($lines as $line) {
            $property = Helper::getProperty($line, 'Address');
            if ($property !== null) {
                $address = preg_replace('/\/32/', '', $property) ?? '';
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
