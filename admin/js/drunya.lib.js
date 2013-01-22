var wRight = 0;
var wLeft = 0;
var wStep = 10;
var winTimeout = 50;
var openedWindows = new Array();
//var wObj = document.getElementById('test');

function wiOpen(pref) {
	$('#' + pref + '_dWin').fadeIn(1000);
}


function hideWin(pref) {
	$('#' + pref + '_dWin').fadeOut(500);
}

function addWin(prefix) {
	document.getElementById(prefix + '_add').style.display = '';
	document.getElementById(prefix + '_view').style.display = 'none';	
}


function save(prefix) {

	var inp = document.getElementById(prefix + '_inp').value;
	if (prefix == 'cat')
		var id_sec = document.getElementById(prefix + '_secId').value;
	else
		var id_sec = '';
	if (typeof inp == 'undefined' || typeof inp == '' || inp.length < 2) {
		alert('Слишком короткое название');
		return;
	} else {
		$.post('load_cat.php?ac=add', {title : inp, type: prefix, id_sec: id_sec}, function(data) { window.location.href = ''; });
		
	}
}
function _confirm() {
	return confirm('Вы уверенны?');
}


/* help window */
function showHelpWin(text, title) {
	var helpWin = document.createElement('div');
	helpWin.innerHTML = '' +
		'<div class="fps-win helpwin" id="helpBox" style="">' +
		'<div class="xw-tl"><div class="xw-tr"><div style="overflow:visible;font-size:10px;line-height:15px;" class="xw-tc xw-help">' +
		'<span class="title" unselectable="on" title="">' + title + '</span><div onClick="document.getElementById(\'helpBox\').style.display = \'none\'" class="close" unselectable="on"></div>' +
		'</div></div></div><div class="xw-ml"><div class="xw-mr"><div class="xw-mc">' +
		'<div style="padding:15px; clear:both; max-width:300px;" class="comment2">' +
		text +
		'</div>' +
		'</div></div></div><div class="xw-bl"><div class="xw-br"><div class="xw-bc"><div class="xw-footer"></div></div></div></div>' +
		'</div>';
	document.body.appendChild(helpWin);
}




/* ****** TOP MENU ******* */
var drunya_menu = false;
var menu_item_over = false;
document.onclick = function() {
	if (menu_item_over == false) {
		drunya_menu = false;
		hideAll();
	}
}
function drunyaMenu(params) {

	this.content = '';
	this.content = this.content + 

							'<div style="position: absolute; right: 65px;" id="uzadpnR">'+
								'<div style="float: left;">'+
								'<div class="admExtLeft" unselectable="on"></div>'+
								'<div class="admExtCenter" unselectable="on"><a href="exit.php" unselectable="on">Exit</a></div>'+
								'<div class="admExtRight" unselectable="on"></div>'+
								'</div>'+
							'</div>'+
							
							'<div id="_umenu0" class="x-unselectable" style="visibility: visible; z-index: 9999; left: 3px; top: 1px;">'+
								'<div style="overflow: hidden; width: 807px;" class="u-menuh" unselectable="on">'+
									'<div class="u-menubody" unselectable="on">'+
										'<div style="overflow: hidden; width: 807px;" class="u-menucont" unselectable="on">';

	for(var key in params){
		var param = params[key];
		this.content = this.content +
			'<div id="dMenuItem_' + key + '" onMouseOver="showDrunyaSubMenu(\'_' + key + '\')" onMouseOut="hideDrunyaSubMenu(\'_' + key + '\')"'+
				'onClick="activateDrunyaSubMenu(\'_' + key + '\')" style="float: left;" class="u-menuhitem u-menuhitemparent" unselectable="on">'+
				'<div class="admBarLeft" unselectable="on"></div>'+
				'<div class="admBarCenter" unselectable="on">'+
				'<div unselectable="on">' + param[0] + '</div>'+
				'</div>'+
				'<div class="admBarRight" unselectable="on"></div>'+
			'</div>';
	}

	this.content = this.content + 
						'</div>'+
					'</div>'+
				'</div>'+
			'</div>';
	var dismove = 0;
	for(var key in params){
		var param = params[key];
		
		if (key != 0) 
			dismove = dismove + (param[0].length * 10);
		else
			dismove = 0;
			
		this.content = this.content + 
			'<div id="_umenu_' + key + '" class="x-unselectable sub" style="visibility: visible; display: none; position: absolute; z-index: 9999;">'+
				'<div style="left: ' + dismove + 'px;" class="topm-sub-cont" unselectable="on">'+
					'<div class="fmsub-tl"><div class="fmsub-tr"><div class="fmsub-tc"></div></div></div>'+
						'<div class="fmsub-cl"><div class="fmsub-cr"><div class="fmsub-cc">';
							for(var _key in param[1]){
								var line = param[1][_key];
								if (line == 'sep') {
									this.content = this.content + 
									'<div class="u-menuvsep" unselectable="on"></div>';
								} else {
									this.content = this.content + 
									'<div class="topm-sub-item">' + line + '</div>';
								}
							}
							this.content = this.content +

						'</div></div></div>'+
					'<div class="fmsub-bl"><div class="fmsub-br"><div class="fmsub-bc"></div></div></div>'+
					'<div class="xw-bl" unselectable="on">'+
						'<div class="xw-br" unselectable="on">'+
							'<div class="xw-bc" unselectable="on">'+
								'<div class="xw-footer" unselectable="on"></div>'+
							'</div>'+
						'</div>'+
					'</div>'+
				'</div>'+
			'</div>';
	}

	document.getElementById('uzadmp').innerHTML = this.content;
}

function activateDrunyaSubMenu(id) {
	drunya_menu = true;
	showDrunyaSubMenu(id);
	return;
}

function showDrunyaSubMenu(id) {
	menu_item_over = true;
	document.getElementById('dMenuItem' + id).className = 'u-menuitemhl';
	hideAll();
	if (drunya_menu == true) {
		document.getElementById('_umenu' + id).style.display = '';
	}
	return;
}
function hideAll() {
	var top_container = document.getElementById('uzadmp');
	var top_sub_menu_items = top_container.getElementsByTagName('div');
	if (typeof top_sub_menu_items != 'undefined') {
		for (i = 0; i < top_sub_menu_items.length; i++) {
			if (typeof top_sub_menu_items[i].className != 'undefined') {
				if (top_sub_menu_items[i].className == 'x-unselectable sub') {
					top_sub_menu_items[i].style.display = 'none';
				}
			}
		}
	}
	var container = document.getElementById('f-wrap');
	var sub_menu_items = container.getElementsByTagName('div');
	if (typeof sub_menu_items != 'undefined') {
		for (i = 0; i < sub_menu_items.length; i++) {
			if (typeof sub_menu_items[i].className != 'undefined') {
				if (sub_menu_items[i].className == 'd_submenu') {
					sub_menu_items[i].style.display = 'none';
				}
			}
		}
	}
	return;
}
function hideDrunyaSubMenu(id) {
	menu_item_over = false;
	document.getElementById('dMenuItem' + id).className = 'u-menuhitem';
	return;
}


/* ************  MENU BLOCK ************ */



function hideSubMenu(id) {
	/*
	setTimeout(function() {
		document.getElementById(id).style.display = 'none';
	}, 3000);
	*/
	return;
}

function showSubMenu(id) {
	hideAll();
	document.getElementById(id).style.display = '';
}

/**
 * Change image when changing template
 */
function showScreenshot(path) {
	var img = document.getElementById('screenshot');
	if (img != 'undefined') {
		img.src = path;
	}
}



FpsLib = new function(){
	this.showLoader = function(){
		$('#ajax-loader').show();
	};
	this.hideLoader = function(){
		$('#ajax-loader').hide();
	};
};