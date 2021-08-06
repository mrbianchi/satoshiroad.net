jQuery("body").on("change","#wplc_environment", function() {
       
        var selection = jQuery(this).val();
        if (selection === '1') {
            /* low grade host */
            jQuery("#wplc_iterations").val(20);
            jQuery("#wplc_delay_between_loops").val(1000000);
        }
        else if (selection === '2') {
            /* low grade host */
            jQuery("#wplc_iterations").val(55);
            jQuery("#wplc_delay_between_loops").val(500000);
        }
        else if (selection === '3') {
            /* low grade host */
            jQuery("#wplc_iterations").val(60);
            jQuery("#wplc_delay_between_loops").val(400000);
        }
        else if (selection === '4') {
            /* low grade host */
            jQuery("#wplc_iterations").val(200);
            jQuery("#wplc_delay_between_loops").val(250000);
        }
})