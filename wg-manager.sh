#!/usr/bin/env bash

set -euo pipefail

WG_INTERFACE="wg0"
WG_DIR="/etc/wireguard"

COMMAND="${1:-}"
PREFIX_DIR="${2:-}"

function get_keys() {
    echo "publicKey=$(cat ${WG_DIR}/${WG_INTERFACE}.pub)"
    echo "privateKey=$(cat ${WG_DIR}/${WG_INTERFACE}.key)"
    echo "presharedKey=$(cat ${PREFIX_DIR}/${WG_INTERFACE}.psk)"
}

function generate_keys() {
    umask 077

    wg genkey | tee ${WG_DIR}/${WG_INTERFACE}.key | wg pubkey > ${WG_DIR}/${WG_INTERFACE}.pub

    chmod 600 ${WG_DIR}/${WG_INTERFACE}.key
    chmod 644 ${WG_DIR}/${WG_INTERFACE}.pub

    WEB_GROUP="$(stat -c '%G' "$PREFIX_DIR")"
    wg genpsk | install -o root -g "$WEB_GROUP" -m 640 /dev/stdin "${PREFIX_DIR}/${WG_INTERFACE}.psk"

    get_keys
}

function reload() {
    cp ${PREFIX_DIR}/${WG_INTERFACE}.conf ${WG_DIR}/${WG_INTERFACE}.conf
    rm ${PREFIX_DIR}/${WG_INTERFACE}.conf
    wg syncconf ${WG_INTERFACE} <(wg-quick strip ${WG_INTERFACE})
    echo "reloaded=true"
}

function get_config() {
    wg showconf ${WG_INTERFACE}
    echo ""
}

case "$COMMAND" in
    get-keys)
        get_keys
        ;;
    generate-keys)
        generate_keys
        ;;
    reload)
        reload
        ;;
    get-config)
        get_config
        ;;
    *)
        echo "Usage: $0 {get-keys|generate-keys|reload|get-config}"
        exit 1
        ;;
esac
