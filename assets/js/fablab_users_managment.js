// Most function has user object as argument. Example object: 
// 	var user = {
//		"id": 1,
//		"name": Test Person,
//		"banned": 0 // or 1 if banned
//	};

var php_url = {
	// Urls to php functions
	"save": "save_user_data",
	"quota": "set_quota",
	"ban": "ban_user",
	"unban": "unban_user",
	"rm": "delete_user"
};

function disableForm (yes) {
	// Disable UI elements on save, ban etc.
	if (yes) {
		$("#user_content :input").attr('disabled', true);
		$("#tabs > li").addClass('disabled');
		$("#user_content a").addClass('disabled');
		$("#search_people").attr('disabled', true);
		$("#search_results a").attr('style', "pointer-events: none; cursor: default;");
	} else {
		$('#user_content :input').removeAttr('disabled');
		$('#tabs > li').removeClass('disabled');
		$('#user_content a').removeClass('disabled');
		$('#search_people').removeAttr('disabled');
		$("#search_results a").removeAttr('style');
	}
}

function saveData(user) {

	// Send form to controller 
	var post_data = {
		'user_id': user.id,
		'email': $('#email_input').val(),
		'username': $('#name_input').val(),
		'surname': $('#surname_input').val(),
		'phone_number': $('#phone_number_input').val(),
		'address_street': $('#address_street_input').val(),
		'address_postal_code': $('#address_postal_code_input').val(),
		'student_number': $('#student_number_input').val(),
		'groups' : $('#group_form').serialize(),
		'levels' : $('.inner_rating :input').serialize()
	};
	disableForm(true);

	$.ajax({
		type: "POST",
		url: php_url.save,
		data: post_data,
		success: function(data) {
			disableForm(false);
			if (data.length > 0) {
				var message = $.parseJSON(data);
				if (message.success == 1) {
					user.name = post_data.surname;
					alerter("success", "User " + user.name + " data <strong>saved</strong>!");
					$("#search_results > .active").text(user.name);
				} else {
					$.each(message.errors, function(index, value) {
						alerter("warning", value);
					});
				}
			}
		}
	}); 
}

function setQuota(user, amount) {
	// set user quota, sends -1 if amount is not defined
	amount = typeof amount !== 'undefined' ? amount : -1;
	disableForm(true);
	var post_data = {
		'user_id': user.id,
		'amount': amount
	};
	
	$.ajax({
		type: "POST",
		url: php_url.quota,
		data: post_data,
		success: function(data) {
			disableForm(false);
			if (data.length > 0) {
				var message = $.parseJSON(data);
				if (message.success == 1) {
					$("#quota_badge").text(message.amount);
					$("#quota_badge").addClass("animated pulse").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', 
						function() {
							$(this).removeClass("animated pulse");
						});
					alerter("info", "User " + user.name + " <strong>quota</strong> updated!"); 
				} else {
					alerter("warning", "<strong>Error</strong> while updating user " + user.name + " <strong>quota</strong>!"); 
				}

			}
		}
	}); 
}

function banUser(user) {
	// Ban/unban users
	disableForm(true);
	var post_data = {
		'user_id': user.id
	};

	var post_url = "";
	if (user.banned == 1) {
		post_url = php_url.unban;
	} else {
		post_url = php_url.ban;
	}
	
	$.ajax({
		type: "POST",
		url: post_url,
		data: post_data,
		success: function(data) {
			disableForm(false);
			if (data.length > 0) {
				$("#ban_button").addClass("animated pulse").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', 
					function() {
						$(this).removeClass("animated pulse");
					});
				var $contents = $('#ban_button').contents();
				if (user.banned == 0) {
					user.banned = 1;
					$("#ban_button span").attr("class","glyphicon glyphicon-ok-circle");
					$contents[$contents.length - 1].nodeValue = ' Unban';
					alerter("info", "User " + user.name + " <strong>banned</strong>!"); 
				} else {
					user.banned = 0;
					$("#ban_button span").attr("class","glyphicon glyphicon-ban-circle");
					$contents[$contents.length - 1].nodeValue = ' Ban';
					alerter("info", "User " + user.name + " <strong>unbanned</strong>!"); 
				}

			}
		}
	}); 
}

function deleteUser(user) {
	// Delete user from db. 
	disableForm(true);
	var post_data = {
		'user_id': user.id
	};
	
	$.ajax({
		type: "POST",
		url: php_url.rm,
		data: post_data,
		success: function(data) {
			disableForm(false);
			if (data.length > 0) {
				$("#search_results > .active").remove();
				$("#user_content").addClass("animated fadeOut").one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', 
					function() {
						$( "#user_content" ).empty();
					});
				alerter("info", "User " + user.name + " <strong>deleted</strong>!"); 
			}
		}
	}); 
}

function alerter(alert_type, alert_message) {
	// alerter function for on-screen alerts
	$.notify({
	// options
	message: alert_message 
	},{
		// settings
		type: alert_type,
		animate: {
			enter: 'animated fadeInDown',
			exit: 'animated fadeOutUp'
		}
	});
}
