<?php

if (!defined('access_inc')) {
    function check_access()
    {
        //check_access($xoopsUser->uid(),$xoopsUser->groups())

        global $xoopsDB;

        global $xoopsUser;

        $access['bans_add'] = $access['bans_edit'] = $access['bans_delete'] = $access['bans_unban'] = 'no';

        if ($xoopsUser) {
            // look and see if member is in webadmins

            $admins = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('amx_webadmins'));

            while (false !== ($admin = $xoopsDB->fetchArray($admins))) {
                if ('user' == $admin['type']) {
                    if ($admin['user_group_id'] == $xoopsUser->uid()) {
                        $levels[] = $admin['level'];
                    }
                } else {
                    foreach ($xoopsUser->groups() as $group) {
                        if ($admin['user_group_id'] == $group) {
                            $levels[] = $admin['level'];
                        }
                    }
                }
            }

            // if levels , go and check the access for that level

            $num_levels = count($levels);

            if ($num_levels > 0) {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('amx_levels') . ' WHERE ';

                for ($x = 0; $x < ($num_levels - 1); $x++) {
                    $sql .= 'level=' . $levels[$x] . ' OR ';
                }

                $sql .= 'level=' . $levels[$x];

                $results = $xoopsDB->query($sql);

                while (false !== ($result = $xoopsDB->fetchArray($results))) {
                    if ('yes' == $result['bans_add']) {
                        $access['bans_add'] = 'yes';
                    }

                    if ('yes' == $result['bans_edit']) {
                        $access['bans_edit'] = 'yes';
                    }

                    if ('yes' == $result['bans_delete']) {
                        $access['bans_delete'] = 'yes';
                    }

                    if ('yes' == $result['bans_unban']) {
                        $access['bans_unban'] = 'yes';
                    }
                }
            }
        }

        return $access;
    }

    define('access_inc', true);
}
