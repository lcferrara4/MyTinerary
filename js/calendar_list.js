$(document).on("click", ".openMyModal", function() {
    var myCalID = $(this).data('id');
    $(".modal-body #CalendarID").val( myCalID );
    console.log(myCalID);
});
$(document).on("click", ".openDeleteModal", function() {
    console.log("in delete function");
    var CalID = $(this).data('id');
    console.log(CalID);
    $(".modal-footer #DCalendarID").val( CalID );
    console.log("deleting"); 
});
