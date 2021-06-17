jQuery(function($) {
    if( $('#wpforms-form-119').length > 0 ) {
        const queryString = window.location.search;
        if( queryString ) {
            const urlParams = new URLSearchParams(queryString);
            const course = urlParams.get('course');
            if ( $('[course-id="' + course + '"]').length > 0 ) {
                $('input[value="' + course + '"]').prop('checked', true).trigger('change');
            }else {
                $('.entry-row .entry-column:first-child').html("")
            }
            
        }else {
            $('.entry-row .entry-column:first-child').html("")
        }
    } else {
        
    }
})