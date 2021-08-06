jQuery(function() {

    jQuery("body").on("click","#wplc_sample_ring_tone",function(e) {
        var v = jQuery("#wplc_ringtone").val();
        if (typeof v !== "undefined") {
            new Audio(v).play() 
        }
        e.preventDefault();
    });
    jQuery("body").on("click","#wplc_sample_message_tone",function(e) {
        var v = jQuery("#wplc_messagetone").val();
        if (typeof v !== "undefined") {
            new Audio(v).play()
        }
        e.preventDefault();
    });
    jQuery("body").on("click", "#wplc_add_agent", function(e) {
         e.preventDefault();

        var uid = parseInt(jQuery("#wplc_agent_select").val());
        var em = jQuery("#wplc_selected_agent_"+uid).attr('em');
        var em2 = jQuery("#wplc_selected_agent_"+uid).attr('em2');
        var name = jQuery("#wplc_selected_agent_"+uid).attr('name');
        
        if (uid) {
            var data = {
                action: 'wplc_add_agent',
                security: wplc_admin_strings.nonce,
                uid: uid
            };
            jQuery.post(ajaxurl, data, function(response) {
                if (response === "1") {
                    /* success */
                    var wplchtml = "<li id=\"wplc_agent_li_"+uid+"\"><p><img src=\"//www.gravatar.com/avatar/"+em+"?s=80&d=mm\"></p><h3>"+name+"</h3><small>"+em2+"</small><p><button class='button button-secondary' id='wplc_remove_agent' uid='"+uid+"'>"+wplc_admin_strings.remove_agent+"</button></p></li>"
                    jQuery(wplchtml).insertBefore("#wplc_add_new_agent_box").hide().fadeIn(2000);
                    jQuery("#wplc_selected_agent_"+uid).remove();
                } else {
                    /* failure */
                }
            });
        }
       


    });

     jQuery("body").on("click", ".wplc_remove_agent", function(e) {
        
        var uid = parseInt(jQuery(this).attr('uid'));
        
        if (uid) {
            var data = {
                action: 'wplc_remove_agent',
                security: wplc_admin_strings.nonce,
                uid: uid
            };
            jQuery.post(ajaxurl, data, function(response) {
                if (response === "1") {
                    /* success */
                   
                    jQuery("#wplc_agent_li_"+uid).fadeOut(500);
                } else {
                    /* failure */
                }
            });
        }
        e.preventDefault();


    });

 });