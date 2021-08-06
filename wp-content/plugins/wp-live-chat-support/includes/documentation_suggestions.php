<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action("wplc_hook_message_sent","wplc_mrg_filter_control_message_control",10,1); 
function wplc_mrg_filter_control_message_control($data) {

	$string = wplc_tokenise_mrg($data['msg'],'string');
	$string = wplc_remove_stop_words_mrg($string);
	if (count(explode(' ',$string)) >= 3) {
		/* only start searching if we have 3 or more words to look for */
		if (function_exists("wplc_record_chat_notification")) {
			

			$returned_data = wplc_documentation_find_mrg($string);
			if ($returned_data) {
				$formatted_msg = "<div class='wplc_doc_suggestion'><h3>Are you looking for help with:</h3>";
				foreach ($returned_data as $dataset) {
					$formatted_msg = $formatted_msg . "<a href='".$dataset['url']."' >".$dataset['title']. "[".$dataset['proximity']."%]</a><br /><small>".$dataset['excerpt']."</small>";				
				}
				$formatted_msg = $formatted_msg."</div>";
				$suggestion_data = array(
					'cid' => $cid,
					'msg' => $msg,
					'formatted_msg' => $formatted_msg
				);
				wplc_record_chat_notification('doc_suggestion',$data['cid'],$suggestion_data);
			}
		}
	}
	return;
}

function wplc_documentation_find_mrg($string) {
	$my_query = new WP_Query( array( 's' => $string ) );
	$returned_data = array();
	$i = 0;
    if ( $my_query->have_posts() ) {

	    while ( $my_query->have_posts() ) {
	        $my_query->the_post();
	    	$i++;
	        $pid = get_the_ID();
	        $returned_data[$i]['title'] = get_the_title();
	        $returned_data[$i]['uri'] = get_the_permalink();

	        $description = wplc_remove_stop_words_mrg(wplc_tokenise_mrg(get_the_content(),'string'));
	        $lev = levenshtein($description, $string);

	        $returned_data[$i]['excerpt'] = get_the_excerpt();
	        $returned_data[$i]['levenshtein'] = $lev;
	        $returned_data[$i]['proximity'] = similar_text($description, $string);

	    }
	}
	return $returned_data;




}
function wplc_remove_stop_words_mrg($string) {
	$tokenised_array = explode(" ",$string);

    $stopwords = array("#", "a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "co", "con", "could", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
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
function wplc_mrg_filter_control_chat_notification_documentation_suggestion($type,$cid,$data) {

    if ($type == "doc_suggestion") {

        global $wpdb;
        global $wplc_tblname_msgs;


        $msg = $data['formatted_msg'];

        $wpdb->insert( 
            $wplc_tblname_msgs, 
            array( 
                    'chat_sess_id' => $cid, 
                    'timestamp' => current_time('mysql'),
                    'msgfrom' => __('System notification',"wplivechat"),
                    'msg' => $msg,
                    'status' => 0,
                    'originates' => 0
            ), 
            array( 
                    '%s', 
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