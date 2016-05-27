// Positionnement des popups
function setPopupPosition(e, box) {
	var mousex = e.pageX + 20;
	var boxWidth = box.width();

	// Distance of element from the right edge of viewport
	var boxVisX = $(window).width() - ( mousex + boxWidth );

	//If box exceeds the X coordinate of viewport
	if (boxVisX < 20) {
		mousex = e.pageX - boxWidth - 20;
	}

	box.css({ left: mousex });
}
