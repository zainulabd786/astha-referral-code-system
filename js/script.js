jQuery(document).ready(function ($) {
   // Confirm password validation
   $("#password_alert").hide();
   $("#confirm_password").on('keyup', function () {
      var password = $("#password").val();
      var confirmPassword = $("#confirm_password").val();
      if (password != confirmPassword) {
         $("#password_alert").show().html("Password does not match !");
         $("#as_register").attr('disabled', true)
      } else {
         $("#password_alert").hide();
         $("#as_register").attr('disabled', false)
      }
   });

   $.fn.dataTable.ext.search.push(
      function (settings, data, dataIndex) {
         var min = $('#min').datepicker('getDate');
         var max = $('#max').datepicker('getDate');
         var startDate = new Date(data[0]);
         if (min == null && max == null) return true;
         if (min == null && startDate <= max) return true;
         if (max == null && startDate >= min) return true;
         if (startDate <= max && startDate >= min) return true;
         return false;
      }
   );

   $('#min').datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
   $('#max').datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true });
   var table = $('#earnings_table').DataTable({
      dom: 'Bfrtip',
      buttons: [
         {
            extend: 'copyHtml5',
            title: 'Data export'
         },
         {
            extend: 'excelHtml5',
            title: 'Data export'
         },
         {
            extend: 'csvHtml5',
            title: 'Data export'
         }
      ]
   });

   // Event listener to the two range filtering inputs to redraw on input
   $('#min, #max').change(function () {
      table.draw();
   });

   // style user login form
   $("#loginform").find("input.input").addClass("form-control")
   $("#loginform").find("input.button").addClass("btn btn-primary")


});