/**
 * Generate a new password, which may then be copied to the form
 * with suggestPasswordCopy().
 *
 * Function copied from phpMyAdmin
 *
 * @param   string   name of the field that should be filled
 *
 * @return  string   the generated password
 */
function suggestPassword(fieldName) {
    // restrict the password to just letters and numbers to avoid problems:
    // "editors and viewers regard the password as multiple words and
    // things like double click no longer work"
    var pwchars = "23456789abcdefhjmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWYXZ";
    var passwordlength = 12;    // do we want that to be dynamic?  no, keep it simple :)
    var passwd = '';

    for ( i = 0; i < passwordlength; i++ ) {
        passwd += pwchars.charAt( Math.floor( Math.random() * pwchars.length ) )
    }

    if((fieldName != '') && (field = document.getElementById(fieldName))) {
        field.value = passwd;
    }
    return passwd;
}


/**
 * Copy the generated password (or anything in the field) to the form
 *
 * Function copied from phpMyAdmin
 *
 * @param   string   name of the field to copy the password from
 * @param   string   name of the field to copy the password to
 * @param   string   name of the field to copy the password to
 *
 * @return  boolean  always true
 */
function copyPassword(sourceFieldName, target1FieldName, target2FieldName) {
    document.getElementById(target1FieldName).value = document.getElementById(sourceFieldName).value;
    document.getElementById(target2FieldName).value = document.getElementById(sourceFieldName).value;
    return true;
}


/**
 * Enables forward-destination field and selection box only if forwarding is enabled
 */
function fwform() {
    document.getElementById('forward').disabled = !document.getElementById('on_forward').checked;
    if (document.getElementById('forwardmenu') != null) { //userchange.php has no box
      document.getElementById('forwardmenu').disabled = !document.getElementById('on_forward').checked;
    }
    return true;
}


/**
 * If item on forwarding destination is selected from a list of destinations
 * the forwarding text box is updated
 */
function boxadd() {
    var exstring = document.getElementById('forward').value;
    var box = document.getElementById('forwardmenu');
    var selectitem = box.options[box.selectedIndex].value;
    if (!exstring.match(/\S/)) {
      document.getElementById('forward').value=selectitem;
    } else {
      document.getElementById('forward').value += "," + selectitem;
    }
}


/**
 * Add event listener
 */
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('pwcopy') != null) {
      document.getElementById('pwcopy').addEventListener('click', function() { copyPassword('suggest', 'clear', 'vclear')});
    }
    if (document.getElementById('pwgenerate') != null) {
      document.getElementById('pwgenerate').addEventListener('click', function() { suggestPassword('suggest') });
    }
    if (document.getElementById('on_forward') != null) {
      document.getElementById('on_forward').addEventListener('change', function() { fwform() });
    }
    if (document.getElementById('forwardmenu') != null) {
    document.getElementById('forwardmenu').addEventListener('change', function() { boxadd() });
    }

    // settings at page load
    if (document.getElementById('forward') != null) {
      fwform();
    }
});
