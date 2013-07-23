//change button text to void
$('#voteModal .modal-footer button').text('Void Ballot & Exit');
//disable checkboxes if max number of votes is reached
$('.position input[type="checkbox"]').click(function(event) {
  //algorithm: goes up to position div and then enables or disables input elements using find()
  var checked = $(this).closest('.position').find('input:checked').length; //get total number of checkboxes
  if (checked==$(this).closest('.position').attr('reqvotes')) $(this).closest('.position').find('input:not(:checked)').prop('disabled', true); //disable checkboxes if max num of votes is reached
  else if (checked<$(this).closest('.position').attr('reqvotes')) $(this).closest('.position').find('input:not(:checked)').prop('disabled', false); //re-enable checkboxes if votes less than max
});

$('.modal-footer button.btn-danger').click(function(event) {
  event.preventDefault();
  //ask for confirmation before closing
  bootbox.confirm("Do you really want to void your ballot? Your vote will <em>not</em> be counted.", function(result) {
    if (result) {
      //void session and close window
      window.close();
    } else {
      $('#voteModal').modal({
        backdrop: 'static',
        keyboard: false
      }); //restore the modal
    }
  });
});

//run form validation and submit AJAX when submit button clicked
$("form#ballot").submit(function(event) {
  event.preventDefault();
  var $form = $("form#ballot"),
      url = $form.attr('action'); //form var to make referencing the form easier
  $('.position .alert-error').remove(); //remove all previous errors
  //iterate over positions
  $form.find('.position').each(function() {
    var votes = 0; //total number of candidates selected
    $(this).find('input').each(function() {
      if ($(this).is(':checked')) votes++; //add one vote
    });
    //if votes less than required number of votes, highlight the field
    if (votes<$(this).attr('reqvotes')/* || votes>$(this).attr('reqvotes')*/) {
      //show error message, but make it fade into view
      if (votes<$(this).attr('reqvotes')) $(this).closest('.position').append('<div class="alert alert-error fade" data-alert="alert">You must fill out this field to submit your ballot.</div>');
      else {
        $(this).closest('.position').find('input').prop('checked', false).prop('disabled', false);
        $(this).closest('.position').append('<div class="alert alert-error fade" data-alert="alert">Please select your choices again. Suspicious activity has been reported.</div>');
      }
      if ($(this).closest('.position').is(':first-child')) $(this).closest('.position').css('padding', '0 0 0 0'); //we no longer need padding because it is the top element and the alert is added
      else $(this).closest('.position').css('padding', '10px 0 0 0'); //adjust padding for alert
    }
  });
  if ($('.position .alert-error').length>0) {
    window.setTimeout(function () {
      $('.position .alert-error').addClass('in');
    }, 200); //fade errors into view over 200ms
    return;
  }
  //$('#voteModal').modal('hide'); //we are going to replace this with confirmation prompt
  //$('#voteModal').on('hidden', function () {}); //wait for it to be hidden before continuing
  bootbox.confirm("Are you sure? Your vote cannot be changed.", function(result) {
    if (result) {
      //the user is sure so we will submit now
      var ballot = {}; //data we are going to submit, JSON array
      $form.find('.position').each(function() {
        var selection = 1; //to prevent variable collision
        $(this).find('input').each(function() {
          if ($(this).is(':checked')) {
            ballot[$(this).attr('name')+':'+selection] = $(this).val(); // ballot[position1] = candidateid
            selection++; //for additional selections
          }
        });
      });
      var posting = $.post(url, ballot); //submit data via POST
      $('button.btn-block').addClass('disabled').text('Loading...');
      posting.done(function(result) {
        result = $.parseJSON(result);
        if (result['code']=='200') {
          $('#voteModal .modal-body').empty().append(result['data']);
          $('#voteModal .modal-footer button').text('Close & Exit').removeClass('btn-danger').addClass('btn-primary').unbind('click');
        } else if (result['code']=='409') {
          $('#voteModal .modal-body').empty().append('<div id="ballotError" class="alert alert-error fade" data-alert="alert"><strong>Error:</strong> '+result['data']+'</div>');
          $('#voteModal .modal-footer button').text('Exit').removeClass('btn-danger').addClass('btn-primary').unbind('click');
          window.setTimeout(function () {
            $('#ballotError').addClass('in');
          }, 200);
        } else {
          if ($('#ballotError').length) $('#ballotError').remove();
          $('#voteModal .modal-body').prepend('<div id="ballotError" class="alert alert-error fade" data-alert="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Error:</strong> '+result['data']+'</div>');
          $('button.btn-block').removeClass('disabled').text('Cast Ballot');
          window.setTimeout(function () {
            $('#ballotError').addClass('in');
          }, 200);
        }
      });
    }
  });
});