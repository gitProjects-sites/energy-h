'use strict';
let app = {
	body: $('body'),
	header: $('.header'),
	cookieBar: $('.cookie-bar'),
	scrollbar_width: 0,
	init: function () {
		app.scrollbar_width = app.scrollbarWidth();
	},
	//scrollbar width
	scrollbarWidth: function () {
		var block = $('<div>').css({
				'height': '50px',
				'width': '50px'
			}),
			indicator = $('<div>').css({
				'height': '200px'
			});
		$('body').append(block.append(indicator));
		var w1 = $('div', block).innerWidth();
		block.css('overflow-y', 'scroll');
		var w2 = $('div', block).innerWidth();
		$(block).remove();
		return (w1 - w2);
	},
	fillScrollWidth: function (status, obj) {
		var container = $(obj),
			body = app.body,
			ex = '.fancybox-slide';

		if (status == 'hide') {
			if (!body.hasClass('hide-scroll')) {
				body.addClass('hide-scroll');
				if (document.documentElement.clientWidth != window.innerWidth) {
					app.header.css('padding-right', app.scrollbar_width);
					app.cookieBar.css('padding-right', app.scrollbar_width);
				}
				bodyScrollLock.lock(Array.from(document.querySelectorAll(ex)));
			} else if (!body.hasClass('hide-scroll-2')) {
				body.addClass('hide-scroll-2');
			} else if (!body.hasClass('hide-scroll-3')) {
				body.addClass('hide-scroll-3');
			}
		} else {
			if (body.hasClass('hide-scroll-3')) {
				body.removeClass('hide-scroll-3')
			} else if (body.hasClass('hide-scroll-2')) {
				body.removeClass('hide-scroll-2')
			} else {
				body.removeClass('hide-scroll');
				//				body.css('padding-right', '');
				app.header.css('padding-right', '');
				app.cookieBar.css('padding-right', '');
				bodyScrollLock.unlock(Array.from(document.querySelectorAll(ex)));
			}

		}
	},
	headerFixed: function () {
		if (app.site_modals_open) return;
		if ($(window).scrollTop() > 0) {
			app.header.addClass('fixed');
		} else {
			if (!app.body.hasClass('hide-scroll')) {
				app.header.removeClass('fixed');
			}
		}
	},
	precent: function (type, x, y) {
		switch (type) {
			case 1: //How many percent is the number X of the number Y
				return (x * 100) / y;
			case 2: //What is X percent of the number Y
				return (y * x) / 100;
		}
	},
	moutationObserver: function (queriesArr, callback) {
		queriesArr.forEach(function (obj) {
			$(obj.element).each(function () {
				if (typeof callback === 'function') callback('added', this);
			});
		});
		let observer = new MutationSummary({
			callback: function (mutations) {
				let mutationAdded = mutations[0].added,
					mutationRemoved = mutations[0].removed;

				console.log(mutations);
				if (mutationAdded.length) {
					mutationAdded.forEach(function (obj) {
						if (typeof callback === 'function') callback('added', obj);
					});
				}
				if (mutationRemoved.length) {
					mutationRemoved.forEach(function (obj) {
						if (typeof callback === 'function') callback('removed', obj);
					});
				}
			},
			queries: queriesArr
		});
	},
	resizeDelay: function (delay, callback) {
		var resize_timer;
		$(window).resize(function () {
			clearTimeout(resize_timer);
			resize_timer = setTimeout(function () {
				//content
				if (typeof callback === 'function') {
					callback();
				}
			}, delay);
		});
	},
}

app.init();


$(document).ready(function () {
	//	app.headerFixed();
	//	app.placeholderToggle(".input input, .textarea textarea");
	//	app.customSelect.init();
	//	app.zIndexFromLast($('.custom-select'), 3);
});
//$(document).scroll(function () {
//    app.headerFixed();      
//});

app.moutationObserver([
		{
			element: '[data-fs-type]'
		}
	],
	function (status, obj) {
		if (status === 'added') {
			window.FormComponents.initEl(obj);
		} else if (status === 'removed') {
			window.FormComponents.removeEvent(obj);
		}
	}
);




function headerFixation() {
//	let thEl = app.header.children('.h-top'),
//		svgLogo = app.header.find('.logo use:nth-child(1)');
	calc();
	$(document).scroll(function () {
		calc();
	});

	function calc() {
		let scrollOffset = parseInt(getComputedStyle(app.header[0]).getPropertyValue('--h-top-offset'), 10),
			scroll = $(document).scrollTop();
//			scrollPrecent = (scroll < scrollOffset) ? app.precent(1, scroll, scrollOffset) : 100,
//			fixScroll = (scroll <= scrollOffset) ? scroll : scrollOffset,
//			getLogoOffset = parseInt(getComputedStyle(app.header[0]).getPropertyValue('--logo-fixed-offset'), 10),
//			logoOffset = app.precent(2, scrollPrecent, getLogoOffset),
//			getLogoScale = parseFloat(getComputedStyle(app.header[0]).getPropertyValue('--logo-fixed-scale'), 10),
//			logoScale = 1 - app.precent(2, scrollPrecent, 1 - getLogoScale);
//
//		app.header.css('transform', `matrix3d(1,0,0.00,0,0.00,1,0.00,0,0,0,1,0,0,-${fixScroll},0,1)`);
//		thEl.css('transform', `matrix3d(1,0,0.00,0,0.00,1,0.00,0,0,0,1,0,0,${fixScroll},0,1)`);
//		svgLogo.css('transform', `translateY(${logoOffset}%) scale(${logoScale})`);

		if (scroll > scrollOffset) {
			app.header.addClass('fixed');
		} else {
			app.header.removeClass('fixed');
		}
	}


}

headerFixation();






//object-fit
$(function () {
	objectFitImages()
});




//form validation
$(".validation").each(function () {
	$(this).parsley({
		excluded: '.excluded',
		classHandler: function (el) {
			return el.$element.closest('.err-target');
		}
	});
});


$('[data-fancybox]').fancybox({
	touch: true,
	smallBtn: true,
	animationEffect: "fade",
	backFocus: false,
	autoFocus: false,
	lang: 'ru',
	i18n: {
		ru: {
			CLOSE: 'Закрыть',
			NEXT: 'Вперёд',
			PREV: 'Назад',
			ERROR: '<b>Ошибка 404</b>Страница не найдена',
			PLAY_START: 'Запустить слайдшоу',
			PLAY_STOP: 'Остановить слайдшоу',
			FULL_SCREEN: 'На весь экран',
			THUMBS: 'Миниатюры',
			DOWNLOAD: 'Загрузить',
			SHARE: 'Поделится',
			ZOOM: 'Увеличить'
		}
	},
	beforeShow: function (instance, current) {
		app.fillScrollWidth('hide', 'body');
	},
	afterClose: function (instance, current) {
		app.fillScrollWidth('show', 'body');
	}
});

$(document).on('click', '[data-modal]', function (e) {
	e.preventDefault();
	var $that_attr = $(this).attr('href');
	$.fancybox.close();
	$.fancybox.open({
		touch: false,
		src: $that_attr,
		type: 'inline',
		smallBtn: true,
		animationEffect: "fade",
		backFocus: false,
		autoFocus: false,
		lang: 'ru',
		i18n: {
			ru: {
				CLOSE: 'Закрыть',
				NEXT: 'Вперёд',
				PREV: 'Назад',
				ERROR: '<b>Ошибка 404</b>Страница не найдена',
				PLAY_START: 'Запустить слайдшоу',
				PLAY_STOP: 'Остановить слайдшоу',
				FULL_SCREEN: 'На весь экран',
				THUMBS: 'Миниатюры',
				DOWNLOAD: 'Загрузить',
				SHARE: 'Поделится',
				ZOOM: 'Увеличить'
			}
		},
		beforeShow: function (instance, current) {
			app.fillScrollWidth('hide', 'body');
		},
		afterClose: function (instance, current) {
			app.fillScrollWidth('show', 'body');
		}
	});
});


$().fancybox({
	touch: true,
	selector: '[data-gallery]',
	smallBtn: true,
	buttons: false,
	animationEffect: "fade",
	autoFocus: true,
	backFocus: false,
	//	afterLoad: function (instance, current) {
	//		if (instance.group.length > 1 && current.$content) {
	//			current.$content.append(`<button type="button" data-fancybox-close class="fancybox-button fancybox-close-small" title="Close"><svg xmlns="http://www.w3.org/2000/svg" version="1" viewBox="0 0 24 24"><path d="M13 12l5-5-1-1-5 5-5-5-1 1 5 5-5 5 1 1 5-5 5 5 1-1z"></path></svg></button>
	//								  <a data-fancybox-next class="button-next fb-sw-button-next" href="javascript:;"><i class="icon-arr-r"></i></a>
	//								  <a data-fancybox-prev class="button-prev fb-sw-button-prev" href="javascript:;"><i class="icon-arr-l"></i></a>`);
	//		}
	//	},
	beforeShow: function (instance, current) {
		if (!app.body.hasClass('gallery-open')) {
			app.body.addClass('gallery-open');
			app.fillScrollWidth('hide', 'body');
		}
		instance.$refs.container.removeClass('last-slide first-slide');
		if (current.index >= instance.group.length - 1) {
			instance.$refs.container.addClass('last-slide');
		} else if (current.index === 0) {
			instance.$refs.container.addClass('first-slide');
		}
	},
	afterClose: function (instance, current) {
		app.body.removeClass('gallery-open');
		app.fillScrollWidth('show', 'body');
	}
});


$('.ou-slider').each(function () {
	let that = $(this),
		btnWrp = that.siblings('.ou-btn-wrp');

	let sliderInstance = new Swiper(that[0], {
		slidesPerView: 'auto',
		spaceBetween: 0,
		touchRatio: 1,
		loop: false,
		resistance: true,
		resistanceRatio: 0,
		navigation: {
			nextEl: btnWrp.find('.sw-button-next')[0],
			prevEl: btnWrp.find('.sw-button-prev')[0]
		}

	});
});



$('.ribbon').each(function () {
	let that = $(this),
		slider = that.find('.ribbon-slider');

	let sliderInstance = new Swiper(slider[0], {
		slidesPerView: 'auto',
		spaceBetween: 0,
		touchRatio: 1,
		resistance: true,
		resistanceRatio: 0,
		loop: false,
		navigation: {
			nextEl: that.find('.sw-button-next')[0],
			prevEl: false
		},
		breakpoints: {
			1: {
				slidesPerView: 'auto'
			}
		},
		on: {
			init: function (swiper) {
				swiper.slideOver = false;
				let activeIndex = $(swiper.slides).filter('.swiper-slide.active').index();
				if (activeIndex > 0 && window.matchMedia('(min-width: 992px)').matches) activeIndex = activeIndex - 1;
				swiper.slideTo(activeIndex, 0);
				ribbonArrCalc(swiper);
			},
			resize: function (swiper) {
				ribbonArrCalc(swiper);
			},
			setTranslate: function (swiper) {
				if (swiper.isEnd) {
					that.addClass('end');
				} else {
					that.removeClass('end');
				}
			}
		}
	});
	that.on('click', '.sw-button-prev', function (e) {
		e.preventDefault();
		sliderInstance.slideTo(0);
	});
});

function ribbonArrCalc(swiper) {
	let ribbon = $(swiper.el).closest('.ribbon'),
		arrowWidth = parseInt(getComputedStyle(ribbon[0]).getPropertyValue('--ribbon-arrow-size'), 10) + parseInt(getComputedStyle(ribbon[0]).getPropertyValue('--ribbon-items-offset'), 10);
	if (swiper.slideOver) {
		if (swiper.virtualSize < swiper.width + arrowWidth) {
			ribbon.removeClass('over');
			swiper.slideOver = false;
		}
	} else {
		if (swiper.virtualSize > swiper.width) {
			ribbon.addClass('over');
			swiper.slideOver = true;
		}
	}
}




$('.ar-img-slider').each(function () {
	let slider = $(this),
		pgWrp = slider.find('.swiper-pagination-custom');

	new Swiper(slider[0], {
		slidesPerView: 1,
		speed: 800,
		loop: true,
		touchRatio: 0.1,
		resistance: false,
		resistanceRatio: 0.1,
		longSwipesRatio: 0.1,
		followFinger: false,
		spaceBetween: 0,
		pagination: {
			el: pgWrp[0],
			clickable: true,
			bulletClass: 'sw-dot',
			bulletActiveClass: 'sw-dot-active',
			renderBullet: function (index, className) {
				return '<button type="button" class="' + className + '"></button>';
			}
		}
	});
});


$(document).on('click', '.js-cookie-close', function (e) {
	e.preventDefault();
	let bar = $(this).closest('.cookie-bar');
	bar.addClass('close');
	setCookie('BITRIX_SM_cookie_use', 1);
	setTimeout(function () {
		bar.hide();
	}, 600);
});


$(document).on('click', '.nav-btn', function (e) {
	e.preventDefault();
	app.body.toggleClass('nav-open');
});

$(document).on('mousedown', function (event) {
	if ($(event.target).closest('body.nav-open .nav-btn, body.nav-open .h-nav-list').length)
		return;
	app.body.removeClass('nav-open');
	event.stopPropagation();
});

$(document).ready(function () {
	let searchInput = $('.h-search input');
	if (searchInput.val() === '') searchInput.parent().removeClass('active');
});


$(document).on({
	focus: function (e) {
		e.preventDefault();
		$(this).parent().addClass('active');
	},
	blur: function (e) {
		e.preventDefault();
		let val = $(this).val();

		if (!val.replace(/\s/g, '').length) {
			val = '';
			$(this).val(val);
		};
		if (val === '') $(this).parent().removeClass('active');
	}
}, '.h-search input');


$(document).on('click', '.h-search-close', function (e) {
	e.preventDefault();
	$(this).siblings('input').val('').trigger('change');
	$(this).parent().removeClass('active');
});


$(document).ready(function () {
	scrollAnimate();
});

$(document).scroll(function () {
	scrollAnimate();
});

function scrollAnimate() {
	$('.scroll-animate').each(function () {
		if (!$(this).hasClass('scroll-animate-ok')) {
			let animateBoxOffset = $(this).offset().top,
				scroll = $(window).scrollTop() + $(window).height() - ($(window).height() / 2);

			if (scroll >= animateBoxOffset) {
				$(this).addClass('scroll-animate-ok');

				$(this).find('.ed-count b').each(function () {
					animateValue(this, 0, +$(this).attr('data-count-to'), 2000);
				});
			}
		}
	});
}


function animateValue(obj, start, end, duration) {
	let startTimestamp = null;
	let dg = Number.isInteger(end);
	const step = (timestamp) => {
		if (!startTimestamp) startTimestamp = timestamp;
		const progress = Math.min((timestamp - startTimestamp) / duration, 1);
		let prg = 0;
		if (dg) {
			prg = (progress * (end - start) + start).toFixed(0);
		} else {
			prg = (progress * (end - start) + start).toFixed(1);
		}
		obj.innerHTML = Number(prg).toLocaleString('ru-RU');
		if (progress < 1) {
			window.requestAnimationFrame(step);
		}
	};
	window.requestAnimationFrame(step);
}





//map
let mapOnScreen = {
	map_id: 'map',
	map: null,
	map_padding: {
		left: 0,
		right: 0
	},
	zoom: 14,
	flyToOffset: [0, 140],
	fit_bouds_duration: 2000,



	GEOJSON: {
		"features": []
	},
	group_objects: [],
	markers_on_map: [],
	allPopups: [],
	bounds: null,
	first_start_flag: true,
	custom_map: false,

	markers_tab_container: $('.mac-tabs'),
	marker_list_container: $('.mac-list'),

	mapPadding: function () {

		if (window.matchMedia('(max-width: 767px)').matches) {
			mapOnScreen.map_padding = {
				left: 15,
				right: 15
			}
		} else {
			mapOnScreen.map_padding = {
				left: 100,
				right: 100
			}
		}


	},
	init: function () {
		let that = this;
		if ($('#' + mapOnScreen.map_id).hasClass('custom-map')) {
			that.custom_map = true;
		}

		//first load

		this.bounds = new mapboxgl.LngLatBounds();


		this.createGEOJSON();
		this.create();
		this.map.on('load', function () {
			that.markersSet();
			that.changeGroup(that.markers_tab_container.find('.mac-tab-item.active'));

			that.createMarkers.markers();
			mapOnScreen.map.on('data', function (e) {
				if (e.sourceId !== 'marker_point' || !e.isSourceLoaded) return;
				that.createMarkers.dataLoad();
				if (that.first_start_flag) {
					that.first_start_flag = false;
					that.map.fitBounds(that.bounds, {
						padding: 20,
						duration: 0,
						maxZoom: 13
					});

				}

			});
			that.createMarkers.events();
			that.popup();


			that.map.scrollZoom.disable();
			that.map.scrollZoom.setWheelZoomRate(0.01);


			let overlayTimmers = [];
			that.map.on('wheel', function (event) {
				if (event.originalEvent.ctrlKey) {
					event.originalEvent.preventDefault();
					if (!that.map.scrollZoom._enabled) that.map.scrollZoom.enable();
				} else {
					overlayTimmers.forEach(function (obj) {
						clearTimeout(obj);
					});
					overlayTimmers = [];
					$('#' + that.map_id).addClass('ovarlay-show');
					let timmerId = setTimeout(function () {
						$('#' + that.map_id).removeClass('ovarlay-show');
					}, 800);
					overlayTimmers.push(timmerId);

					if (that.map.scrollZoom._enabled) that.map.scrollZoom.disable();
				}
			});


		});

		this.markerList.init();

	},
	create: function () {
		let that = this;
		mapboxgl.accessToken = 'pk.eyJ1IjoibmVvbWFzdGVyeCIsImEiOiJja2Zuems2dGIwaTh2MnFtcXlzdTdrM2FqIn0.YYc8Yu9wb3iYUOBMI8hqqA';
		mapOnScreen.map = new mapboxgl.Map({
			container: mapOnScreen.map_id,
			style: 'mapbox://styles/neomasterx/clita411k004w01qyahcf4j8x',
			center: [0, 0],
			zoom: 14
		}).setPadding({
			left: this.map_padding.left,
			right: this.map_padding.right
		});
		this.map._fadeDuration = 0;

		//mobile drag disable
		if (window.matchMedia('(max-width: 767px)').matches) {
			this.map['dragPan'].disable();
		}


	},
	markersSet: function () {
		let that = this,
			icoOpt = {
				'circle-stroke-width': 20,
				'circle-radius': 40,
				'text-size': 30
			}

		if (window.matchMedia('(max-width: 1199px)').matches) {
			icoOpt = {
				'circle-stroke-width': 15,
				'circle-radius': 30,
				'text-size': 25
			}
		}
		if (window.matchMedia('(max-width: 991px)').matches) {
			icoOpt = {
				'circle-stroke-width': 10,
				'circle-radius': 20,
				'text-size': 20
			}
		}


		that.map.addSource('marker_point', {
			type: 'geojson',
			data: that.GEOJSON,
			//								data: 'https://docs.mapbox.com/mapbox-gl-js/assets/earthquakes.geojson',
			cluster: true,
			clusterRadius: 75,
			clusterMaxZoom: 20
		});

		that.map.addLayer({
			id: 'clusters',
			type: 'circle',
			source: 'marker_point',
			filter: ['has', 'point_count'],
			paint: {
				//					'circle-color': 'rgba(0, 0, 0, 0)'
				'circle-color': 'rgba(218, 41, 28, 1)',
				'circle-stroke-color': 'rgba(218, 41, 28, 0.6)',
				'circle-stroke-width': icoOpt['circle-stroke-width'],
				'circle-radius': icoOpt['circle-radius'],
				'circle-opacity-transition': {
					'duration': 300
				},
				'circle-stroke-opacity-transition': {
					'duration': 300
				}
			}

		});
		that.map.addLayer({
			id: 'cluster-count',
			type: 'symbol',
			source: 'marker_point',
			filter: ['has', 'point_count'],
			layout: {
				'text-field': '{point_count_abbreviated}',
				'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
				'text-size': icoOpt['text-size'],
				'text-allow-overlap': true
			},
			paint: {
				'text-color': 'rgba(255, 255, 255, 1)',
				'text-color-transition': {
					'duration': 300
				}
			}
		});
		that.map.addLayer({
			id: 'points',
			type: 'circle',
			source: 'marker_point',
			filter: ['!', ['has', 'point_count']],
			paint: {
				'circle-color': 'rgba(0, 0, 0, 0)',
				'circle-radius': 10,
				//
				//				'circle-color': 'rgba(213, 64, 64, 0.9)',
				//				'circle-stroke-color': 'rgba(213, 64, 64, 0.6)',
				//				'circle-stroke-width': 4,
				//				'circle-radius': 8
			}
		}, 'clusters');

	},
	createMarkers: {
		markers: {},
		markers_on_screen: {},
		new_markers: {},
		markers_id: null,

		update: function (show_delay) {
			let that = mapOnScreen.createMarkers;

			for (var i = 0; i < mapOnScreen.markers_on_map.length; i++) {
				$(mapOnScreen.markers_on_map[i].getElement()).removeClass('visible');
			}

			that.new_markers = {};
			let features = mapOnScreen.map.querySourceFeatures('marker_point');

			for (var i = 0; i < features.length; i++) {
				var props = features[i].properties,
					id = props.id;
				if (!props.cluster) {
					for (var im = 0; im < mapOnScreen.markers_on_map.length; im++) {
						if (mapOnScreen.markers_on_map[im].mark_id === id) {
							$(mapOnScreen.markers_on_map[im].getElement()).addClass('visible');
						}
					}
				}
			}
			that.markers_on_screen = that.new_markers;
		},

		markers: function () {
			let that = mapOnScreen;
			for (var i = 0; i < mapOnScreen.markers_on_map.length; i++) {
				$(mapOnScreen.markers_on_map[i].getElement()).fadeOut(300)
					.promise().done(function (obj) {
						obj.remove();
					});

			}


			that.group_objects.forEach(function (marker) {
				// create a DOM element for the marker
				var el = document.createElement('div');

				el.className = 'marker-in-map';
				el.setAttribute('id', 'marker-id-' + marker[0]);
				el.setAttribute('data-marker-id', marker[0]);

				$(el).append($(marker[3])).hide().delay(300).fadeIn(300);

				//				mapOnScreen.bounds.extend([marker[2], marker[1]]);
				// add marker to map
				let current_marker = new mapboxgl.Marker(el)
					.setLngLat([marker[2], marker[1]])
					.addTo(mapOnScreen.map);

				current_marker.mark_id = marker[0];
				that.markers_on_map.push(current_marker);

			});

		},

		dataLoad: function () {
			mapOnScreen.map.on('move', mapOnScreen.createMarkers.update);
			mapOnScreen.map.on('moveend', mapOnScreen.createMarkers.update);
			mapOnScreen.map.on('idle', mapOnScreen.createMarkers.update);
			mapOnScreen.createMarkers.update();

		},
		events: function () {
			mapOnScreen.map.on('click', 'clusters', function (e) {
				var features = mapOnScreen.map.queryRenderedFeatures(e.point, {
					layers: ['clusters']
				});
				var clusterId = features[0].properties.cluster_id;
				mapOnScreen.map.getSource('marker_point').getClusterExpansionZoom(
					clusterId,
					function (err, zoom) {
						if (err) return;
						mapOnScreen.map.easeTo({
							center: features[0].geometry.coordinates,
							zoom: zoom,
							duration: 1000
						});
					}
				);
			});

			mapOnScreen.map.on('mouseenter', 'clusters', function () {
				mapOnScreen.map.getCanvas().style.cursor = 'pointer';
			});
			mapOnScreen.map.on('mouseleave', 'clusters', function () {
				mapOnScreen.map.getCanvas().style.cursor = '';
			});



			$('#' + mapOnScreen.map_id).on('click', '.marker-in-map', function (e) {
				let id = Number($(this).attr('data-marker-id'));
				mapOnScreen.openPopup(id);
			});
		}
	},
	openPopup: function (id) {
		let currentObject = false,
			i = 0;
		while (i < mapOnScreen.group_objects.length) {
			if (mapOnScreen.group_objects[i][0] === id) {
				currentObject = mapOnScreen.group_objects[i];
				break;
			}
			i++;
		}
		mapOnScreen.allPopups.forEach(function (obj) {
			obj.remove();
		});
		mapOnScreen.allPopups = [];
		mapOnScreen.map.flyTo({
			center: [mapOnScreen.group_objects[i][2], mapOnScreen.group_objects[i][1]],
			offset: mapOnScreen.flyToOffset,
			duration: 5000,
			zoom: mapOnScreen.zoom,
			curve: 3
		});
		let popup = new mapboxgl.Popup()
			.setLngLat([currentObject[2], currentObject[1]])
			.setHTML(currentObject[3])
			.addTo(mapOnScreen.map);

		mapOnScreen.allPopups.push(popup);

	},
	createGEOJSON: function () {
		let that = this,
			geo_arry = [];

		that.bounds = new mapboxgl.LngLatBounds();

		that.group_objects.forEach(function (obj, i) {
			let push_array = {
				'properties': {
					'id': obj[0],
					'lat': obj[2],
					'lng': obj[1]
				},
				'geometry': {
					'coordinates': [obj[2], obj[1]]
				}
			}
			geo_arry.push(push_array);

			that.bounds.extend([obj[2], obj[1]]);


		});
		this.GEOJSON.features = geo_arry;


	},
	getOffset: function () {
		//		map_padding
	},
	markerList: {
		init: function () {
			this.create();
		},
		showAll: function () {
			for (var i = 0; i < mapOnScreen.markers_on_map.length; i++) {
				let elem = $(mapOnScreen.markers_on_map[i].getElement());
				elem.removeClass('active');
				elem.removeClass('no-active');
			}
			mapOnScreen.map.setPaintProperty('clusters', 'circle-opacity', 1);
			mapOnScreen.map.setPaintProperty('clusters', 'circle-stroke-opacity', 1);
			mapOnScreen.map.setPaintProperty('cluster-count', 'text-color', 'rgba(255, 255, 255, 1)');
			mapOnScreen.map.fitBounds(mapOnScreen.bounds, {
				padding: 20,
				duration: mapOnScreen.fit_bouds_duration,
				maxZoom: 13
			});
			$('[data-marker-id]').removeClass('active');
			let show_all_button = $('.show-all');
			setTimeout(function () {
				$('.mac-box').parent().slideDown(600);
				show_all_button.removeClass('active');
				setTimeout(function () {
					$('.mac-box').removeClass('disabled active');
					show_all_button.slideUp(600);
				}, 300);
			}, 0);
		},
		click: function (id) {
			id = Number(id);

			if (!mapOnScreen.custom_map) {
				mapOnScreen.map.setPaintProperty('clusters', 'circle-opacity', 0);
				mapOnScreen.map.setPaintProperty('clusters', 'circle-stroke-opacity', 0);
				mapOnScreen.map.setPaintProperty('cluster-count', 'text-color', 'rgba(255, 255, 255, 0)');

				for (var i = 0; i < mapOnScreen.markers_on_map.length; i++) {
					let elem = $(mapOnScreen.markers_on_map[i].getElement());
					elem.removeClass('active');
					elem.addClass('no-active');
				}
			}
			for (let i = 0; i < mapOnScreen.group_objects.length; i++) {
				if (mapOnScreen.group_objects[i][0] == id) {
					if (!mapOnScreen.custom_map) {
						let elem = $('#marker-id-' + id);
						elem.removeClass('no-active');
						elem.addClass('active');
					}
					mapOnScreen.map.flyTo({
						center: [mapOnScreen.group_objects[i][2], mapOnScreen.group_objects[i][1]],
						duration: 5000,
						offset: mapOnScreen.flyToOffset,
						zoom: mapOnScreen.zoom,
						curve: 3
					});
					break;
				}
			}
			if (!mapOnScreen.custom_map) {
				let show_all_button = $('.show-all');

				$('[data-marker-id]').removeClass('active disabled');
				$('[data-marker-id=' + id + ']').addClass('active');
				show_all_button.css('display', 'inline-block').hide().slideDown(600);
				setTimeout(function () {
					$('.mac-box:not(.active)').addClass('disabled').parent().slideUp(600);
					$('.mac-box.active[data-marker-id=' + id + ']').parent().slideDown(600);
					show_all_button.addClass('active');
				}, 0);
			}




		},
		create: function () {
			let list_html = null;
			mapOnScreen.group_objects.forEach(function (marker_obj) {
				let list_item_html = marker_obj[3];

				if (list_html == null) {
					list_html = list_item_html
				} else {
					list_html = list_html + list_item_html
				}
			});
			mapOnScreen.marker_list_container.empty();
			$(list_html).appendTo(mapOnScreen.marker_list_container);
		}
	},
	filter: function (group_name) {
		if (group_name == 'all' || group_name == '') {
			this.group_objects = objects;
		} else {
			this.group_objects = [];
			for (let i = 0; i < objects.length; i++) {
				if (objects[i][3].toString().indexOf(group_name) !== -1) {
					this.group_objects.push(objects[i]);
				}
			}
		}
	},
	mapTab: function (group_name) {
		$('.mt-content:not(#' + group_name + ')').hide();
		$('#' + group_name).show();
	},
	changeGroup: function (obj) {
		let that = this,
			group_name;

		if ($(obj).length) {
			group_name = $(obj).attr('href').replace(/#/g, '');
		} else {
			group_name = 'all';
		}

		this.filter(group_name);

		this.createGEOJSON();


		this.map.getSource('marker_point').setData(this.GEOJSON);
		mapOnScreen.map.setPaintProperty('clusters', 'circle-opacity', 1);
		mapOnScreen.map.setPaintProperty('clusters', 'circle-stroke-opacity', 1);
		mapOnScreen.map.setPaintProperty('cluster-count', 'text-color', 'rgba(255, 255, 255, 1)');


		for (let i = 0; that.group_objects.length < i; i++) {
			that.bounds.extend([that.group_objects[i][2], that.group_objects[i][1]]);
		}
		that.createMarkers.markers();


		if (!that.first_start_flag) {
			that.map.fitBounds(that.bounds, {
				padding: 20,
				duration: that.fit_bouds_duration,
				maxZoom: 13
			});
		}


		mapOnScreen.createMarkers.update();

		//create marker list
		if (this.custom_map) {
			this.mapTab(group_name);
		} else {
			this.markerList.create();
		}
	},
	popup: function () {
		$('#' + this.map_id).on('click', '.marker-in-map', function (e) {
			let id = Number($(this).attr('data-marker-id'));

		});
	}
}
$(document).ready(function () {
	mapOnScreen.mapPadding();
	if ($('#' + mapOnScreen.map_id).length) {
		mapOnScreen.init();
	}
});
app.resizeDelay(1000, function () {
	mapOnScreen.mapPadding();
});

$(document).on('click', '[data-map-goTo-id]', function (e) {
	e.preventDefault();
	let id = Number($(this).attr('data-map-goTo-id')),
		scrollOffset = $('#' + mapOnScreen.map_id).offset().top + ($('#' + mapOnScreen.map_id).outerHeight() / 2),
		scroll = scrollOffset - ($(window).height() / 2);

	console.log(scrollOffset);

	$('html, body').animate({
		scrollTop: scroll - (app.header.outerHeight() / 3)
	}, {
		easing: 'swing',
		duration: 1000,
		complete: function () {
			mapOnScreen.openPopup(id);
		}
	});
});
