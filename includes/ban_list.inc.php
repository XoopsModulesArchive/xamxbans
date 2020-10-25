<?php

$ban_array = [];
$ban_info = [];

$limit = $_GET['limit'] ?? 20;
$page  = $_GET['page'] ?? 0;

$numrows = CountBans();

$pages = (int)($numrows / $limit);

if ($numrows % $limit) {
    $pages++;
}

$current = ($page / $limit) + 1;

if (($pages < 1) || (0 == $pages)) {
    $total = 1;
} else {
    $total = $pages;
}

$first = $page + 1;

if (!((($page + $limit) / $limit) >= $pages) && 1 != $pages) {
    $last = $page + $limit;
} else {
    $last = $numrows;
}

if ('yes' == $CFG->show_reason_in_list) {
    $player_width = '25%';

    $reason_width = '25%';
} else {
    $player_width = '50%';
}

$jumpblock[]['limit'] = '10';
$jumpblock[]['limit'] = '20';
$jumpblock[]['limit'] = '50';
$jumpblock[]['limit'] = '100';
$jumpblock[]['limit'] = '250';

$ban_array = [];
$ban_info = [];

$bans = GetBans($page, $limit);

foreach ($bans as $row) {
    $svr_ip = $row['server_ip'];

    $ban_info = [
        'gametype' => GetGametype($svr_ip),
        'bid' => $row['bid'],
        'fban_created' => $row['fban_created'],
        'player_nick' => $row['player_nick'],
        'admin_nick' => $row['admin_nick'],
        'ban_reason' => $row['ban_reason'],
    ];

    $ban_array[] = $ban_info;
}

$back_page = '';
$next_page = '';

if (0 != $page) {
    $back_page = $page - $limit;
}

$page_array = [];

for ($i = 1; $i <= $pages; $i++) {
    $this_page = [
        'page' => $i,
        'limit' => $limit * ($i - 1),
    ];

    $page_array[] = $this_page;
}

if (!((($page + $limit) / $limit) >= $pages) && 1 != $pages) {
    $next_page = $page + $limit;
}

$xoopsTpl->assign('showreason', $CFG->show_reason_in_list);
$xoopsTpl->assign('jumpblock', $jumpblock);
$xoopsTpl->assign('total', $total);
$xoopsTpl->assign('limit', $limit);
$xoopsTpl->assign('current', $current);
$xoopsTpl->assign('bans', $ban_array);
$xoopsTpl->assign('ban_count', count($ban_array));
$xoopsTpl->assign('back_page', $back_page);
$xoopsTpl->assign('next_page', $next_page);
$xoopsTpl->assign('page', $page);
$xoopsTpl->assign('page_array', $page_array);
$xoopsTpl->assign('player_width', $player_width);
$xoopsTpl->assign('reason_width', $reason_width);
