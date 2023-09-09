<?php
if(!IS_LOGGED) {
	header("Location: $site_url/404");
    exit;
}
if ($music->config->event_system != 1 || $music->user->artist == 0) {
	header("Location: $site_url/404");
    exit;
}
$music->have_events = $db->where('user_id',$music->user->id)->getValue(T_EVENTS,'COUNT(*)');
$music->site_title = lang("Create Event") . ' | ' . $music->config->title;
$music->site_description = $music->config->description;
$music->site_pagename = "create-event";
$music->site_content     = loadPage('create-event/content');