(function($) {
	if ($.fn.carouFredSel) return;

	$.fn.carouFredSel = function(o) {
		if (this.length == 0) {
			debug(true, 'No element found for "'+this.selector+'".');
			return this;
		}
		if (this.length > 1) {
			return this.each(function() {
				$(this).carouFredSel(o);
			});
		}
		var $cfs = this,
			$tt0 = this[0];

		$cfs.init = function(o, setOrig) {
			var obs = ['items', 'scroll', 'auto', 'prev', 'next', 'pagination'];
			o = getObject(o);
			for (var a = 0, l = obs.length; a < l; a++) {
				o[obs[a]] = getObject(o[obs[a]]);
			}
			if (typeof o.scroll == 'number') {
				if (o.scroll <= 50)					o.scroll	= { items		: o.scroll 	};
				else								o.scroll	= { duration	: o.scroll 	};
			} else {
				if (typeof o.scroll == 'string')	o.scroll	= { easing		: o.scroll 	};
			}
				 if (typeof o.items == 'number')	o.items		= { visible		: o.items 	};
			else if (typeof o.items == 'string')	o.items		= { visible		: o.items,
																	width		: o.items,
																	height		: o.items	};

			if (setOrig) opts_orig = $.extend(true, {}, $.fn.carouFredSel.defaults, o);

			opts = $.extend(true, {}, $.fn.carouFredSel.defaults, o);
			opts.variableVisible = false;
			opts.d = {};

			direction = (opts.direction == 'up' || opts.direction == 'left') ? 'next' : 'prev';

			var dims = [
			//	  0			  1				  2				  3			  4				  5				  6		  7		  8				  9 10 11 12
				['width'	, 'innerWidth'	, 'outerWidth'	, 'height'	, 'innerHeight'	, 'outerHeight'	, 'left', 'top'	, 'marginRight'	, 0, 1, 2, 3],
				['height'	, 'innerHeight'	, 'outerHeight'	, 'width'	, 'innerWidth'	, 'outerWidth'	, 'top'	, 'left', 'marginBottom', 3, 2, 1, 0]
			];
			var dn = dims[0].length,
				dx = (opts.direction == 'right' || opts.direction == 'left') ? 0 : 1;

			for (var d = 0; d < dn; d++) {
				opts.d[dims[0][d]] = dims[dx][d];
			}

			var	all_itm = getItems($cfs),
				lrgst_b = getTrueLargestSize(all_itm, opts, 'outerHeight', false);


			//	DEPRECATED
			if (opts.padding == 'auto') {
				debug(true, 'The option "padding: auto" is deprecated, use "align: center".');
				opts.padding = false;
				opts.align = 'center';
			}
			//	/DEPRECATED


			//	secondairy size set to auto -> measure largest size and set it
			if (opts[opts.d['height']] == 'auto') {
				opts[opts.d['height']] = lrgst_b;
				opts.items[opts.d['height']] = lrgst_b;
			}

			//	primairy item-size not set -> measure it or set to "variable"
			if (!opts.items[opts.d['width']]) {
				opts.items[opts.d['width']] = (hasVariableSizes(all_itm, opts, 'outerWidth'))
					? 'variable'
					: all_itm[opts.d['outerWidth']](true);
			}

			//	secondairy item-size not set -> measure it or set to "variable"
			if (!opts.items[opts.d['height']]) {
				opts.items[opts.d['height']] = (hasVariableSizes(all_itm, opts, 'outerHeight'))
					? 'variable'
					: all_itm[opts.d['outerHeight']](true);
			}

			//	secondairy size not set -> set to secondairy item-size
			if (!opts[opts.d['height']]) {
				opts[opts.d['height']] = opts.items[opts.d['height']];
			}

			//	visible-items not set
			if (!opts.items.visible) {
				//	primairy item-size variable -> set visible items variable
				if (opts.items[opts.d['width']] == 'variable') {
					opts.items.visible = 'variable';
				} else {
					//	primairy size is number -> calculate visible-items
					if (typeof opts[opts.d['width']] == 'number') {
						opts.items.visible = Math.floor(opts[opts.d['width']] / opts.items[opts.d['width']]);
					} else {
						//	measure and calculate primairy size and visible-items
						var maxS = getTrueInnerSize($wrp.parent(), opts, 'innerWidth');
						opts.items.visible = Math.floor(maxS / opts.items[opts.d['width']]);
						opts[opts.d['width']] = opts.items.visible * opts.items[opts.d['width']];
						opts.align = false;
					}
				}
			}

			//	primairy size not set -> calculate it or set to "variable"
			if (!opts[opts.d['width']]) {
				if (opts.items.visible != 'variable' && opts.items[opts.d['width']] != 'variable') {
					opts[opts.d['width']] = opts.items.visible * opts.items[opts.d['width']];
					opts.align = false;
				} else {
					opts[opts.d['width']] = 'variable';
				}
			}

			//	variable primairy item-sizes with variabe visible-items
			if (opts.items.visible == 'variable') {
				opts.variableVisible = true;
				opts.maxDimention = (opts[opts.d['width']] == 'variable')
					? getTrueInnerSize($wrp.parent(), opts, 'innerWidth')
					: opts[opts.d['width']];
				if (opts.align === false) {
					opts[opts.d['width']] = 'variable';
				}
				opts.items.visible = getVisibleItemsNext($cfs, opts, 0);
			}

			if (typeof opts.padding == 'undefined') {
				opts.padding = 0;
			}

			//	align not set -> set to center if primairy size is number
			if (typeof opts.align == 'undefined') {
				opts.align = (opts[opts.d['width']] == 'variable')
					? false
					: 'center';
			}

			opts.items.oldVisible = opts.items.visible;
			opts.usePadding = false;
			opts.padding = getPadding(opts.padding);

			if (opts.align == 'top') 		opts.align = 'left';
			if (opts.align == 'bottom') 	opts.align = 'right';

			switch (opts.align) {
				//	align: center, left or right, only on fixed sizes
				case 'center':
				case 'left':
				case 'right':
					if (opts[opts.d['width']] != 'variable') {
						var p = getAlignPadding(getCurrentItems($cfs, opts), opts);
						opts.usePadding = true;
						opts.padding[opts.d[1]] = p[1];
						opts.padding[opts.d[3]] = p[0];
					}
					break;

				//	padding
				default:
					opts.align = false;
					opts.usePadding = (
						opts.padding[0] == 0 &&
						opts.padding[1] == 0 &&
						opts.padding[2] == 0 &&
						opts.padding[3] == 0
					) ? false : true;
					break;
			}

			if (typeof opts.items.minimum	!= 'number')	opts.items.minimum		= (opts.variableVisible) ? 1 : opts.items.visible;
			if (typeof opts.scroll.items	!= 'number')	opts.scroll.items		= (opts.variableVisible) ? 'variable' : opts.items.visible;
			if (typeof opts.scroll.duration	!= 'number')	opts.scroll.duration	= 500;

			opts.auto		= getNaviObject(opts.auto, false, true);
			opts.prev		= getNaviObject(opts.prev);
			opts.next		= getNaviObject(opts.next);
			opts.pagination	= getNaviObject(opts.pagination, true);

			opts.auto		= $.extend(true, {}, opts.scroll, opts.auto);
			opts.prev		= $.extend(true, {}, opts.scroll, opts.prev);
			opts.next		= $.extend(true, {}, opts.scroll, opts.next);
			opts.pagination	= $.extend(true, {}, opts.scroll, opts.pagination);

			if (typeof opts.pagination.keys				!= 'boolean')	opts.pagination.keys 			= false;
			if (typeof opts.pagination.anchorBuilder	!= 'function')	opts.pagination.anchorBuilder	= $.fn.carouFredSel.pageAnchorBuilder;
			if (typeof opts.auto.play					!= 'boolean')	opts.auto.play					= true;
			if (typeof opts.auto.nap					!= 'boolean')	opts.auto.nap					= true;
			if (typeof opts.auto.delay					!= 'number')	opts.auto.delay					= 0;
			if (typeof opts.auto.pauseDuration			!= 'number')	opts.auto.pauseDuration			= (opts.auto.duration < 10) ? 2500 : opts.auto.duration * 5;

			if (opts.debug) {
				debug(true, 'Carousel width: '+opts.width);
				debug(true, 'Carousel height: '+opts.height);
				debug(true, 'Item widths: '+opts.items.width);
				debug(true, 'Item heights: '+opts.items.height);
				debug(true, 'Number of items visible: '+opts.items.visible);
				if (opts.auto.play)		debug(true, 'Number of items scrolled automatically: '+opts.auto.items);
				if (opts.prev.button)	debug(true, 'Number of items scrolled backward: '+opts.prev.items);
				if (opts.next.button)	debug(true, 'Number of items scrolled forward: '+opts.next.items);
			}
		};	//	/init

		$cfs.build = function() {
			if ($cfs.css('position') == 'absolute' || $cfs.css('position') == 'fixed') {
				debug(opts.debug, 'Carousels CSS-attribute "position" should be "static" or "relative".');
			}
			$wrp.css({
				position	: 'relative',
				overflow	: 'hidden',
				marginTop	: $cfs.css('marginTop'),
				marginRight	: $cfs.css('marginRight'),
				marginBottom: $cfs.css('marginBottom'),
				marginLeft	: $cfs.css('marginLeft')
			});
			$cfs.data('cfs_origCss', {
				width		: $cfs.css('width'),
				height		: $cfs.css('height'),
				marginTop	: $cfs.css('marginTop'),
				marginRight	: $cfs.css('marginRight'),
				marginBottom: $cfs.css('marginBottom'),
				marginLeft	: $cfs.css('marginLeft'),
				'float'		: $cfs.css('float'),
				position	: $cfs.css('position'),
				top			: $cfs.css('top'),
				left		: $cfs.css('left')
			}).css({
				marginTop	: 0,
				marginRight	: 0,
				marginBottom: 0,
				marginLeft	: 0,
				'float'		: 'none',
				position	: 'absolute'
			});
			if (opts.usePadding) {
				getItems($cfs).each(function() {
					var m = parseInt($(this).css(opts.d['marginRight']));
					if (isNaN(m)) m = 0;
					$(this).data('cfs_origCssMargin', m);
				});
			}
		};	//	/build

		$cfs.bind_events = function() {
			$cfs.unbind_events();
			$cfs.bind('pause', function(e, g) {
				e.stopPropagation();
				if (typeof g != 'boolean') g = false;
				if (g) isPausedGlobal = true;
				if (autoTimeout != null)	clearTimeout(autoTimeout);
				if (autoInterval != null)	clearInterval(autoInterval);
				if (timerInterval != null)	clearInterval(timerInterval);

				isPaused = (g) ? 'global' : true;

				var dur1 = opts.auto.pauseDuration - pauseTimePassed,
					perc = 100 - Math.ceil( dur1 * 100 / opts.auto.pauseDuration );

				if (perc != 0) {
					if (opts.auto.onPausePause) opts.auto.onPausePause.call($tt0, perc, dur1);
				}
			});

			$cfs.bind('play', function(e, d, f, g) {
				e.stopPropagation();
				$cfs.trigger('pause');
				if (!opts.auto.play) return;

				var a = sortParams([d, f, g], ['string', 'number', 'boolean']);
				if (a[0] != 'prev' && a[0] != 'next') 	a[0] = direction;
				if (typeof a[1] != 'number') 			a[1] = 0;

				if (a[2]) isPausedGlobal = false;
				if (isPausedGlobal) return;

				isPaused = false;

				var dur1 = opts.auto.pauseDuration - pauseTimePassed,
					dur2 = dur1 + a[1];
					perc = 100 - Math.ceil(dur1 * 100 / opts.auto.pauseDuration);

				autoTimeout = setTimeout(function() {
					if ($cfs.is(':animated')) {
						$cfs.trigger('play', a[0]);
					} else {
						pauseTimePassed = 0;
						$cfs.trigger(a[0], opts.auto);
					}
				}, dur2);

				if (opts.auto.pauseOnHover === 'resume') {
					autoInterval = setInterval(function() {
						pauseTimePassed += 50;
					}, 50);
				}
				if (opts.auto.onPauseEnd && perc == 0) {
					opts.auto.onPauseEnd.call($tt0, perc, dur1);
				}
				if (opts.auto.onPauseStart) {
					timerInterval = setTimeout(function() {
						opts.auto.onPauseStart.call($tt0, perc, dur1);
					}, a[1]);
				}
			});

			$cfs.bind('prev next', function(e, o, n, c) {
				e.stopPropagation();
				if (isPausedGlobal || $cfs.is(':animated') || $cfs.is(':hidden')) {
					e.stopImmediatePropagation();
					return;
				}
				if (opts.items.minimum >= totalItems) {
					debug(opts.debug, 'Not enough items: not scrolling');
					e.stopImmediatePropagation();
					return;
				}
				pauseTimePassed = 0;
				var a = sortParams([o, n, c], ['object', 'number', 'function']);

				if (typeof a[0] != 'object')	a[0] = opts[e.type];
				if (typeof a[2] == 'function')	a[0].onAfter = a[2];
				if (typeof a[1] != 'number') {
						 if (typeof a[0].items == 'number') 		a[1] = a[0].items;
					else if (typeof opts[e.type].items == 'number')	a[1] = opts[e.type].items;
					else 											a[1] = opts.items.visible;
				}
				if (typeof a[1] != 'number') return debug(opts.debug, 'Not a valid number: '+a[1]+'. Not scrolling');
				if (a[0].conditions && !a[0].conditions.call($tt0)) return debug(opts.debug, 'Callback "conditions" returned false.');

				$cfs.trigger('slide_'+e.type, a);
			});


			$cfs.bind('slidePrev', function(e, sO, nI) {
				e.stopPropagation();
				//	DEPRECATED
				debug(true, 'The custom event "slidePrev" is deprecated, use "slide_prev".');
				//	/DEPRECATED
				$cfs.trigger('slide_prev', [sO, nI]);
			}).bind('slide_prev', function(e, sO, nI) {
				e.stopPropagation();
				if (opts.variableVisible) {
					oI = nI;
					opts.items.oldVisible = opts.items.visible;
					var itm = getItems($cfs);
					if (opts.usePadding) resetMargin(itm, opts);
					opts.items.visible = getVisibleItemsPrev($cfs, opts, oI);
					nI = opts.items.visible - opts.items.oldVisible + oI;
					if (nI <= 0) {
						opts.items.visible = getVisibleItemsNext($cfs, opts, totalItems-oI);
						nI = oI;
					}
					if (opts.usePadding) resetMargin(itm, opts, true);
				}
				if (!opts.circular) {
					var nulItem = totalItems - firstItem;
					if (nulItem - nI < 0) {
						nI = nulItem;
					}
					if (firstItem == 0) {
						nI = 0;
					}
				}

				firstItem += nI;
				if (firstItem >= totalItems) firstItem -= totalItems;

				if (!opts.circular) {
					if (firstItem == 0 && nI != 0 && sO.onEnd) sO.onEnd.call($tt0);
					if (opts.infinite) {
						if (nI == 0) {
							$cfs.trigger('next', totalItems-opts.items.visible);
							return;
						}
					} else enableNavi(opts, firstItem);
				}

				if (nI == 0) return;

				getItems($cfs, ':gt('+(totalItems-nI-1)+')').prependTo($cfs);
				if (totalItems < opts.items.visible + nI) {
					getItems($cfs, ':lt('+((opts.items.visible+nI)-totalItems)+')').clone(true).appendTo($cfs);
				}

				var c_old = getOldItemsPrev($cfs, opts, nI),
					c_new = getNewItemsPrev($cfs, opts),
					l_cur = getItems($cfs, ':eq('+(nI-1)+')'),
					l_old = c_old.filter(':last'),
					l_new = c_new.filter(':last');

				if (opts.usePadding) {
					resetMargin(l_old, opts);
					resetMargin(c_new, opts);
				}
				if (opts.align) var p = getAlignPadding(getNewItemsPrev($cfs, opts, nI), opts);

				var i_siz = getTotalSize(getItems($cfs, ':lt('+nI+')'), opts, 'width'),
					w_siz = mapWrapperSizes(getSizes(c_new, opts, true), opts, !opts.usePadding);

				if (opts.usePadding) {
					resetMargin(l_old, opts, opts.padding[opts.d[1]]);
					resetMargin(l_cur, opts, opts.padding[opts.d[3]]);
				}
				if (opts.align) {
					opts.padding[opts.d[1]] = p[1];
					opts.padding[opts.d[3]] = p[0];
				}
				var a_cfs = {},
					a_new = {},
					a_cur = {},
					a_old = {},
					a_dur = sO.duration;

					 if (sO.fx == 'none')	a_dur = 0;
				else if (a_dur == 'auto')	a_dur = opts.scroll.duration / opts.scroll.items * nI;
				else if (a_dur <= 0)		a_dur = 0;
				else if (a_dur < 10)		a_dur = i_siz / a_dur;

				var a_conf = {
					duration: a_dur,
					easing	: sO.easing
				};
				if (sO.onBefore) sO.onBefore.call($tt0, c_old, c_new, w_siz, a_dur);
				if (opts.usePadding) {
					var new_m = opts.padding[opts.d[3]];
					a_cur[opts.d['marginRight']] = l_cur.data('cfs_origCssMargin');
					a_new[opts.d['marginRight']] = l_new.data('cfs_origCssMargin') + opts.padding[opts.d[1]];
					a_old[opts.d['marginRight']] = l_old.data('cfs_origCssMargin');

					l_cur.stop().animate(a_cur, a_conf);
					l_new.stop().animate(a_new, a_conf);
					l_old.stop().animate(a_old, a_conf);

				} else {
					var new_m = 0;
				}
				a_cfs[opts.d['left']] = new_m;

				if (opts[opts.d['width']] == 'variable' || opts[opts.d['height']] == 'variable') {
					$wrp.stop().animate(w_siz, a_conf);
				}

				//	alternative effects
				switch(sO.fx) {
					case 'crossfade':
					case 'cover':
					case 'uncover':
						var $cf2 = $cfs.clone().appendTo($wrp);
						break;
				}
				switch(sO.fx) {
					case 'uncover':
						getItems($cf2, ':lt('+nI+')').remove();
					case 'crossfade':
					case 'cover':
						getItems($cf2, ':gt('+(opts.items.oldVisible-1)+')').remove();
						break;
				}
				switch(sO.fx) {
					case 'fade':
						fx_fade(sO, $cfs, 0, a_dur);
						break;
					case 'crossfade':
						$cf2.css({ opacity: 0 });
						fx_fade(sO, $cf2, 1, a_dur);
						fx_fade(sO, $cfs, 1, a_dur, function() { $cf2.remove(); });
						break;
					case 'cover':
						fx_cover(sO, $cfs, $cf2, opts, a_dur, true);
						break;
					case 'uncover':
						fx_uncover(sO, $cf2, opts, a_dur, true);
						break;
				}
				switch(sO.fx) {
					case 'fade':
					case 'crossfade':
					case 'cover':
					case 'uncover':
						f_dur = a_dur;
						a_dur = 0;
						break;
				}
				//	/alternative effects

				var c_nI = nI;
				$cfs.css(opts.d['left'], -i_siz);
				$cfs.animate(a_cfs, {
					duration: a_dur,
					easing	: sO.easing,
					complete: function() {
						var overFill = opts.items.visible+c_nI-totalItems;
						if (overFill > 0) {
							getItems($cfs, ':gt('+(totalItems-1)+')').remove();
							c_old = getItems($cfs, ':gt('+(totalItems-(c_nI-overFill)-1)+')').get().concat( getItems($cfs, ':lt('+overFill+')').get() );
						}
						if (opts.usePadding) {
							var l_itm = getItems($cfs, ':eq('+(opts.items.visible+nI-1)+')');
							l_itm.css(opts.d['marginRight'], l_itm.data('cfs_origCssMargin'));
						}

						var fn = (sO.onAfter) ? function() { sO.onAfter.call($tt0, c_old, c_new, w_siz); } : false;
						switch(sO.fx) {
							case 'fade':
							case 'uncover':
								fx_fade(sO, $cfs, 1, f_dur, fn);
								break;

							default:
								if (fn) fn();
								break;
						}
					}
				});
				$cfs.trigger('updatePageStatus', [false, w_siz]).trigger('play', a_dur);
			});

			$cfs.bind('slideNext', function(e, sO, nI) {
				e.stopPropagation();
				//	DEPRECATED
				debug(true, 'The custom event "slideNext" is deprecated, use "slide_next".');
				//	/DEPRECATED
				$cfs.trigger('slide_next', [sO, nI]);
			}).bind('slide_next', function(e, sO, nI) {
				e.stopPropagation();
				if (opts.variableVisible) {
					opts.items.oldVisible = opts.items.visible;
					var itm = getItems($cfs);
					if (opts.usePadding) resetMargin(itm, opts);
					opts.items.visible = getVisibleItemsNext($cfs, opts, nI);
					if (opts.items.oldVisible - nI >= opts.items.visible) opts.items.visible = getVisibleItemsNext($cfs, opts, ++nI);
					if (opts.usePadding) resetMargin(itm, opts, true);
				}
				if (!opts.circular) {
					if (firstItem == 0) {
						if (nI > totalItems - opts.items.visible) {
							nI = totalItems - opts.items.visible;
						}
					} else {
						if (firstItem - nI < opts.items.visible) {
							nI = firstItem - opts.items.visible;
						}
					}
				}

				firstItem -= nI;
				if (firstItem < 0) firstItem += totalItems;

				if (!opts.circular) {
					if (firstItem == opts.items.visible && nI != 0 && sO.onEnd) sO.onEnd.call($tt0);
					if (opts.infinite) {
						if (nI == 0) {
							$cfs.trigger('prev', totalItems-opts.items.visible);
							return;
						}
					} else enableNavi(opts, firstItem);
				}

				if (nI == 0) return;
				if (totalItems < opts.items.visible + nI) getItems($cfs, ':lt('+((opts.items.visible+nI)-totalItems)+')').clone(true).appendTo($cfs);

				var c_old = getOldItemsNext($cfs, opts),
					c_new = getNewItemsNext($cfs, opts, nI),
					l_cur = c_old.filter(':eq('+(nI-1)+')'),
					l_old = c_old.filter(':last'),
					l_new = c_new.filter(':last');

				if (opts.usePadding) {
					resetMargin(l_old, opts);
					resetMargin(l_new, opts);
				}
				if (opts.align) var p = getAlignPadding(getNewItemsNext($cfs, opts, nI), opts);

				var i_siz = getTotalSize(getItems($cfs, ':lt('+nI+')'), opts, 'width'),
					w_siz = mapWrapperSizes(getSizes(c_new, opts, true), opts, !opts.usePadding);

				if (opts.usePadding) {
					resetMargin(l_old, opts, opts.padding[opts.d[1]]);
					resetMargin(l_new, opts, opts.padding[opts.d[1]]);
				}
				if (opts.align) {
					opts.padding[opts.d[1]] = p[1];
					opts.padding[opts.d[3]] = p[0];
				}

				var a_cfs = {},
					a_old = {},
					a_cur = {},
					a_dur = sO.duration;

					 if (sO.fx == 'none')	a_dur = 0;
				else if (a_dur == 'auto')	a_dur = opts.scroll.duration / opts.scroll.items * nI;
				else if (a_dur <= 0)		a_dur = 0;
				else if (a_dur < 10)		a_dur = i_siz / a_dur;

				var a_conf = {
					duration: a_dur,
					easing	: sO.easing
				};
				if (sO.onBefore) sO.onBefore.call($tt0, c_old, c_new, w_siz, a_dur);
				if (opts.usePadding) {
					a_old[opts.d['marginRight']] = l_old.data('cfs_origCssMargin');
					a_cur[opts.d['marginRight']] = l_cur.data('cfs_origCssMargin') + opts.padding[opts.d[3]];
					l_new.css(opts.d['marginRight'], l_new.data('cfs_origCssMargin') + opts.padding[opts.d[1]]);
					l_old.stop().animate(a_old, a_conf);
					l_cur.stop().animate(a_cur, a_conf);
				}
				a_cfs[opts.d['left']] = -i_siz;

				if (opts[opts.d['width']] == 'variable' || opts[opts.d['height']] == 'variable') {
					$wrp.stop().animate(w_siz, a_conf);
				}

				//	alternative effects
				switch(sO.fx) {
					case 'crossfade':
					case 'cover':
					case 'uncover':
						var $cf2 = $cfs.clone().appendTo($wrp);
						break;
				}
				switch(sO.fx) {
					case 'crossfade':
					case 'cover':
						getItems($cf2, ':lt('+nI+')').remove();
					case 'uncover':
						getItems($cf2, ':gt('+(opts.items.visible-1)+')').remove();
						break;
				}
				switch(sO.fx) {
					case 'fade':
						fx_fade(sO, $cfs, 0, a_dur);
						break;
					case 'crossfade':
						$cf2.css({ opacity: 0 });
						fx_fade(sO, $cf2, 1, a_dur);
						fx_fade(sO, $cfs, 1, a_dur, function() { $cf2.remove(); });
						break;
					case 'cover':
						fx_cover(sO, $cfs, $cf2, opts, a_dur, false);
						break;
					case 'uncover':
						fx_uncover(sO, $cf2, opts, a_dur, false);
						break;
				}
				switch(sO.fx) {
					case 'fade':
					case 'crossfade':
					case 'cover':
					case 'uncover':
						f_dur = a_dur;
						a_dur = 0;
						break;
				}
				//	/alternative effects

				var c_nI = nI;
				$cfs.animate(a_cfs, {
					duration: a_dur,
					easing	: sO.easing,
					complete: function() {
						var overFill = opts.items.visible+c_nI-totalItems,
							new_m = (opts.usePadding) ? opts.padding[opts.d[3]] : 0;
						$cfs.css(opts.d['left'], new_m);
						if (overFill > 0) {
							getItems($cfs, ':gt('+(totalItems-1)+')').remove();
						}
						var l_itm = getItems($cfs, ':lt('+c_nI+')').appendTo($cfs).filter(':last');
						if (overFill > 0) {
							c_new = getCurrentItems($cfs, opts);
						}
						if (opts.usePadding) {
							if (totalItems < opts.items.visible+c_nI) {
								var l_cur = getItems($cfs, ':eq('+(opts.items.visible-1)+')');
								l_cur.css(opts.d['marginRight'], l_cur.data('cfs_origCssMargin') + opts.padding[opts.d[3]]);
							}
							l_itm.css(opts.d['marginRight'], l_itm.data('cfs_origCssMargin'));
						}

						var fn = (sO.onAfter) ? function() { sO.onAfter.call($tt0, c_old, c_new, w_siz); } : false;
						switch(sO.fx) {
							case 'fade':
							case 'uncover':
								fx_fade(sO, $cfs, 1, f_dur, fn);
								break;

							default:
								if (fn) fn();
								break;
						}
					}
				});
				$cfs.trigger('updatePageStatus', [false, w_siz]).trigger('play', a_dur);
			});

			$cfs.bind('slideTo', function(e, num, dev, org, obj) {
				e.stopPropagation();
				if ($cfs.is(':animated')) return;

				num = getItemIndex(num, dev, org, firstItem, totalItems, $cfs);
				if (num == 0) return;
				if (typeof obj != 'object') obj = false;

				if (opts.circular) {
					if (num <= totalItems / 2) 	$cfs.trigger('next', [obj, num]);
					else 						$cfs.trigger('prev', [obj, totalItems-num]);
				} else {
					if (firstItem == 0 ||
						firstItem > num)		$cfs.trigger('next', [obj, num]);
					else						$cfs.trigger('prev', [obj, totalItems-num]);
				}
			});

			$cfs.bind('insertItem', function(e, itm, num, org, dev) {
				e.stopPropagation();
				if (typeof itm == 'object' &&
					typeof itm.jquery == 'undefined')	itm = $(itm);
				if (typeof itm == 'string') 			itm = $(itm);
				if (typeof itm != 'object' ||
					typeof itm.jquery == 'undefined' ||
					itm.length == 0) return debug(opts.debug, 'Not a valid object.');

				if (typeof num == 'undefined' || num == 'end') {
					$cfs.append(itm);
				} else {
					num = getItemIndex(num, dev, org, firstItem, totalItems, $cfs);
					var $cit = getItems($cfs, ':eq('+num+')');
					if (opts.usePadding) {
						itm.each(function() {
							var m = parseInt($(this).css(opts.d['marginRight']));
							if (isNaN(m)) m = 0;
							$(this).data('cfs_origCssMargin', m);
						});
					}
					if ($cit.length) {
						if (num < firstItem) firstItem += itm.length;
						if (firstItem >= totalItems) firstItem -= totalItems;
						$cit.before(itm);
					} else {
						$cfs.append(itm);
					}
				}
				totalItems = getItems($cfs).length;
				$cfs.trigger('linkAnchors');
				var sz = setSizes($cfs, opts);
				showNavi(opts, totalItems);
				enableNavi(opts, firstItem);
				$cfs.trigger('updatePageStatus', [true, sz]);
			});

			$cfs.bind('removeItem', function(e, num, org, dev) {
				e.stopPropagation();
				if (typeof num == 'undefined' || num == 'end') {
					getItems($cfs, ':last').remove();
				} else {
					num = getItemIndex(num, dev, org, firstItem, totalItems, $cfs);
					var $cit = getItems($cfs, ':eq('+num+')');
					if ($cit.length){
						if (num < firstItem) firstItem -= $cit.length;
						$cit.remove();
					}
				}
				totalItems = getItems($cfs).length;
				var sz = setSizes($cfs, opts);
				showNavi(opts, totalItems);
				enableNavi(opts, firstItem);
				$cfs.trigger('updatePageStatus', [true, sz]);
			});

			$cfs.bind('currentPosition currentPage currentVisible isPaused', function(e, fn) {
				e.stopPropagation();
				if (typeof fn != 'function') e.stopImmediatePropagation();
			});
			$cfs.bind('currentPosition', function(e, fn) {
				if (firstItem == 0) var val = 0;
				else var val = totalItems - firstItem;
				fn.call($tt0, val);
			});

			$cfs.bind('currentPage', function(e, fn) {
				var max = Math.ceil(totalItems/opts.items.visible-1);
				if (firstItem == 0) var nr = 0;
				else if (firstItem < totalItems % opts.items.visible) var nr = 0;
				else if (firstItem == opts.items.visible && !opts.circular) var nr = max;
				else var nr = Math.round((totalItems-firstItem)/opts.items.visible);
				if (nr < 0) nr = 0;
				if (nr > max) nr = max;
				fn.call($tt0, nr);
			});

			$cfs.bind('currentVisible', function(e, fn) {
				fn.call($tt0, getCurrentItems($cfs, opts));
			});

			$cfs.bind('isPaused', function(e, fn) {
				fn.call($tt0, isPaused);
			});

			$cfs.bind('configuration', function(e, a, b, reInit) {
				e.stopPropagation();

				//	return entire configuration-object
				if (typeof a == 'function') {
					a.call($tt0, opts);

				//	set multiple options via object
				} else if (typeof a == 'object') {
					reInit = (b === false) ? false : 'ObjectConfiguration';
					for (var c in a) {
						if (typeof a[c] == 'object') {
							for (var d in a[c]) {
								$cfs.trigger('configuration', [c+'.'+d, a[c][d], reInit]);
							}
						} else	$cfs.trigger('configuration', [c, a[c], reInit]);
					}
					if (b !== false) {
						resetMargin(getItems($cfs), opts);
						$cfs.init(opts_orig);
						setSizes($cfs, opts);
					}
				} else if (typeof a != 'undefined') {

					//	return value for specific option
					if (typeof b == 'function') {
						var val = eval('opts.'+a);
						if (typeof val == 'undefined') val = '';
						b.call($tt0, val);

					//	set individual option
					} else if (typeof b != 'undefined') {
						if (typeof reInit == 'undefined') reInit = true;
						if ($cfs.is(':animated')) {
							setTimeout(function() {
								$cfs.trigger('configuration', [a, b, reInit]);
							}, 100);
							return debug(opts.debug, 'carousel animated, configuration timeout.');
						}
						eval('opts_orig.'+a+' = b');
						if (reInit === true) {
							resetMargin(getItems($cfs), opts);
							$cfs.init(opts_orig);
							setSizes($cfs, opts);
						} else if (reInit != 'ObjectConfiguration') {
							eval('opts.'+a+' = b');
						}
					}
				}
			});

			$cfs.bind('linkAnchors', function(e, $con, sel) {
				e.stopPropagation();
				if (typeof $con == 'undefined' || $con.length == 0) $con = $('body');
				else if (typeof $con == 'string') $con = $($con);
				if (typeof $con != 'object') return debug(opts.debug, 'Not a valid object.');
				if (typeof sel != 'string' || sel.length == 0) sel = 'a.caroufredsel';
				$con.find(sel).each(function() {
					var h = this.hash || '';
					if (h.length > 0 && getItems($cfs).index($(h)) != -1) {
						$(this).unbind('click').click(function(e) {
							e.preventDefault();
							$cfs.trigger('slideTo', h);
						});
					}
				});
			});

			$cfs.bind('updatePageStatus', function(e, build, sizes) {
				e.stopPropagation();
				if (!opts.pagination.container) return;
				if (typeof build == 'boolean' && build) {
					getItems(opts.pagination.container).remove();
					for (var a = 0, l = Math.ceil(totalItems/opts.items.visible); a < l; a++) {
						var i = getItems($cfs, ':eq(' + getItemIndex(a*opts.items.visible, 0, true, firstItem, totalItems, $cfs) + ')');
						opts.pagination.container.append(opts.pagination.anchorBuilder(a+1, i));
					}
					getItems(opts.pagination.container).unbind('click').each(function(a) {
						$(this).click(function(e) {
							e.preventDefault();
							$cfs.trigger('slideTo', [a * opts.items.visible, 0, true, opts.pagination]);
						});
					});
				}
				$cfs.trigger('currentPage', function(nr) {
					getItems(opts.pagination.container).removeClass('selected').filter(':eq('+nr+')').addClass('selected');
				});
			});

			$cfs.bind('destroy', function(e, orgOrder) {
				e.stopPropagation();
				if (orgOrder) {
					$cfs.trigger('slideTo', [0, 0, true, { duration: 0 }]);
				}
				if (opts.usePadding) {
					resetMargin(getItems($cfs), opts);
				}
				$cfs.trigger('pause').css($cfs.data('cfs_origCss'));
				$cfs.unbind_events();
				$cfs.unbind_buttons();
				$wrp.replaceWith($cfs);
			});
		};	//	/bind_events

		$cfs.unbind_events = function() {
			$cfs.unbind('pause').unbind('play')
				.unbind('prev').unbind('slidePrev').unbind('slide_prev')
				.unbind('next').unbind('slideNext').unbind('slide_next')
				.unbind('slideTo')
				.unbind('insertItem').unbind('removeItem')
				.unbind('currentPosition').unbind('currentPage').unbind('currentVisible').unbind('isPaused')
				.unbind('linkAnchors')
				.unbind('destroy')
				.unbind('updatePageStatus')
				.unbind('configuration');
		};	//	/unbind_events

		$cfs.bind_buttons = function() {
			$cfs.unbind_buttons();
			showNavi(opts, totalItems);
			enableNavi(opts, firstItem);
			if (opts.auto.pauseOnHover && opts.auto.play) {
				$wrp.hover(
					function() { $cfs.trigger('pause'); },
					function() { $cfs.trigger('play');	}
				);
			}
			if (opts.prev.button) {
				opts.prev.button.click(function(e) {
					e.preventDefault();
					$cfs.trigger('prev');
				});
				if (opts.prev.pauseOnHover && opts.auto.play) {
					opts.prev.button.hover(
						function() { $cfs.trigger('pause');	},
						function() { $cfs.trigger('play');	}
					);
				}
			}
			if (opts.next.button) {
				opts.next.button.click(function(e) {
					e.preventDefault();
					$cfs.trigger('next');
				});
				if (opts.next.pauseOnHover && opts.auto.play) {
					opts.next.button.hover(
						function() { $cfs.trigger('pause');	},
						function() { $cfs.trigger('play');	}
					)
				}
			}
			if ($.fn.mousewheel) {
				if (opts.prev.mousewheel) {
					$wrp.mousewheel(function(e, delta) {
						if (delta > 0) {
							e.preventDefault();
							num = (typeof opts.prev.mousewheel == 'number') ? opts.prev.mousewheel : '';
							$cfs.trigger('prev', num);
						}
					});
				}
				if (opts.next.mousewheel) {
					$wrp.mousewheel(function(e, delta) {
						if (delta < 0) {
							e.preventDefault();
							num = (typeof opts.next.mousewheel == 'number') ? opts.next.mousewheel : '';
							$cfs.trigger('next', num);
						}
					});
				}
			}
			if (opts.pagination.container) {
				if (opts.pagination.pauseOnHover && opts.auto.play) {
					opts.pagination.container.hover(
						function() { $cfs.trigger('pause');	},
						function() { $cfs.trigger('play');	}
					);
				}
			}
			if (opts.next.key || opts.prev.key) {
				$(document).keyup(function(e) {
					var k = e.keyCode;
					if (k == opts.next.key)	{
						e.preventDefault();
						$cfs.trigger('next');
					}
					if (k == opts.prev.key) {
						e.preventDefault();
						$cfs.trigger('prev');
					}
				});
			}
			if (opts.pagination.keys) {
				$(document).keyup(function(e) {
					var k = e.keyCode;
					if (k >= 49 && k < 58) {
						k = (k-49) * opts.items.visible;
						if (k <= totalItems) {
							e.preventDefault();
							$cfs.trigger('slideTo', [k, 0, true, opts.pagination]);
						}
					}
				});
			}
			if (opts.auto.play) {
				$cfs.trigger('play', opts.auto.delay);
				if ($.fn.nap && opts.auto.nap) {
					$cfs.nap('pause', 'play');
				}
			}
		};	//	/bind_buttons

		$cfs.unbind_buttons = function() {
			showNavi(opts, 'hide');
			enableNavi(opts, 'removeClass');
			if (opts.pagination.container) {
				getItems(opts.pagination.container).remove();
			}
		};	//	/unbind_buttons



		//	DEPRECATED
		$cfs.configuration = function(a, b) {
			debug(true, 'The "configuration" public method is deprecated, use the "configuration" custom event.');
			var cr = false;
			var fn = function(val) { cr = val; };
			if (!a) a = fn;
			if (!b) b = fn;
			$cfs.trigger('configuration', [a, b]);
			return cr;
		};

		$cfs.current_position = function() {
			debug(true, 'The "current_position" public method is deprecated, use the "currentPosition" custom event.');
			var cp = false;
			$cfs.trigger('currentPosition', function(val) { cp = val; });
			return cp;
		};

		$cfs.destroy = function() {
			debug(true, 'The "destroy" public method is deprecated, use the "destroy" custom event.');
			$cfs.trigger('destroy');
			return $cfs;
		};

		$cfs.link_anchors = function($c, se) {
			debug(true, 'The "link_anchors" public method is deprecated, use the "linkAnchors" custom event.');
			$cfs.trigger('linkAnchors', [$c, se]);
			return $cfs;
		};
		//	/DEPRECATED




		if ($cfs.parent().is('.caroufredsel_wrapper')) {
			var $wrp = $cfs.parent();
			$cfs.trigger('destroy');
		}
		var $wrp			= $cfs.wrap('<div class="caroufredsel_wrapper" />').parent(),
			opts 			= {},
			opts_orig		= o,
			totalItems		= getItems($cfs).length,
			firstItem 		= 0,
			autoTimeout		= null,
			autoInterval	= null,
			timerInterval	= null,
			pauseTimePassed	= 0,
			isPaused		= true,
			isPausedGlobal	= false,
			direction		= 'next';

		$cfs.init(opts_orig, true);
		$cfs.build();
		$cfs.bind_events();
		$cfs.bind_buttons();

		if (opts.items.start !== 0 && opts.items.start !== false) {
			var s = opts.items.start;
			if (s === true) {
				s = window.location.hash;
				if (!s.length) s = 0;
			} else if (s === 'random') {
				s = Math.floor(Math.random() * totalItems);
			}
			$cfs.trigger('slideTo', [s, 0, true, { duration: 0 }]);
		}
		var siz = setSizes($cfs, opts, false),
			itm = getCurrentItems($cfs, opts);

		if (opts.onCreate) {
			opts.onCreate.call($tt0, itm, siz);
		}

		$cfs.trigger('updatePageStatus', [true, siz]);
		$cfs.trigger('linkAnchors');
		return this;
	};

	//	public
	$.fn.carouFredSel.defaults = {
		debug: false,
		infinite: true,
		circular: true,
		direction: 'left',
		items: {
			start: 0
		},
		scroll: {
			easing: 'swing',
			pauseOnHover: false,
			mousewheel: false
		}
	};

	$.fn.carouFredSel.pageAnchorBuilder = function(nr, itm) {
		return '<a href="#"><span>'+nr+'</span></a>';
	};


	//	private
	function fx_fade(sO, c, x, d, f) {
		var o = {
			duration: d,
			easing: sO.easing
		};
		if (typeof f == 'function') o.complete = f;
		c.animate({
			opacity: x
		}, o);
	}
	function fx_cover(sO, c1, c2, o, d, prev) {
		var cur_w = getSizes(getItems(c2), o, true)[0],
			cur_p = (prev) ? -cur_w : cur_w,
			css_o = {},
			ani_o = {};
		css_o[o.d['width']] = cur_w;
		css_o[o.d['left']] = cur_p;
		ani_o[o.d['left']] = 0;
		c1.animate({ opacity: '+=0' }, d);
		c2.css(css_o).animate(ani_o, {
			duration: d,
			easing: sO.easing,
			complete: function() {
				$(this).remove();
			}
		});
	}
	function fx_uncover(sO, c, o, d, prev) {
		var cur_w = getSizes(getItems(c), o, true)[0],
			cur_p = (prev) ? cur_w : -cur_w,
			css_o = {},
			ani_o = {};
		css_o[o.d['width']] = cur_w;
		ani_o[o.d['left']] = cur_p;
		c.css(css_o).animate(ani_o, {
			duration: d,
			easing: sO.easing,
			complete: function() {
				$(this).remove();
			}
		});
	}

	function showNavi(o, t) {
		if (t == 'show' || t == 'hide') {
			var f = t;
		} else if (o.items.minimum >= t) {
			debug(o.debug, 'Not enough items: not scrolling');
			var f = 'hide';
		} else {
			var f = 'show';
		}
		if (o.prev.button) o.prev.button[f]();
		if (o.next.button) o.next.button[f]();
		if (o.pagination.container) o.pagination.container[f]();
	}
	function enableNavi(o, f) {
		if (o.circular || o.infinite) return;
		var fx = (f == 'removeClass' || f == 'addClass') ? f : false;
		if (o.next.button) {
			var fn = fx || (f == o.items.visible) ? 'addClass' : 'removeClass';
			o.next.button[fn]('disabled');
		}
		if (o.prev.button) {
			var fn = fx || (f == 0) ? 'addClass' : 'removeClass';
			o.prev.button[fn]('disabled');
		}
	}
	function sortParams(vals, typs) {
		var _arr = [];
		for (var a = 0, l1 = vals.length; a < l1; a++) {
			for (var b = 0, l2 = typs.length; b < l2; b++) {
				if (typeof vals[a] == typs[b] && !_arr[b]) {
					_arr[b] = vals[a];
					break;
				}
			}
		}
		return _arr;
	}
	function getKeyCode(k) {
		if (k == 'right')	return 39;
		if (k == 'left')	return 37;
		if (k == 'up')		return 38;
		if (k == 'down')	return 40;
		return -1;
	}
	function getObject(obj) {
		if (typeof obj == 'undefined') obj = {};
		return obj;
	}
	function getNaviObject(obj, pagi, auto) {
		if (typeof pagi != 'boolean') pagi = false;
		if (typeof auto != 'boolean') auto = false;

		obj = getObject(obj);
		if (typeof obj == 'string') {
			var temp = getKeyCode(obj);
			if (temp == -1) obj = $(obj);
			else 			obj = temp;
		}

		//	pagination
		if (pagi) {
			if (typeof obj 				== 'boolean')	obj = { keys: obj };
			if (typeof obj.jquery 		!= 'undefined')	obj = { container: obj };
			if (typeof obj.container	== 'string')	obj.container = $(obj.container);
			if (typeof obj.event		!= 'string')	obj.event = 'click';

		//	auto
		} else if (auto) {
			if (typeof obj == 'boolean')				obj = { play: obj };
			if (typeof obj == 'number')					obj = { pauseDuration: obj };

		//	prev + next
		} else {
			if (typeof obj.jquery	!= 'undefined')		obj = { button: obj };
			if (typeof obj 			== 'number')		obj = { key: obj };
			if (typeof obj.button	== 'string')		obj.button = $(obj.button);
			if (typeof obj.key		== 'string')		obj.key = getKeyCode(obj.key);
			if (typeof obj.event	!= 'string')		obj.event = 'click';
		}
		return obj;
	}
	function getItemIndex(num, dev, org, firstItem, totalItems, $cfs) {
		if (typeof num == 'string') {
			if (isNaN(num)) num = $(num);
			else 			num = parseInt(num);
		}
		if (typeof num == 'object') {
			if (typeof num.jquery == 'undefined') num = $(num);
			num = getItems($cfs).index(num);
			if (num == -1) num = 0;
			if (typeof org != 'boolean') org = false;
		} else {
			if (typeof org != 'boolean') org = true;
		}
		if (isNaN(num))	num = 0;
		else 			num = parseInt(num);
		if (isNaN(dev))	dev = 0;
		else 			dev = parseInt(dev);

		if (org) {
			num += firstItem;
		}
		num += dev;
		if (totalItems > 0) {
			while (num >= totalItems)	{	num -= totalItems; }
			while (num < 0)				{	num += totalItems; }
		}
		return num;
	}

	function getItems(c, f) {
		if (typeof f != 'string') f = '';
		return $('> *'+f, c);
	}
	function getCurrentItems(c, o) {
		return getItems(c, ':lt('+o.items.visible+')');
	}
	function getOldItemsPrev(c, o, n) {
		return getItems(c, ':lt('+(o.items.oldVisible+n)+'):gt('+(n-1)+')');
	}
	function getNewItemsPrev(c, o) {
		return getItems(c, ':lt('+o.items.visible+')');
	}
	function getOldItemsNext(c, o) {
		return getItems(c, ':lt('+o.items.oldVisible+')');
	}
	function getNewItemsNext(c, o, n) {
		return getItems(c, ':lt('+(o.items.visible+n)+'):gt('+(n-1)+')');
	}

	function resetMargin(i, o, m) {
		var x = (typeof m == 'boolean') ? m : false;
		if (typeof m != 'number') m = 0;
		i.each(function() {
			var t = parseInt($(this).css(o.d['marginRight']));
			if (isNaN(t)) t = 0;
			$(this).data('cfs_tempCssMargin', t);
			$(this).css(o.d['marginRight'], ((x) ? $(this).data('cfs_tempCssMargin') : m + $(this).data('cfs_origCssMargin')));
		});
	}
	function getSizes(i, o, wrapper) {
		s1 = getTotalSize(i, o, 'width', wrapper);
		s2 = getLargestSize(i, o, 'height', wrapper);
		return [s1, s2];
	}
	function getLargestSize(i, o, dim, wrapper) {
		if (typeof wrapper != 'boolean') wrapper = false;
		if (typeof o[o.d[dim]] == 'number' && wrapper) return o[o.d[dim]];
		if (typeof o.items[o.d[dim]] == 'number') return o.items[o.d[dim]];
		var di2 = (dim.toLowerCase().indexOf('width') > -1) ? 'outerWidth' : 'outerHeight';
		return getTrueLargestSize(i, o, di2);
	}
	function getTrueLargestSize(i, o, dim) {
		var s = 0;
		i.each(function() {
			var m = $(this)[o.d[dim]](true);
			if (s < m) s = m;
		});
		return s;
	}
	function getTrueInnerSize($el, o, dim) {
		var siz = $el[o.d[dim]](),
			arr = (o.d[dim].toLowerCase().indexOf('width') > -1) ? ['paddingLeft', 'paddingRight'] : ['paddingTop', 'paddingBottom'];
		for (a = 0, l = arr.length; a < l; a++) {
			var m = parseInt($el.css(arr[a]));
			if (isNaN(m)) m = 0;
			siz -= m;
		}
		return siz;
	}
	function getTotalSize(i, o, dim, wrapper) {
		if (typeof wrapper != 'boolean') wrapper = false;
		if (typeof o[o.d[dim]] == 'number' && wrapper) return o[o.d[dim]];
		if (typeof o.items[o.d[dim]] == 'number') return o.items[o.d[dim]] * i.length;
		var di2 = (dim.toLowerCase().indexOf('width') > -1) ? 'outerWidth' : 'outerHeight';
		return getTotalSizeVariable(i, o, di2);
	}
	function getTotalSizeVariable(i, o, dim) {
		var s = 0;
		i.each(function() {
			s += $(this)[o.d[dim]](true);
		});
		return s;
	}

	function hasVariableSizes(i, o, dim) {
		var s = false,
			v = false;
		i.each(function() {
			c = $(this)[o.d[dim]](true);
			if (s === false) s = c;
			else if (s != c) v = true;
		});
		return v;
	}

	function mapWrapperSizes(ws, o, p) {
		if (typeof p != 'boolean') p = true;
		var pad = (o.usePadding && p) ? o.padding : [0, 0, 0, 0];
		var wra = {};
			wra[o.d['width']] = ws[0] + pad[1] + pad[3];
			wra[o.d['height']] = ws[1] + pad[0] + pad[2];

		return wra;
	}
	function setSizes($c, o, p) {
		var $w = $c.parent(),
			$i = getItems($c),
			$v = getCurrentItems($c, o),
			sz = mapWrapperSizes(getSizes($v, o, true), o, p);

		$w.css(sz);

		if (o.usePadding) {
			var $l = $v.filter(':last');
			$l.css(o.d['marginRight'], $l.data('cfs_origCssMargin') + o.padding[o.d[1]]);
			$c.css(o.d['top'], o.padding[o.d[0]]);
			$c.css(o.d['left'], o.padding[o.d[3]]);
		}
		$c.css(o.d['width'], getTotalSize($i, o, 'width')*2);
		$c.css(o.d['height'], getLargestSize($i, o, 'height'));
		return sz;
	}

	function getPadding(p) {
		if (typeof p == 'undefined') return [0, 0, 0, 0];

		if (typeof p == 'number') return [p, p, p, p];
		else if (typeof p == 'string') p = p.split('px').join('').split(' ');

		if (typeof p != 'object') {
			return [0, 0, 0, 0];
		}
		for (var i = 0; i < 4; i++) {
			p[i] = parseInt(p[i]);
		}
		switch (p.length) {
			case 0: return [0, 0, 0, 0];
			case 1: return [p[0], p[0], p[0], p[0]];
			case 2: return [p[0], p[1], p[0], p[1]];
			case 3: return [p[0], p[1], p[2], p[1]];
			default: return [p[0], p[1], p[2], p[3]];
		}
	}
	function getAlignPadding(itm, o) {
		var x = (typeof o[o.d['width']] == 'number') ? Math.ceil(o[o.d['width']] - getTotalSize(itm, o, 'width')) : 0;
		switch (o.align) {
			case 'left':
				return [0, x];
				break;
			case 'right':
				return [x, 0];
				break;
			case 'center':
			default:
				var x1 = Math.ceil(x/2), x2 = Math.floor(x/2);
				return [x1, x2];
				break;
		}
	}

	function getVisibleItemsPrev($c, o, nI) {
		var items = getItems($c),
			total = 0,
			start = o.items.visible - nI - 1,
			x = 0;

		if (start < 0) start = items.length-1;
		for (var a = start; a >= 0; a--) {
			total += items.filter(':eq('+ a +')')[o.d['outerWidth']](true);
			if (total > o.maxDimention) return x;
			if (a == 0) a = items.length;
			x++;
		}
	}
	function getVisibleItemsNext($c, o, nI) {
		var items = getItems($c),
			total = 0,
			x = 0;

		for (var a = nI, l = items.length-1; a <= l; a++) {
			total += items.filter(':eq('+ a +')')[o.d['outerWidth']](true);
			if (total > o.maxDimention) return x;
			if (a == items.length-1) a = -1;
			x++;
		}
	}

	function debug(d, m) {
		if (!d) return false;
		if (typeof m == 'string') m = 'carouFredSel: ' + m;
		if (window.console && window.console.log) window.console.log(m);
		return false;
	}



	$.fn.caroufredsel = function(o) {
		return this.carouFredSel(o);
	};

})(jQuery);
jQuery(function($){
	$(".topic_list li").hover(function(){
		$(this).addClass('h');
	},function(){
		$(this).removeClass('h');
	});
	
	$(".topic_list .tl_c .pic").hover(function(){
		var li = $(this).parent().parent();
		var html = $('.show_big_img',li).html();
		if(html.length > 10)
		{
			html = html.replace(/timgsrc/g,'src');
			html = '<div class="tl_pic_float">'+ html +'<i></i></div>';
			var offset = $(this).offset();
			var left = offset.left;
			var top = offset.top;
			$("body").append(html);
			$(".tl_pic_float").css({"top":top-128,"left":left-42});
		}
		
	},function(){
		$(".tl_pic_float").remove();
	});
});
