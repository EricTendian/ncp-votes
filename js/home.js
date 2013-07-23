$('a.btn-primary').click(function() {
  if ($(this).hasClass('disabled')) {alert('Voting will open at 3:30pm on Friday.'); return;}
  //load the voting box modal
  $('#voteModal').modal({
    backdrop: 'static',
    keyboard: false
  });
  //focus on the username for quick access
  $('#voteModal').on('shown', function () {
      $('input[name="username"]').focus();
  });
});
$(".form-signin").submit(function(event) {
  event.preventDefault(); //use AJAX instead of POST
  var $form = $(this),
      user = $form.find('input[name="username"]').val(), //get the username
      pwd = $form.find('input[name="password"]').val(), //get the password
      url = $form.attr('action'); //get the destination url
  var posting = $.post(url, {username: user, password: pwd}); //submit the login request
  $('button.btn-block').addClass('disabled').text('Loading...');
  posting.done(function(data) {
    if ($('<div>'+data+'</div>').find('form#ballot').length) $('.modal-body').empty().append(data); //it returned with the ballot, so clear the body and insert the ballot
    else {
      $( '.form-signin input[name="password"]' ).val(''); //clear the password as it is probably wrong
      $( '.form-signin input[name="password"]' ).focus(); //focus on the password for re-entry
      if ($('#loginError').length) $('#loginError').remove(); //remove any old login errors
      $( '.modal-body' ).prepend( '<div id="loginError" class="alert alert-error fade" data-alert="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Error:</strong> '+data+'</div>' ); //add the new login error
      $('button.btn-block').removeClass('disabled').text('Sign In');
      window.setTimeout(function () {
        $( '#loginError' ).addClass('in');
      }, 200); //make it fade in over 200ms
    }
  });
});