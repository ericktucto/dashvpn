import type Ip from "@/components/dashvpn/types/ip";

export function str2Ip(ip: string): Ip {
    const parts = ip.split('.')
    if (parts.length !== 4) {
        throw new Error('Invalid IP address')
    }
    return {
        first: parseInt(parts[0]),
        second: parseInt(parts[1]),
        third: parseInt(parts[2]),
        fourth: parseInt(parts[3])
    }
}
export function ip2Str(ip: Ip): string {
    return `${ip.first}.${ip.second}.${ip.third}.${ip.fourth}`
}
