#!/usr/bin/perl

#    AMXBans, managing bans for Half-Life modifications
#    Copyright (C) 2003, 2004  Ronald Renes / Niek Albers
#
#		web		: http://www.xs4all.nl/~yomama/amxbans/
#		mail	: yomama@xs4all.nl
#		ICQ		: 104115504
#    
#		This file is part of AMXBans.
#
#    AMXBans is free software; you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation; either version 2 of the License, or
#    (at your option) any later version.
#
#    AMXBans is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with AMXBans; if not, write to the Free Software
#    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#

use strict;
use DBI;

my $dsn = {
    host     => '127.0.0.1',
    username => '',
    password => '',
    database => 'xoops',
    dbprefix => 'xoops_',
};

main();

sub main {
    my $dbh = connect_db($dsn);

    my $sth = $dbh->prepare(
        ' SELECT *
            FROM '. $dsn->{dbprefix} .'amx_bans
            WHERE ban_created + ban_length*60 < UNIX_TIMESTAMP()
            AND ban_length != 0 '
    );

    my $rv = $sth->execute();

    while ( my $r = $sth->fetchrow_hashref ) {
        my $sql = 'INSERT INTO '. $dsn->{dbprefix} .'amx_banhistory 
            (bhid, 
            player_ip, player_id,  player_nick, 
            admin_ip,  admin_id,   admin_nick, 
            ban_type,  ban_reason, ban_created, ban_length, 
            server_name, 
            unban_created, unban_reason, unban_admin_nick)'. q/ 
            VALUES ('',?,?,?,?,?,?,?,?,?,?,?,?,'Bantime expired','amxbans')/;

        my $sth = $dbh->prepare($sql);
        my $rv  = $sth->execute(
            $r->{player_ip},  $r->{player_id},   $r->{player_nick},
            $r->{admin_ip},   $r->{admin_id},    $r->{admin_nick},
            $r->{ban_type},   $r->{ban_reason},  $r->{ban_created},
            $r->{ban_length}, $r->{server_name}, time()
          )
          or die $DBI::errstr;

        $sth = $dbh->prepare('DELETE FROM '. $dsn->{dbprefix} .'amx_bans WHERE bid = ?');
        $sth->execute( $r->{bid} );

    }

}

sub connect_db {
    my ($dsn) = @_;

    my @dsnlist = (
        'dbi:' . 'mysql' . ':dbname=' . $dsn->{database} . ';host=' .
          $dsn->{host},
        $dsn->{username}
    );
    return DBI->connect( @dsnlist, $dsn->{password} ) or die $DBI::errstr;

}
