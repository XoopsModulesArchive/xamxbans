<?php
/* 

    AMXBans, managing bans for Half-Life modifications
    Copyright (C) 2003, 2004  Ronald Renes / Jeroen de Rover

		web		: http://www.xs4all.nl/~yomama/amxbans/
		mail	: yomama@xs4all.nl
		ICQ		: 104115504
    
		This file is part of AMXBans.

    AMXBans is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    AMXBans is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with AMXBans; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

echo '<center><b>Admins and Levels</b><br><br>';

if (isset($_REQUEST['level_apply'])) {
    if (!isset($_REQUEST['type'])) {
        UpdateAdminLevel('update', $_REQUEST['level'], $_REQUEST['bans_add'], $_REQUEST['bans_edit'], $_REQUEST['bans_delete'], $_REQUEST['bans_unban']);
        echo 'Updating level...<br>Level ' . $_REQUEST['level'] . ' updated successfully.';

        if ($CFG->use_logfile == 'yes') {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user           = $xoopsUser->uname();;
            $lvl = $_REQUEST['level'];
            AddLogEntry("$remote_address | $user | Level $lvl got edited");
        }
    } else {
        UpdateAdminLevel('insert', $_REQUEST['level'], $_REQUEST['bans_add'], $_REQUEST['bans_edit'], $_REQUEST['bans_delete'], $_REQUEST['bans_unban']);
        echo 'Inserting level...<br>Level ' . $_REQUEST['level'] . ' inserted successfully.';

        if ($CFG->use_logfile == 'yes') {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user           = $xoopsUser->uname();;
            $lvl = $_REQUEST['level'];
            AddLogEntry("$remote_address | $user | Level $lvl was created");
        }
    }
} elseif (isset($_REQUEST['level_remove'])) {
    UpdateAdminLevel('remove', $_REQUEST['level'], $_REQUEST['bans_add'], $_REQUEST['bans_edit'], $_REQUEST['bans_delete'], $_REQUEST['bans_unban']);
    echo 'Removing level...<br>Level ' . $_REQUEST['level'] . ' removed successfully.';

    if ($CFG->use_logfile == 'yes') {
        $remote_address = $_SERVER['REMOTE_ADDR'];
        $user           = $_SESSION['uid'];
        $lvl            = $_REQUEST['level'];
        AddLogEntry("$remote_address | $user | Level $lvl was removed");
    }
} elseif (isset($_REQUEST['amxadmin_apply'])) {
    if (!isset($_REQUEST['type'])) {
        UpdateAMXAdmin('update', $_REQUEST['id'], $_REQUEST['username'], $_REQUEST['password'], $_REQUEST['access'], $_REQUEST['flags'], $_REQUEST['steamid'], $_REQUEST['nickname']);
        echo 'Updating AMXadmindetails...<br>AMXadmindetails for admin ' . $_REQUEST['username'] . ' updated successfully.';

        if ($CFG->use_logfile == 'yes') {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user           = $_SESSION['uid'];
            $adm            = $_REQUEST['username'];
            $eyedee         = $_REQUEST['id'];
            AddLogEntry("$remote_address | $user | AMXAdmindetails for $adm ($eyedee) updated");
        }
    } else {
        UpdateAMXAdmin('insert', $_REQUEST['id'], $_REQUEST['username'], $_REQUEST['password'], $_REQUEST['access'], $_REQUEST['flags'], $_REQUEST['steamid'], $_REQUEST['nickname']);
        echo 'Inserting admin...<br>AMXadmin inserted successfully.';

        if ($CFG->use_logfile == 'yes') {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user           = $_SESSION['uid'];
            $adm            = $_REQUEST['username'];
            $eyedee         = $_REQUEST['id'];
            AddLogEntry("$remote_address | $user | AMXAdmin $adm ($eyedee) was created");
        }
    }
} elseif (isset($_REQUEST['amxadmin_remove'])) {
    UpdateAMXAdmin('remove', $_REQUEST['id'], $_REQUEST['username'], $_REQUEST['password'], $_REQUEST['access'], $_REQUEST['flags'], $_REQUEST['steamid'], $_REQUEST['steamid']);
    echo 'Removing AMXadmin...<br>AMXadmin ' . $_REQUEST['username'] . ' removed successfully.';

    if ($CFG->use_logfile == 'yes') {
        $remote_address = $_SERVER['REMOTE_ADDR'];
        $user           = $_SESSION['uid'];
        $adm            = $_REQUEST['username'];
        $eyedee         = $_REQUEST['id'];
        AddLogEntry("$remote_address | $user | AMXAdmin $adm ($eyedee) was removed");
    }
} elseif (isset($_REQUEST['webadmin_apply'])) {
    if ($_REQUEST['utype'] == 'user') {
        $uid = $_REQUEST['users'];
    } else {
        $uid = $_REQUEST['groups'];
    }
    if (!isset($_REQUEST['type'])) {
        UpdateWebAdmin('update', $_REQUEST['id'], $_REQUEST['utype'], $uid, $_REQUEST['level']);
        echo 'Updating Webadmindetails<br>Webadmindetails updated successfully.';

        if ($CFG->use_logfile == 'yes') {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user           = $_SESSION['uid'];
            $adm            = $_REQUEST['username'];
            $eyedee         = $_REQUEST['id'];
            AddLogEntry("$remote_address | $user | WebAdmindetails for $adm ($eyedee) updated");
        }
    } else {
        UpdateWebAdmin('insert', 0, $_REQUEST['utype'], $uid, $_REQUEST['level']);

        echo 'Inserting Webadmin<br>Webadmin inserted successfully.';

        if ($CFG->use_logfile == 'yes') {
            $remote_address = $_SERVER['REMOTE_ADDR'];
            $user           = $_SESSION['uid'];
            $adm            = $_REQUEST['username'];
            $eyedee         = $_REQUEST['id'];
            AddLogEntry("$remote_address | $user | WebAdmin $adm ($eyedee) was created");
        }
    }
} elseif (isset($_REQUEST['webadmin_remove'])) {
    UpdateWebAdmin('remove', $_REQUEST['id'], '', '', '');
    echo 'Removing Webadmin<br>Webadmin removed successfully.';

    if ($CFG->use_logfile == 'yes') {
        $remote_address = $_SERVER['REMOTE_ADDR'];
        $user           = $_SESSION['uid'];
        $adm            = $_REQUEST['username'];
        $eyedee         = $_REQUEST['id'];
        AddLogEntry("$remote_address | $user | WebAdmin $adm ($eyedee) was removed");
    }
}

?>

</center>
<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td colspan="6"><b>Level management...</b></td>
    </tr>

    <tr>
        <td colspan="1" align="center">&nbsp;</td>
        <td colspan="4" align="center">Bans</td>
        <td colspan="1" align="center">&nbsp</td>
    </tr>

    <tr>
        <td width="1%"><i>Level</i></td>

        <td align="center"><i>add</i></td>
        <td align="center"><i>edit</i></td>
        <td align="center"><i>delete</i></td>
        <td align="center"><i>unban</i></td>
        <td><i>Action</i></td>
    <tr>

        <?php

        $level = GetLevels();

        if ($level == 0) {
            echo "<tr><td colspan=\"6\" bgcolor=\"#f0ffff\" align=\"center\"><br>No levels found in DB<br><br></td></tr>\n";
        } else {
        foreach ($level

        as $row) {
        ?>

        <form name="manage7" method="post" action="index.php?task=admin_mgmt">

    <tr>
        <td bgcolor="#f0ffff" align="center">
            <input type="hidden" name="level" value="<?php print $row['level'] ?>">
            <?php print $row['level'] ?>&nbsp;
        </td>
        <td bgcolor="#f0ffff" align="center">

            <select name="bans_add" style="font: 8pt bold">
                <option value="yes" <?php if ($row['bans_add'] == 'yes') {
                    print 'selected';
                } ?>>yes
                </option>
                <option value="no" <?php if ($row['bans_add'] == 'no') {
                    print 'selected';
                } ?>>no
                </option>
            </select>

        </td>
        <td bgcolor="#f0ffff" align="center">

            <select name="bans_edit" style="font: 8pt bold">
                <option value="yes" <?php if ($row['bans_edit'] == 'yes') {
                    print 'selected';
                } ?>>yes
                </option>
                <option value="no" <?php if ($row['bans_edit'] == 'no') {
                    print 'selected';
                } ?>>no
                </option>
            </select>

        </td>
        <td bgcolor="#f0ffff" align="center">

            <select name="bans_delete" style="font: 8pt bold">
                <option value="yes" <?php if ($row['bans_delete'] == 'yes') {
                    print 'selected';
                } ?>>yes
                </option>
                <option value="no" <?php if ($row['bans_delete'] == 'no') {
                    print 'selected';
                } ?>>no
                </option>
            </select>

        </td>
        <td bgcolor="#f0ffff" align="center">

            <select name="bans_unban" style="font: 8pt bold">
                <option value="yes" <?php if ($row['bans_unban'] == 'yes') {
                    print 'selected';
                } ?>>yes
                </option>
                <option value="no" <?php if ($row['bans_unban'] == 'no') {
                    print 'selected';
                } ?>>no
                </option>
            </select>

        </td>

        <td bgcolor="#f0ffff" align="right"><input type="submit" name="level_apply" value=" apply " style="font: 8pt bold"> <input type="submit" name="level_remove" value=" remove " style="font: 8pt bold" onclick="javascript:return confirm('Are you sure you want to remove this level ?')"></td>

    </tr>
    </form>

    <?php
    }
    if (isset($_REQUEST['level_add'])) {
        ?>

        <form name="manage6" method="post" action="index.php?task=admin_mgmt">
            <input type="hidden" name="type" value="insert">
            <tr>
                <td bgcolor="#f0ffff" align="center">

                    <select name="level" style="font: 8pt bold">

                        <?php

                        $existing_levels = GetAdminLevels();

                        for ($i = 1; $i < 100; $i++) {
                            if (in_array($i, $existing_levels)) {
                                next($existing_levels);
                            } else {
                                echo "<option value=\"$i\">$i</option>\n";
                            }
                        }

                        ?>

                    </select>

                </td>
                <td bgcolor="#f0ffff" align="center">

                    <select name="bans_add" style="font: 8pt bold">
                        <option value="yes">yes</option>
                        <option value="no" selected>no</option>
                    </select>

                </td>
                <td bgcolor="#f0ffff" align="center">

                    <select name="bans_edit" style="font: 8pt bold">
                        <option value="yes">yes</option>
                        <option value="no" selected>no</option>
                    </select>

                </td>
                <td bgcolor="#f0ffff" align="center">

                    <select name="bans_delete" style="font: 8pt bold">
                        <option value="yes">yes</option>
                        <option value="no" selected>no</option>
                    </select>

                </td>
                <td bgcolor="#f0ffff" align="center">

                    <select name="bans_unban" style="font: 8pt bold">
                        <option value="yes">yes</option>
                        <option value="no" selected>no</option>
                    </select>

                </td>

                <td bgcolor="#f0ffff" align="right"><input type="submit" name="level_apply" value=" apply " style="font: 8pt bold"></td>

            </tr>
        </form>

        <?php
    }
    }
    ?>

    <form name="add1" method="post" action="index.php?task=admin_mgmt">
        <tr>
            <td colspan="6" align="right"><input type="submit" name="level_add" value=" add level " style="font: 8pt bold"></td>
        </tr>
    </form>
</table><br>

<?php ////////////////////?>

<table name="admin" border="1" cellpadding="4" cellspacing="0" width="100%">
    <tr>
        <td colspan="4"><b>Webadmin management...</b></b></td>
    </tr>

    <tr>
        <td align="center"><i>Type</i></td>
        <td align="center"><i>User/Group</i></td>
        <td align="center"><i>level</i></td>
        <td><i>Action</i></td>
    <tr>

        <?php

        $admin = GetWebAdmins();
        if ($admin == 0) {
            echo "<tr>\n";
            echo "<td colspan=\"4\" bgcolor=\"#f0ffff\" align=\"center\"><br>No admins found in DB<br><br></td>\n";
            echo "</tr>\n";
        } else {
        foreach ($admin

        as $row) {
        ?>
        <form name="manageadmins<?php echo $row['id']; ?>" method="post" action="index.php?task=admin_mgmt">


    <tr>


        <td align="center">
            <input type="hidden" name="id" value="<?php print $row['id']; ?>">
            <input type="hidden" name="level" value="<?php print $row['level']; ?>">
            <input name="utype" type="radio" value="user" onChange="this.form.users.disabled=false;this.form.groups.disabled=true" <?php if ($row['type'] == 'user') echo 'checked' ?>>User
            <input name="utype" type="radio" value="group" onChange="this.form.users.disabled=true;this.form.groups.disabled=false" <?php if ($row['type'] == 'group') echo 'checked' ?> >Group
        </td>

        <td bgcolor="#f0ffff" align="center">
            <select name="users" style="font: 8pt bold;" <?php if ($row['type'] == 'group') echo 'disabled' ?>>
                <?php
                $xusers = GetxoppsUsers();
                foreach ($xusers as $user) {
                    ?>
                    <option value="<?= $user['uid'] ?>" <?php if ($user['uid'] == $row['user_group_id']) {
                        print 'selected';
                    } ?>><?= $user['uname'] ?></option>
                    <?php
                }

                ?>
            </select>
            <select name="groups" style="font: 8pt bold;" <?php if ($row['type'] == 'user') echo 'disabled' ?>>
                <?php
                $xgroups = GetxoppsGroups();
                foreach ($xgroups as $group) {
                    ?>
                    <option value="<?= $group['groupid'] ?>" <?php if ($group['groupid'] == $row['user_group_id']) {
                        print 'selected';
                    } ?>><?= $group['name'] ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td bgcolor="#f0ffff" align="center">

            <select name="level" style="font: 8pt bold;">

                <?php

                $existing_lvl = GetAdminLevels();

                foreach ($existing_lvl as $key) {
                    ?>

                    <option value="<?= $key ?>" <?php if ($key == $row['level']) {
                        print 'selected';
                    } ?>><?= $key ?></option>

                    <?php
                }
                ?>

            </select>


        </td>
        <td bgcolor="#f0ffff" align="right"><input type="submit" name="webadmin_apply" value=" apply " style="font: 8pt bold"> <input type="submit" name="webadmin_remove" value=" remove " style="font: 8pt bold" onclick="javascript:return confirm('Are you sure you want to remove this admin ?')"></td>
    </tr>
    </form>

    <?php
    }



    if (isset($_REQUEST['webadmin_add'])) {
    ?>

    <form name="manage5" method="post" action="index.php?task=admin_mgmt">
        <td align="center">
            <input type="hidden" name="type" value="insert">
            <input name="utype" type="radio" value="user" onChange="this.form.users.disabled=false;this.form.groups.disabled=true">User
            <input name="utype" type="radio" value="group" onChange="this.form.users.disabled=true;this.form.groups.disabled=false">Group
        </td>

        <td bgcolor="#f0ffff" align="center">
            <select name="users" style="font: 8pt bold;" disabled>
                <?php
                $xusers = GetxoppsUsers();
                foreach ($xusers as $user) {
                    ?>
                    <option value="<?= $user['uid'] ?>"><?= $user['uname'] ?></option>
                    <?php
                }

                ?>
            </select>
            <select name="groups" style="font: 8pt bold;" disabled>
                <?php
                $xgroups = GetxoppsGroups();
                foreach ($xgroups as $group) {
                    ?>
                    <option value="<?= $group['groupid'] ?>"><?= $group['name'] ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td bgcolor="#f0ffff" align="center">

            <select name="level" style="font: 8pt bold;">
                <?php
                ////http://devedge.netscape.com/library/manuals/2000/javascript/1.3/reference/document.html
                $existing_lvl = GetAdminLevels();

                foreach ($existing_lvl as $key) { ?>
                    <option value="<?= $key ?>"><?= $key ?></option><?php }
                ?>
            </select>


        </td>
        <td bgcolor="#f0ffff" align="right"><input type="submit" name="webadmin_apply" value=" apply " style="font: 8pt bold"></td>
        </tr>

        <?php
        }
        }

        ?>
        <form name="manage4" method="post" action="index.php?task=admin_mgmt">
            <tr>
                <td colspan="4" align="right"><input type="submit" name="webadmin_add" value=" add admin " style="font: 8pt bold"></td>
            </tr>
        </form>
</table>
<br>
<?php ////////////////////////////////////?>
<table border="1" cellpadding="4" cellspacing="0" width="100%">
    <tr>
        <td colspan="7"><b>AMXadmin management...</b><br><font size="1"> refer to the readme (section 5) if you have trouble getting this to work</font></td>
    </tr>

    <tr>
        <td width="16%" align="center"><i>Nickname/SteamID/IP</i></td>
        <td width="16%" align="center"><i>password</i></td>
        <td width="16%" align="center"><i>access</i></td>
        <td width="4%" align="center"><i>flags</i></td>
        <td width="16%" align="center"><i>steamid</i></td>
        <td width="16%" align="center"><i>nickname</i></td>
        <td width="16%"><i>Action</i></td>
    <tr>

        <?php

        $admin = GetAMXAdmins();

        if ($admin == 0) {
            echo "<tr>\n";
            echo "<td colspan=\"7\" bgcolor=\"#f0ffff\" align=\"center\"><br>No admins found in DB<br><br></td>\n";
            echo "</tr>\n";
        } else {
        foreach ($admin

        as $row) {
        ?>

        <form name="manage3" method="post" action="" index.php?task=admin_mgmt"">


    <tr>
        <td bgcolor="#f0ffff" align="center">

            <input type="hidden" name="id" value="<?php print $row['id']; ?>">
            <input type="hidden" name="username" value="<?php print $row['username']; ?>">
            <input type="hidden" name="access" value="<?php print $row['access']; ?>">
            <input type="hidden" name="flags" value="<?php print $row['flags']; ?>">
            <input type="hidden" name="steamid" value="<?php print $row['steamid']; ?>">
            <input type="hidden" name="nickname" value="<?php print $row['nickname']; ?>">

            <input type="text" name="username" value="<?php print $row['username']; ?>" style="font: 8pt bold; width: 120px"></td>
        <td bgcolor="#f0ffff" align="center"><input type="text" name="password" style="font: 8pt bold; width: 100px"></td>
        <td bgcolor="#f0ffff" align="center"><input type="text" name="access" value="<?php print $row['access']; ?>" style="font: 8pt bold; width: 120px"></td>
        <td bgcolor="#f0ffff" align="center"><input type="text" name="flags" value="<?php print $row['flags']; ?>" style="font: 8pt bold; width: 40px"></td>
        <td bgcolor="#f0ffff" align="center"><input type="text" name="steamid" value="<?php print $row['steamid']; ?>" style="font: 8pt bold; width: 120px"></td>
        <td bgcolor="#f0ffff" align="center"><input type="text" name="nickname" value="<?php print $row['nickname']; ?>" style="font: 8pt bold; width: 120px"></td>
        <td bgcolor="#f0ffff" align="right"><input type="submit" name="amxadmin_apply" value=" apply " style="font: 8pt bold"> <input type="submit" name="amxadmin_remove" value=" remove " style="font: 8pt bold" onclick="javascript:return confirm('Are you sure you want to remove this admin ?')"></td>
    </tr>
    </form>

    <?php
    }
    }

    if (isset($_REQUEST['amxadmin_add'])) {
        ?>

        <form name="manage2" method="post" action="" index.php?task=admin_mgmt"">
            <input type="hidden" name="type" value="insert">
            <tr>
                <td bgcolor="#f0ffff" align="center"><input type="text" name="username" style="font: 8pt bold; width: 120px"></td>
                <td bgcolor="#f0ffff" align="center"><input type="text" name="password" style="font: 8pt bold; width: 100px"></td>
                <td bgcolor="#f0ffff" align="center"><input type="text" name="access" style="font: 8pt bold; width: 120px"></td>
                <td bgcolor="#f0ffff" align="center"><input type="text" name="flags" style="font: 8pt bold; width: 40px"></td>
                <td bgcolor="#f0ffff" align="center"><input type="text" name="steamid" style="font: 8pt bold; width: 120px"></td>
                <td bgcolor="#f0ffff" align="center"><input type="text" name="nickname" style="font: 8pt bold; width: 120px"></td>
                <td bgcolor="#f0ffff" align="right"><input type="submit" name="amxadmin_apply" value=" apply " style="font: 8pt bold"> <input type="submit" name="amxremove" value=" remove " style="font: 8pt bold" onclick="javascript:return confirm('Are you sure you want to remove this admin ?')">
                </td>
            </tr>
        </form>

        <?php
    }

    ?>

    <form name="manage1" method="post" action="" index.php?task=admin_mgmt"">
        <tr>
            <td colspan="7" align="right"><input type="submit" name="amxadmin_add" value=" add admin " style="font: 8pt bold"></td>
        </tr>
    </form>
</table>



