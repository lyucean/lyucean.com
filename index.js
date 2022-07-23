var offcanvas_el = document.getElementById('leftMobileMenu')
var offcanvas = new bootstrap.Offcanvas(offcanvas_el)
var nav_links = offcanvas_el.querySelectorAll('.nav-link')

for (var i = 0; i < nav_links.length; i++) {
  nav_links[i].onclick = function () {
    offcanvas.hide()
    setTimeout(function () {
      window.location = window.location.href
    }, 100)
  }
}

