<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_filter("wplc_filter_setting_tabs","wplc_api_settings_tab_heading_doc_suggestions_mrg");
function wplc_api_settings_tab_heading_doc_suggestions_mrg($tab_array) {
    $tab_array['doc'] = array(
      "href" => "#tabs-doc-suggest",
      "icon" => 'fa fa-lightbulb-o',
      "label" => __("Doc Suggestions","wplivechat")
    );
    return $tab_array;
}



add_action("wplc_hook_settings_page_more_tabs","wplc_mrg_hook_settings_page_more_doc_suggestions",9);
/**
 * Adds 'Doc Suggestions' content to settings area
 * @return void
 */
function wplc_mrg_hook_settings_page_more_doc_suggestions() {
	$wplc_doc_sugg_data = get_option("WPLC_DOC_SUGG_SETTINGS"); 
    ?>
		<div id="tabs-doc-suggest">
			<h3><?php _e("Documentation Suggestions", "wplivechat") ?></h3>
			<table class="wp-list-table wplc_list_table widefat fixed striped pages">
				<tbody>
					<tr>
						<td width="300" valign="top">
							<label for="wplc_enable_doc_suggestions"><?php _e("Enable Documentation Suggestions","wplivechat"); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e("When a user sends a message the plugin will automatically detect if there are posts or pages that can be suggested to the user in order for the user to get more information about what they are asking. This is useful when the user has typed their message and is still waiting for an agent to answer their chat.","wplivechat"); ?> "></i></label>
						</td>
						<td valign="top">
						  <input type="checkbox" value="1" name="wplc_enable_doc_suggestions" <?php if (isset($wplc_doc_sugg_data['wplc_enable_doc_suggestions']) && $wplc_doc_sugg_data['wplc_enable_doc_suggestions'] == '1') { echo "checked"; } ?>> 
  						</td>
	              	</tr>
				</tbody>
			</table>

			<br>

		</div>
		
		<?php
	

}


add_action('wplc_hook_admin_settings_save','wplc_doc_sugg_save_settings_mrg');
/**
 * Save settings hook for the documentation suggestions settings area
 * @return void
 */
function wplc_doc_sugg_save_settings_mrg() {
    if (isset($_POST['wplc_save_settings'])) {
    	$wplc_doc_sugg_data = array();
        if (isset($_POST['wplc_enable_doc_suggestions'])) {
            $wplc_doc_sugg_data['wplc_enable_doc_suggestions'] = sanitize_text_field($_POST['wplc_enable_doc_suggestions']);
        } else {
            $wplc_doc_sugg_data['wplc_enable_doc_suggestions'] = 0;
        }
        
        update_option('WPLC_DOC_SUGG_SETTINGS', $wplc_doc_sugg_data);

    }
}


add_action("wplc_hook_message_sent","wplc_mrg_filter_control_message_control",10,1); 
/**
 * Main function to find suggestions, hooked onto the wplc_hook_message_sent hook
 * @param  array $data Array of data, such as msg, cid, etc
 * @return void
 */
function wplc_mrg_filter_control_message_control($data) {

	if ($data['orig'] != '2') { return; /* was not triggered by a user sending a message */ }
	$wplc_doc_sugg_data = get_option("WPLC_DOC_SUGG_SETTINGS"); 
	
	if (isset($wplc_doc_sugg_data['wplc_enable_doc_suggestions']) && $wplc_doc_sugg_data['wplc_enable_doc_suggestions'] == '1') {
		$string = wplc_tokenise_mrg($data['msg'],'string');
		$original_string = $data['msg'];
		$string = wplc_remove_stop_words_mrg($string);

        $cdata = wplc_get_chat_data(intval($data['cid']));
		
		// Only send documentation hints if the admin hasn't started chatting to the user, so the conversation isn't interrupted
		if($cdata->agent_id == 0)
		{
			/* only start searching if we have 2 or more words to look for */
			
			if (function_exists("wplc_record_chat_notification")) {

				$returned_data = wplc_documentation_find_mrg($original_string);
				if ($returned_data) {

	                $formatted_msg = apply_filters("wplc_pro_filter_doc_suggestion_html","",$returned_data);

	                $returned_data['admin'] = true;
	                $formatted_msg_admin = apply_filters("wplc_pro_filter_doc_suggestion_html","",$returned_data);
                    
                    $suggestion_data = array(
                        'cid' => $data['cid'],
                        'msg' => $original_string,
                        'formatted_msg' => $formatted_msg,
                        'formatted_msg_admin' => $formatted_msg_admin
                    );
					
					/**
					 * Send what we have found as a system notification
					 */
					wplc_record_chat_notification('doc_suggestion',$data['cid'],$suggestion_data);
					

				}
			}
		}
	}
	return;
}



add_filter("wplc_pro_filter_doc_suggestion_html","wplc_mrg_filter_control_doc_suggestion_html",10,2);
/**
 * Build the documentation HTML output 
 * @param  string $content       
 * @param  array  $returned_data Response data from the server
 * @return string                HTML output
 */
function wplc_mrg_filter_control_doc_suggestion_html($content,$returned_data) {

    $dir = dirname(dirname(__FILE__));
    $template_content_template = file_get_contents($dir."/includes/templates/documentation-suggestion.html");
    $template_content_list_template = file_get_contents($dir."/includes/templates/documentation-suggestion-item.html");
    
    if (isset($returned_data['admin']) && $returned_data['admin'] == true) {
        $template_content_template = str_replace("{doc_suggestion_header}",__("The following was sent to the user as suggested documents:","wplivechat"),$template_content_template);
    } else {
        $template_content_template = str_replace("{doc_suggestion_header}",__("While you wait for the agent, perhaps these documents may help?","wplivechat"),$template_content_template);
    }

    $list_content = "";

    foreach ($returned_data as $dataset) {
        $new_content_list = $template_content_list_template;
        $new_content_list = str_replace("{doc_uri}",$dataset['uri'],$new_content_list);
        $new_content_list = str_replace("{doc_proximity}",$dataset['weighted_proximity'],$new_content_list);
        $new_content_list = str_replace("{doc_title}",$dataset['title'],$new_content_list);
        $list_content .= $new_content_list;

    }

    $template_content_template = str_replace("{doc_suggestion_list}",$list_content,$template_content_template);    
    
    return $template_content_template;
} 





/**
 * Search the database for matches based on what the user typed
 * @param  string $string Input string
 * @return array          Comprehensive array containing response data
 */
function wplc_documentation_find_mrg($string) {

    $original_string = $string;

    /**
     * Remove stop words so that we can search for each word in the database
     */
    $search_strings = wplc_tokenise_mrg(wplc_remove_stop_words_mrg($string));

    /**
     * Set acceptable post types to look within
     */
    $post_types = array( 'post', 'page');

    /**
     * Set the minimum weighted proximity for it to be a valid result
     */
    $min_prox = 25;

    $returned_data = array();
    $i = 0;
    foreach ($search_strings as $string) {
    	$my_query = new WP_Query( array( 's' => $string, 'post_status' => 'publish', 'post_type' => $post_types ) );
    	
        if ( $my_query->have_posts() ) {

    	    while ( $my_query->have_posts() ) {
    	        $my_query->the_post();
    	    	$i++;
    	        $pid = get_the_ID();
    	        

    	        $description = wplc_tokenise_mrg(get_the_content(),'string');
                $tmp_title = get_the_title();
                
                $lev_title = levenshtein(wplc_tokenise_mrg($tmp_title,'string'), $original_string);
				
				$lev_content = 0;
				$words = preg_split('/\s+/', wp_filter_post_kses(strip_tags($description)));
				foreach($words as $word)
					$lev_content += levenshtein($word, $original_string);
				
                similar_text(wplc_tokenise_mrg($tmp_title,'string'), $original_string,$title_proximity);
                similar_text(wplc_tokenise_mrg($description,'string'), $original_string,$desc_proximity);
                $weighted_prox = ($title_proximity * 0.7) + ($desc_proximity * 0.3);
                if ($weighted_prox >= $min_prox) {  

                    $returned_data[$i]['title'] = get_the_title();
                    $returned_data[$i]['uri'] = get_the_permalink();
                    $returned_data[$i]['word'] = $string;
        	        $returned_data[$i]['excerpt'] = get_the_excerpt();
                    $returned_data[$i]['original_string'] = $original_string;
                    $returned_data[$i]['title_levenshtein'] = $lev_title;
                    $returned_data[$i]['title_proximity'] = $title_proximity;
                    $returned_data[$i]['content_levenshtein'] = $lev_content;
                    $returned_data[$i]['content_proximity'] = $desc_proximity;
                    $returned_data[$i]['weighted_proximity'] = $weighted_prox;
                }

    	    }
    	}
    }

    $unique_identifier_array = array();
    $rank_array = array();
    foreach ($returned_data as $key => $dats) {
        if (!in_array($dats['uri'],$unique_identifier_array)) {
            array_push($unique_identifier_array, $dats['uri']);
            $rank_array[$key] = $dats['weighted_proximity'];
        } else {
            /* its in, lets remove the key */
            unset($returned_data[$key]);
        }
    }
    arsort($rank_array);

    $sorted_array = array();
    $i = 0;
    /**
     * Set the max amount of listings we want to display here.
     */
    $max_listings = 5;

    foreach ($rank_array as $key => $val) {

        $sorted_array[$i] = $returned_data[$key];
        $i++;
        if ($i >= $max_listings) { break; }

    }

	return $sorted_array;




}

/**
 * Remove certain stop words from the original string
 * @param  string $string 	Original query string
 * @return array 			Array of each word that remains
 */
function wplc_remove_stop_words_mrg($string) {
	$tokenised_array = explode(" ",$string);

    $stopwords = array("#", "a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "co", "con", "could", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "i", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "via", "want", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
    $new_array = array();
    foreach ($stopwords as $word) {
        foreach ($tokenised_array as $key => $val) {
            if ($val == $word) {
                unset($tokenised_array[$key]);
            }
            preg_match('/(\s+|^)@\S+/',$val,$match);
            if (isset($match[0])) {
                unset($tokenised_array[$key]);   
            }
        }
    }
    $cnt = 0;
    foreach ($tokenised_array as $key => $val) {
        $new_array[$cnt] = $val;
        $cnt++;
    }

    return implode(" ",$new_array);

}

/**
 * Tokenise and normalise the query
 *
 * Strip all html, tags, characters etc.
 * 
 * @param  string $string 	Input string
 * @param  string $method  	If set to array will return as array. If set to anything else will return as string
 * @return string or array
 */
function wplc_tokenise_mrg($string,$method = 'array') {
    $text = strtolower($string);
    $matches = wp_filter_post_kses(html_entity_decode($text)); // strip the rest of the HTML code
    $matches = str_replace("  "," ",$matches);
    $matches = preg_replace("/http(s)*:\/\/.+/i"," ",$matches);
    $matches = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $matches); // remove all non-utf8 characters
    $matches = preg_replace('/[,.]/', ' ', $matches); // Replace commas, hyphens etc (count them as spaces)
    $matches = preg_replace('/\<script.*?\<\/script\>/ism', '', $matches); //remove script tags
    $matches = preg_replace('/\<style.*?\<\/style\>/ism', '', $matches); // remove style tags
    $matches = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $matches); // remove square bracket tags
    $matches = wp_filter_post_kses(html_entity_decode($matches)); // strip the rest of the HTML code
    $matches = preg_replace('/\s+/', ' ',$matches);
    $matches = str_replace("?","",$matches);
    $matches = str_replace("!","",$matches);
    $matches = str_replace("@","",$matches);
    $matches = str_replace("#","",$matches);
    $matches = str_replace("$","",$matches);
    $matches = str_replace("%","",$matches);
    $matches = str_replace("^","",$matches);
    $matches = str_replace("&","",$matches);
    $matches = str_replace("*","",$matches);
    $matches = str_replace(")","",$matches);
    $matches = str_replace("(","",$matches);
    if ($method == 'array') {
        $matches = explode(" ",$matches);
    }


    return $matches;

}



add_action("wplc_hook_chat_notification","wplc_mrg_filter_control_chat_notification_documentation_suggestion",10,3);
/**
 * System notification to send both the agent and the user the suggestions
 * @param  string $type Notification type
 * @param  intval $cid  Chat ID
 * @param  array  $data data array containing important elements
 * @return void
 */
function wplc_mrg_filter_control_chat_notification_documentation_suggestion($type,$cid,$data) {
    if ($type == "doc_suggestion") {

        global $wpdb;
        global $wplc_tblname_msgs;


        $msg = $data['formatted_msg'];
        $msg_admin = $data['formatted_msg_admin'];
        
        $check = $wpdb->insert( 
            $wplc_tblname_msgs, 
            array( 
                    'chat_sess_id' => intval($cid), 
                    'timestamp' => current_time('mysql'),
                    'msgfrom' => __('System notification',"wplivechat"),
                    'msg' => $msg,
                    'status' => 0,
                    'originates' => 0
            ), 
            array( 
                    '%d', 
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%d'
            ) 
        );


        /* save this for the agent as well */
        $check = $wpdb->insert( 
            $wplc_tblname_msgs, 
            array( 
                    'chat_sess_id' => intval($cid), 
                    'timestamp' => current_time('mysql'),
                    'msgfrom' => __('System notification',"wplivechat"),
                    'msg' => $msg_admin,
                    'status' => 0,
                    'originates' => 3
            ), 
            array( 
                    '%d', 
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%d'
            ) 
        );
        
    }
    return;
} 
