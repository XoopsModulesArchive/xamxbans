/* 

    AMXBans, managing bans for Half-Life modifications
    Copyright (C) 2003, 2004  Ronald Renes / Jeroen de Rover

		web		: http://www.xs4all.nl/~yomama/amxbans/
		mail	: yomama@xs4all.nl
		ICQ		: 104115504
		IRC		: #xs4all (Quakenet, nickname YoMama)
    
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

		Check out readme.html for more information

		Current version: v2.1

*/

#include <amxmodx>
#include <mysql>

// 16k * 4 = 64k stack size
#pragma dynamic 16384

new mysql = 0
new merror[32]
new mhost[64]
new muser[32]
new mpass[32]
new mdb[32]
new dbprefix[32]
new amxbans_version[10] = "amxx_3.0"
new ban_motd[4096] = "You have been banned from this server. Reason: %s. Length: %s minutes. Your steamid: %s."
new Float:kick_delay=10.0

#define MAX_SERVERS	64
#define bufgrootte	2048

new server_allow[MAX_SERVERS][50]
new server_deny[MAX_SERVERS][50]
new server_allow_count = 0
new server_deny_count = 0

public amx_allowserver() {
	if (server_allow_count >= MAX_SERVERS) {
		server_print("Server allow limit reached!")
		return PLUGIN_HANDLED
	}

	read_args(server_allow[server_allow_count],49)
	server_allow_count++
	return PLUGIN_HANDLED
}

public amx_denyserver() {
	if (server_deny_count >= MAX_SERVERS) {
		server_print("Server deny limit reached!")
		return PLUGIN_HANDLED
	}

	//read_argv(1,server_deny[server_deny_count],49)
	read_args(server_deny[server_deny_count],49)
	server_deny_count++
	return PLUGIN_HANDLED
}

public amx_listallow(id) {
	if (server_allow_count>0) {
		for (new i=0; i<server_allow_count; i++)
		server_print("ALLOWED: %s", server_allow[i])
	} else {
		server_print("ALL servers allowed (minus servers denied)")
	}
}

public amx_listdeny(id) {
	if (server_deny_count>0) {
		for (new i=0; i<server_deny_count; i++)
		server_print("DENIED: %s", server_deny[i])
	} else {
		server_print("NO servers are denied (unless server_allow_count > 0)")
	}
}

public sql_init() {
	get_cvar_string("amx_mysql_host",mhost,64)
	get_cvar_string("amx_mysql_user",muser,32)
	get_cvar_string("amx_mysql_pass",mpass,32)
	get_cvar_string("amx_mysql_db",mdb,32)
	get_cvar_string("xoops_dbprefix",dbprefix,31)


	mysql = mysql_connect(mhost,muser,mpass,mdb,merror,32)

	if(mysql < 1) {
		server_print("[AMXX] MySQL error : could not connect")
		server_print("[AMXX] %s",merror)
	}

	return PLUGIN_CONTINUE
}


public sql_ban(adminid,player,ban_type[],player_steamid[], player_ip[], player_nick[], admin_ip[], admin_steamid[], admin_nick[], ban_reason[], ban_length[]) 
{
	new query[1024]

	if (equal(ban_type, "S"))
	{
		format(query,1024,"SELECT player_id FROM %samx_bans WHERE player_id='%s'", dbprefix, player_steamid)
	}
	else
	{
		format(query,1024,"SELECT player_ip FROM %samx_bans WHERE player_ip='%s'", dbprefix, player_ip)
	}
	mysql_query(mysql,query)

	mysql_error(mysql,merror,32)

	if (merror[0]) 
	{
		client_print(adminid,print_console,"[AMXX] MYSQL errorin sql_ban_steamid.checksteamid: %s",merror)
		server_print("[AMXX] MYSQL error: %s",merror)
		return PLUGIN_HANDLED
	}

	if (mysql_nextrow(mysql)>0) 
	{
		if (strlen(player_ip)>0)
		{
			format(query,1024,"UPDATE %samx_bans SET player_ip='%s' WHERE player_id='%s'", dbprefix, player_ip, player_steamid)
			mysql_query(mysql,query)
			mysql_error(mysql,merror,32)

			if (merror[0]) 
			{
				client_print(adminid,print_console,"[AMXX] MYSQL error in sql_ban_steamid.updateip: %s",merror)
				server_print("[AMXX] MYSQL error: %s",merror)
			}
			else
			{
				server_print("[AMXX] SteamID %s has already been banned, updated IP", player_steamid)
				client_print(adminid,print_console,"[AMXX] SteamID %s has already been banned, updated IP.", player_steamid)
			}
		}
		else
		{
			server_print("[AMXX] SteamID %s has already been banned", player_steamid)
			client_print(adminid,print_console,"[AMXX] SteamID %s has already been banned", player_steamid)
		}
		return PLUGIN_HANDLED
	}

	new ip[32]
	get_cvar_string("ip", ip, 32)
	new port[10]
	get_cvar_string("port", port, 10)
	new server_name[100]
	get_cvar_string("hostname",server_name,100)

	new ban_created = getsystemtime()

	format(query,1024,"INSERT into %samx_bans (player_id,player_ip,player_nick,admin_ip,admin_id,admin_nick,ban_type,ban_reason,ban_created,ban_length,server_name,server_ip) values('%s','%s','%s','%s','%s','%s','%s','%s','%i','%s','%s','%s:%s')", dbprefix,player_steamid, player_ip, player_nick, admin_ip, admin_steamid, admin_nick, ban_type,ban_reason, ban_created, ban_length,server_name, ip,port)
	mysql_query(mysql,query)
	mysql_error(mysql,merror,32)
	if (merror[0]) 
	{
		client_print(adminid,print_console,"[AMXX] MYSQL Error in sql_ban_steamid.insert: %s", merror)
		return PLUGIN_HANDLED
	}

	new bid[20]
	format(query,1024,"SELECT bid FROM %samx_bans WHERE player_id='%s' AND player_ip='%s' AND ban_type='%s'", dbprefix, player_steamid, player_ip, ban_type)
	mysql_query(mysql,query)
	mysql_error(mysql,merror,32)
	if (merror[0]) 
	{
		client_print(adminid,print_console,"[AMXX] MYSQL errorin sql_ban_steamid.checksteamid: %s",merror)
		server_print("[AMXX] MYSQL error: %s",merror)
		return PLUGIN_HANDLED
	}
	if (mysql_nextrow(mysql)>0) 
	{
		mysql_getfield(mysql,1,bid,20)
		
	}
	else
		copy(bid,20, "0");
	
	if (player) 
	{
		server_print("[AMXX] SteamID %s and player IP %s banned succesfully (bantype %s) - Player kicked", player_steamid, player_ip, ban_type)
		if (equal(ban_type, "S"))
		{
			client_print(adminid,print_console,"[AMXX] SteamID %s banned succesfully (player IP logged) - Player will be kicked", player_steamid)
		}
		else
		{
			client_print(adminid,print_console,"[AMXX] SteamID %s and player IP banned succesfully - Player will be kicked", player_steamid)
		}
		new id_str[3]
		num_to_str(player,id_str,3)

		client_print(player,print_console,"[AMXX] ===============================================")
		client_print(player,print_console,"[AMXX] You have been banned from this server.")
		client_print(player,print_console,"[AMXX] Reason: %s.", ban_reason)
		client_print(player,print_console,"[AMXX] Time in min: %s.", ban_length)
		client_print(player,print_console,"[AMXX] Your SteamID: %s.", player_steamid)
		client_print(player,print_console,"[AMXX] Your IP: %s.", player_ip)
		client_print(player,print_console,"[AMXX] ===============================================")

		new msg[4096]
		format(msg, 4096, ban_motd, bid)
		show_motd(player, msg, "Banned")
		set_task(kick_delay,"delayed_kick",1,id_str,3)
	} 
	else 
	{
		server_print("[AMXX] SteamID %s banned succesfully", player_steamid)
		client_print(adminid,print_console,"[AMXX] SteamID %s banned succesfully", player_steamid)
	}

	return PLUGIN_CONTINUE
}


public amx_ban(id) 
{
	if (!(get_user_flags(id)&ADMIN_KICK)) 
	{
		server_print("[AMXX] You have no access to that command")
		client_print(id,print_console,"[AMXX] You have no access to that command")
		return PLUGIN_HANDLED
	}

	if (read_argc() < 4) 
	{
		server_print("[AMXX] amx_ban < time in minutes > < steamid or name > < reason >")
		client_print(id,print_console,"[AMXX] amx_ban < time in minutes > < steamid or name > < reason >")
		return PLUGIN_HANDLED
	}

	new player_ip[50], player_steamid[50], player_nick[100], admin_ip[100], admin_steamid[50], admin_nick[100], ban_reason[255], ban_length[50]

	get_user_ip(id,admin_ip,100,1)
	get_user_authid(id, admin_steamid, 50)
	get_user_name(id,admin_nick,100)

	new steamidorusername[50]

	new text[128]
	read_args(text,128)
	parse(text,ban_length,50,steamidorusername,50)
	new length1 = strlen(ban_length)
	new length2 = strlen(steamidorusername)
	new length = length1 + length2
	length+=2
	new reason[128]
	read_args(reason,128)
	format(ban_reason, 255, "%s", reason[length]);

	new player = find_player("c",steamidorusername)
	if (!player)
		player = find_player("bl",steamidorusername)

	if (player) 
	{
		if (get_user_flags(player)&ADMIN_IMMUNITY) {
			server_print("[AMXX] The player has immunity")
			client_print(id,print_console,"[AMXX] The player has immunity")
			return PLUGIN_HANDLED
		} else if (is_user_bot(player)) {
			server_print("[AMXX] Bot cannot be banned")
			client_print(id,print_console,"[AMXX] Bot cannot be banned")
			return PLUGIN_HANDLED
		} else if (is_user_hltv(player)) {
			server_print("[AMXX] HLTV proxy cannot be banned")
			client_print(id,print_console,"[AMXX] HLTV proxy cannot be banned")
			return PLUGIN_HANDLED
		}

		get_user_authid(player, player_steamid, 50)
		get_user_name(player, player_nick, 100)
		get_user_ip(player, player_ip, 50, 1)
	} 
	else 
	{
		format(player_steamid, 50, "%s", steamidorusername)
		format(player_nick, 100, "unknown_%s", player_steamid)
		format(player_ip, 50, "")
	}
	
	server_print("[DEBUG] Banning player %s , ip %s (S)", player_steamid, player_ip);
	sql_ban(id,player,"S",player_steamid, player_ip, player_nick, admin_ip, admin_steamid, admin_nick, ban_reason, ban_length)


	return PLUGIN_HANDLED
}

public amx_banip(id) 
{
	if (!(get_user_flags(id)&ADMIN_KICK)) 
	{
		server_print("[AMXX] You have no access to that command")
		client_print(id,print_console,"[AMXX] You have no access to that command")
		return PLUGIN_HANDLED
	}

	if (read_argc() < 4) 
	{
		server_print("[AMXX] amx_banplayer < time in minutes > < steamid or name> < reason >")
		client_print(id,print_console,"[AMXX] amx_banplayer < time in minutes > < steamid, name or ip> < reason >")
		return PLUGIN_HANDLED
	}

	new player_ip[50],player_steamid[50], player_nick[100], admin_ip[100], admin_steamid[50], admin_nick[100], ban_reason[255], ban_length[50]

	get_user_ip(id,admin_ip,100,1)
	get_user_authid(id, admin_steamid, 50)
	get_user_name(id,admin_nick,100)

	new steamidorusername[50]

	new text[128]
	read_args(text,128)
	parse(text,ban_length,50,steamidorusername,50)
	new length1 = strlen(ban_length)
	new length2 = strlen(steamidorusername)
	new length = length1 + length2
	length+=2
	new reason[128]
	read_args(reason,128)
	format(ban_reason, 255, "%s", reason[length]);

	new player = find_player("c",steamidorusername)
	if (!player)
		player = find_player("bl",steamidorusername)

	if (player) 
	{
		if (get_user_flags(player)&ADMIN_IMMUNITY) {
			server_print("[AMXX] The player has immunity")
			client_print(id,print_console,"[AMXX] The player has immunity")
			return PLUGIN_HANDLED
		} 
		else if (is_user_bot(player)) 
		{
			server_print("[AMXX] Bot cannot be banned")
			client_print(id,print_console,"[AMXX] Bot cannot be banned")
			return PLUGIN_HANDLED
		} 
		else if (is_user_hltv(player)) 
		{
			server_print("[AMXX] HLTV proxy cannot be banned")
			client_print(id,print_console,"[AMXX] HLTV proxy cannot be banned")
			return PLUGIN_HANDLED
		}

		get_user_authid(player, player_steamid, 50)
		get_user_name(player, player_nick, 100)
		get_user_ip(player, player_ip, 50, 1)

	} 
	else 
	{
		format(player_steamid, 50, "%s", steamidorusername)
		format(player_nick, 100, "unknown_%s", player_steamid)
		format(player_ip, 50, "");
	}

	server_print("[DEBUG] Banning player %s , ip %s  (SI)", player_steamid, player_ip);
	sql_ban(id,player,"SI",player_steamid, player_ip, player_nick, admin_ip, admin_steamid, admin_nick, ban_reason, ban_length)

	return PLUGIN_HANDLED
}


public amx_find(id) 
{
	if (!(get_user_flags(id)&ADMIN_KICK)) {
		server_print("[AMXX] You have no access to that command")
		client_print(id,print_console,"[AMXX] You have no access to that command")
		return PLUGIN_HANDLED
	}

	if (read_argc() < 2) {
		server_print("[AMXX] amx_find < steamid or name>")
		client_print(id,print_console,"[AMXX] amx_find < steamid or name>")
		return PLUGIN_HANDLED
	}

	mysql_error(mysql,merror,32)

	if (merror[0]) {
		server_print("[AMXX] MYSQL error in amx_find.init: %s",merror)
		client_print(id,print_console,"[AMXX] MYSQL error: %s",merror)
		return PLUGIN_HANDLED
	}

	new steamidorusername[50] , player_steamid[50]
	read_argv(1,steamidorusername,50)

 	new player = find_player("c",steamidorusername) //by steamid

 	if (!player)
		player = find_player("bl",steamidorusername) // by nick

	if (player) {
		if (get_user_flags(player)&ADMIN_IMMUNITY) {
			server_print("[AMXX] The player has immunity")
			client_print(id,print_console,"[AMXX] The player has immunity")
			return PLUGIN_HANDLED
		} else if (is_user_bot(player)) {
			server_print("[AMXX] Player is a bot")
			client_print(id,print_console,"[AMXX] Player is a bot")
			return PLUGIN_HANDLED
		} else if (is_user_hltv(player)) {
			server_print("[AMXX] Player is a HLTV proxy")
			client_print(id,print_console,"[AMXX] Player is a HLTV proxy")
			return PLUGIN_HANDLED
		}

		get_user_authid(player, player_steamid, 50)

	} else {
		new test = str_to_num(steamidorusername)

		if (test == 0) {
			server_print("[AMXX] Illegal steamid (player not found)")
			client_print(id, print_console, "[AMXX] Illegal steamid (player not found)")
			return PLUGIN_HANDLED
		}

		format(player_steamid, 50, "%s", steamidorusername)
	}

	new query[1024]
	format(query,1024,"SELECT bid,ban_created,ban_length,ban_reason,admin_nick,admin_id,player_nick FROM %samx_bans WHERE player_id='%s' order by ban_created desc", dbprefix, player_steamid)
	mysql_query(mysql,query)
	mysql_error(mysql,merror,32)

	if (merror[0]) {
		client_print(id,print_console,"[AMXX] MYSQL Error in amx_find.get_player: %s", merror)
		return PLUGIN_HANDLED
	}

	new bid[20], ban_created[50], ban_length[50], ban_reason[100], admin_nick[100],admin_steamid[50],player_nick[100],remaining[50]
	new ban_created_int, ban_length_int, current_time_int, ban_left
	new res = mysql_nextrow(mysql)

	if (res > 0) 
	{
		client_print(id, print_console, "[AMXX] Found Player (steamid: %s)", player_steamid)
		server_print("[AMXX] Found Player (steamid: %s)", player_steamid)
		while (res>0) {
			mysql_getfield(mysql,1,bid,20)
			mysql_getfield(mysql,2,ban_created,50)
			mysql_getfield(mysql,3,ban_length,50)
			mysql_getfield(mysql,4,ban_reason,100)
			mysql_getfield(mysql,5,admin_nick,50)
			mysql_getfield(mysql,6,admin_steamid,50)
			mysql_getfield(mysql,7,player_nick,50)

			current_time_int = getsystemtime()
			ban_created_int = str_to_num(ban_created)
			ban_length_int = str_to_num(ban_length) * 60 // in secs

			if ((ban_length_int == 0) || (ban_created_int==0)) {
				remaining = "eternity!"
			} else {
				ban_left = (ban_created_int+ban_length_int-current_time_int) / 60

				if (ban_left<0)
					format(remaining,50,"none",ban_left)
				else
					format(remaining,50,"%i minutes",ban_left)
			}

			client_print(id, print_console, "[AMXX] BanId %s; Player %s", bid, player_nick)
			client_print(id, print_console, "[AMXX]     banned by %s[%s] for %s", admin_nick, admin_steamid, ban_reason)
			client_print(id, print_console, "[AMXX]     ban length: %s minutes, remaining: %s", ban_length,remaining)
			client_print(id, print_console, "[AMXX] =================")

			server_print("[AMXX] BanId %s; Player %s", bid, player_nick)
			server_print("[AMXX]     banned by %s[%s] for %s", admin_nick, admin_steamid, ban_reason)
			server_print("[AMXX]     ban length: %s minutes; remaining: %s", ban_length,remaining)
			server_print("[AMXX] =================")
			res = mysql_nextrow(mysql)
		}
	} else {
		client_print(id, print_console, "[AMXX] No ban history for player (steamid: %s)", player_steamid)
		server_print("[AMXX] No ban history for player (steamid: %s)", player_steamid)
	}

	return PLUGIN_HANDLED
}

public client_authorized(id) 
{
	new authid[50],plip[50]
	get_user_authid(id,authid,50)
	get_user_ip(id,plip,50,1)

	new servers_allowed[bufgrootte]
	if (server_allow_count>0) {
		new tmp[1024]
		copy(servers_allowed, bufgrootte, " and (")
		for (new i=0; i<server_allow_count; i++) {
			format(tmp, 1024, "(server_ip='%s')", server_allow[i])
			add(servers_allowed, bufgrootte, tmp)

			if (i<server_allow_count-1) add(servers_allowed, bufgrootte, " or ")
		}

		add(servers_allowed, bufgrootte, ")")
	} else {
		format(servers_allowed, bufgrootte, "")
	}

	new servers_denied[bufgrootte]

	if (server_deny_count>0) {
		new tmp[1024]
		copy(servers_denied, bufgrootte, " and not (")

		for (new i=0; i<server_deny_count; i++) {
			format(tmp, 1024, "(server_ip='%s')", server_deny[i])
			add(servers_denied, bufgrootte, tmp)

			if (i<server_deny_count-1) add(servers_denied, bufgrootte, " or ")
		}

		add(servers_denied, bufgrootte, ")")
	} else {
		format(servers_denied, bufgrootte, "")
	}

	new query[bufgrootte] 
	format(query,bufgrootte,"SELECT bid,ban_created,ban_length,ban_reason,admin_nick,admin_id,player_nick,server_name,server_ip,ban_type FROM %samx_bans WHERE ((player_id='%s') and ((ban_type='S') or (ban_type='SI'))) or ((player_ip='%s') and (ban_type='SI')) %s %s", dbprefix, authid,plip,servers_allowed,servers_denied) 
	mysql_query(mysql,query) 


	mysql_error(mysql,merror,32)
	if (merror[0]) 
	{
		server_print("[AMXX] MYSQL error in connect.findsteamidorip: %s",merror)
		return PLUGIN_HANDLED
	}

	if(mysql_nextrow(mysql)>0) 
	{
		new bid[20], ban_created[50], ban_length[50], ban_reason[100], admin_nick[100],admin_steamid[50],player_nick[100],server_name[100],server_ip[100],bantype[10]
		mysql_getfield(mysql,1,bid,20)
		mysql_getfield(mysql,2,ban_created,50)
		mysql_getfield(mysql,3,ban_length,50)
		mysql_getfield(mysql,4,ban_reason,100)
		mysql_getfield(mysql,5,admin_nick,50)
		mysql_getfield(mysql,6,admin_steamid,50)
		mysql_getfield(mysql,7,player_nick,50)
		mysql_getfield(mysql,8,server_name,100)
		mysql_getfield(mysql,9,server_ip,100)
		mysql_getfield(mysql,10,bantype,10)

		new current_time_int = getsystemtime()
		new ban_created_int = str_to_num(ban_created)
		new ban_length_int = str_to_num(ban_length) * 60 // in secs

		if ((ban_length_int == 0) || (ban_created_int==0) || (ban_created_int+ban_length_int > current_time_int)) 
		{
			new time_msg[32]

			if ((ban_length_int == 0) || (ban_created_int==0)) 
			{
				time_msg = "Permanent"
			} 
			else 
			{
				new ban_left = (ban_created_int+ban_length_int-current_time_int) / 60
				format(time_msg,32,"%i minutes",ban_left)
			}

			client_cmd(id, "echo ^"[AMXX] You have been banned by admin %s from this server.^"", admin_nick)

			if (ban_length_int==0) {
				client_cmd(id, "echo ^"[AMXX] You have banned permanently. ^"")
			} else {
				client_cmd(id, "echo ^"[AMXX] Remaining %s. ^"", time_msg)
			}

			client_cmd(id, "echo ^"[AMXX] Reason %s. ^"", ban_reason)
			client_cmd(id, "echo ^"[AMXX] Your nick: %s. Your steamid: %s. ^"", player_nick, authid)
			client_cmd(id, "echo ^"[AMXX] Your IP is %s. ^"", plip)

			new id_str[3]
			num_to_str(id,id_str,3)
			set_task(1.0,"delayed_kick",0,id_str,3)
			return PLUGIN_HANDLED
		} 
		else 
		{
			client_cmd(id, "echo ^"[AMXX] You were been banned at least once, dont let it happen again!.^"")

			new unban_created = getsystemtime()

			format(query,1024,"INSERT INTO %samx_banhistory (player_id,player_ip,player_nick,admin_id,admin_nick,ban_type,ban_reason,ban_created,ban_length,server_name,unban_created,server_ip) values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%i','%s')", dbprefix, authid,plip,player_nick,admin_steamid,admin_nick,bantype,ban_reason,ban_created,ban_length,server_name,unban_created,server_ip)
			server_print("query %s ", query)
			mysql_query(mysql,query)
			mysql_error(mysql,merror,32)

			if (merror[0]) 
			{
				server_print("[AMXX] MYSQL error in connect.copy")
				server_print("Error %s",merror)
				return PLUGIN_HANDLED
			}

			format(query,1024,"DELETE FROM %samx_bans WHERE bid=%s", dbprefix, bid)
			server_print("query %s ", query)
			mysql_query(mysql,query)
			mysql_error(mysql,merror,32)

			if (merror[0]) 
			{
				server_print("[AMXX] MYSQL error in connect.delete")
				server_print("Error %s",merror)
				return PLUGIN_HANDLED
			}

			return PLUGIN_HANDLED
		}
	}
	return PLUGIN_CONTINUE
}

public delayed_kick(id_str[]) 
{
	new player_id = str_to_num(id_str)
	new userid = get_user_userid(player_id)
	server_cmd("kick #%d",userid)
	return PLUGIN_CONTINUE
}

public amx_motd(id) 
{
	show_motd(id,"motd.html","Message of the Day") 
	return PLUGIN_CONTINUE 
}

public cmdLst(id,level,cid)
{
	new players[32], inum, authid[32],name[32], ip[50]
	get_players(players,inum)
	console_print(id,"^n%s %s %s", "nick","authid","ip")

	for(new a = 0; a < inum; ++a) {
		get_user_ip(players[a],ip,49,0)
		get_user_authid(players[a],authid,31)
		get_user_name(players[a],name,31)
		console_print(id,"#WM#%s#WMW#%s#WMW#%s",name,authid,ip)
	}

	return PLUGIN_HANDLED
}

public plugin_init() 
{
	register_plugin("AMXBans","2.0","LuX / YoMama")
	register_clcmd("amx_ban","amx_ban",ADMIN_KICK,"amx_ban <time in minutes> <steamid or name> <reason>")
	register_srvcmd("amx_ban","amx_ban",-1,"amx_ban <time in minutes> <steamid or name> <reason>")
	register_clcmd("amx_banip","amx_banip",ADMIN_KICK,"amx_banip <time in minutes> <ip,steamid or name> <reason>")
	register_srvcmd("amx_banip","amx_banip",-1,"amx_banip <time in minutes> <ip,steamid or name> <reason>")
	register_clcmd("amx_find","amx_find",ADMIN_KICK,"amx_find <steamid or name>")
	register_srvcmd("amx_find","amx_find",-1,"amx_find <steamid or name>")
	register_clcmd("amx_motd","amx_motd",0,"amx_motd")
	register_concmd("amx_list","cmdLst",0,"- displays playerinfo")

	register_srvcmd("amx_allowserver","amx_allowserver")
	register_srvcmd("amx_denyserver","amx_denyserver")
	register_srvcmd("amx_listallow","amx_listallow",-1,"amx_listallow")
	register_srvcmd("amx_listdeny","amx_listdeny",-1,"amx_listdeny")

	register_cvar("amxbans_version", amxbans_version)

	set_task(5.0,"init_function")

	return PLUGIN_CONTINUE
}

public init_function() 
{
	sql_init()
	banmod_online()
}

public banmod_online() 
{
	new timestamp = getsystemtime()
	new ip[32]
	get_cvar_string("ip", ip, 32)
	new port[10]
	get_cvar_string("port", port, 10)

	new servername[200]
	get_cvar_string("hostname",servername,100)

	new query[1024]



	format(query, 1024, "select timestamp,hostname,address,gametype,rcon,amxban_version,amxban_motd,motd_delay from %samx_serverinfo where address = '%s:%s'", dbprefix, ip,port)
	mysql_query(mysql,query)

	mysql_error(mysql,merror,32)

	if (merror[0]) 
	{
		server_print("[AMXX] MYSQL error: %s",merror)
		return PLUGIN_HANDLED
	}

	if (mysql_nextrow(mysql)==0) 
	{
		format(query, 1024, "insert into %samx_serverinfo (hostname, address, timestamp, amxban_version) values ('%s','%s:%s','%i', '%s')", dbprefix, servername, ip,port,timestamp, amxbans_version)
		mysql_query(mysql,query)

		mysql_error(mysql,merror,32)

		if (merror[0]) {
			server_print("[AMXX] MYSQL error: %s",merror)
			return PLUGIN_HANDLED
		}

		server_print("[AMXX] AMXBans module is online")
		log_message("[AMXX] AMXBans module is online")
	} 
	else 
	{
		new ban_motd_tmp[4096]
		mysql_getfield(mysql, 7, ban_motd_tmp, 4096)
		
		
		if (strlen(ban_motd_tmp)) 
		{
			copy(ban_motd,4096,ban_motd_tmp)
			
			server_print("[AMXX] Using Motd: %s", ban_motd);
		}
		
		
		new kick_delay_str[10] 
		mysql_getfield(mysql, 8, kick_delay_str, 10)
		if (floatstr(kick_delay_str)>1.0) 
			kick_delay=floatstr(kick_delay_str)
		else 
			kick_delay=12.0
		
		format(query, 1024, "update %samx_serverinfo set timestamp='%i',hostname='%s',amxban_version='%s' where address = '%s:%s'", dbprefix, timestamp, servername,amxbans_version, ip,port)
		mysql_query(mysql,query)

		mysql_error(mysql,merror,32)

		if (merror[0]) {
			server_print("[AMXX] MYSQL error: %s",merror)
			return PLUGIN_HANDLED
		}
	}

	return PLUGIN_CONTINUE
}


//------------------------------------------------------------
//  Special subroutines (2002-10-21, XAD2000)
//------------------------------------------------------------

stock getsystemtime() {

   return get_systime(0);
}
