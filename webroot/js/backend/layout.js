/*!
 * This file is part of MeCms.
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 */

/**
 * Gets the maximum height available
 */
function getAvailableHeight() {
	return $(window).height() - $('#topbar').outerHeight(true);
}

/**
 * Sets the height for the container (content and sidebar)
 */
function setContainerHeight() {
	//Gets the maximum height available
	var availableHeight = getAvailableHeight();
	
	//The content has the maximum height available
	$('#content').css('min-height', availableHeight);
	
	//The sidebar height is the maximum available height or the content height, if this is greater
	$('#sidebar').css('min-height', availableHeight > $('#content').height() ? availableHeight : $('#content').height());
}

/**
 * Sets the height for KCFinder
 */
function setKcfinderHeight() {
	if(!$('#kcfinder').length)
		return;
		
	//For now, the maximum height is the maximum height available
	var maxHeight = getAvailableHeight();
	
	//Subtracts content padding
	maxHeight -= parseInt($('#content').css('padding-top')) + parseInt($('#content').css('padding-bottom'));
	
	//Subtracts the height of each child element of content
	$('#content > * > *:not(#kcfinder)').each(function() {
		maxHeight -= $(this).outerHeight(true);
	});
		
	$('#kcfinder').height(maxHeight);
}

//On windows load and resize
$(window).on('load resize', function() {
	//Sets the height for the container (content and sidebar)
	setContainerHeight();
});

$(function() {
	//Sets the height for KCFinder
	setKcfinderHeight();
	
	//Adds the "data-parent" attribute for collapsed sidebar
	$('#sidebar a').attr('data-parent', '#sidebar');
	
	var sidebarPosition = $('#sidebar').position();
	
	$('#sidebar').affix({
		offset: {
			top: sidebarPosition.top,
//			bottom: function () {
//				return (this.bottom = $('.footer').outerHeight(true))
//			}
		}
	})
	
	console.log(sidebarPosition.top);
});