var wplcApiUrls = {
	blogFeedUrl: 'https://wp-livechat.com/wp-json/wp/v2/posts',
  visitorURL: wplc_get_random_server() + '/api/v1/total-visitors-online?api_key='+nifty_api_key
}

function getTotalVisitors() {
	jQuery.getJSON( wplcApiUrls.visitorURL, function( data ) {
		jQuery('#totalVisitors').html( data );
	});
}

function getBlogPosts() {
	jQuery.getJSON( wplcApiUrls.blogFeedUrl, function( data ) {
		
		const limit = 5;
		let output = '';
		
		for (let i in data){
			if(i >= limit){
				continue;
			}

			const post = data[i];
			const html = `<div class='wplc_post'>
							<div class='wplc_post_title'>${post.title.rendered}</div>
							<p class='wplc_post_excerpt'>${post.excerpt.rendered}</p>
							<div class='wplc_post_readmore'>
								<a href='${post.link}' target='_BLANK' title='${post.title.rendered}'>Read More</a>
							</div>
						</div>`;
			output += html;
    }
    
		jQuery('#wplc_blog_posts').html( output );
		
	});
}

jQuery(document).ready(function($){
	getBlogPosts();
	getTotalVisitors();
});