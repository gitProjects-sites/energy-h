(function () {
	let FormComponents = window.FormComponents;
	FormComponents = (function () {
		function FormComponents(mainEl) {
			let _ = this;
			_.data = {
				$mainEl: $(mainEl),
				selectDefault: {
					classes: {
						elem: '.fc-select-default-js',
						title: '.fc-select-title-js',
						titleChild: '.fc-selected-text-js',
						optionsContainer: '.fc-select-list-js',
						dropDown: '.fc-select-drop-down-js',
						input: '.fc-select-input-js',
						placeholder: '.fc-select-title-placeholder-js',
						scroll: '.fc-select-scroll-js'
					}
				},
				tabs: {
					classes: {
						elem: '.fc-tabs-js',
						title: '.fc-tabs-title-js',
						content: '.fc-tabs-content-js',
						box: '.fc-tab-box',
						sliderWrp: '.fc-tabs-title-slider-wrp-js',
					}
				},
				inputs: {
					classes: {
						input: 'input',
					}
				},
				textarea: {
					classes: {
						textarea: 'textarea',
						autosizeContainer: '.text-autosize-container',
					}
				},
			}
		}
		return FormComponents;
	}());


	FormComponents.prototype.initEl = function (el) {
		let _ = this,
			$el = $(el);
		let elData = $el.attr('data-fs-type');

		switch (elData) {
			case 'select-default':
				_.selectDefault($el);
				break;
			case 'tabs':
				_.tabsDefault($el);
				break;
			case 'input-default':
				_.inputDefault($el);
				break;
			case 'textarea-default':
				_.textareaDefault($el);
				break;
			case 'textarea-autoheight':
				_.textareaAutoheight($el);
				break;
		}
		setTimeout(function () {
			$el.addClass('fc-ready');
		}, 0);
	}
	FormComponents.prototype.removeEvent = function (el) {
		let _ = this,
			$el = $(el);
		$el.off();
	}
	FormComponents.prototype.disableAttrChange = function ($el, input, callback) {
		let _ = this,
			disabledStatus = false;
		setDisabled();
		if (typeof callback === 'function') {
			callback(disabledStatus);
		}
		var observer = new MutationObserver(function () {
			setDisabled();
			if (typeof callback === 'function') {
				callback(disabledStatus);
			}
		});
		observer.observe(input[0], {
			attributes: true,
			attributeFilter: ['disabled']
		});

		function setDisabled() {
			if (input.prop('disabled')) {
				disabledStatus = true;
				$el.addClass('fs-disabled');
			} else {
				disabledStatus = false;
				$el.removeClass('fs-disabled');
			}
		}

	}
	FormComponents.prototype.closeAllClickOnEmptySpace = function ($el) {
		let _ = this;
		$(document).on('mousedown', function (event) {
			if ($(event.target).closest(`${_.data.selectDefault.classes.elem}.open`).length) return false;
			//close all select
			$(_.data.selectDefault.classes.elem + '.open').removeClass('open');

			event.stopPropagation();
		});
	}


	//elements
	FormComponents.prototype.selectDefault = function ($el) {
		let _ = this;

		_.closeAllClickOnEmptySpace();
		let startIndex = 3;
		$($(_.data.selectDefault.classes.elem).get().reverse()).each(function () {
			$(this).css('z-index', startIndex);
			startIndex++;
		});


		function SelectDefault($el) {
			let t = this;
			t.data = {
				closeAfterSelect: true,
				multiple: false,
				selectedFirst: true,

				$el: $el,
				disabled: false,
				placeholder: false,
				elements: {
					titleChild: $el.find(_.data.selectDefault.classes.titleChild),
					input: $el.find(_.data.selectDefault.classes.input),
					ul: $el.find(_.data.selectDefault.classes.optionsContainer),
					placeholder: $el.find(_.data.selectDefault.classes.placeholder),
					scroll: $el.find(_.data.selectDefault.classes.scroll)
				}
			}
			let option = t.data.$el.attr('data-fs-options');
			if (option !== undefined) {
				option = (option.length) ? option.replace(/\s+/g, ' ').trim() : '';
				if (option !== '') {
					let newOption = JSON.parse(option);
					if (newOption.multiple === true) {
						if (newOption.closeAfterSelect === undefined) {
							newOption.closeAfterSelect = false;
						}
					}
					$.extend(t.data, newOption);
				}
			}

			t.data.placeholder = t.data.elements.placeholder.length ? true : false;

			t.init($el);
		}
		SelectDefault.prototype.init = function ($el) {
			let t = this;
			_.disableAttrChange($el, t.data.elements.input, function (disabledStatus) {
				t.data.disabled = disabledStatus;
			});

			t.startEvent(t.data.$el);
			t.customScroll(t.data.elements.scroll);
			//			if(t.data.selectedFirst && !t.data.multiple) {
			//				t.data.elements.ul.children(':first-child').addClass('selected');
			//			}
			t.setSelected($el);
		}

		SelectDefault.prototype.startEvent = function ($el) {
			let t = this;
			$el.on({
				click: function (e) {
					e.preventDefault();
					if (t.data.disabled) return false;
					if ($el.hasClass('open')) {
						t.close($el);
					} else {
						t.closeAll($el);
						t.open($el);
					}
				}
			}, _.data.selectDefault.classes.title).on({
				click: function (e) {
					if (t.data.disabled) return false;
					e.preventDefault();
					if (t.data.multiple) {
						t.selectOptionMultiple($(this), $el);
					} else {
						t.selectOption($(this), $el);
					}
				}
			}, _.data.selectDefault.classes.optionsContainer + ' button');


		}
		SelectDefault.prototype.open = function ($el) {
			let t = this;
			$el.addClass('open');
		}
		SelectDefault.prototype.close = function ($el) {
			let t = this;
			$el.removeClass('open');
		}
		SelectDefault.prototype.closeAll = function ($notEl) {
			let t = this;
			$(_.data.selectDefault.classes.elem + '.open').not($notEl).removeClass('open');
		}
		SelectDefault.prototype.selectOption = function ($item, $el) {
			let t = this,
				val = $item.attr('data-val');


			if (val !== undefined && val !== '') {
				t.data.elements.ul.find('li.selected').removeClass('selected');
				$item.parent().addClass('selected');
				t.setSelected($el);
				if (t.data.closeAfterSelect) t.close($el);
			} else {
				t.clear();
			}
		}
		SelectDefault.prototype.selectOptionMultiple = function ($item, $el) {
			let t = this;
			let $li = $item.parent(),
				val = $item.attr('data-val'),
				ul = t.data.elements.ul;

			if (val !== undefined && val !== '') {
				ul.find('button[data-val=""]').parent().removeClass('selected');
				$li.toggleClass('selected');
				t.setSelected($el);
			} else {
				t.clear();
			}

			if (t.data.closeAfterSelect) t.close($el);
		}

		SelectDefault.prototype.setSelected = function ($el) {
			let t = this;
			let $liSelectedButton = t.data.elements.ul.find('li.selected>button'),
				$liSelectedButtonLength = $liSelectedButton.length,
				titleText = '',
				vals = '';

			if ($liSelectedButtonLength) {
				$el.addClass('selected');
				$liSelectedButton.each(function (i) {
					let val = $(this).attr('data-val');

					if (val !== undefined && val !== '') {
						titleText += `<span>${$(this).text()}</span>`;
						vals += (i === 0 ? '' : ($liSelectedButtonLength > 1 ? '&' : '')) + val;
					} else {
						$el.removeClass('selected');
					}
				});
				if (titleText !== '') t.data.elements.titleChild.html(titleText);
			} else {
				$el.removeClass('selected');
			}
			t.data.elements.input.val(vals).trigger('change');
		}
		SelectDefault.prototype.clear = function () {
			let t = this,
				ul = t.data.elements.ul;

			let itemNoVal = ul.find('button[data-val=""]').parent();
			if (itemNoVal.length) {
				ul.find('li.selected').not(itemNoVal).removeClass('selected');
				itemNoVal.addClass('selected');
			} else {
				ul.find('li.selected').removeClass('selected');
				if (!t.data.placeholder) {
					ul.find('li:first-child').addClass('selected');
				}
			}


			t.setSelected(t.data.$el);
			t.close(t.data.$el);
		}


		SelectDefault.prototype.customScroll = function ($scroll) {
			let t = this;


			Scrollbar.use(ModalPlugin);
			Scrollbar.init($scroll[0], {
				alwaysShowTracks: true,
				damping: 0.05,
				continuousScrolling: true,
				overscrollEffect: true,
				overscroll: true
			}).addListener(function (status) {
				if (typeof callback === 'function') {
					if (status.offset.y >= status.limit.y) {
						$scroll.addClass('end');
					} else if (status.offset.y < status.limit.y) {
						$scroll.removeClass('end');
					}
					if (status.offset.y >= 5) {
						$scroll.addClass('start');
					} else if (status.offset.y <= 5) {
						$scroll.removeClass('start');
					}
				}
			});
		}

		$el[0].SelectDefault = new SelectDefault($el);
	}

	FormComponents.prototype.tabsDefault = function ($el) {
		let _ = this;

		function TabsDefault($el) {
			let t = this;
			t.data = {
				duration: 600,
				waitResize: false,
				waitForAnimate: false,

				titleSlider: false,
				animateFlag: false,
				$el: $el,
				currentIndex: 0,
				sliderInstance: undefined,
				elements: {
					allTitle: $el.find(_.data.tabs.classes.title + '>li>a'),
					content: $el.find(_.data.tabs.classes.content),
					allBox: $el.find(_.data.tabs.classes.box),
					sliderWrp: $el.children(_.data.tabs.classes.sliderWrp),
				}
			}
			let option = t.data.$el.attr('data-fs-options');
			if (option !== undefined) {
				option = (option.length) ? option.replace(/\s+/g, ' ').trim() : '';
				if (option !== '') {
					let newOption = JSON.parse(option);
					$.extend(t.data, newOption);
				}
			}
			//set curent index
			if (t.data.elements.sliderWrp.length) {
				t.data.titleSlider = true;
				t.data.currentIndex = $el.find(_.data.tabs.classes.sliderWrp + ' .swiper-slide.active').index();
				t.sliderInit(t.data.elements.sliderWrp);
			} else {
				t.data.titleSlider = false;
				t.data.currentIndex = $el.find(_.data.tabs.classes.title + '>li.active').index();
			}
			t.startEvent(t.data.$el);
		}

		TabsDefault.prototype.sliderInit = function (sliderWrp) {
			let t = this;

			let that = sliderWrp,
				slider = that.children('.fc-tabs-title-slider-js');

			t.data.sliderInstance = new Swiper(slider[0], {
				slidesPerView: 'auto',
				speed: t.data.duration,
				spaceBetween: 0,
				touchRatio: 1,
				resistance: true,
				resistanceRatio: 0,
				loop: false,
				navigation: {
					nextEl: that.find('.fc-tabs-title-slider-button-next-js')[0],
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
						slideArrCalc(swiper);
					},
					resize: function (swiper) {
						slideArrCalc(swiper);
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
			that.on('click', '.fc-tabs-title-slider-button-prev-js', function (e) {
				e.preventDefault();
				t.data.sliderInstance.slideTo(0);
			});


			function slideArrCalc(swiper) {
				let arrowWidth = parseInt(getComputedStyle(t.data.$el[0]).getPropertyValue('--tab-arrow-size'), 10) + parseInt(getComputedStyle(t.data.$el[0]).getPropertyValue('--tab-items-offset'), 10);
				if (swiper.slideOver) {
					if (swiper.virtualSize < swiper.width + arrowWidth) {
						that.removeClass('over');
						swiper.slideOver = false;
					}
				} else {
					if (swiper.virtualSize > swiper.width) {
						that.addClass('over');
						swiper.slideOver = true;
					}
				}
			}
		}

		TabsDefault.prototype.startEvent = function ($el) {
			let t = this;
			if (t.data.titleSlider) {
				$el.on({
					click: function (e) {
						e.preventDefault();
						let target = e.target,
							currentItem = $(target.parentElement);
						if (currentItem.hasClass('active')) return false;
						t.changeTab($el, currentItem, t.data.elements.sliderWrp.find('.swiper-slide'));
						t.data.sliderInstance.slideTo(currentItem.index());
					}
				}, _.data.tabs.classes.sliderWrp + ' .swiper-slide a');
			} else {
				$el.on({
					click: function (e) {
						e.preventDefault();
						let target = e.target,
							currentItem = $(target.parentElement);
						if (currentItem.hasClass('active')) return false;
						let allLi = t.data.elements.allTitle.parent()
						t.changeTab($el, currentItem, allLi);
					}
				}, _.data.tabs.classes.title + '>li>a');
			}
		}

		TabsDefault.prototype.changeTab = function ($el, currentLI, allLi) {
			let t = this;
			if (t.data.waitForAnimate && t.data.animateFlag) return false;
			t.data.animateFlag = true;

			let newIndex = currentLI.index(),
				allBox = t.data.elements.allBox,
				newBox = allBox.eq(newIndex),
				prevBox = allBox.eq(t.data.currentIndex),
				contentContainer = t.data.elements.content;

			$el.addClass('animate');

			//nav
			allLi.removeClass('active');
			currentLI.addClass('active');


			//contant
			allBox.removeClass('current');
			newBox.show().addClass('current');

			let newContentHeight = newBox.outerHeight(),
				prevContentHeight = prevBox.outerHeight();

			$el.trigger('beforeAnimate.tab', [t.data]);

			Promise.all([
				new Promise(function (resolve, reject) {
					transition.begin(newBox[0],
							['opacity',
							 `0`,
							 `1`,
							 `${t.data.waitResize ? (t.data.duration / 3) : t.data.duration}ms`,
							 `${t.data.waitResize ? (t.data.duration / 2) : 0}ms`], {
							timingFunction: 'ease',
							beginFromCurrentValue: true,
							onTransitionEnd: function (element, finished) {
								resolve(finished);
							}
						});
				}),
				new Promise(function (resolve, reject) {
					transition.begin(prevBox[0],
							['opacity',
							 `1`,
							 `0`,
							 `${t.data.waitResize ? (t.data.duration / 3) : t.data.duration}ms`,
							 `0ms`], {
							timingFunction: 'ease',
							beginFromCurrentValue: true,
							onTransitionEnd: function (element, finished) {
								resolve(finished);
							}
						});
				}),
				new Promise(function (resolve, reject) {
					transition.begin(contentContainer[0],
							['height',
							 `${prevContentHeight}px`,
							 `${newContentHeight}px`,
							 `${t.data.waitResize ? (t.data.duration / 3) : t.data.duration}ms`,
							 `${t.data.waitResize ? (t.data.duration / 3) : 0}ms`], {
							timingFunction: 'ease',
							beginFromCurrentValue: true,
							onTransitionEnd: function (element, finished) {
								resolve(finished);
							}
						});
				})
			]).then(function (finished) {
				if (!finished.some(function (x) {
						return x === false;
					})) {

					newBox.css('opacity', '');
					allBox.not(`:eq(${newIndex})`).hide().css('opacity', '');
					contentContainer.css('height', '');
					$el.removeClass('animate');

					$el.trigger('afterAnimate.tab', [t.data]);
					t.data.animateFlag = false;
				}
			}).catch(function (error) {
				console.error(error);
				t.data.animateFlag = false;
			});

			t.data.currentIndex = newIndex;
		}

		$el[0].TabsDefault = new TabsDefault($el);
	}

	FormComponents.prototype.inputDefault = function ($el) {
		let _ = this;

		function InputDefault($el) {
			let t = this;
			t.data = {
				$el: $el,
				disabled: false,
				telMask: '+7 (999) 999-99-99',
				elements: {
					input: $el.children(_.data.inputs.classes.input)
				}
			}
			let option = t.data.$el.attr('data-fs-options');
			if (option !== undefined) {
				option = (option.length) ? option.replace(/\s+/g, ' ').trim() : '';
				if (option !== '') {
					let newOption = JSON.parse(option);
					$.extend(t.data, newOption);
				}
			}


			t.init(t.data.$el);
		}
		InputDefault.prototype.init = function ($el) {
			let t = this;
			let val = t.data.elements.input.val();
			if (val !== '') t.setActive($el, 'active');
			_.disableAttrChange($el, t.data.elements.input, function (disabledStatus) {
				t.data.disabled = disabledStatus;
			});

			t.startEvent(t.data.$el);
			_.telMask(t.data.$el, t.data.telMask);
		}

		InputDefault.prototype.startEvent = function ($el) {
			let t = this;

			$el.on({
				focus: function (e) {
					e.preventDefault();
					t.setActive($el, 'active');
				},
				blur: function (e) {
					e.preventDefault();
					let val = $(this).val();

					if (!val.replace(/\s/g, '').length) {
						val = '';
						$(this).val(val);
					};
					if (val === '') t.setActive($el, 'inactive');
				}
			}, _.data.inputs.classes.input);
		}

		InputDefault.prototype.setActive = function ($el, event) {
			let t = this;
			if (event === 'active') {
				$el.addClass('active');
			} else {
				$el.removeClass('active');
			}
		}

		$el[0].InputDefault = new InputDefault($el);
	}

	FormComponents.prototype.textareaDefault = function ($el) {
		let _ = this;

		function TextareaDefault($el) {
			let t = this;
			t.data = {
				$el: $el,
				disabled: false,
				elements: {
					textarea: $el.children(_.data.textarea.classes.textarea)
				}
			}
			let option = t.data.$el.attr('data-fs-options');
			if (option !== undefined) {
				option = (option.length) ? option.replace(/\s+/g, ' ').trim() : '';
				if (option !== '') {
					let newOption = JSON.parse(option);
					$.extend(t.data, newOption);
				}
			}

			t.init(t.data.$el);
		}
		TextareaDefault.prototype.init = function ($el) {
			let t = this;
			let val = t.data.elements.textarea.val();
			if (val !== '') t.setActive($el, 'active');
			_.disableAttrChange($el, t.data.elements.textarea, function (disabledStatus) {
				t.data.disabled = disabledStatus;
			});

			t.startEvent(t.data.$el);
		}

		TextareaDefault.prototype.startEvent = function ($el) {
			let t = this;

			$el.on({
				focus: function (e) {
					e.preventDefault();
					t.setActive($el, 'active');
				},
				blur: function (e) {
					e.preventDefault();
					let val = $(this).val();

					if (!val.replace(/\s/g, '').length) {
						val = '';
						$(this).val(val);
					};
					if (val === '') t.setActive($el, 'inactive');
				}
			}, _.data.textarea.classes.textarea);
		}

		TextareaDefault.prototype.setActive = function ($el, event) {
			let t = this;
			if (event === 'active') {
				$el.addClass('active');
			} else {
				$el.removeClass('active');
			}
		}

		$el[0].TextareaDefault = new TextareaDefault($el);
	}

	FormComponents.prototype.textareaAutoheight = function ($el) {
		let _ = this;

		function TextareaAutoheight($el) {
			let t = this;
			t.data = {
				$el: $el,
				disabled: false,
				elements: {
					textarea: $el.children(_.data.textarea.classes.textarea),
					autosizeContainer: $el.children(_.data.textarea.classes.autosizeContainer)
				}
			}
			let option = t.data.$el.attr('data-fs-options');
			if (option !== undefined) {
				option = (option.length) ? option.replace(/\s+/g, ' ').trim() : '';
				if (option !== '') {
					let newOption = JSON.parse(option);
					$.extend(t.data, newOption);
				}
			}

			t.init(t.data.$el);
		}
		TextareaAutoheight.prototype.init = function ($el) {
			let t = this;
			let val = t.data.elements.textarea.val();
			if (val !== '') t.setActive($el, 'active');
			_.disableAttrChange($el, t.data.elements.textarea, function (disabledStatus) {
				t.data.disabled = disabledStatus;
			});

			t.setSize(t.data.elements.textarea);
			t.startEvent(t.data.$el);
		}

		TextareaAutoheight.prototype.startEvent = function ($el) {
			let t = this;

			$el.on({
				focus: function (e) {
					e.preventDefault();
					t.setActive($el, 'active');
				},
				blur: function (e) {
					e.preventDefault();
					let val = $(this).val();

					if (!val.replace(/\s/g, '').length) {
						val = '';
						$(this).val(val);
					};
					if (val === '') t.setActive($el, 'inactive');
				}
			}, _.data.textarea.classes.textarea);

			$el.on('input change', _.data.textarea.classes.textarea, function () {
				t.setSize(this);
			});
		}

		TextareaAutoheight.prototype.setSize = function (textarea) {
			let t = this;
			let container = t.data.elements.autosizeContainer;
			container.text($(textarea).val());
			if (container[0].offsetWidth > container[0].clientWidth) {
				//scroll
				$(textarea).css('overflow-y', 'auto');
			} else {
				//no scroll
				$(textarea).css('overflow-y', '');
			}
			$(textarea).css('height', container.outerHeight());
		}


		//textarea autosize
		//  	$(window).on('load', function(){
		//  		$(".js-auto-size").each(function () {
		//  			textAutosize(this);
		//  		});
		//  	});
		//  
		//  	function textAutosize(that) {
		//  		var timerId;
		//  		$(document).on('focusin', that, function (event) {
		//  			var container = $(that).siblings('.text-autosize-container'),
		//  				that_max_height = parseInt($(that).css('max-height'), 10);
		//  			timerId = setInterval(function () {
		//  				var container_height = container.outerHeight();
		//  				container.text($(that).val());
		//  				$(that).css('height', container_height);
		//  				if (container_height >= that_max_height) {
		//  					$(that).css('overflow-y', 'auto');
		//  					container.css('overflow-y', 'auto');
		//  				} else {
		//  					$(that).css('overflow-y', '');
		//  					container.css('overflow-y', '');
		//  				}
		//  			}, 16);
		//  		});
		//  		$(document).on('focusout', that, function () {
		//  			clearTimeout(timerId);
		//  		});
		//  	}









		TextareaAutoheight.prototype.setActive = function ($el, event) {
			let t = this;
			if (event === 'active') {
				$el.addClass('active');
			} else {
				$el.removeClass('active');
			}
		}

		$el[0].TextareaAutoheight = new TextareaAutoheight($el);
	}









	//tel mask
	FormComponents.prototype.telMask = function ($el, mask) {
		let _ = this,
			telInput = $el.find('[type="tel"]');
		if (telInput.length) {
			telInput.inputmask({
				mask: mask,
				showMaskOnFocus: true,
				showMaskOnHover: false
			});
		}
	}





	//	placeholderToggle: function (that) {
	//		$(that).each(function () {
	//			if ($(this).val() !== '') {
	//				$(this).parent().addClass("active");
	//			}
	//			if ($(this).is(':disabled')) {
	//				$(this).parent().addClass("disabled");
	//			}
	//		});
	//
	//		$(that).parent().addClass('input-load');
	//		setTimeout(function () {
	//			$(that).parent().removeClass('input-load');
	//		}, 300);
	//
	//		$(that).focus(function () {
	//			$(this).parent().addClass("active");
	//		});
	//
	//		$(that).blur(function () {
	//			if ($(this).val() === "") {
	//				$(this).parent().removeClass("active");
	//			}
	//		});
	//	},
	//	inputHeightSensor: function () {
	//		$('.input2:not(.ta)').each(function () {
	//			let obj = $(this),
	//				label = obj.find('.pl-active2');
	//			new ResizeSensor(obj[0], function () {
	//				obj.css('padding-right', label.width() + 10);
	//			});
	//		});
	//	}









	//init core
	window.FormComponents = new FormComponents(window);

}());
