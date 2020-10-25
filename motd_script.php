<?php

function xoops_module_install_xamxbans(&$module)
{
    global $xoopsDB;

    global $xoopsConfig;

    $ret = false;

    $sql = 'ALTER TABLE ' . $xoopsDB->prefix('amx_serverinfo') . " ALTER COLUMN amxban_motd SET DEFAULT '" . $xoopsConfig['xoops_url'] . "/modules/xamxbans/motd_details.php?bid=%s'";

    $result = $xoopsDB->query($sql);

    if ($result) {
        $ret = true;
    }

    return $ret;
}
