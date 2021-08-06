function wplc_emit_custom_data_event( action, event_object ){

    if (typeof socket !== "undefined" && socket.connected === true) {

        var a_wplc_cid = Cookies.get('wplc_cid');

        if( typeof a_wplc_cid !== 'undefined' ) {

            socket.emit('custom data',{ action: action, chatid: a_wplc_cid, ndata: event_object } );    

        }

    }

}


var scrolling = false;
jQuery(document).scroll(function() {
    if( !scrolling ){
        scrolling = true;
    }
    clearTimeout(jQuery.data(this, 'scrollTimer'));
    jQuery.data(this, 'scrollTimer', setTimeout(function() {
        // do something
        wplc_emit_custom_data_event( 'user_scrolling', { scrolling: true } );
        scrolling = false;
    }, 250));
});

jQuery(function(){

    jQuery("input, textarea").on("focus", function(){

        var input_field_id = jQuery(this).attr('id');
        var input_field_class = jQuery(this).attr('class');
        var input_field_value = jQuery(this).val();
        
        var input_field_obj = {
            id: input_field_id,
            class: input_field_class,
            content: input_field_value,
            fields: true
        }
        wplc_emit_custom_data_event( 'input_field_change', input_field_obj );

    });
});

jQuery(document).on("click", function(e) {
    if ( (jQuery(e.target).closest('a').length) || (jQuery(e.target).closest('input').length)  || (jQuery(e.target).closest('select').length) || (jQuery(e.target).closest('textarea').length)  )  { return; /* handled elsewhere */ } else {

        var parent1 = jQuery(e.target).parent().attr('id');
        var parent2 = jQuery(e.target).parent().parent().attr('id');
        var parent3 = jQuery(e.target).parent().parent().parent().attr('id');
        var parent4 = jQuery(e.target).parent().parent().parent().parent().attr('id');
        var parent5 = jQuery(e.target).parent().parent().parent().parent().parent().attr('id');
        var parent6 = jQuery(e.target).parent().parent().parent().parent().parent().parent().attr('id');
        var parent7 = jQuery(e.target).parent().parent().parent().parent().parent().parent().parent().attr('id');
        var parent8 = jQuery(e.target).parent().parent().parent().parent().parent().parent().parent().parent().attr('id');
        var parent9 = jQuery(e.target).parent().parent().parent().parent().parent().parent().parent().parent().parent().attr('id');
        var parent10 = jQuery(e.target).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().attr('id');


        if (parent1 !== "wp-live-chat" && 
            parent2 !== "wp-live-chat" && 
            parent3 !== "wp-live-chat" && 
            parent4 !== "wp-live-chat" && 
            parent5 !== "wp-live-chat" && 
            parent6 !== "wp-live-chat" && 
            parent7 !== "wp-live-chat" && 
            parent8 !== "wp-live-chat" && 
            parent9 !== "wp-live-chat" && 
            parent10 !== "wp-live-chat") {
            


            //html2canvas(e.target).then(function(canvas) {
                //wplc_emit_custom_data_event( 'send_user_canvas', canvas.toDataURL("image/png"));
            //});
            var elem_id = jQuery(e.target).attr('id');
            var elem_class = jQuery(e.target).attr('class');

            var event_data = {
                elem_id: elem_id, 
                elem_class: elem_class

            }

            wplc_emit_custom_data_event( 'send_user_click_data', event_data );
        }

    }
});      

var timeout = null;

jQuery(document).on('mousemove', function() {
    clearTimeout(timeout);

    timeout = setTimeout(function() {
        wplc_emit_custom_data_event( 'send_user_mouse_idling', { idle: true } );
    }, 300000);
});

jQuery(document).mousedown(function(ev){
    if(ev.which == 3){
        wplc_emit_custom_data_event( 'send_user_right_clicked', { right_clicked: true } );
    }
});
       

jQuery(document).on("click", "a", function() {
    //this == the link that was clicked
    var href = jQuery(this).attr("href");
    var text = jQuery(this).html();
    wplc_emit_custom_data_event( 'send_link_click', { link_click: true, text: text } );
});

window.onkeydown = function(e){

    

    if(e.keyCode == 70 && e.ctrlKey){
        wplc_emit_custom_data_event( 'send_user_ctrl_f', { ctrl_f: true } );
    }

    if(e.keyCode == 80 && e.ctrlKey){
        wplc_emit_custom_data_event( 'send_user_ctrl_p', { ctrl_p: true } );
    }

    if(e.keyCode == 67 && e.ctrlKey){
        wplc_emit_custom_data_event( 'send_user_ctrl_c', { ctrl_c: true } );
    }

    if(e.keyCode == 86 && e.ctrlKey){
        wplc_emit_custom_data_event( 'send_user_ctrl_v', { ctrl_v: true } );
    }

    if(e.keyCode == 44){
        // Multiple ways to go about this due to different OS's
        // wplc_emit_custom_data_event( { ctrl_: true } );
    }    

    if( ( e.ctrlKey && e.keyCode == 16 && ( e.keyCode == 74 || e.keyCode == 73 ) ) || e.keyCode == 123 ){
        wplc_emit_custom_data_event( 'send_user_console_opened', { console_log_opened: true } );
    }

    if ( e.ctrlKey && e.shiftKey && e.keyCode === 74) {
        wplc_emit_custom_data_event( 'send_user_console_opened', { console_log_opened: true } );   
    }


    
}