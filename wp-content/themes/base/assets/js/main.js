var app = function(t) {
	function e(e) {
		for(var r, o, a = e[0], c = e[1], l = e[2], u = 0, d = []; u < a.length; u++) o = a[u], i[o] && d.push(i[o][0]), i[o] = 0;
		for(r in c) Object.prototype.hasOwnProperty.call(c, r) && (t[r] = c[r]);
		for(p && p(e); d.length;) d.shift()();
		return s.push.apply(s, l || []), n()
	}

	function n() {
		for(var t, e = 0; e < s.length; e++) {
			for(var n = s[e], r = !0, a = 1; a < n.length; a++) {
				var c = n[a];
				0 !== i[c] && (r = !1)
			}
			r && (s.splice(e--, 1), t = o(o.s = n[0]))
		}
		return t
	}
	var r = {},
		i = {
			1: 0
		},
		s = [];

	function o(e) {
		if(r[e]) return r[e].exports;
		var n = r[e] = {
			i: e,
			l: !1,
			exports: {}
		};
		return t[e].call(n.exports, n, n.exports, o), n.l = !0, n.exports
	}
	o.m = t, o.c = r, o.d = function(t, e, n) {
		o.o(t, e) || Object.defineProperty(t, e, {
			enumerable: !0,
			get: n
		})
	}, o.r = function(t) {
		"undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(t, Symbol.toStringTag, {
			value: "Module"
		}), Object.defineProperty(t, "__esModule", {
			value: !0
		})
	}, o.t = function(t, e) {
		if(1 & e && (t = o(t)), 8 & e) return t;
		if(4 & e && "object" == typeof t && t && t.__esModule) return t;
		var n = Object.create(null);
		if(o.r(n), Object.defineProperty(n, "default", {
				enumerable: !0,
				value: t
			}), 2 & e && "string" != typeof t)
			for(var r in t) o.d(n, r, function(e) {
				return t[e]
			}.bind(null, r));
		return n
	}, o.n = function(t) {
		var e = t && t.__esModule ? function() {
			return t.default
		} : function() {
			return t
		};
		return o.d(e, "a", e), e
	}, o.o = function(t, e) {
		return Object.prototype.hasOwnProperty.call(t, e)
	}, o.p = "/js/";
	var a = window.webpackJsonpapp = window.webpackJsonpapp || [],
		c = a.push.bind(a);
	a.push = e, a = a.slice();
	for(var l = 0; l < a.length; l++) e(a[l]);
	var p = c;
	return s.push([7, 0]), n()
}({
	1: function(t, e, n) {
		var r, i, s, o;

		function a(t) {
			return(a = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
				return typeof t
			} : function(t) {
				return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
			})(t)
		}
		/*!
		 * enquire.js v2.1.6 - Awesome Media Queries in JavaScript
		 * Copyright (c) 2017 Nick Williams - http://wicky.nillia.ms/enquire.js
		 * License: MIT */
		! function(n) {
			"object" == a(e) && void 0 !== t ? t.exports = n() : (i = [], void 0 === (s = "function" == typeof(r = n) ? r.apply(e, i) : r) || (t.exports = s))
		}(function() {
			return function t(e, n, r) {
				function i(a, c) {
					if(!n[a]) {
						if(!e[a]) {
							if(!c && ("function" == typeof o && o)) return o(a, !0);
							if(s) return s(a, !0);
							var l = new Error("Cannot find module '" + a + "'");
							throw l.code = "MODULE_NOT_FOUND", l
						}
						var p = n[a] = {
							exports: {}
						};
						e[a][0].call(p.exports, function(t) {
							var n = e[a][1][t];
							return i(n || t)
						}, p, p.exports, t, e, n, r)
					}
					return n[a].exports
				}
				for(var s = "function" == typeof o && o, a = 0; a < r.length; a++) i(r[a]);
				return i
			}({
				1: [function(t, e, n) {
					function r(t, e) {
						this.query = t, this.isUnconditional = e, this.handlers = [], this.mql = window.matchMedia(t);
						var n = this;
						this.listener = function(t) {
							n.mql = t.currentTarget || t, n.assess()
						}, this.mql.addListener(this.listener)
					}
					var i = t(3),
						s = t(4).each;
					r.prototype = {
						constuctor: r,
						addHandler: function(t) {
							var e = new i(t);
							this.handlers.push(e), this.matches() && e.on()
						},
						removeHandler: function(t) {
							var e = this.handlers;
							s(e, function(n, r) {
								if(n.equals(t)) return n.destroy(), !e.splice(r, 1)
							})
						},
						matches: function() {
							return this.mql.matches || this.isUnconditional
						},
						clear: function() {
							s(this.handlers, function(t) {
								t.destroy()
							}), this.mql.removeListener(this.listener), this.handlers.length = 0
						},
						assess: function() {
							var t = this.matches() ? "on" : "off";
							s(this.handlers, function(e) {
								e[t]()
							})
						}
					}, e.exports = r
				}, {
					3: 3,
					4: 4
				}],
				2: [function(t, e, n) {
					function r() {
						if(!window.matchMedia) throw new Error("matchMedia not present, legacy browsers require a polyfill");
						this.queries = {}, this.browserIsIncapable = !window.matchMedia("only all").matches
					}
					var i = t(1),
						s = t(4),
						o = s.each,
						a = s.isFunction,
						c = s.isArray;
					r.prototype = {
						constructor: r,
						register: function(t, e, n) {
							var r = this.queries,
								s = n && this.browserIsIncapable;
							return r[t] || (r[t] = new i(t, s)), a(e) && (e = {
								match: e
							}), c(e) || (e = [e]), o(e, function(e) {
								a(e) && (e = {
									match: e
								}), r[t].addHandler(e)
							}), this
						},
						unregister: function(t, e) {
							var n = this.queries[t];
							return n && (e ? n.removeHandler(e) : (n.clear(), delete this.queries[t])), this
						}
					}, e.exports = r
				}, {
					1: 1,
					4: 4
				}],
				3: [function(t, e, n) {
					function r(t) {
						this.options = t, !t.deferSetup && this.setup()
					}
					r.prototype = {
						constructor: r,
						setup: function() {
							this.options.setup && this.options.setup(), this.initialised = !0
						},
						on: function() {
							!this.initialised && this.setup(), this.options.match && this.options.match()
						},
						off: function() {
							this.options.unmatch && this.options.unmatch()
						},
						destroy: function() {
							this.options.destroy ? this.options.destroy() : this.off()
						},
						equals: function(t) {
							return this.options === t || this.options.match === t
						}
					}, e.exports = r
				}, {}],
				4: [function(t, e, n) {
					e.exports = {
						isFunction: function(t) {
							return "function" == typeof t
						},
						isArray: function(t) {
							return "[object Array]" === Object.prototype.toString.apply(t)
						},
						each: function(t, e) {
							for(var n = 0, r = t.length; n < r && !1 !== e(t[n], n); n++);
						}
					}
				}, {}],
				5: [function(t, e, n) {
					var r = t(2);
					e.exports = new r
				}, {
					2: 2
				}]
			}, {}, [5])(5)
		})
	},
	7: function(t, e, n) {
		"use strict";
		n.r(e),
			function(t, e, r) {
				var i = n(0),
					s = n.n(i),
					o = n(1),
					a = n.n(o),
					c = (n(8), n(9), n(6)),
					l = n.n(c);
				n(14);
				s.a, s.a,
					function(t) {
						function e() {
							if(!window.matchMedia("(max-width: 767px)").matches) {
								var e = t(".main-menu"),
									n = t(".main-menu > li"),
									r = e.outerHeight(),
									i = e.find(".main-menu__item").outerHeight(),
									s = i + 10 < r;
								! function(e) {
									setTimeout(function() {
										e.each(function() {
											this.getBoundingClientRect().top > 10 && t(this).addClass("menu-hide")
										}), t(".main-menu").removeClass("overflow")
									}, 150)
								}(n), s && (e.css({
									height: i,
									"padding-right": "60px"
								}), e.addClass("overflow"), e.delegate(".menu-more,.menu-more-close", "click", function() {
									e.toggleClass("auto-height")
								}), e.append('<span class="menu-more">Еще...<span>'), e.append('<span class="menu-more-close">+<span>'), e.toggleClass("add"))
							}
						}

						function n() {
							var e = t(".main-menu");
							t(".main-menu li").removeClass("menu-hide"), e.removeClass("overflow"), e.toggleClass("add"), e.removeAttr("style"), e.removeClass(".auto-height"), e.undelegate(".menu-more,.menu-more-close", "click"), e.find(".menu-more,.menu-more-close").remove()
						}

						function r(e) {
							var n = t(".main-menu__modal"),
								r = t(window).height();
							window.matchMedia("(min-width: 767px)").matches || n.css("height", r - 60 + "px")
						}
						t(document).ready(function() {
							a.a.register("screen and (min-width:1199px)", {
									match: function() {
										n(), e()
									},
									unmatch: function() {
										n(), e()
									}
								}), a.a.register("screen and (min-width:992px)", {
									match: function() {
										n(), e()
									},
									unmatch: function() {
										n(), e()
									}
								}), a.a.register("screen and (min-width:767px)", {
									match: function() {
										n(), e()
									},
									unmatch: function() {
										n()
									}
								}),
								function() {
									var r = t(".main-menu-container");
									r.sticky({
										topSpacing: 0
									}), r.on("sticky-start", e), r.on("sticky-end", function() {
										n(), e()
									})
								}(), t(".mobile-menu-burger").on("click", function() {
									t(this).toggleClass("open")
								}), t(".mob-cls-btn").on("click", function() {
									t(".mobile-menu-burger").toggleClass("open")
								}), t(window).on("load resize", r), t(".main-menu a").each(function() {
									"#" === this.getAttribute("href") && t(this).on("click", function(t) {
										t.preventDefault()
									})
								}),
								function() {
									var e = {
										arrows: !1,
										fade: !0,
										dots: !0,
										appendDots: t(".top-slider-dots"),
										customPaging: function() {
											return ""
										}
									};
									"undefined" != typeof autoplaySpeed && autoplaySpeed > 0 && (e.autoplay = !0, e.autoplaySpeed = autoplaySpeed), t(".top-slider").slick(e)
								}(), new l.a("+ 7 (999)999-99-99").mask(".phone"), a.a.register("screen and (max-width:768px)", {
									match: function() {
										t(".nurse-slider").slick({
											arrows: !1,
											dots: !0,
											centerMode: !0,
											centerPadding: "20px",
											appendDots: t(".nurse-slider-dots"),
											customPaging: function() {
												return ""
											}
										})
									},
									unmatch: function() {
										t(".nurse-slider").slick("unslick")
									}
								}), a.a.register("screen and (max-width:768px)", {
									match: function() {
										t(".history-slider").slick({
											arrows: !1,
											dots: !0,
											appendDots: t(".history-slider-dots"),
											customPaging: function() {
												return ""
											}
										})
									},
									unmatch: function() {
										t(".history-slider").slick("unslick")
									}
								}), a.a.register("screen and (max-width:768px)", {
									match: function() {
										t(".vip-nurses-slider").slick({
											arrows: !1,
											dots: !0,
											centerMode: !0,
											centerPadding: "40px",
											appendDots: t(".vip-nurses-slider-dots"),
											customPaging: function() {
												return ""
											}
										})
									},
									unmatch: function() {
										t(".vip-nurses-slider").slick("unslick")
									}
								}), t(".rev-slider").slick({
									arrows: !0,
									dots: !1,
									slidesToShow: 2,
									appendArrows: t(".review-arrows"),
									prevArrow: '<div class="arrow-left arrow"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">\n<use x="0" y="0" xlink:href="#arrow"></use>\n</svg></div>',
									nextArrow: '<div class="arrow-right arrow"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">\n<use x="0" y="0" xlink:href="#arrow"></use>\n</svg></div>',
									responsive: [{
										breakpoint: 1199,
										settings: {
											slidesToShow: 1
										}
									}, {
										breakpoint: 576,
										settings: {
											arrows: !1,
											slidesToShow: 1,
											dots: !0,
											appendDots: t(".review-slider-dots"),
											customPaging: function() {
												return ""
											}
										}
									}]
								}), t(".serts-list").slick({
									arrows: !0,
									dots: !1,
									slidesToShow: 4,
									appendArrows: t(".serts-arrows"),
									prevArrow: '<div class="arrow-left arrow"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">\n<use x="0" y="0" xlink:href="#arrow"></use>\n</svg></div>',
									nextArrow: '<div class="arrow-right arrow"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">\n<use x="0" y="0" xlink:href="#arrow"></use>\n</svg></div>',
									responsive: [{
										breakpoint: 1199,
										settings: {
											slidesToShow: 3
										}
									}, {
										breakpoint: 768,
										settings: {
											slidesToShow: 2
										}
									}, {
										breakpoint: 576,
										settings: {
											arrows: !1,
											slidesToShow: 1,
											centerMode: !0,
											centerPadding: "20px",
											dots: !0,
											appendDots: t(".serts-slider-dots"),
											customPaging: function() {
												return ""
											}
										}
									}]
								}), a.a.register("screen and (max-width:768px)", {
									match: function() {
										t(".managers-slider").slick({
											arrows: !1,
											dots: !0,
											centerMode: !0,
											centerPadding: "30px",
											appendDots: t(".managers-slider-dots"),
											customPaging: function() {
												return ""
											}
										})
									},
									unmatch: function() {
										t(".managers-slider").slick("unslick")
									}
								}), t(".related-vip-slider").slick({
									arrows: !0,
									dots: !1,
									slidesToShow: 4,
									appendArrows: t(".related-vip-arrows"),
									prevArrow: '<div class="arrow-left arrow"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">\n<use x="0" y="0" xlink:href="#arrow"></use>\n</svg></div>',
									nextArrow: '<div class="arrow-right arrow"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">\n<use x="0" y="0" xlink:href="#arrow"></use>\n</svg></div>',
									responsive: [{
										breakpoint: 1199,
										settings: {
											slidesToShow: 3
										}
									}, {
										breakpoint: 768,
										settings: {
											slidesToShow: 2
										}
									}, {
										breakpoint: 576,
										settings: {
											arrows: !1,
											slidesToShow: 1,
											centerMode: !0,
											centerPadding: "20px",
											dots: !0,
											appendDots: t(".related-vip-slider-dots"),
											customPaging: function() {
												return ""
											}
										}
									}]
								}), t(".accordion-js").each(function() {
									var e = t(this).find(".accordion-item");
									e.on("click", function() {
										t(this).find(".accordion-item__title").toggleClass("in").next().slideToggle(300), e.not(this).find(".accordion-item__title").removeClass("in").next().slideUp(300)
									})
								}), t(".display-all").on("click", function(e) {
									e.preventDefault(), t(this).parent("div").toggleClass("show-all")
								}), t(".gallery-slider").slick({
									arrows: !0,
									dots: !1,
									slidesToShow: 3,
									appendArrows: t(".gallery-arrows"),
									prevArrow: '<div class="arrow-left arrow"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">\n<use x="0" y="0" xlink:href="#arrow"></use>\n</svg></div>',
									nextArrow: '<div class="arrow-right arrow"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve">\n<use x="0" y="0" xlink:href="#arrow"></use>\n</svg></div>',
									responsive: [{
										breakpoint: 1199,
										settings: {
											slidesToShow: 2
										}
									}, {
										breakpoint: 576,
										settings: {
											arrows: !1,
											slidesToShow: 1,
											dots: !0,
											appendDots: t(".gallery-slider-dots"),
											customPaging: function() {
												return ""
											}
										}
									}]
								}), t(".re-size").hover(function() {
									t(this).addClass("hover")
								}, function() {
									t(this).removeClass("hover")
								}), t(".share-js").on("click", function(e) {
									e.preventDefault();
									var n = t(this).data("share");
									if(t(".ya-share2__item_service_" + n).click(), "viber" === n || "whatsapp" === n || "telegram" === n) {
										var r = t(".ya-share2__item_service_" + n + " .ya-share2__link").attr("href");
										window.open(r)
									}
								}),
								function() {
									var e;
									t(".file-hidden").on("change", function(n) {
										var r = t(this).val(),
											i = t(this).parent(".wpcf7-form-control-wrap").siblings(".file-input");
										e || (e = i.text());
										var s = r.substring(r.lastIndexOf("/") + 1);
										s ? i.text(s) : i.text(e)
									})
								}(), t(window).scroll(function() {
									t(this).scrollTop() > 30 ? t(".btn-top").addClass("up-show") : t(".btn-top").removeClass("up-show")
								}), t(".btn-top").click(function() {
									t("body,html").animate({
										scrollTop: 0
									}, 800)
								})
								// , t(".wpcf7").each(function() {
									// if(t(this).closest(".popup").length > 0) return !0;
									// t(this).on("wpcf7mailsent", function() {
										// t.fancybox.open({
											// src: "#success",
											// type: "inline"
										// })
									// })
								// })
						})
					}(r)
			}.call(this, n(0), n(0), n(0))
	},
	8: function(t, e, n) {
		var r, i, s;

		function o(t) {
			return(o = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(t) {
				return typeof t
			} : function(t) {
				return t && "function" == typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? "symbol" : typeof t
			})(t)
		}
		i = [n(0)], void 0 === (s = "function" == typeof(r = function(t) {
			var e = Array.prototype.slice,
				n = Array.prototype.splice,
				r = {
					topSpacing: 0,
					bottomSpacing: 0,
					className: "is-sticky",
					wrapperClassName: "sticky-wrapper",
					center: !1,
					getWidthFrom: "",
					widthFromWrapper: !0,
					responsiveWidth: !1,
					zIndex: "inherit"
				},
				i = t(window),
				s = t(document),
				a = [],
				c = i.height(),
				l = function() {
					for(var e = i.scrollTop(), n = s.height(), r = n - c, o = e > r ? r - e : 0, l = 0, p = a.length; l < p; l++) {
						var u = a[l],
							d = u.stickyWrapper.offset().top,
							h = d - u.topSpacing - o;
						if(u.stickyWrapper.css("height", u.stickyElement.outerHeight()), e <= h) null !== u.currentTop && (u.stickyElement.css({
							width: "",
							position: "",
							top: "",
							"z-index": ""
						}), u.stickyElement.parent().removeClass(u.className), u.stickyElement.trigger("sticky-end", [u]), u.currentTop = null);
						else {
							var f, w = n - u.stickyElement.outerHeight() - u.topSpacing - u.bottomSpacing - e - o;
							w < 0 ? w += u.topSpacing : w = u.topSpacing, u.currentTop !== w && (u.getWidthFrom ? (padding = u.stickyElement.innerWidth() - u.stickyElement.width(), f = t(u.getWidthFrom).width() - padding || null) : u.widthFromWrapper && (f = u.stickyWrapper.width()), null == f && (f = u.stickyElement.width()), u.stickyElement.css("width", f).css("position", "fixed").css("top", w).css("z-index", u.zIndex), u.stickyElement.parent().addClass(u.className), null === u.currentTop ? u.stickyElement.trigger("sticky-start", [u]) : u.stickyElement.trigger("sticky-update", [u]), u.currentTop === u.topSpacing && u.currentTop > w || null === u.currentTop && w < u.topSpacing ? u.stickyElement.trigger("sticky-bottom-reached", [u]) : null !== u.currentTop && w === u.topSpacing && u.currentTop < w && u.stickyElement.trigger("sticky-bottom-unreached", [u]), u.currentTop = w);
							var m = u.stickyWrapper.parent(),
								g = u.stickyElement.offset().top + u.stickyElement.outerHeight() >= m.offset().top + m.outerHeight() && u.stickyElement.offset().top <= u.topSpacing;
							g ? u.stickyElement.css("position", "absolute").css("top", "").css("bottom", 0).css("z-index", "") : u.stickyElement.css("position", "fixed").css("top", w).css("bottom", "").css("z-index", u.zIndex)
						}
					}
				},
				p = function() {
					c = i.height();
					for(var e = 0, n = a.length; e < n; e++) {
						var r = a[e],
							s = null;
						r.getWidthFrom ? r.responsiveWidth && (s = t(r.getWidthFrom).width()) : r.widthFromWrapper && (s = r.stickyWrapper.width()), null != s && r.stickyElement.css("width", s)
					}
				},
				u = {
					init: function(e) {
						return this.each(function() {
							var n = t.extend({}, r, e),
								i = t(this),
								s = i.attr("id"),
								o = s ? s + "-" + r.wrapperClassName : r.wrapperClassName,
								c = t("<div></div>").attr("id", o).addClass(n.wrapperClassName);
							i.wrapAll(function() {
								if(0 == t(this).parent("#" + o).length) return c
							});
							var l = i.parent();
							n.center && l.css({
								width: i.outerWidth(),
								marginLeft: "auto",
								marginRight: "auto"
							}), "right" === i.css("float") && i.css({
								float: "none"
							}).parent().css({
								float: "right"
							}), n.stickyElement = i, n.stickyWrapper = l, n.currentTop = null, a.push(n), u.setWrapperHeight(this), u.setupChangeListeners(this)
						})
					},
					setWrapperHeight: function(e) {
						var n = t(e),
							r = n.parent();
						r && r.css("height", n.outerHeight())
					},
					setupChangeListeners: function(t) {
						if(window.MutationObserver) {
							var e = new window.MutationObserver(function(e) {
								(e[0].addedNodes.length || e[0].removedNodes.length) && u.setWrapperHeight(t)
							});
							e.observe(t, {
								subtree: !0,
								childList: !0
							})
						} else window.addEventListener ? (t.addEventListener("DOMNodeInserted", function() {
							u.setWrapperHeight(t)
						}, !1), t.addEventListener("DOMNodeRemoved", function() {
							u.setWrapperHeight(t)
						}, !1)) : window.attachEvent && (t.attachEvent("onDOMNodeInserted", function() {
							u.setWrapperHeight(t)
						}), t.attachEvent("onDOMNodeRemoved", function() {
							u.setWrapperHeight(t)
						}))
					},
					update: l,
					unstick: function(e) {
						return this.each(function() {
							for(var e = t(this), r = -1, i = a.length; i-- > 0;) a[i].stickyElement.get(0) === this && (n.call(a, i, 1), r = i); - 1 !== r && (e.unwrap(), e.css({
								width: "",
								position: "",
								top: "",
								float: "",
								"z-index": ""
							}))
						})
					}
				};
			window.addEventListener ? (window.addEventListener("scroll", l, !1), window.addEventListener("resize", p, !1)) : window.attachEvent && (window.attachEvent("onscroll", l), window.attachEvent("onresize", p)), t.fn.sticky = function(n) {
				return u[n] ? u[n].apply(this, e.call(arguments, 1)) : "object" !== o(n) && n ? void t.error("Method " + n + " does not exist on jQuery.sticky") : u.init.apply(this, arguments)
			}, t.fn.unstick = function(n) {
				return u[n] ? u[n].apply(this, e.call(arguments, 1)) : "object" !== o(n) && n ? void t.error("Method " + n + " does not exist on jQuery.sticky") : u.unstick.apply(this, arguments)
			}, t(function() {
				setTimeout(l, 0)
			})
		}) ? r.apply(e, i) : r) || (t.exports = s)
	}
});