var search_urls = {
	"search": "user_search",
	"fetch": "fetch_user_data"
};

function ajaxSearch() {
	// Find users using ajax calls.
	var input_data = $('#search_people').val();

	var post_data = {
		'search_data': input_data
	};

	$.ajax({
		type: "POST",
		url: search_urls.search,
		data: post_data,
		success: function(data) {
			// return success
			if (data.length > 0) {
				$('#search_results').addClass('auto_list');
				$('#search_results').html(data);
			}
		}
	});
}

function fetchUserData(user_id) {
	// Fetch user data (ready made html)
	var post_data = {
		'user_id': user_id
	};
	$('#user_data_form').addClass("animated fadeOut");
	$.ajax({
		type: "POST",
		url: search_urls.fetch,
		data: post_data,
		success: function(data) {
			// return success
			if (data.length > 0) {
				$('#user_data_form').removeClass("animated fadeOut");
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