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
            if (!in_array($_SERVER['REMOTE_ADDR'], $allowIPs)) {
                throw new SecurityException("Access Denied - " . $_SERVER['REMOTE_ADDR']);
            }
        }
    }
}
