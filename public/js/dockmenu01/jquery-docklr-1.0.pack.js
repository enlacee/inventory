;
(function ($) {
	$.fn.docklr = function (method) {
		var defaults = {
			alignment: 'top',
			minSize: 40,
			position: 'fixed',
			sideRange: 0,
			zoomFactor: 1
		},
		methods = {
			init: function (opts) {
				var opts = $.extend({},
				defaults, opts);
				return this.each(function () {
					var $this = $(this);
					$this.addClass('docklr').addClass('docklr-js').addClass('docklr-' + opts.alignment);
					var isHoriz = (opts.alignment === 'bottom' || opts.alignment === 'top') ? true: false,
					isFixed = (opts.position === 'fixed') ? true: false,
					initW = 0,
					initH = 0,
					initX = 'auto',
					initY = 'auto',
					tipLeft = 'auto',
					tipLeftTo = 'auto',
					tipTop = 'auto',
					tipTopTo = 'auto',
					freeze = false,
					minSize = opts.minSize,
					range = opts.sideRange * 1,
					zoom = opts.zoomFactor,
					padding = 4,
					statusMouse = 0,
					stacks = {},
					id = Math.floor(Math.random() * 100);
					$this.css({
						'left': initX,
						'top': initY,
						'position': opts.position
					}).children('li').css({
						'width': minSize + (isHoriz ? 2 * padding: 0),
						'height': minSize + (isHoriz ? 0 : 2 * padding)
					});
					initW = $this.width();
					initH = $this.height();
					switch (opts.alignment) {
					case 'top':
						tipTop = minSize * zoom + 20;
						tipTopTo = tipTop - 20;
						break;
					case 'left':
						tipLeft = minSize * zoom + 20;
						tipLeftTo = tipLeft - 20;
						tipTop = tipLeft / 3;
						tipTopTo = tipTop;
						break;
					case 'right':
						tipLeft = ( - 1) * minSize * zoom - 20;
						tipLeftTo = tipLeft + 20;
						tipTop = tipLeft / ( - 3);
						tipTopTo = tipTop;
						break;
					default:
						tipTop = ( - 1) * minSize * zoom / 2;
						tipTopTo = tipTop + 20
					};
					if ($.browser.msie) {
						tipTop = tipTop == 'auto' ? 0 : tipTop;
						tipTopTo = tipTopTo == 'auto' ? 0 : tipTopTo;
						tipLeft = tipLeft == 'auto' ? 0 : tipLeft;
						tipLeftTo = tipLeftTo == 'auto' ? 0 : tipLeftTo
					};
					if (isFixed) {
						initX = isHoriz ? ($(window).width() - initW) / 2 + $(window).scrollLeft() : 'auto';
						initY = isHoriz ? 'auto': ($(window).height() - initH) / 2 + $(window).scrollTop()
					} else {
						var $parent = $this.parent();
						initX = isHoriz ? ($parent.width() - initW) / 2 + $parent.scrollLeft() : 'auto';
						initY = isHoriz ? 'auto': ($parent.height() - initH) / 2 + $parent.scrollTop()
					};
					$this.css({
						'left': initX,
						'top': initY,
						'position': opts.position
					}).bind({
						
						'mouseleave': function (e) {
							/*- Al salir del mouse -*/
							if (freeze) return false;
							$(this).css({
								'left': initX,
								'top': initY
							}).children('li').each(function () {
								$(this).css({
									'height': minSize + (isHoriz ? 0 : 2 * padding),
									'width': minSize + (isHoriz ? 2 * padding: 0)
								})
							})
						}
					}).children('li').children('a').bind({
						'mouseenter': function () {
							
							/*- Cuando ingresa el mouse -*/
							if (freeze) return false;
							var title = $(this).children('img').attr('alt');
							if ($(this).find('.docklr-tooltip').length < 1) {
								$(this).removeClass('docklr-bounce').append('<div class="docklr-tooltip">' + title + '</div>').find('.docklr-tooltip').css({
									'display': 'block',
									'left': tipLeftTo,
									'top': tipTopTo,
									'opacity': 0
								}).animate({
									'left': tipLeft,
									'top': tipTop,
									'opacity': 0.9
								},
								150)
							}
						},
						'mouseleave': function () {
							/*- Al salir del foco del icono -*/
							$(this).find('.docklr-tooltip').animate({
								'left': tipLeftTo,
								'top': tipTopTo,
								'opacity': 0
							},
							150, function () {
								$(this).remove()
							})
						},
						'click': function (e) {
							/*- Click en iconos normales -*/
							//$('.docklr-active').remove();
							//$(this).addClass('docklr-bounce');
						}
					}).end().has('ul').each(function () {
						stacks[$(this).index()] = $(this).children('ul').css({
							'opacity': 0
						}).detach()
					}).bind('click', function (e) {
						/*- Al hacer click en el que tiene mas de dos opciones -*/
						if (freeze) {
							/*- Click al cerrar el que tiene mas opciones -*/
							$('#docklr-stacks-' + id).animate({
								'opacity': 0
							},
							500, function () {
								$(this).remove()
							});
							freeze = false
						} else {
							freeze = true;
							var stackLeft = $(this).offset().left,
							stackBottom = minSize * zoom,
							stackTop = 'auto',
							stackRight = 'auto';
							$(this).find('.docklr-tooltip').remove();
							if (opts.alignment === 'top') {
								stackTop = stackBottom;
								stackBottom = 'auto'
							} else if (opts.alignment == 'left') {
								stackLeft = stackBottom * 2;
								stackTop = initY;
								stackBottom = 'auto'
							} else if (opts.alignment == 'right') {
								stackRight = stackBottom * 3;
								stackLeft = 'auto';
								stackTop = initY;
								stackBottom = 'auto'
							};
							if ($.browser.msie) {
								stackLeft = stackLeft == 'auto' ? 0 : stackLeft;
								stackBottom = stackBottom == 'auto' ? 0 : stackBottom;
								stackTop = stackTop == 'auto' ? 0 : stackTop;
								stackRight = stackRight == 'auto' ? 0 : stackRight
							};
							$this.parent().append('<div id="docklr-stacks-' + id + '"></div>');
							stacks[$(this).index()].addClass('docklr-stacks').appendTo($('#docklr-stacks-' + id)).css({
								'position': isFixed ? 'fixed': 'absolute',
								'left': stackLeft,
								'top': stackTop,
								'bottom': stackBottom,
								'right': stackRight,
								'opacity': 1.0
							}).children('li').each(function () {
								var total = $(this).parent().children('li').length;
								var index = $(this).index();
								var height = minSize + 2 * padding;
								var title = $(this).children('a').children('img').attr('alt');
								
								$(this).parent().mouseenter(function(){
									statusMouse = 0;
									
								}).mouseleave(function(){
									statusMouse = 1;
									setTimeout(function(){
										if ( statusMouse != 0 ){
											$('#docklr-stacks-' + id).animate({
												'opacity': 0
											},
											500, function () {
												$(this).remove()
											});
											freeze = false
									
											$('#div_cont_menu').css({
												'left': initX,
												'top': initY
											}).children('li').each(function () {
												$(this).css({
													'height': minSize + (isHoriz ? 0 : 2 * padding),
													'width': minSize + (isHoriz ? 2 * padding: 0)
												})
											})
										}
									},2000);
								});
								
								$(this).children('a').bind('click', function (e) {
									$('#docklr-stacks-' + id).animate({
										'opacity': 0
									},
									500, function () {
										$(this).remove()
									});
									freeze = false
									
									$(this).parent().parent().parent().parent().children('ul').css({
										'left': initX,
										'top': initY
									}).children('li').each(function () {
										$(this).css({
											'height': minSize + (isHoriz ? 0 : 2 * padding),
											'width': minSize + (isHoriz ? 2 * padding: 0)
										})
									})
									
								});
								$(this).css({
									'opacity': 0,
									'height': height,
									'width': minSize,
									'margin-left': minSize - Math.sin((index / total) * Math.PI) * minSize * zoom / 2,
									'margin-top': ( - 1) * height
								}).children('a').append('<div class="docklr-tooltip">' + title + '</div>').find('.docklr-tooltip').show().end().end().delay(300 / (index + 1)).animate({
									'opacity': 1,
									'margin-top': 0
								},
								300)
							})
						}
					})
				})
			}
		};
		if (methods[method] && method.toLowerCase() != 'init') {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1))
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments)
		} else {
			$.error('Method "' + method + '" does not exist in Docklr')
		}
	}
})(jQuery);
