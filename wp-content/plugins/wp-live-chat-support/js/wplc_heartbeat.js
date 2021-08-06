(function( $ ) {
    "use strict";
    $(function() {
        $( document ).on( 'heartbeat-send', function( e, data ) {
            data['client'] = 'wplc_heartbeat';
        });


        var wplc_set_transient = null;

        wplc_set_transient = setInterval(function () {
          wpcl_admin_set_transient();
        }, 60000);
        wpcl_admin_set_transient();
        function wpcl_admin_set_transient() {
          var data = {
              action: 'wplc_admin_set_transient',
              security: wplc_transient_nonce

          };
          if (typeof ajaxurl === "undefined" && typeof wplc_ajaxurl !== "undefined") { var ajaxurl = wplc_ajaxurl; }
          $.post(ajaxurl, data, function (response) {});
        }
    });
}( jQuery ));