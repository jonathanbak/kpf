<?php

namespace Kpf;
use Kpf\Exception\SecurityException;

class Security
{
    /**
     * @throws Exception\ConfigException
     * @throws Exception\DirectoryException
     * @throws Exception\Exception
     * @throws SecurityException
     */
    public static function ruleStart()
    {
        //firewall 가동
        $firewallFlag = Application::getConfig()->common(Constant::KEY_SECURE);
        if (empty($firewallFlag)) $firewallFlag = 0;
        if ($firewallFlag == 1 || strtolower($firewallFlag) == 'on') {
            $allowIPs = Application::getConfig()->common(Constant::KEY_ALLOW_IP);
            $remoteIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            if (!in_array($remoteIp, $allowIPs)) {
                throw new SecurityException("Access Denied - " . $remoteIp);
            }
        }
    }
}
