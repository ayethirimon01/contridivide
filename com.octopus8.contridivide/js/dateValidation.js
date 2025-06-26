// CRM.$(function($) {
//     console.log("date validation file is loaded");
//   // Watch for all datepickers after the DOM and CiviCRM load
//   setTimeout(function () {
//     $('.hasDatepicker').each(function () {
//       $(this).datepicker('option', 'maxDate', 0);
//       console.log('Applied maxDate 0 to:', this);
//     });
//   }, 500); // Slight delay ensures CiviCRM datepicker is initialized
// });

CRM.$(function($) {
  console.log("date validation file is loaded");

  setTimeout(function () {
    // Find the hidden actual input by ID
    var hiddenInput = $('#receive_date');

    if (hiddenInput.length) {
      // Find the visible datepicker input inside the same wrapper
      var datepickerInput = hiddenInput.siblings('input.hasDatepicker');

      if (datepickerInput.length) {
        datepickerInput.datepicker('option', 'maxDate', 0);
        console.log('maxDate 0 applied to:', datepickerInput[0]);
      } else {
        console.warn('Visible datepicker input not found for receive_date');
      }
    } else {
      console.warn('Hidden receive_date input not found');
    }
  }, 500); // Delay to wait for CiviCRM datepicker to be initialized
});

