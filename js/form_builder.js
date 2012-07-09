$( function() {

      var action = 'formBuilder';      

      form_id = $('#form_id').val();

      if(form_id) {
          console.log('fart');
          $('#save_form').val('Update Form');
      }

      $('#rem_attr').attr('disabled','disabled');
      $('#add_attr').attr('disabled','disabled');

      $('#input_type').live('change', function() {

          input_type = $(this).val();

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {select_input_type: true, input_type: input_type, action: action},
              success: function(msg) {
                  $('#input_builder').html(msg);
                  $('#add_attr').removeAttr('disabled');
                  $('#add_input').show();
                  $('#update_input').hide();
              }
          });          

      });

      $('#clear_form').click( function() {

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {clear_form: true, form_id: form_id, action: action},
              success: function(msg) {
                  $('#form_built').html(null);
                  $('#add_input').show();
                  $('#update_input').hide();
              }
          });

      });

      $('#save_form').click( function() {
          var form_name   = $('#form_name').val();
          var wp_post_id  = $('#wp_post_id').val();
          var success_msg = $('#success_msg').val();

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {
                     save_form: true, 
                     form_name: form_name, 
                     wp_post_id: wp_post_id, 
                     success_msg: success_msg, 
                     form_id: form_id, 
                     action: action
              },
              success: function(msg) {
                  $('#notify').html(msg);
              }
          });

      });

      $('#add_attr').click( function() {

          var num = $('.attribute').length;
           
          console.log(num);
 
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {add_attr: true, input_type: 'attribute', action:action},
                success: function(msg) {
                    $('#input_builder').append(msg);
                }
            });        

          $('#rem_attr').removeAttr('disabled');
      });

      $('#rem_attr').click( function() {

          var num = $('.attribute').length;

          $('.attribute').eq(num-1).remove();

          $('#add_attr').removeAttr('disabled');

          if (num-1 == 0)
              $('#rem_attr').attr('disabled','disabled');

      });
});
