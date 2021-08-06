var orig_title_wplc;
jQuery(document).on("wplc_end_chat bleeper_chat_ended_notification", function( e ) { 
  if (typeof wplc_extra_div_enabled !== "undefined" && wplc_extra_div_enabled === "1") {
	   jQuery("#wp-live-chat-4").hide();
	   jQuery("#wplc-extra-div").show();
     orig_title_wplc = jQuery("#wplc_first_message").html();
     jQuery("#wplc_first_message").html(wplc_end_chat_string);
     jQuery("#nimblesquirrel_div").attr('style', 'width: 100% !important');
   }
   
});


jQuery(document).on("wplc_minimize_chat", function( e ) {
    if (typeof wplc_extra_div_enabled !== "undefined" && wplc_extra_div_enabled === "1") {
        jQuery("#wplc-extra-div").hide();
        jQuery("#wplc_first_message").html(orig_title_wplc);
    }
});


jQuery(document).on("click", "#wp-live-chat-header", function() {
  jQuery("#nimblesquirrel_div").attr('style', 'width: 100% !important');
});
