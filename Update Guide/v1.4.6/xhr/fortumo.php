<?php
if ($option == 'get_fortumo') {
	$hash = rand(11111,55555).rand(11111,55555);
    $db->where('id',$music->user->id)->update(T_USERS,array('fortumo_hash' => $hash));
    $data['status'] = 200;
	$data['url'] = 'https://pay.fortumo.com/mobile_payments/'.$music->config->fortumo_service_id.'?cuid='.$hash;
}
if ($option == 'success_fortumo') {
	if (!empty($_GET) && !empty($_GET['amount']) && !empty($_GET['status']) && $_GET['status'] == 'completed' && !empty($_GET['cuid']) && !empty($_GET['price'])) {
        $fortumo_hash = secure($_GET['cuid']);
        $amount = (int) secure($_GET['price']);
        $user = $db->objectBuilder()->where('fortumo_hash',$fortumo_hash)->getOne(T_USERS);
        if (!empty($user)) {
        	$music->user = $user;
			$updateUser = $db
                    ->where("id", $music->user->id)
                    ->update(T_USERS, ["wallet" => $db->inc($amount),
		                               'fortumo_hash' => '']);
            CreatePayment([
                "user_id" => $music->user->id,
                "amount" => $amount,
                "type" => "WALLET",
                "pro_plan" => 0,
                "info" => "Replenish My Balance",
                "via" => "fortumo",
            ]);
        }
    }
    header("Location: $site_url/ads");
    exit();
}