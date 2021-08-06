<?php 
/* Handles all functions related to the WP Live Chat Support API */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*
 * Accepts a chat within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * - Agent ID 
*/
function wplc_api_accept_chat(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){ 
				if(isset($request['chat_id'])){
					if(isset($request['agent_id'])){

						$cid = wplc_return_chat_id_by_rel($request['chat_id']);
						if(wplc_change_chat_status($cid, 3, intval($request['agent_id']))){
							
							
							do_action("wplc_hook_update_agent_id", sanitize_text_field($request['cid']) ,intval($request['agent_id']));

							$return_array['response'] = "Chat accepted successfully";
							$return_array['code'] = "200";
							$return_array['data'] = array("chat_id" => intval($request['chat_id']),
														  "agent_id" => intval($request['agent_id']));
						} else {
							$return_array['response'] = "Status could not be changed";
							$return_array['code'] = "404";
						}
				 	} else {
						$return_array['response'] = "No 'agent_id' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
													      "chat_id"   => "Chat ID",
													      "agent_id"   => "Agent ID");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      "chat_id"   => "Chat ID",
												      "agent_id"   => "Agent ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      "chat_id"   => "Chat ID",
										      "agent_id"   => "Agent ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										  "chat_id"   => "Chat ID",
										  "agent_id"   => "Agent ID");
	}
	
	return $return_array;
}

/*
 * Ends a chat within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * - Agent ID 
*/
function wplc_api_end_chat(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					if(isset($request['agent_id'])){

						$cid = $request['chat_id'];
						if( ! filter_var($request['chat_id'], FILTER_VALIDATE_INT) ) {
					        /*  We need to identify if this CID is a node CID, and if so, return the WP CID */
					        $cid = wplc_return_chat_id_by_rel($cid);
					    }

						if(wplc_change_chat_status($cid, 1, intval($request['agent_id']))){

							do_action('wplc_send_transcript_hook', $cid);

							$return_array['response'] = "Chat ended successfully";
							$return_array['code'] = "200";
							$return_array['data'] = array("chat_id" => $cid,
														  "agent_id" => intval($request['agent_id']));
						} else {
							$return_array['response'] = "Status could not be changed";
							$return_array['code'] = "404";
						}
				 	} else {
						$return_array['response'] = "No 'agent_id' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
													      "chat_id"   => "Chat ID",
													      "agent_id"   => "Agent ID");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      "chat_id"   => "Chat ID",
												      "agent_id"   => "Agent ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      "chat_id"   => "Chat ID",
										      "agent_id"   => "Agent ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										  "chat_id"   => "Chat ID",
										  "agent_id"   => "Agent ID");
	}
	
	return $return_array;
}


/*
 * Edit a message in DB
 * Required GET/POST variables:
 * - Token 
 * - Chat ID (Rel)
 * - Message
 * - Message ID (Rel)
*/
function wplc_edit_message(WP_REST_Request $request){
    $return_array = array();
    if(isset($request)){
        if(isset($request['server_token'])){
            $check_token = get_option('wplc_api_secret_token');
            if($check_token !== false && $request['server_token'] === $check_token){
                if(isset($request['chat_id'])){
                    if(isset($request['message'])){
                        if(isset($request['msg_id'])){
                            global $wpdb;
                            global $wplc_tblname_msgs;

                            $chat_id = sanitize_text_field($request['chat_id']);
                            $message = sanitize_text_field($request['message']);
                            $msg_id = sanitize_text_field($request['msg_id']); //We assume this is rel

                            if( ! filter_var($chat_id, FILTER_VALIDATE_INT) ) {
                                /*  We need to identify if this CID is a node CID, and if so, return the WP CID */
                                $chat_id = wplc_return_chat_id_by_rel($chat_id);
                            }

                            $message = apply_filters("wplc_filter_message_control", $message);


                            if(!empty($chat_id) && !empty($message) && !empty($msg_id)){
                                $wpdb->update( 
                                    $wplc_tblname_msgs, 
                                    array( 
                                        'msg' => $message
                                    ), 
                                    array(
                                        'chat_sess_id' => $chat_id,
                                        'rel' => $msg_id
                                    )
                                ); 

                                $return_array['response'] = "Success";
                                $return_array['code'] = "200";

                            } else {
                                $return_array['response'] = "One or more value not set";
                                $return_array['code'] = "401";
                                $return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
                                                                        "chat_id"   => "Chat ID",
                                                                        "message" => "Message",
                                                                        "msg_id" => "Your Message ID");
                            }


                        } else {
                            $return_array['response'] = "No 'msg_id' found";
                            $return_array['code'] = "401";
                            $return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
                                                                    "chat_id"   => "Chat ID",
                                                                    "message" => "Message",
                                                                    "msg_id" => "Your Message ID");
                        }
                    } else {
                        $return_array['response'] = "No 'message' found";
                        $return_array['code'] = "401";
                        $return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
                                                                    "chat_id"   => "Chat ID",
                                                                    "message" => "Message",
                                                                    "msg_id" => "Your Message ID");
                    }
                 } else {
                    $return_array['response'] = "No 'chat_id' found";
                    $return_array['code'] = "401";
                    $return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
                                                                    "chat_id"   => "Chat ID",
                                                                    "message" => "Message",
                                                                    "msg_id" => "Your Message ID");
                }
             } else {
                $return_array['response'] = "Secret server_token is invalid";
                $return_array['code'] = "401";
            }
        }else{
            $return_array['response'] = "No secret 'server_token' found";
            $return_array['code'] = "401";
            $return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
                                                                    "chat_id"   => "Chat ID",
                                                                    "message" => "Message",
                                                                    "msg_id" => "Your Message ID");
        }
    }else{
        $return_array['response'] = "No request data found";
        $return_array['code'] = "400";
        $return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
                                                                    "chat_id"   => "Chat ID",
                                                                    "message" => "Message",
                                                                    "msg_id" => "Your Message ID");
    }
    
    return $return_array;
}


/*
 * Send a message to a chat within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * - Message
*/
function wplc_api_send_message(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['server_token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['server_token'] === $check_token){
				if(isset($request['chat_id'])){
					if(isset($request['message'])){
						if(isset($request['relay_action'])){

								$chat_id = sanitize_text_field($request['chat_id']);
								$message = $request['message'];
								$action = $request['relay_action'];

								if (!empty($request['msg_id'])) {
									$other = new stdClass();
									$other->msgID = $request['msg_id'];
								} else {
									$other = false;
								}

								if($action == "wplc_user_send_msg"){
									$message = sanitize_text_field($message);

									wplc_record_chat_msg("1", $chat_id, $message, false, false, $other); 
									wplc_update_active_timestamp($chat_id);

					                $return_array['response'] = "Message sent successfully";
									$return_array['code'] = "200";
									$return_array['data'] = array("chat_id" => intval($request['chat_id']),
																  "agent_id" => intval($request['agent_id']));

									do_action("wplc_new_user_message_after_record_hook", $chat_id, $message);


								} else if ($action == "wplc_admin_send_msg"){
									$message = sanitize_text_field($message);
									wplc_record_chat_msg("2", $chat_id, $message, true, sanitize_text_field( $request['agent_id'] ), $other);
									wplc_update_active_timestamp($chat_id);

					                $return_array['response'] = "Message sent successfully";
									$return_array['code'] = "200";
									$return_array['data'] = array("chat_id" => intval($request['chat_id']),
																  "agent_id" => intval($request['agent_id']));

									do_action("wplc_new_user_message_after_record_hook", $chat_id, $message);
								}


				           
				        } else {
							$return_array['request_information'] = __("Action not set", "wplivechat");
						}
					} else {
						$return_array['response'] = "No 'message' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
													      	  "chat_id"   => "Chat ID",
													      	  "message" => "Message");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID",
												      	  "message" => "Message");
				}
		 	} else {
				$return_array['response'] = "Secret server_token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'server_token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID",
										      	  "message" => "Message");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID",
									      	  "message" => "Message");
	}
	
	return $return_array;
}

/*
 * Fetch a chat status within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
*/
function wplc_api_get_status(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					$status = wplc_return_chat_status(intval($request['chat_id']));
					if($status){
						$return_array['response'] = "Chat status found";
						$return_array['code'] = "200";
						$return_array['data'] = array("chat_id" => intval($request['chat_id']),
													  "status" => $status);
					} else {
						$return_array['response'] = "Chat status not found";
						$return_array['code'] = "404";
						$return_array['data'] = array("chat_id" => intval($request['chat_id']));
					}
					

			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID");
	}
	
	return $return_array;
}


/*
 * Fetch a chat status within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * Optional:
 * - Limit (Defaults to 50/Max Limit of 50)
 * - Offset (Defaults to 0)
*/
function wplc_api_get_messages(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					$limit = 50;
					$offset = 0;
					if(isset($request['limit'])){
						$limit = intval($request['limit']);
					}
					if(isset($request['offset'])){
						$offset = intval($request['offset']);
					}

					if ( isset( $request['received_via'] ) ) {
						$received_via = sanitize_text_field( $request['received_via'] );
					} else {
						$received_via = 'u';
					}

					$message_data = wplc_api_return_messages($request['chat_id'], $limit, $offset, $received_via);
					
					if($message_data){
						$return_array['response'] = "Message data returned";
						$return_array['code'] = "200";
						$return_array['data'] = array("messages" => $message_data);
					} else {
						$return_array['response'] = "Messages not found";
						$return_array['code'] = "404";
						$return_array['data'] = array("chat_id" => intval($request['chat_id']));
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID");
	}
	
	return $return_array;
}

/*
 * Fetch a chat sessions within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
*/
function wplc_api_get_sessions(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				$session_data = wplc_api_return_sessions();
				if($session_data){
					$return_array['response'] = "Sessions found";
					$return_array['code'] = "200";
					$return_array['data'] = array("sessions" => $session_data);
				} else {
					$return_array['response'] = "No sessions found";
					$return_array['code'] = "404";
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID");
	}
	
	return $return_array;
}

/*
 * Records an admin message via the API
*/
function wplc_api_record_admin_message($cid, $msg){
	global $wpdb;
    global $wplc_tblname_msgs;

    $fromname = apply_filters("wplc_filter_admin_name","Admin");
    $orig = '1';
    
    $msg = apply_filters("wplc_filter_message_control",$msg);

    $wpdb->insert( 
	$wplc_tblname_msgs, 
	array( 
            'chat_sess_id' => $cid, 
            'timestamp' => current_time('mysql'),
            'msgfrom' => $fromname,
            'msg' => $msg,
            'status' => 0,
            'originates' => $orig
	), 
	array( 
            '%s', 
            '%s',
            '%s',
            '%s',
            '%d',
            '%s'
	) 
    );
    
    wplc_update_active_timestamp(sanitize_text_field($cid));
    wplc_change_chat_status(sanitize_text_field($cid),3);
    
    return true;
}

/*
 * Returns messages from server
*/
function wplc_api_return_messages($cid, $limit, $offset, $received_via = 'u'){

	$cid = wplc_return_chat_id_by_rel($cid);

	$messages = wplc_return_chat_messages( $cid, false, true, false, false, 'array', false );

	if ($received_via === 'u') {
		wplc_mark_as_read_user_chat_messages( $cid );
	} else {
		wplc_mark_as_read_agent_chat_messages( $cid, $received_via );
	}
	return $messages;


}


function wplc_api_return_sessions() {
    global $wpdb;
    global $wplc_tblname_chats;
    
    $results = $wpdb->get_results("SELECT * FROM $wplc_tblname_chats WHERE `status` = 3 OR `status` = 2 OR `status` = 10 OR `status` = 5 or `status` = 8 or `status` = 9 ORDER BY `timestamp` ASC");
    
    $session_array = array();
            
    if ($results) {
        foreach ($results as $result) {
            $ip_info = maybe_unserialize($result->ip);
            $user_ip = $ip_info['ip'];
            if($user_ip == ""){
                $user_ip = __('IP Address not recorded', 'wplivechat');
            }

            $browser = 'Unknown';
            $browser_image = '';
            if(!empty($ip_info['user_agent'])){
            	$browser = wplc_return_browser_string($ip_info['user_agent']);
            	$browser_image = wplc_return_browser_image($browser,"16");
            }
         
            
           	$session_array[$result->id] = array();
           
           	$session_array[$result->id]['name'] = $result->name;
           	$session_array[$result->id]['email'] = $result->email;
           
           	$session_array[$result->id]['status'] = $result->status;
           	$session_array[$result->id]['timestamp'] = wplc_time_ago($result->timestamp);

           if ((current_time('timestamp') - strtotime($result->timestamp)) < 3600) {
               $session_array[$result->id]['type'] = __("New","wplivechat");
           } else {
               $session_array[$result->id]['type'] = __("Returning","wplivechat");
           }
           
           $session_array[$result->id]['image'] = "//www.gravatar.com/avatar/".md5($result->email)."?s=30&d=mm";
           $session_array[$result->id]['data']['browsing'] = $result->url;
           $path = parse_url($result->url, PHP_URL_PATH);
           
           if (strlen($path) > 20) {
                $session_array[$result->id]['data']['browsing_nice_url'] = substr($path,0,20).'...';
           } else { 
               $session_array[$result->id]['data']['browsing_nice_url'] = $path;
           }
           
           $session_array[$result->id]['data']['browser'] = WPLC_PLUGIN_URL . "/images/$browser_image";
           $session_array[$result->id]['data']['ip'] = $user_ip;
        }
    }
    
    return $session_array;
}


/**
 * Starts a chat 
 * @param  $name string <Visitors Name>
 * @param  $email string <Visitors Email Address>
 * @param  $session string <Visitors Session ID>
 * @param  $wplc_cid int <Current visitor chat ID>
 * @return $return_array array
 */
function wplc_api_call_start_chat( WP_REST_Request $request ){

	$return_array = array();
	
	if(isset($request)){
		
		if( isset( $request['server_token'] ) ){
		
			if( isset( $request['wplc_name'] ) && isset( $request['wplc_email'] ) && isset( $request['session'] ) ){

				$cid = isset( $request['wplc_cid'] ) ? intval( $request['wplc_cid'] ) : null;

				

				global $wpdb;
			    global $wplc_tblname_chats;

			    $wplc_settings = get_option('WPLC_SETTINGS');
			      
			    $user_data = array(
		            'ip' => "",
		            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'])
		        );
		        $wplc_ce_ip = null;
			    
			    
		
		        $other_data = array();
		        $other_data['unanswered'] = true;

		        $other_data = apply_filters("wplc_start_chat_hook_other_data_hook", $other_data);

		        $wpdb->insert( 
		            $wplc_tblname_chats, 
		            array( 
		                'status' => 2, 
		                'timestamp' => current_time('mysql'),
		                'name' => $request['wplc_name'],
		                'email' => $request['wplc_email'],
		                'session' => $request['session'],
		                'ip' => maybe_serialize($user_data),
		                'url' => $request['url'],
		                'last_active_timestamp' => current_time('mysql'),
		                'other' => maybe_serialize($other_data),
		                'rel' => $request['cid']
		            ), 
		            array( 
		                '%s', 
		                '%s',
		                '%s',
		                '%s',
		                '%s',
		                '%s',
		                '%s',
		                '%s',
		                '%s',
		                '%s'
		            ) 
		        );
		        
		        $cid = $wpdb->insert_id;

		            wplc_mrg_notify_via_email( array(
		            	'name' => $request['wplc_name'],
			            'email' => $request['wplc_email'],
		            ) );
				

		        do_action("wplc_start_chat_hook_after_data_insert", $cid, 2, $request['wplc_name']);

    			do_action("wplc_change_chat_status_hook", $cid, 2); /* so we fire off onesignal events */

    			do_action("wplc_hook_initiate_chat",array("cid" => $request['cid'], "name" => $request['wplc_name'], "email" => $request['wplc_email']));
		 

				$return_array['response'] = "Visitor successfully started chat";
				$return_array['code'] = "200";
				$return_array['data'] = array( 'wplc_cid' => $cid );

			} else {

				$return_array['response'] = "Missing Parameter";
				$return_array['code'] = "401";
				$return_array['requirements'] = array( 'wplc_name' => 'VISITORS_NAME', 'wplc_email' => 'VISITORS_EMAIL', 'session' => 'VISITORS_SESSION' );

			}
			
		} else{

			$return_array['response'] = "No 'security' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array( 'server_token' => 'SECRET_TOKEN', 'wplc_name' => 'VISITORS_NAME', 'wplc_email' => 'VISITORS_EMAIL', 'session' => 'VISITORS_SESSION' );

		}

	}else{

		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array( 'server_token' => 'SECRET_TOKEN', 'wplc_name' => 'VISITORS_NAME', 'wplc_email' => 'VISITORS_EMAIL', 'session' => 'VISITORS_SESSION' );

	}

	return $return_array;

}

/*
 * Function Removed: wplc_api_call_to_server_visitor
 * Reason: Not in use unless manual override of AJAX path is added
 * This is not possible for users, and was purely a conceptual piece of code
*/

/*
 * Upload end point
*/
function wplc_api_remote_upload(WP_REST_Request $request){
	$return_array = array();
	$return_array['response'] = 'false';

	$return_array = apply_filters("wplc_api_remote_upload_filter", $return_array, $request); 

	return $return_array;
}

/*
 * Rest Permission check for restricted end points
*/
function wplc_api_permission_check(){
    return check_ajax_referer( 'wp_rest', '_wpnonce', false );
}

function wplc_validate_agent_check(WP_REST_Request $request){
    $return_array = array();
    if(isset($request)){
        if(isset($request['agent_id'])){
            if( get_user_meta(intval($request['agent_id']), 'wplc_ma_agent', true) ){
                $return_array['response'] = "true";
                $return_array['code'] = "200";
            } else {
                $return_array['response'] = "false";
                $return_array['code'] = "200";
            }
        }else{
            $return_array['response'] = "No Agent ID found";
            $return_array['code'] = "401";
            $return_array['requirements'] = array("agent_id" => "Agent ID");
        }
    }else{
        $return_array['response'] = "No request data found";
        $return_array['code'] = "400";
        $return_array['requirements'] = array("agent_id" => "Agent ID");
    }
    
    return $return_array;
}


# PRO API FUNCTIONS
function wplc_api_send_agent_message_mrg(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['server_token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['server_token'] === $check_token){
				if(isset($request['chat_id'])){
					if(isset($request['message'])){
						if(isset($request['relay_action'])){

								$chat_id = sanitize_text_field($request['chat_id']);
								$message = $request['message'];
								$action = $request['relay_action'];

								if (!empty($request['msg_id'])) {
									$other = new stdClass();
									$other->msgID = $request['msg_id'];
								} else {
									$other = false;
								}
								
								if ($action == "wplc_admin_send_msg"){
									$message = sanitize_text_field( $message );
									$ato = intval( $request['ato'] );
									wplc_api_record_agent_chat_msg_mrg(sanitize_text_field( $request['agent_id'] ), $chat_id, $message, true, $ato, $other);
									wplc_update_active_timestamp($chat_id);

					                $return_array['response'] = "Message sent successfully";
									$return_array['code'] = "200";
									$return_array['data'] = array("chat_id" => intval($request['chat_id']),
																  "agent_id" => intval($request['agent_id']));

									do_action("wplc_new_user_message_after_record_hook", $chat_id, $message);
								}


				           
				        } else {
							$return_array['request_information'] = __("Action not set", "wplivechat");
						}
					} else {
						$return_array['response'] = "No 'message' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
													      	  "chat_id"   => "Chat ID",
													      	  "message" => "Message");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID",
												      	  "message" => "Message");
				}
		 	} else {
				$return_array['response'] = "Secret server_token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'server_token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID",
										      	  "message" => "Message");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("server_token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID",
									      	  "message" => "Message");
	}
	
	return $return_array;
}



function wplc_api_get_agent_unread_message_counts_mrg( WP_REST_Request $request ) {
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
			    
				$current_agent = intval( sanitize_text_field( $request['agent_id'] ) );

			    /**
			     * Get all agents
			     * @var [type]
			     */
			    $user_array = get_users(array(
			        'meta_key' => 'wplc_ma_agent',
			    ));


			    $a_array = array();
			    if ($user_array) {
			        foreach ($user_array as $user) {
			        	$unread = wplc_return_unread_agent_messages_mrg( $current_agent, $user->ID );
			        	$a_array[$user->ID] = $unread;
			        	
			        }
			    }
			    $return_array['response'] = "Unread count agents"; /* needs to be exactly this for the JS to fire correctly */
				$return_array['code'] = "200";
				$return_array['data'] = $a_array;

		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN");
	}
	
	return $return_array;
}

function wplc_api_initiate_chat_mrg(WP_REST_REQUEST $request) {

	$return_array = array();
	if(isset($request)) {
		if(isset($request['security'])) {
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token) {
					if(isset($request['rel']) || isset($request['cid'])) {
						if(isset($request['aid'])) {


							$aid = intval( $request['aid'] );

				            if (isset($request['rel'])) {
				            	$cid = $request['rel'];
				            } else {
				            	$cid = $request['cid'];
				            }


							global $wplc_tblname_chats;
							global $wpdb;
						    $results = $wpdb->get_results("SELECT * FROM $wplc_tblname_chats WHERE `rel` = '".$cid."' OR `id` = '".$cid."' LIMIT 1");
						    if (!$results) {
						        /* it doesnt exist, lets put it in the table */
						        
						        $wpdb->insert( 
						            $wplc_tblname_chats, 
						            array( 
						                'status' => 3, 
						                'timestamp' => current_time('mysql'),
						                'name' => 'Guest',
						                'email' => 'none',
						                'session' => '1',
						                'ip' => '0',
						                'url' => '',
						                'last_active_timestamp' => current_time('mysql'),
						                'other' => '',
						                'agent_id' => $aid,
						                'rel' => $cid,
						            ), 
						            array( 
						                '%s', 
						                '%s',
						                '%s',
						                '%s',
						                '%s',
						                '%s',
						                '%s',
						                '%s',
						                '%s',
						                '%d',
						                '%s'
						            ) 
						        );
						        
						        
						        $cid = $wpdb->insert_id;
						        do_action("wplc_hook_update_agent_id",$cid,$aid);  
						        //var_dump("DONE!!");  
						    }


				        
						} else {
							$return_array['response'] = "No 'AID' found (base64 encoded)";
							$return_array['code'] = "401";
							$return_array['requirements'] = array(
															"security" => "YOUR_SECRET_TOKEN",
													      	"cid"   => "Chat ID",
													      	"aid"   => "agent ID");
						}				        

					} else {
						$return_array['response'] = "No 'REL' or 'CID' found (base64 encoded)";
						$return_array['code'] = "401";
						$return_array['requirements'] = array(
														"security" => "YOUR_SECRET_TOKEN",
												      	"cid"   => "Chat ID",
												      	"rel/cid"   => "related ID or Chat ID");
					}


					
			 	
		 	} else {
				$return_array['response'] = "Nonce is invalid";
				$return_array['code'] = "401";
			}
		} else {
			$return_array['response'] = "No 'security' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array(
												"security" => "YOUR_SECRET_TOKEN",
										      	"cid"   => "Chat ID",
										      	"wplc_extra_data"   => "Data array");
		}
	} else {
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array(
											"security" => "YOUR_SECRET_TOKEN",
									      	"cid"   => "Chat ID",
									      	"wplc_extra_data"   => "Data array");
	}
	
	return $return_array;


            

}

function wplc_api_email_notification_mrg(WP_REST_Request $request) {
	$return_array = array();
	if(isset($request)){
		if(isset($request['security'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['security'] === $check_token){
				if(isset($request['cid'])){
					if(isset($request['wplc_extra_data'])){
						
						$data = $request['wplc_extra_data'];

						$wplc_acbc_data = get_option("WPLC_ACBC_SETTINGS");
					    if (isset($wplc_acbc_data['wplc_pro_chat_notification']) && $wplc_acbc_data['wplc_pro_chat_notification'] == "yes") {
					    	if (isset($wplc_acbc_data['wplc_pro_chat_email_address'])) { $email_address = $wplc_acbc_data['wplc_pro_chat_email_address']; } else { $email_address = ""; }
					    	if (!$email_address || $email_address == "") { $email_address = get_option('admin_email'); }

					    	/** Removed, this is now achieved with the start chat hook instead */
					    	/** Removing this resolves duplicate events */
					        /*$subject = sprintf( __( 'Incoming chat from %s (%s) on %s', 'wplivechat' ),
					                $data['name'],
					                $data['email'],
					                get_option('blogname')
					        );

					        $msg = sprintf( __( '%s (%s) wants to chat with you. <br /><br />Log in: %s', 'wplivechat' ),
					                $data['name'],
					                $data['email'],
					                get_option('home')."/wp-login.php"
					        );

					        wplcmail($email_address,"WP Live Chat Support", $subject, $msg);*/
					    }
					    return true;


					} else {
						$return_array['response'] = "No 'Data' array found (base64 encoded)";
						$return_array['code'] = "401";
						$return_array['requirements'] = array(
														"security" => "YOUR_SECRET_TOKEN",
												      	"cid"   => "Chat ID",
												      	"wplc_extra_data"   => "Data array");
					}


					
			 	} else {
					$return_array['response'] = "No 'CID' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array(
														"security" => "YOUR_SECRET_TOKEN",
												      	"cid"   => "Chat ID",
												      	"wplc_extra_data"   => "Data array");
				}
		 	} else {
				$return_array['response'] = "Nonce is invalid";
				$return_array['code'] = "401";
			}
		} else{
			$return_array['response'] = "No 'security' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array(
												"security" => "YOUR_SECRET_TOKEN",
										      	"cid"   => "Chat ID",
										      	"wplc_extra_data"   => "Data array");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array(
											"security" => "YOUR_SECRET_TOKEN",
									      	"cid"   => "Chat ID",
									      	"wplc_extra_data"   => "Data array");
	}
	
	return $return_array;


}


function wplc_api_is_typing_mrg(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['security'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['security'] === $check_token){
				if(isset($request['cid'])){
					if(isset($request['user'])){
						if(isset($request['type'])){
							if (wplc_typing_mrg($request['user'],sanitize_text_field($request['cid']),sanitize_text_field($request['type']))) {
								
								$return_array['response'] = "Successful";
								$return_array['code'] = "200";
								$return_array['data'] = array("cid" => intval($request['cid']),
															  "user" => intval($request['user']),
															  "type" => intval($request['type']));
							} else {
								$return_array['response'] = "Failed to send typing indicaator";
								$return_array['code'] = "401";
								$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
															      "cid"   => "Chat ID",
															      "user"   => "User type",
															      'type' => "TYPE");

							}
						} else {

						$return_array['response'] = "No 'type' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
													      "cid"   => "Chat ID",
													      "user"   => "User type",
													      'type' => "TYPE");
						}

				 	} else {
						$return_array['response'] = "No 'user' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
													      "cid"   => "Chat ID",
													      "user"   => "User type",
													      'type' => "TYPE");
					}
			 	} else {
					$return_array['response'] = "No 'cid' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
												      "cid"   => "Chat ID",
												      "user"   => "User type",
												      'type' => "TYPE");
				}
		 	} else {
				$return_array['response'] = "Nonce is invalid";
				$return_array['code'] = "401";
			}
		} else{
			$return_array['response'] = "No 'security' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
										      "cid"   => "Chat ID",
										      "user"   => "User type",
										      'type' => "TYPE");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
									      "cid"   => "Chat ID",
									      "user"   => "User type",
									      'type' => "TYPE");
	}
	
	return $return_array;
}


function wplc_api_record_agent_chat_msg_mrg($from, $cid, $msg, $rest_check = false, $ato = false, $other = false) {
    global $wpdb;
    global $wplc_tblname_msgs;


    
    if( ! filter_var($cid, FILTER_VALIDATE_INT) ) {
        
        /**
         * We need to identify if this CID is a node CID, and if so, return the WP CID from the wplc_chat_msgs table
         */
        $cid = wplc_return_chat_id_by_rel($cid);
    }

    /**
     * check if this CID even exists, if not, create it
     *
     * If it doesnt exist, it most likely is an agent-to-agent chat that we now need to save.
     */
        
    global $wplc_tblname_chats;
    $results = $wpdb->get_results("SELECT * FROM $wplc_tblname_chats WHERE `rel` = '".$cid."' OR `id` = '".$cid."' LIMIT 1");
    if (!$results) {
        /* it doesnt exist, lets put it in the table */
        
        //var_dump("WRITING TO TABLE");
        $wpdb->insert( 
            $wplc_tblname_chats, 
            array( 
                'status' => 3, 
                'timestamp' => current_time('mysql'),
                'name' => 'agent-to-agent chat',
                'email' => 'none',
                'session' => '1',
                'ip' => '0',
                'url' => '',
                'last_active_timestamp' => current_time('mysql'),
                'other' => '',
                'rel' => $cid,
            ), 
            array( 
                '%s', 
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            ) 
        );
        
        
        $cid = $wpdb->insert_id;        
    }

    $msg_id = '';
    if ($other !== false) {
        if (!empty($other->msgID)) {
            $msg_id = $other->msgID;
        } else {
            $msg_id = '';
        }
    }

    $user_info = get_userdata( $from );
	if( $user_info ){
    	$fromname = $user_info->display_name;
    } else {
    	$fromname = 'agent';
    }
    $orig = '1';

    
    $orig_msg = $msg;

    $msg = apply_filters("wplc_filter_message_control",$msg);


    $wpdb->insert( 
    	$wplc_tblname_msgs, 
    	array( 
                'chat_sess_id' => $cid, 
                'timestamp' => current_time('mysql'),
                'msgfrom' => $fromname,
                'msg' => $msg,
                'status' => 0,
                'originates' => $orig,
                'other' => '',
                'rel' => $msg_id,
                'ato' => $ato,
                'afrom' => $from
    	), 
    	array( 
                '%s', 
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d'
    	) 
    );
    
    $data = array(
        'cid' => $cid,
        'from' => $from,
        'msg' => $orig_msg,
        'orig' => $orig
    );
    do_action("wplc_hook_message_sent",$data);

    wplc_update_active_timestamp(sanitize_text_field($cid));
    
    
    return true;

}

add_filter("wplc_api_remote_upload_filter", "wplc_api_remote_upload_handler_mrg", 10, 2);
/*
 * Processes remote uploads, from app or main agent files as an example
*/
function wplc_api_remote_upload_handler_mrg($return_array, $request){
	$remote_files = $request->get_file_params();
	if(is_array($remote_files)){
		$upload_dir = wp_upload_dir();
		$user_dirname = $upload_dir['basedir'];

		if( !file_exists( $user_dirname."/wp_live_chat/" ) ){
			    @mkdir($user_dirname.'/wp_live_chat/');
		}  

		if( !file_exists( realpath($user_dirname."/wp_live_chat/" .  intval($request['cid']) ) ) ){
			@mkdir( realpath($user_dirname.'/wp_live_chat/'. intval($request['cid']) ) );
		}              

		if (isset($remote_files['file'])) {

			$file_name = strtolower( sanitize_file_name($remote_files['file']['name']) );
            $file_name = basename($file_name); //This prevents traversal
		   	
		   	if(!wplc_check_file_name_for_unsafe_extension($file_name)){
           	   if(wplc_check_file_name_for_safe_extension($file_name)){
				   	if( file_exists( realpath($user_dirname . "/wp_live_chat/" . intval($request['cid']) . "/" .  sanitize_file_name($remote_files['file']['name']) )) ){
				       $file_name = rand(0, 10) . "-" . $file_name;
				   	} 

				   	$file_name = str_replace(" ", "_", $file_name);

				   	if(move_uploaded_file( realpath( sanitize_file_name($remote_files['file']['tmp_name'])), realpath($user_dirname."/wp_live_chat/" . intval($request['cid']) . "/" . $file_name)) ){

				   		if(wplc_check_file_mime_type( realpath($user_dirname. "/wp_live_chat/" . intval($request['cid']) . "/" . $file_name) )){
					       $response = realpath($upload_dir['baseurl']."/wp_live_chat/" . intval($request['cid']) . "/" . $file_name);
					      
					       $return_array['response'] = wp_filter_post_kses(strip_tags($response));
					    } else {
					    	@unlink( realpath($user_dirname. "/wp_live_chat/" . intval($request['cid']) . "/" . $file_name) );
					    	 $return_array['response'] = __('Security Violation - MIME Type not allowed', 'wplivechat');
					    }
				  	} else {
				      $return_array['response'] = __('File could not be uploaded', 'wplivechat');
				  	}
			   } else {
			   	 $return_array['response'] = __('Security Violation - Does not match allowed file types', 'wplivechat');
			   }
			} else {
				$return_array['response'] = __('Security Violation - File unsafe', 'wplivechat');
			}

		} else {
			$return_array['response'] = '0';
		}
           
	} else {
		$return_array['response'] = '0';
	}

	return $return_array;
}