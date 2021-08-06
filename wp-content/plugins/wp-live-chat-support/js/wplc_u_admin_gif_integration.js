/**
 * wplc_u_admin_gif_integration.js add events that let the user insert GIFs by typing a text command 
 * on the chatbox message input. For instance:
 * 
 *   - The user can add a random GIF via a forward slash command like we do on Slack "/gif happy"
 */

var WPLC_GIF_I_DEBUG = false;

function WPLC_U_Admin_GIF_Integration() {
	this._initialized = false;
}

WPLC_U_Admin_GIF_Integration.prototype = {

    GIF_INTEGRATION_COMMAND_PATTERN: /\/gif/,
    GIF_WORD_PATTERN: "/gif",
    GIF_EXTENSION_PATTERN: /\.(gif)\b/,

    SELECTORS : {
        CHATBOX_EDIT_MSG_INPUT: "#wplc_chatmsg, #inputMessage",
        GIF_SEARCH_BOX: "#wplc_gif_integration_search_box",
        GIF_INSIDE_GIF_SEARCH_BOX: "#wplc_gif_integration_search_box .gif-sb-body .gifs-container .gif-img",
        GIF_SEARCH_BOX_INPUT_SEARCH_TERMS: "#wplc_gif_integration_search_box > div.gif-sb-header > .gif-sb-search-term",
        GIF_SEARCH_BOX_BTN_CONFIRM_SEARCH: "#wplc_gif_integration_search_box > div.gif-sb-header > .gif-sb-confirm-search",
        GIF_SEARCH_BOX_BTN_CLOSE_SEARCH: "#wplc_gif_integration_search_box > div.gif-sb-header > .gif-sb-close",
        GIF_SEARCH_BOX_BODY: ".gif-sb-body",
        GIF_SEARCH_BOX_GIFS_CONTAINER: ".gif-sb-body .gifs-container",
        GIF_SEARCH_BOX_BODY_GIFS: ".gif-sb-body .gifs-container .gif-img",
        GIF_SEARCH_BOX_LOADER: "#wplc_gif_integration_search_box .gif-sb-loader"
    },
    
    CLASSES: {
        GIF_SEARCH_BOX: "wplc-gif-integration-search-box",
        HIDDEN: "hidden",
        SHOW: "show",
        SELECTED_GIF: "selected-gif",
        GIF_IMG: "gif-img"
    },

    IDXS: {
        GIPHY_API: "1",
        TENOR_API: "2",
    },

    MSGS: {
        INSTRUCTIONS_GIF_SEARCH: "To search for a GIF write '/gif your_search' or use the search bar for more results"
    },

    IMAGES: {
        ICO_CONFIRM_SEARCH: "Search",
        ICO_CLOSE_SEARCH: "X",
    },

    gif_settings: undefined,
    chosen_gif: undefined,
    previous_search_term: "",
    current_search_term: "",
    canceled_search_request: false,
    
    // Getters and Setters
    getClassSelector: function(className) {
        try {
            return "." + className;
        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to getSelectorFromClass " + err);
            }
        }
    },

    getIdFromSelector: function(selector) {
        try {
            return selector.replace("#", "");
        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to getId " + err);
            }
        }
    },

    // Other methods
    /**
     * Add the selected GIF URL to the message that is going to be sent
     */
    applyChosenGifToNewMessage: function() {
        try {

            if (typeof(this.chosen_gif) !== "undefined") {
                jQuery(this.SELECTORS.CHATBOX_EDIT_MSG_INPUT).val(this.chosen_gif);
            }
        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to applyChosenGifToNewMessage " + err);
            }
        }
    },

    fetchGifResultsFromGiphy: function(topGifs) {
        try {
            
            if (typeof(topGifs) !== "undefined") {

                for (var i = 0; i < topGifs.length; i++) {
                    var gifUrl = topGifs[i]["images"]["downsized_large"]["url"];
    
                    if (typeof(gifUrl) !== "undefined") {
                        var gifHtml = jQuery('<img/>');
                        gifHtml.attr('class', this.CLASSES.GIF_IMG);
                        gifHtml.attr('src', gifUrl);
    
                        jQuery(this.SELECTORS.GIF_SEARCH_BOX).find(this.SELECTORS.GIF_SEARCH_BOX_GIFS_CONTAINER).append(gifHtml);
                    }
                }
    
                if (this.chosen_gif === "") {
                    this.chosen_gif = topGifs[0]["images"]["downsized_large"]["url"];
                }
            }

        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to fetchGifResultsFromGiphy " + err);
            }
        }
    },

    fetchGifResultsFromTenor: function(topGifs) {
        try {

            if (typeof(topGifs) !== "undefined") {

                for (var i = 0; i < topGifs.length; i++) {
                    var gifUrl = topGifs[i]["media"][0]["gif"]["url"];

                    if (typeof(gifUrl) !== "undefined") {
                        var gifHtml = jQuery('<img/>');
                        gifHtml.attr('class', this.CLASSES.GIF_IMG);
                        gifHtml.attr('src', gifUrl);

                        jQuery(this.SELECTORS.GIF_SEARCH_BOX).find(this.SELECTORS.GIF_SEARCH_BOX_GIFS_CONTAINER).append(gifHtml);
                    }

                    if (this.chosen_gif === "") {
                        this.chosen_gif = topGifs[0]["media"][0]["gif"]["url"];
                    }
                }
            }

        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to fetchGifResultsFromTenor " + err);
            }
        }
    },

    gifSearch: function(searchTerm) {
        try {
            this.canceled_search_request = false;

            if (searchTerm.length > 1) {
                var ctx = this;
                ctx.chosen_gif = "";

                if (searchTerm !== "") {
                    jQuery(ctx.SELECTORS.GIF_SEARCH_BOX_LOADER).removeClass(ctx.CLASSES.HIDDEN);

                    var data = {
                        'action': 'wplc_search_gif',
                        'security': wplc_nonce,
                        'search_term': searchTerm
                    };
            
                    jQuery.post(wplc_ajaxurl, data, function(response) {
                        try {

                            if (!ctx.canceled_search_request && response !== "") {
                                var responseObjects = JSON.parse(response);
                                
                                if (typeof(responseObjects) !== "undefined") {

                                    switch(ctx.gif_settings.preferred_gif_provider) {
                                    
                                        case ctx.IDXS.GIPHY_API: {                                                                                
                                            var topGifs = responseObjects["data"];
                                            ctx.fetchGifResultsFromGiphy(topGifs);
                                            break;
                                        }
        
                                        case ctx.IDXS.TENOR_API: {
                                            var topGifs = responseObjects["results"];
                                            ctx.fetchGifResultsFromTenor(topGifs);
                                            break;
                                        }
                                    }
                                }

                                if (ctx.chosen_gif !== "") {
                                    ctx.applyChosenGifToNewMessage();
                                } else {
                                    jQuery(ctx.SELECTORS.CHATBOX_EDIT_MSG_INPUT).val("Please try again ...");
                                }
                            }

                            jQuery(ctx.SELECTORS.GIF_SEARCH_BOX_LOADER).addClass(ctx.CLASSES.HIDDEN);

                        } catch(err) {
                            if (WPLC_GIF_I_DEBUG) {
                                console.log("Warning: Failed to process GIF from provider " + err);
                            }
                        }
                    });
                }
            }

        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to gifSearch " + err);
            }
        }
    },

    cancelGifSearch: function() {
        try {
            this.canceled_search_request = true;

        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to cancelGifSearch " + err);
            }
        }
    },
    
    openGifSearchBox: function() {
        try {
            jQuery(this.SELECTORS.GIF_SEARCH_BOX).removeClass(this.CLASSES.HIDDEN);
            jQuery(this.SELECTORS.GIF_SEARCH_BOX).addClass(this.CLASSES.SHOW);
            jQuery(this.SELECTORS.GIF_SEARCH_BOX).find(this.SELECTORS.GIF_SEARCH_BOX_GIFS_CONTAINER).html("");

        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to openGifSearchBox " + err);
            }
        }
    },

    closeGifSearchBox: function() {
        try {
            jQuery(this.SELECTORS.GIF_SEARCH_BOX).removeClass(this.CLASSES.SHOW);
            jQuery(ctx.SELECTORS.GIF_SEARCH_BOX_LOADER).addClass(ctx.CLASSES.HIDDEN);

        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to closeGifSearchBox " + err);
            }
        }
    },

    getSearchTermFromNewMsgInput: function() {
        try {
            var currentMsg = jQuery(this.SELECTORS.CHATBOX_EDIT_MSG_INPUT).val();
            var gifCommandPattern = new RegExp(this.GIF_INTEGRATION_COMMAND_PATTERN);
            
 
            if (gifCommandPattern.test(currentMsg)) {
                this.openGifSearchBox();

                // Ensure the user inserted a complete word before starting the search
                var msgParts = currentMsg.split(" ");
                var searchTerm = "";
                
                for (var i = 0; i < msgParts.length; i++) {
                    var word = msgParts[i];

                    if (this.GIF_WORD_PATTERN === word) {
                        
                        if (i + 1 < msgParts.length) {
                            searchTerm = msgParts[i + 1];
                            
                            if (searchTerm.length > 0) {
                                return searchTerm;
                            }
                        }
                    }
                }
            } else {
                this.closeGifSearchBox();
            }
        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to getSearchTermFromNewMsgInput " + err);
            }
        }

        return "";
    },

    identifyGifCommands: function() {
        try {
            this.current_search_term = this.getSearchTermFromNewMsgInput();

            if (this.current_search_term !== "") {

                // Lock the search until the search term is different from the previous one
                if (this.previous_search_term !== this.current_search_term) {
                    this.previous_search_term = this.current_search_term;
                    this.gifSearch(this.current_search_term);
                }
            } else {
                this.cancelGifSearch();
            }

        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to identifyGifCommands " + err);
            }
        }
    },

    createGifSearchBox: function() {
        try {
            var searchBox = jQuery(this.SELECTORS.GIF_SEARCH_BOX);
            var searchBoxContent = jQuery(searchBox).html();
            
            if (typeof(searchBoxContent) === "undefined" || typeof(jQuery(searchBox).html()) === "") {
                // Build the template of the Gif Search box
                var searchBoxHtml = "<div id="+ this.getIdFromSelector(this.SELECTORS.GIF_SEARCH_BOX) +" class='" + this.CLASSES.GIF_SEARCH_BOX + " " + this.CLASSES.HIDDEN + "'>";
                searchBoxHtml += "<div class='gif-sb-header'>";
                searchBoxHtml += "<input class='gif-sb-search-term' placeholder='Search...'>";
                //searchBoxHtml += "<a target='_blank' class='gif-sb-confirm-search'><i class='fa fa-search'></i></a>";
                searchBoxHtml += "<a target='_blank' class='gif-sb-close'><i>"+ this.IMAGES.ICO_CLOSE_SEARCH +"</i></a>";
                searchBoxHtml += "</div>";
                searchBoxHtml += "<div class='gif-sb-body'>";
                searchBoxHtml += "<div class='gifs-container'></div>";
                searchBoxHtml += "<div class='gif-sb-loader hidden'>Loading...</div>";
                searchBoxHtml += "</div>";
                searchBoxHtml += "<div class='gif-sb-footer'>";
                searchBoxHtml += this.MSGS.INSTRUCTIONS_GIF_SEARCH;
                searchBoxHtml += "</div>";
                searchBoxHtml += "</div>";

                // Inject the search box into the DOM
                jQuery(this.SELECTORS.CHATBOX_EDIT_MSG_INPUT).parent().parent().prepend(searchBoxHtml);
            }

        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to closeGifSearchBox " + err);
            }
        }
    },

    selectGif: function(gifObj) {
        try {
            var ctx = this;

            jQuery(ctx.SELECTORS.GIF_SEARCH_BOX).find(ctx.SELECTORS.GIF_SEARCH_BOX_BODY_GIFS).removeClass(ctx.CLASSES.SELECTED_GIF);
            jQuery(gifObj).addClass(ctx.CLASSES.SELECTED_GIF);

            var selectedGifUrl = jQuery(gifObj).attr("src");
            ctx.chosen_gif = selectedGifUrl;

            ctx.applyChosenGifToNewMessage();

            setTimeout(function() {
                ctx.closeGifSearchBox();
            }, 500);
        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to selectGif " + err);
            }
        }
    },

    refinedSearch: function() {
        try {
            this.previous_search_term = jQuery(this.SELECTORS.GIF_SEARCH_BOX_INPUT_SEARCH_TERMS).val();
            this.current_search_term = this.previous_search_term;
            this.gifSearch(this.current_search_term);

        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to refinedSearch " + err);
            }
        }
    },

    initEvents: function() {
		try {
            var ctx = this;

            // Monitor the message being edited in order to detect a GIF integration command
            jQuery(document).on("keydown", ctx.SELECTORS.CHATBOX_EDIT_MSG_INPUT, function() {
                ctx.createGifSearchBox();
                ctx.identifyGifCommands();
            });

            // Monitor for the ENTER key press on the search input
            jQuery(document).on("keydown", ctx.SELECTORS.GIF_SEARCH_BOX_INPUT_SEARCH_TERMS, function(event) {
                if (event.which === 13) {
                    event.preventDefault();
                    ctx.refinedSearch();
                }

            });

            // Execute a refined search with a text input from the GIF search box
            jQuery(document).on("click", ctx.SELECTORS.GIF_SEARCH_BOX_BTN_CONFIRM_SEARCH, function() {
                ctx.refinedSearch();
            });

            // Select a GIF from the search results within the GIf search box
            jQuery(document).on("click", ctx.SELECTORS.GIF_INSIDE_GIF_SEARCH_BOX, function() {
                ctx.selectGif(this);
            });

            // Close the Gif Search Box
            jQuery(document).on("click", ctx.SELECTORS.GIF_SEARCH_BOX_BTN_CLOSE_SEARCH, function() {
                ctx.closeGifSearchBox();
            });

		} catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to initEvents " + err);
            }
		}
	},
    
    initVars: function() {
        try {
            
            if (typeof(wplc_gif_integration_details) !== "undefined") {
                this.gif_settings = wplc_gif_integration_details;
            }
        } catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to initVars " + err);
            }
        }
    },

	init: function() {
		try {
            var ctx = this;

            jQuery(function() {

                if (typeof(wplc_gif_integration_details) !== "undefined") {
                    ctx._initialized = true;
                    ctx.initVars();

                    if (typeof(ctx.gif_settings.is_gif_integration_enabled) !== "undefined") {

                        if (ctx.gif_settings.is_gif_integration_enabled) {
                            ctx.initEvents();
                        }
                    }
                }
			});
		} catch(err) {
            if (WPLC_GIF_I_DEBUG) {
                console.log("Warning: Failed to init " + err);
            }
		}
	}

}

var wplcGifIntegration = new WPLC_U_Admin_GIF_Integration();
wplcGifIntegration.init();