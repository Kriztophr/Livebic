<?php
if ($option == 'get_ngenius') {
	$data['status'] = 400;
	if (!empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
		$token = GetNgeniusToken();
		if (!empty($token->message)) {
			$data['status'] = 400;
	        $data['message'] = $token->message;
		}
		elseif (!empty($token->errors) && !empty($token->errors[0]) && !empty($token->errors[0]->message)) {
			$data['status'] = 400;
	        $data['message'] = $token->errors[0]->message;
		}
		else{
			$amount = (int) secure($_POST['amount']);
			$postData = new StdClass();
		    $postData->action = "SALE";
		    $postData->amount = new StdClass();
		    $postData->amount->currencyCode = "AED";
		    $postData->amount->value = $amount;
		    $postData->merchantAttributes = new \stdClass();
	        $postData->merchantAttributes->redirectUrl = $music->config->site_url ."/endpoints/ngenius/success_ngenius";
	        //$postData->merchantAttributes->redirectUrl = "http://192.168.1.108/deep/endpoints/ngenius/success_ngenius";
		    $order = CreateNgeniusOrder($token->access_token,$postData);
		    if (!empty($order->message)) {
    			$data['status'] = 400;
		        $data['message'] = $order->message;
    		}
    		elseif (!empty($order->errors) && !empty($order->errors[0]) && !empty($order->errors[0]->message)) {
    			$data['status'] = 400;
		        $data['message'] = $order->errors[0]->message;
    		}
    		else{
    			$db->where('id',$music->user->id)->update(T_USERS,array('ngenius_ref' => $order->reference));
    			$data['status'] = 200;
		        $data['url'] = $order->_links->payment->href;
    		}
		}
	}
	else{
		$data = array(
            'status' => 400,
            'message' => lang("empty_amount")
        );
	}
}
if ($option == 'success_ngenius') {
	if (!empty($_GET['ref'])) {
		$user = $db->objectBuilder()->where('ngenius_ref',secure($_GET['ref']))->getOne(T_USERS);
		if (!empty($user)) {
			$token = GetNgeniusToken();
    		if (!empty($token->message)) {
    			header("Location: $site_url/payment-error?reason=".$token->message);
                exit();
    		}
    		elseif (!empty($token->errors) && !empty($token->errors[0]) && !empty($token->errors[0]->message)) {
    			header("Location: $site_url/payment-error?reason=".$token->errors[0]->message);
                exit();
    		}
    		else{
    			$order = NgeniusCheckOrder($token->access_token,$user->ngenius_ref);
    			if (!empty($order->message)) {
    				header("Location: $site_url/payment-error?reason=".$order->message);
                	exit();
	    		}
	    		elseif (!empty($order->errors) && !empty($order->errors[0]) && !empty($order->errors[0]->message)) {
	    			header("Location: $site_url/payment-error?reason=".$order->errors[0]->message);
                	exit();
	    		}
	    		else{
	    			if ($order->_embedded->payment[0]->state == "CAPTURED") {
						$amount = secure($order->amount->value);
						$updateUser = $db
			                    ->where("id", $user->id)
			                    ->update(T_USERS, ["wallet" => $db->inc($amount),
			                                       'ngenius_ref' => '']);
			            CreatePayment([
			                "user_id" => $user->id,
			                "amount" => $amount,
			                "type" => "WALLET",
			                "pro_plan" => 0,
			                "info" => "Replenish My Balance",
			                "via" => "Ngenius",
			            ]);
	    			}
	    		}
    		}
		}
	}
	header("Location: $site_url/ads");
    exit();
}