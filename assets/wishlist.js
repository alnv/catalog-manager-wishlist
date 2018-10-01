document.addEventListener( 'DOMContentLoaded' , function() {

    var arrWishlist = document.querySelectorAll( '.wishlist' );

    if ( !arrWishlist.length ) return null;

    for ( var i = 0; i < arrWishlist.length; i++ ) {

        setListener( arrWishlist[i] )
    }
});

function setListener( objWishlist ) {

    var objAddForm = objWishlist.querySelector( 'form.form-add .submit' );
    var objDeleteForm = objWishlist.querySelector( 'form.form-delete .submit' );

    if ( objAddForm ) {

        objAddForm.addEventListener( 'click', addAction, true );
    }

    if ( objDeleteForm ) {

        objDeleteForm.addEventListener( 'click', deleteAction, true );
    }
}

function addAction( objEvent ) {

    objEvent.preventDefault();

    var objXHttp = new XMLHttpRequest();
    var strId = this.form.wishlist_id.value;
    var strType = this.form.wishlist_type.value;
    var strAmount = this.form.wishlist_amount.value;

    var strUrl = window.location.href + '?'
        + 'wishlist_id=' + strId
        + '&wishlist_type=' + strType
        + '&wishlist_amount=' + strAmount
        + '&wishlist_ajax=1';

    objXHttp.onreadystatechange = function() {

        if ( this.readyState === 4 && this.status === 200 ) {

            var objState = JSON.parse( this.response );
            var objOldWishlist = document.getElementById( objState.id );
            var objTemplate = document.createElement( 'template' );

            objTemplate.innerHTML = objState.reload;

            var objFreshWishlist = objTemplate.content.querySelector('.wishlist');

            objOldWishlist.parentNode.replaceChild( objFreshWishlist, objOldWishlist );

            setListener( objFreshWishlist );
        }
    };

    objXHttp.open( 'GET', strUrl, true);
    objXHttp.send();

    return false;
}

function deleteAction( objEvent ) {

    objEvent.preventDefault();

    var objXHttp = new XMLHttpRequest();
    var strId = this.form.wishlist_id.value;
    var strType = this.form.wishlist_type.value;
    var strTable = this.form.wishlist_table.value;

    var strUrl = window.location.href + '?'
        + 'wishlist_id=' + strId
        + '&wishlist_type=' + strType
        + '&wishlist_table=' + strTable
        + '&wishlist_ajax=1';

    objXHttp.onreadystatechange = function() {

        if ( this.readyState === 4 && this.status === 200 ) {

            var objState = JSON.parse( this.response );
            var objOldWishlist = document.getElementById( objState.id );
            var objTemplate = document.createElement( 'template' );

            objTemplate.innerHTML = objState.reload;

            var objFreshWishlist = objTemplate.content.querySelector('.wishlist');

            objOldWishlist.parentNode.replaceChild( objFreshWishlist, objOldWishlist );

            setListener( objFreshWishlist );
        }
    };

    objXHttp.open( 'GET', strUrl, true);
    objXHttp.send();
}