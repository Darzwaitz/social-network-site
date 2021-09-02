// add dates to login form on homepage
// years
for (i = new Date().getFullYear(); i > 1920; i--) {
    // console.log(i);
    $("#years").append($('<option/>').val(i).html(i));
}
// months
for (i = 1; i < 13; i++) {
    $('#months').append($('<option/>').val(i).html(i));
}
updateNumberOfDays();

function updateNumberOfDays() {
    $('#days').html('');
    month = $('#months').val();
    year = $('#years').val();
    days = daysInMonth(month, year);

    for (i = 1; i < days + 1; i++) {
        $('#days').append($('<option/>').val(i).html(i));
    }
}
// gets last day of following month, go back 1 - returns number of days from previous month 
function daysInMonth(month, year) {
    return new Date(year, month, 0).getDate()
}

$('#years,#months').on('change', function () {
    updateNumberOfDays()
});
