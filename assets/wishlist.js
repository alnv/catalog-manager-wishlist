document.addEventListener( 'DOMContentLoaded' , function() {
    if ( 'content' in document.createElement( 'template' ) ) {
        var arrWishlist = document.querySelectorAll( '.wishlist-form' );
        if ( !arrWishlist.length ) return null;
        for ( var i = 0; i < arrWishlist.length; i++ ) {
            setListener( arrWishlist[i] );
        }
    }
});

function setListener( objWishlist ) {

    var objAddForm = objWishlist.querySelector( 'form.form-add .submit' );
    var objDeleteForm = objWishlist.querySelector( 'form.form-delete .submit' );
    if (objAddForm) {
        objAddForm.addEventListener( 'click', addAction, true );
    }
    if (objDeleteForm) {
        objDeleteForm.addEventListener( 'click', deleteAction, true );
    }
}

function addAction( objEvent ) {

    objEvent.preventDefault();
    var strAmount = 1;
    var strId = this.form.wishlist_id.value;
    var strType = this.form.wishlist_type.value;
    this.form.parentNode.classList.add( 'loading' );
    if ( this.form.wishlist_amount ) {
        strAmount = this.form.wishlist_amount.value;
    }
    var strUrl = location.protocol +'//' + location.host + location.pathname + '?'
        + 'wishlist_id=' + strId
        + '&wishlist_type=' + strType
        + '&wishlist_amount=' + strAmount
        + '&wishlist_ajax=1';
    sendWishRequest( strUrl );

    return false;
}

function deleteAction( objEvent ) {

    objEvent.preventDefault();
    var strId = this.form.wishlist_id.value;
    var strType = this.form.wishlist_type.value;
    var strTable = this.form.wishlist_table.value;
    this.form.parentNode.classList.add( 'loading' );
    var strUrl = location.protocol +'//' + location.host + location.pathname + '?'
        + 'wishlist_id=' + strId
        + '&wishlist_type=' + strType
        + '&wishlist_table=' + strTable
        + '&wishlist_ajax=1';
    sendWishRequest( strUrl );

    return false;
}

function sendWishRequest( strUrl ) {

    var objXHR = new XMLHttpRequest();
    objXHR.onload = function() {
        if (objXHR.status === 200) {
            if (!objXHR.responseText) return null;
            var objResult = JSON.parse(objXHR.responseText);
            if (objResult && typeof objResult !== 'undefined') {
                var arrOldWishlists = document.querySelectorAll('.id_' + objResult.id);
                for (var i=0;i<arrOldWishlists.length;i++) {
                    var objOldWishlist = arrOldWishlists[i];
                    if (!objOldWishlist) {
                        continue;
                    }
                    var objTemplate = document.createElement('template');
                    objTemplate.innerHTML = objResult.reload;
                    var objFreshWishlist = objTemplate.content.querySelector('.wishlist-form');
                    objOldWishlist.parentNode.replaceChild(objFreshWishlist, objOldWishlist);
                    setListener(objFreshWishlist);
                }
            }
        }
    };

    objXHR.open('GET', strUrl, true);
    objXHR.send();
}