<?php
	include_once('libs/ajax.class.php');
	$ajax = new Ajax();
	$ajax->init();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>
			GitHub Code challenge
		</title>
		<link rel="icon" type="image/x-icon" href="https://assets-cdn.github.com/favicon.ico">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<style>
			.spin{
				text-align: center;
				padding: 15px 0;
				display: none;
			}
			#user-wrap{
				margin: 30px 0 60px 0;
			}
			#user-wrap, #main-user, .media-list {
				display: none;
			}
			#main-user .avatar, .media-object{
				box-shadow: 0 0 3px rgba(0,0,0,.5);
			}			
			#main-user .login{
				font-size: 24px;
			}
			.media-list{
				margin-top: 30px;
			}
			.media-object{
				width: 30px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="page-header">
			  <h1>GitHub <small>Code challenge</small></h1>
			</div>
			<form class="form-inline" method="post" action="/">
				<input name="username" type="text" class="form-control" required placeholder="GitHub Username">
				<button type="submit" class="btn btn-primary">Search</button>
			</form>
			<hr/>
			<div class="spin" id="spin-main">
				<img src="/spin.gif" alt="..." />
			</div>
			
			<div id="user-wrap">

				<div id="main-user" class="row">
					<div class="col-sm-2">
						<a href="" class="url">
							<img class="avatar img-responsive" src="" alt="..." />
						</a>
					</div>
					<div class="col-sm-10">
						<p><a href="" class="login url"></a></p>
						<p class="name">Name: <i></i></p>
						<p class="location">Location: <i></i></p>
						<p class="followers">Followers: <i></i></p>
					</div>
				</div>
				
				<ul class="media-list"></ul>
				<button id="show-more" class="btn btn-default btn-block">Show more</button>
				<div class="spin" id="spin-list">
					<img src="/spin.gif" alt="..." />
				</div>
				
			</div>			
		</div>
		<script>
			
			var _total_followers = 0;
			
			var _request = function(method, args, onSuccess, spin){
				if(spin) spin.show();
				$.ajax({
					url: '/',
					type: 'post',
					dataType: 'json',
					data: { method:method, args:args },
				})
				.done(function(res){
					if(typeof onSuccess == 'function')
						onSuccess(res);
				})
				.fail(function(xhr, textStatus, errorThrown){
				    try{
						var res = JSON.parse(xhr.responseText);
						if(!res._error) throw true;
						else alert(res._error)
					} catch (err) {
						alert('Invalid server response...')
					}
				})
				.always(function(){
					if(spin) spin.hide();
				});
			}
			
			var _get_followers = function(){

				var mediaList = $('.media-list'), 
					showMore = $('#show-more'),
					username = mediaList.data('username'),
					page = parseInt(mediaList.data('page')) || 0;
					
				if(!username || !page) return;	
				else if(page == 1) mediaList.html('');
				
				showMore.hide()
				_request('get_users_followers', { username:username, page: page }, function(users){	
					users.forEach(function(u){
						var li = $('<li/>');
						li.addClass('media');
						li.append('<div class="media-left"><a href="'+u.html_url+'"><img class="media-object" src="'+u.avatar_url+'"></a></div>')
						li.append('<div class="media-body"><h4 class="media-heading">'+u.login+'</h4></div>')							
						mediaList.append(li);
						_total_followers--;
					});
					mediaList.show();
					mediaList.data('page', page + 1);
					if(_total_followers > 0) showMore.show();
				}, $('#spin-list'))
			}
			
			
			$(document).ready(function(){

				var userWrap = $('#user-wrap'), 
					mainUser = $('#main-user'), 
					mediaList = $('.media-list'); 
				
				
				$('form').submit(function(e){
					e.preventDefault();
					userWrap.hide();		
					mainUser.hide();		
					_request('get_users', { username: $('input').val().trim() }, function(user){
						mainUser.find('.avatar').attr('src', user.avatar_url);
						mainUser.find('.url').attr('href', user.html_url);
						mainUser.find('.login').text(user.login);
						mainUser.find('.name i').text(user.name);
						mainUser.find('.location i').text(user.location);
						mainUser.find('.followers i').text(user.followers);
						userWrap.show();
						mainUser.show();
						
						_total_followers = user.followers;
						mediaList.data('username', user.login)
						mediaList.data('page', 1)
						_get_followers();
					}, $('#spin-main'))
					
				});

				$('#show-more').click(function(e){
					e.preventDefault();
					_get_followers();
				});
			})
		</script>
	</body>
</html>