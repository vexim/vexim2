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
    var pwchars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
    var passwordlength = 10;    // do we want that to be dynamic?  no, keep it simple :)
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

