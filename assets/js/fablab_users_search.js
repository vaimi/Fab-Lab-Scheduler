var search_urls = {
	"search": "user_search",
	"fetch": "fetch_user_data"
};

function ajaxSearch() {
	// Find users using ajax calls.
	var input_data = $('#search_people').val();

	var post_data = {
		'search_data': input_data, 
		'csrf_test_name': csrf_token
	};
	$('#search_results > a').remove();
	$('#search_results > .loader').remove();
	$('#search_results').append('<div class="loader">Loading...</div>');

	$.ajax({
		type: "POST",
		url: search_urls.search,
		data: post_data,
		success: function(data) {
			// return success
			if (data.length > 0) {
				$('#search_results > .loader').remove();
				$('#search_results').addClass('auto_list');
				$('#search_results').html(data);
			}
		}
	});
}

function fetchUserData(user_id) {
	// Fetch user data (ready made html)
	var post_data = {
		'user_id': user_id, 
		'csrf_test_name': csrf_token
	};
	$('#user_data_form').html('<div class="loader">Loading...</div>');

	$.ajax({
		type: "POST",
		url: search_urls.fetch,
		data: post_data,
		success: function(data) {
			// return success
			if (data.length > 0) {
				$('#user_data_form > .loader').remove();
				$('#user_data_form').html(data);
			}
		}
	});
}

// Update active list member on click
$(document).on('click', '#search_results a', function() {
	$("#search_results a").removeClass("active");
	$(this).addClass("active");
});