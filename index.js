const menuLeftMobile = document.getElementById('menuLeftMobile')
const offcanvas = new bootstrap.Offcanvas(menuLeftMobile)
const nav_links = menuLeftMobile.querySelectorAll('.nav-link')
const menuTop = document.getElementById('menuTop')

// Прокрутка до нужного элемента страница
function scrollToElement (id) {
  const toElement = document.getElementById(id)
  if (window.screen.width < 768) {
    window.scrollTo({
      top: toElement.offsetTop - menuTop.offsetHeight,
      behavior: 'smooth'
    })
  } else {
    window.scrollTo({
      top: toElement.offsetTop - 10,
      behavior: 'smooth'
    })

    toElement.classList.add('active') // закрепляем меню в Top
  }
}

// изменим действия кнопок меню по умолчанию
for (let i = 0; i < nav_links.length; i++) {
  nav_links[i].onclick = function () {
    offcanvas.hide()
    scrollToElement(this.hash.slice(1))
    history.pushState(null, null, this.hash)
    return false
  }
}

// Слушаем изменение экрана 
window.addEventListener('resize', resizeDom)

function resizeDom () {
  if (window.screen.width < 768) {
    menuTop.classList.add('sticky-top') // закрепляем меню в Top
    menuLeftMobile.classList.add('text-bg-dark2') // Меням цвет у бокового меню
  } else {
    menuTop.classList.remove('sticky-top')
    menuLeftMobile.classList.remove('text-bg-dark2')
  }
}

resizeDom() // перерисуем на старте.

