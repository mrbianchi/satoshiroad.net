jQuery(function(){
    /*Text editor Support*/
    jQuery("#nifty_tedit_b").click(function(evt){
        evt.stopImmediatePropagation();
        niftyTextEdit("b");
    });
    jQuery("#nifty_tedit_i").click(function(evt){
        evt.stopImmediatePropagation();
        niftyTextEdit("i");
    });
    jQuery("#nifty_tedit_u").click(function(evt){
        evt.stopImmediatePropagation();
        niftyTextEdit("u");
    });
    jQuery("#nifty_tedit_strike").click(function(evt){
        evt.stopImmediatePropagation();
        niftyTextEdit("strike");
    });
    jQuery("#nifty_tedit_mark").click(function(evt){
        evt.stopImmediatePropagation();
        niftyTextEdit("mark");
    });
    jQuery("#nifty_tedit_sub").click(function(evt){
        evt.stopImmediatePropagation();
        niftyTextEdit("sub");
    });
    jQuery("#nifty_tedit_sup").click(function(evt){
        evt.stopImmediatePropagation();
        niftyTextEdit("sup");
    });
    jQuery("#nifty_tedit_link").click(function(evt){
        evt.stopImmediatePropagation();
        niftyTextEdit("link");
    });

});


function wplcFormatParser(msg){
    if(msg.indexOf("link:") !== -1){
        msg = msg.replace(/link:/g, "<a target='_blank' href='");
    }
    if(msg.indexOf(":link") !== -1){
        msg = msg.replace(/:link/g, "'>Link</a>");
    }
    if(msg.indexOf("img:") !== -1){
         msg = msg.replace(/img:/g, "<img style='max-width:100%; position: relative; left: 0 !important; border-radius: 0 !important;' src='");
    }
    if(msg.indexOf(":img") !== -1){
        msg = msg.replace(/:img/g, "' />");
    }
    
     if(msg.indexOf("video:") !== -1){
         msg = msg.replace(/video:/g, "<video style='background-color: black; max-width:100%;' src='");
    }
    if(msg.indexOf(":video") !== -1){
        msg = msg.replace(/:video/g, "' controls></video>");
    }
    
    
    if(msg.indexOf("mark:") !== -1){
         msg = msg.replace(/mark:/g, "<mark>");
    }
    if(msg.indexOf(":mark") !== -1){
        msg = msg.replace(/:mark/g, "</mark>");
    }
    
    if(msg.indexOf("strike:") !== -1){
         msg = msg.replace(/strike:/g, "<del>");
    }
    if(msg.indexOf(":strike") !== -1){
        msg = msg.replace(/:strike/g, "</del>");
    }
    
    if(msg.indexOf("sub:") !== -1){
         msg = msg.replace(/sub:/g, "<sub>");
    }
    if(msg.indexOf(":sub") !== -1){
        msg = msg.replace(/:sub/g, "</sub>");
    }
    if(msg.indexOf("sup:") !== -1){
         msg = msg.replace(/sup:/g, "<sup>");
    }
    if(msg.indexOf(":sup") !== -1){
        msg = msg.replace(/:sup/g, "</sup>");
    }
    //run singles last
    
    if(msg.indexOf("b:") !== -1){
         msg = msg.replace(/b:/g, "<strong>");
    }
    if(msg.indexOf(":b") !== -1){
        msg = msg.replace(/:b/g, "</strong>");
    }
    if(msg.indexOf("i:") !== -1){
         msg = msg.replace(/i:/g, "<em>");
    }
    if(msg.indexOf(":i") !== -1){
        msg = msg.replace(/:i/g, "</em>");
    }
    
    if(msg.indexOf("u:") !== -1){
         msg = msg.replace(/u:/g, "<ins>");
    }
    if(msg.indexOf(":u") !== -1){
        msg = msg.replace(/:u/g, "</ins>");
    }
    return msg;
    
}

var selectedIndexStart;
var selectedIndexEnd;
var checkSelection = true;
function getText(elem) {
    if(checkSelection){
      if(elem !== null && (elem.selectionStart !== null && typeof elem.selectionStart !== 'undefined')){
          if(selectedIndexStart !== elem.selectionStart){
              selectedIndexStart = elem.selectionStart;
          }
      }

      if(elem !== null && ( typeof elem.selectionEnd !== 'undefined' && elem.selectionEnd !== null)){
          if(selectedIndexEnd !== elem.selectionEnd){
              selectedIndexEnd = elem.selectionEnd;
          }
      }
        //console.log(selectedIndexStart + " - " + selectedIndexEnd); 
        //console.log(" - "+ elem.value.substr(selectedIndexStart, selectedIndexEnd)); 
    }
     
}

setInterval(function() {
    getText(document.getElementById("wplc_admin_chatmsg"));
}, 1000);

function niftyTextEdit(insertContent){
    checkSelection = false;
    /*Text editor Code here*/
    
    jQuery("#wplc_admin_chatmsg").focus();

    var current = jQuery("#wplc_admin_chatmsg").val();

    var pre = current.substr(0, (selectedIndexStart > 0) ? selectedIndexStart : 0);
    var selection = current.substr(selectedIndexStart, selectedIndexEnd - selectedIndexStart);
    var post = current.substr(((selectedIndexEnd < current.length) ? selectedIndexEnd : current.length ), current.length);

    current = pre + insertContent + ":" + selection + ":" + insertContent + post;
    jQuery("#wplc_admin_chatmsg").val(current);

    checkSelection = true;
}