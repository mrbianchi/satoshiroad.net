 jQuery(function() {
    /* Going online functionality used to be here */
    var wplc_ma_set_transient = null;

    wplc_ma_set_transient = setInterval(function (){wplc_ma_update_agent_transient();}, 60000);
    wplc_ma_update_agent_transient();

    function wplc_ma_update_agent_transient() {
        var data = {
            action: 'wplc_admin_set_transient',
            security: wplc_admin_strings.nonce,
            user_id:  wplc_admin_strings.user_id
        };
        jQuery.post(ajaxurl, data, function(response) {
        });
    }
});