// leftMobileMenu
var menuLeftMobile = document.getElementById('menuLeftMobile')
var offcanvas = new bootstrap.Offcanvas(menuLeftMobile)
var nav_links = menuLeftMobile.querySelectorAll('.nav-link')

for (var i = 0; i < nav_links.length; i++) {
  nav_links[i].onclick = function () {
    offcanvas.hide()

    setTimeout(function () {
      window.location = window.location.href
    }, 300)
  }
}

var width = window.screen.width
var menuTop = document.getElementById('menuTop')
if (width < 768) {
  menuTop.classList.add('sticky-top')
  menuLeftMobile.classList.add('text-bg-dark2')
} else {
  menuTop.classList.remove('sticky-top')
  menuLeftMobile.classList.remove('text-bg-dark2')
}
