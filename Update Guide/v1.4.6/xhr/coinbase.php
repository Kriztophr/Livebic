<?php
if ($option == 'get_coinbase') {
	$data['status'] = 400;
	if (!empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
		$amount = (int) secure($_POST['amount']);
		try {
            $coinbase_hash = rand(1111,9999).rand(11111,99999);
            $redirect_url = $music->config->site_url ."/endpoints/coinbase/success_coinbase?coinbase_hash=".$coinbase_hash;
            $cancel_url = $music->config->site_url ."/endpoints/coinbase/cancel_coinbase?coinbase_hash=".$coinbase_hash; 
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.commerce.coinbase.com/charges');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $postdata =  array('name' => 'Top Up Wallet','description' => 'Top Up Wallet','pricing_type' => 'fixed_price','local_price' => array('amount' => $amount , 'currency' => $music->config->currency), 'metadata' => array('coinbase_hash' => $coinbase_hash,'amount' => $amount),"redirect_url" => $redirect_url,'cancel_url' => $cancel_url);


            curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postdata));

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Cc-Api-Key: '.$music->config->coinbase_key;
            $headers[] = 'X-Cc-Version: 2018-03-22';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $data = array(
                    'status' => 400,
                    'message' => curl_error($ch)
                );
            }
            curl_close($ch);

            $result = json_decode($result,true);
            if (!empty($result) && !empty($result['data']) && !empty($result['data']['hosted_url']) && !empty($result['data']['id']) && !empty($result['data']['code'])) {
                $db->where('id', $music->user->id)->update(T_USERS, array('coinbase_hash' => $coinbase_hash,
                                                                          'coinbase_code' => $result['data']['code']));
                $data['status'] = 200;
                $data['url'] = $result['data']['hosted_url'];
            }
        }
        catch (Exception $e) {
            $data = array(
                'status' => 400,
                'message' => $e->getMessage()
            );
        }

	}
	else{
		$data = array(
            'status' => 400,
            'message' => lang("empty_amount")
        );
	}
}
if ($option == 'success_coinbase') {
	if (!empty($_GET['coinbase_hash']) && is_numeric($_GET['coinbase_hash'])) {
	    $coinbase_hash = secure($_GET['coinbase_hash']);
	    $user           = $db->objectBuilder()->where('coinbase_hash',$coinbase_hash)->getOne(T_USERS);

	    if (!empty($user)) {
	    	$music->user = $user;
	    	$ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.commerce.coinbase.com/charges/'.$user->coinbase_code);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Cc-Api-Key: '.$music->config->coinbase_key;
            $headers[] = 'X-Cc-Version: 2018-03-22';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
            	header("Location: $site_url/payment-error?reason=".curl_error($ch));
                exit();
            }
            curl_close($ch);
            $result = json_decode($result,true);

	    	
            if (!empty($result) && !empty($result['data']) && !empty($result['data']['pricing']) && !empty($result['data']['pricing']['local']) && !empty($result['data']['pricing']['local']['amount']) && !empty($result['data']['payments']) && !empty($result['data']['payments'][0]['status']) && $result['data']['payments'][0]['status'] == 'CONFIRMED') {
            	$amount = (int)$result['data']['pricing']['local']['amount'];
            	$updateUser = $db
	                    ->where("id", $user->id)
	                    ->update(T_USERS, ["wallet" => $db->inc($amount),
	                                       'coinbase_hash' => '',
	                                       'coinbase_code' => '']);
	            CreatePayment([
	                "user_id" => $user->id,
	                "amount" => $amount,
	                "type" => "WALLET",
	                "pro_plan" => 0,
	                "info" => "Replenish My Balance",
	                "via" => "Coinbase",
	            ]);
            }
	    }
	}
	header("Location: $site_url/ads");
    exit();
}
if ($option == 'cancel_coinbase') {
	if (!empty($_GET['coinbase_hash']) && is_numeric($_GET['coinbase_hash'])) {
        $coinbase_hash = secure($_GET['coinbase_hash']);
        $user = $db->where('coinbase_hash',$coinbase_hash)->getOne(T_USERS);
        if (!empty($user)) {
            $db->where('id', $user->id)->update('users', array('coinbase_hash' => '',
                                                               'coinbase_code' => ''));
        }
    }
    header("Location: $site_url/ads");
    exit();
}