function getAllUrlParams(url){var queryString=url?url.split('?')[1]:window.location.search.slice(1);var obj={};if(queryString){queryString=queryString.split('#')[0];var arr=queryString.split('&');for(var i=0;i<arr.length;i++){var a=arr[i].split('=');var paramName=a[0];var paramValue=typeof(a[1])==='undefined'?!0:a[1];paramName=paramName.toLowerCase();if(typeof paramValue==='string')paramValue=paramValue.toLowerCase();if(paramName.match(/\[(\d+)?\]$/)){var key=paramName.replace(/\[(\d+)?\]/,'');if(!obj[key])obj[key]=[];if(paramName.match(/\[\d+\]$/)){var index=/\[(\d+)\]/.exec(paramName)[1];obj[key][index]=paramValue}else{obj[key].push(paramValue)}}else{if(!obj[paramName]){obj[paramName]=paramValue}else if(obj[paramName]&&typeof obj[paramName]==='string'){obj[paramName]=[obj[paramName]];obj[paramName].push(paramValue)}else{obj[paramName].push(paramValue)}}}}
return obj}
(function($) {
    $(document).ready(function(){
        $(".wcfm_dashboard_item_title").addClass("notranslate");
        $(".woocommerce-loop-product__title").addClass("notranslate");
        if($(".sr-advanced-search-filters").length) {
            $("#wcfmmp_store_country").change(function(){
                $(this).parents("form").submit();
            });
            $("#product_cat").change(function(){
                $(this).parents("form").submit();
            });
            $("#deptos .orderby").change(function(){
                $(this).parents("form").submit();
            });
        }
        
        $("li .price").each(function() {
            $(this).parent().after(this);
        });
        $(".woocs_amount").each( function() {
            var text = $(this).text();
            $(this).text(parseFloat(text)+" ฿");
        });
        if(typeof getAllUrlParams().s != "undefined" && $("#secondary").css('display') == 'none') {
            $("#secondary").hide();
            $(".storefront-sorting").hide();
            $("#toggleSecondary").show();
            $("#toggleSecondary").click((c)=>{
                $("#secondary").toggle();
                $(".storefront-sorting").toggle();
            });
        }
        $(".menu-categories a").click(function(){
            $("#category-list").toggle();
            $('body').toggleClass('b_overflow');
            window.history.pushState({},"", "/");
            return false;
        });
        window.onpopstate = function(e){
            if(e && $("#category-list").css('display') != 'none' ){
                $("#category-list").toggle();
                $('body').toggleClass('b_overflow');
                
            }
        };
        $( window ).resize(function() {
            $(".primary-navigation select:not(#width_tmp_select)").each(function(){
                $("#width_tmp_option").html($('option:selected',this).text()); 
                $(this).width($("#width_tmp_select").width()*1.13);
            });
        });
        $(window).on('load', function() {
            window.dispatchEvent(new Event('resize'));
        });
    });
 })(jQuery);