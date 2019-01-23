var modal = document.querySelector('.locations-modal');
var modalName = document.querySelector('.locations-modal .locations-modal-content h3');
var modalAreas = document.querySelector('.locations-modal .locations-modal-content p.areas');

function toggle_modal(element) {
    if ( element != undefined ) {
        modalName.innerText = element.getAttribute('l-name');
        modalAreas.innerText = element.getAttribute('l-areas');
    }
    modal.classList.toggle('show-modal');
}

function window_on_click(event) {
    if ( event.target === modal ) { toggle_modal(); }
}

window.addEventListener('click', window_on_click);

var stateFilter = document.querySelector('#filter-state');
function filter_state() {
    if ( stateFilter.value != 'all-states' ) {
        var elements = document.querySelectorAll( '.list-locations > .container > div.locations-listing:not( .' + stateFilter.value + ' )' );
        elements.forEach(function(e) { e.style.display = 'none'; });
        document.querySelector('.list-locations > .container > div.' + stateFilter.value).style.display = 'flex';
    } else {
        var elements = document.querySelectorAll( '.list-locations > .container > div.locations-listing' );
        elements.forEach(function(e) { e.style.display = 'flex'; });
    }
}
if ( !!stateFilter ) { stateFilter.addEventListener('change', filter_state); }