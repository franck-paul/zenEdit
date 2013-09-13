// utilities functions
(function($) {
	// Return all element not belonging to context
    $.fn.allBut = function(context) {
        var target = this;
        var otherList = $();
        var processList = $(context || 'body').children();

        while (processList.size() > 0) {
            var cElem = processList.first();
            processList = processList.slice(1);

            if (cElem.filter(target).size() != target.size()) {
                if (cElem.has(target).size() > 0) {
                    processList = processList.add(cElem.children());
                } else {
                	if (cElem.css('display') != 'none') {
                		// Get only not hidden element
	                    otherList = otherList.add(cElem);
                	}
                }
            }
        }
        return otherList;
    }
})(jQuery);

var inZen = function(container,page,main) {
	// Switch into zen mode

	if (dotclear.zenMode == '1') return;

	// Get current status of some DOM element
	dotclear.zenMode_body_tc = $('body').css('color');
	dotclear.zenMode_body_bc = $('body').css('background-color');
	dotclear.zenMode_page_bc = page.css('background-color');
	dotclear.zenMode_main_bc = main.css('background-color');
	dotclear.zenMode_main_bi = main.css('background-image');
	dotclear.zenMode_container_mt = container.css('margin-top');

	// Hack some CSS attributes
	container.css('margin-top','4em');
	$('body').css('color','rgb(101,101,101)').css('background-color','rgb(251,251,251)');
	page.css('background-color','transparent');
	main.css('background-color','transparent').css('background-image','none');

	// Hide everything not mandatory
	dotclear.zenMode_other = container.allBut();
	dotclear.zenMode_other.hide(800);

	// Change toolbar button title and icon
	jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEditHide;
	jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-off.png';
	//	Don't know how to refresh this button !?! So, jQuery will help me
	$('button.jstb_zenEdit')
		.attr('title',dotclear.msg.zenEditHide)
		.css('background-image','url(index.php?pf=zenEdit/img/zen-off.png)');

	dotclear.zenMode = '1';
};

var outZen = function(container,page,main) {
	// Exit from zen mode

	if (dotclear.zenMode == '0') return;

	// Restore some CSS attributes as before
	container.css('margin-top',dotclear.zenMode_container_mt);
	$('body').css('color',dotclear.zenMode_body_tc).css('background-color',dotclear.zenMode_body_bc);
	page.css('background-color',dotclear.zenMode_page_bc);
	main.css('background-color',dotclear.zenMode_main_bc).css('background-image',dotclear.zenMode_main_bi);

	// Show everything having been hidden before
	dotclear.zenMode_other.show();

	// Restore toolbar button title
	jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEditShow;
	jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-on.png';
	//	Don't know how to refresh this button !?! So, jQuery will help me
	$('button.jstb_zenEdit')
		.attr('title',dotclear.msg.zenEditShow)
		.css('background-image','url(index.php?pf=zenEdit/img/zen-on.png)');

	dotclear.zenMode = '0';
};

var switchZen = function() {
	main = $('#main');
	page = $('#content');
	container = $('div#entry-content');
	if (dotclear.zenMode == '0') {
		inZen(container,page,main);
	} else {
		outZen(container,page,main);
	}
}

// Toolbar button for series

jsToolBar.prototype.elements.zenEditSpace = {type: 'space',
	format: {
		wysiwyg: true,
		wiki: true,
		xhtml: true,
		markdown: true
	}
};

jsToolBar.prototype.elements.zenEdit = {type: 'button', title: 'Zen', fn:{} };
jsToolBar.prototype.elements.zenEdit.context = 'post';
jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-on.png';
jsToolBar.prototype.elements.zenEdit.fn.wiki = function() {
	switchZen();
};
jsToolBar.prototype.elements.zenEdit.fn.xhtml = function() {
	switchZen();
};
jsToolBar.prototype.elements.zenEdit.fn.wysiwyg = function() {
	switchZen();
};
jsToolBar.prototype.elements.zenEdit.fn.markdown = function() {
	switchZen();
};

$(document).ready(function() {
	if (dotclear.zenMode == '0') {
		jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEditShow;
		jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-on.png';
	} else {
		jsToolBar.prototype.elements.zenEdit.title = dotclear.msg.zenEditHide;
		jsToolBar.prototype.elements.zenEdit.icon = 'index.php?pf=zenEdit/img/zen-off.png';
	}
});