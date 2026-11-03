/**
 * CS Game — frontend.
 */
( function () {
	'use strict';

	var cfg = window.CSGAME || { i18n: {} };
	var $$ = function ( sel, ctx ) { return Array.prototype.slice.call( ( ctx || document ).querySelectorAll( sel ) ); };

	/* Mobile menu */
	var toggle = document.querySelector( '.nav-toggle' );
	var nav = document.getElementById( 'primary-nav' );
	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function () {
			var open = nav.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && nav.classList.contains( 'is-open' ) ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
				toggle.focus();
			}
		} );
	}

	/* TOC: collapse + active heading */
	$$( '.toc' ).forEach( function ( toc ) {
		var btn = toc.querySelector( '.toc__toggle' );
		if ( btn ) {
			if ( window.matchMedia( '(max-width: 1023px)' ).matches ) {
				toc.classList.add( 'is-collapsed' );
				btn.setAttribute( 'aria-expanded', 'false' );
			}
			btn.addEventListener( 'click', function () {
				var collapsed = toc.classList.toggle( 'is-collapsed' );
				btn.setAttribute( 'aria-expanded', collapsed ? 'false' : 'true' );
			} );
		}
		if ( ! ( 'IntersectionObserver' in window ) ) {
			return;
		}
		var links = $$( 'a[href^="#"]', toc );
		var map = {};
		links.forEach( function ( a ) { map[ decodeURIComponent( a.getAttribute( 'href' ).slice( 1 ) ) ] = a; } );
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( en ) {
				if ( en.isIntersecting && map[ en.target.id ] ) {
					links.forEach( function ( l ) { l.classList.remove( 'is-active' ); } );
					map[ en.target.id ].classList.add( 'is-active' );
				}
			} );
		}, { rootMargin: '-80px 0px -70% 0px' } );
		Object.keys( map ).forEach( function ( id ) {
			var el = document.getElementById( id );
			if ( el ) { io.observe( el ); }
		} );
	} );

	/* Bonus filter */
	$$( '[data-bonus-list]' ).forEach( function ( wrap ) {
		var box = wrap.querySelector( '.bonus-filter' );
		if ( ! box ) { return; }
		var tog = box.querySelector( '.bonus-filter__toggle' );
		var cards = $$( '.bonus-card', wrap );
		var empty = wrap.querySelector( '.bonus-list__empty' );

		tog.addEventListener( 'click', function () {
			var c = box.classList.toggle( 'is-collapsed' );
			tog.setAttribute( 'aria-expanded', c ? 'false' : 'true' );
		} );

		var apply = function ( slug ) {
			var shown = 0;
			cards.forEach( function ( card ) {
				var types = ( card.getAttribute( 'data-types' ) || '' ).split( ' ' );
				var ok = '*' === slug || types.indexOf( slug ) !== -1;
				card.hidden = ! ok;
				if ( ok ) { shown++; }
			} );
			if ( empty ) { empty.hidden = shown > 0; }
		};

		$$( '[data-filter]', box ).forEach( function ( chip ) {
			chip.addEventListener( 'click', function () {
				$$( '[data-filter]', box ).forEach( function ( c ) {
					c.classList.remove( 'is-active' );
					c.setAttribute( 'aria-pressed', 'false' );
				} );
				chip.classList.add( 'is-active' );
				chip.setAttribute( 'aria-pressed', 'true' );
				var slug = chip.getAttribute( 'data-filter' );
				apply( slug );
				if ( window.history && history.replaceState ) {
					history.replaceState( null, '', '*' === slug ? location.pathname : location.pathname + '#typ-' + slug );
				}
			} );
		} );

		var m = location.hash.match( /^#typ-(.+)$/ );
		if ( m ) {
			var pre = box.querySelector( '[data-filter="' + m[1].replace( /[^a-z0-9-]/gi, '' ) + '"]' );
			if ( pre ) { pre.click(); }
		}
	} );

	/* Copy promo code */
	document.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-copy]' );
		if ( ! btn ) { return; }
		var code = btn.getAttribute( 'data-copy' );
		var label = btn.querySelector( '.promo__code' );
		var done = function () {
			btn.classList.add( 'is-copied' );
			if ( label ) { label.textContent = cfg.i18n.copied || 'OK'; }
			setTimeout( function () {
				btn.classList.remove( 'is-copied' );
				if ( label ) { label.textContent = code; }
			}, 1800 );
		};
		if ( navigator.clipboard && window.isSecureContext ) {
			navigator.clipboard.writeText( code ).then( done, done );
		} else {
			var ta = document.createElement( 'textarea' );
			ta.value = code;
			ta.style.position = 'fixed';
			ta.style.opacity = '0';
			document.body.appendChild( ta );
			ta.select();
			try { document.execCommand( 'copy' ); } catch ( err ) {}
			document.body.removeChild( ta );
			done();
		}
	} );

	/* Collapsible text */
	$$( '[data-collapsible-text]' ).forEach( function ( box ) {
		var body = box.querySelector( '.collapsible__body' );
		var btn = box.querySelector( '.collapsible__more' );
		if ( ! body || ! btn ) { return; }
		if ( body.scrollHeight <= body.clientHeight + 8 ) {
			box.classList.add( 'is-short' );
			return;
		}
		btn.setAttribute( 'aria-expanded', 'false' );
		btn.addEventListener( 'click', function () {
			var open = box.classList.toggle( 'is-open' );
			btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			btn.textContent = open ? ( cfg.i18n.showLess || '−' ) : ( cfg.i18n.showMore || '+' );
		} );
	} );

	/* Slot demo: load iframe on click */
	$$( '.demo__frame[data-src]' ).forEach( function ( frame ) {
		var play = frame.querySelector( '.demo__play' );
		if ( ! play ) { return; }
		play.addEventListener( 'click', function () {
			var ifr = document.createElement( 'iframe' );
			ifr.src = frame.getAttribute( 'data-src' );
			ifr.setAttribute( 'allowfullscreen', '' );
			ifr.setAttribute( 'loading', 'lazy' );
			ifr.title = 'Demo';
			frame.innerHTML = '';
			frame.appendChild( ifr );
		} );
	} );

	/* Article rating */
	$$( '.article-rating' ).forEach( function ( box ) {
		var btns = $$( '[data-vote]', box );
		var key = 'csgame_voted_' + box.getAttribute( 'data-post' );
		var voted = false;
		try { voted = !! window.localStorage.getItem( key ); } catch ( e ) {}
		var paint = function ( n, cls ) {
			btns.forEach( function ( b, i ) { b.classList.toggle( cls, i < n ); } );
		};
		if ( voted ) {
			box.classList.add( 'is-done' );
			paint( Math.round( parseFloat( ( box.querySelector( '[data-avg]' ).textContent || '0' ).replace( ',', '.' ) ) ), 'is-on' );
			return;
		}
		btns.forEach( function ( b, i ) {
			b.addEventListener( 'mouseenter', function () { paint( i + 1, 'is-hover' ); } );
			b.addEventListener( 'mouseleave', function () { paint( 0, 'is-hover' ); } );
			b.addEventListener( 'click', function () {
				if ( box.classList.contains( 'is-done' ) ) { return; }
				box.classList.add( 'is-done' );
				paint( i + 1, 'is-on' );
				var fd = new FormData();
				fd.append( 'action', 'csgame_vote' );
				fd.append( 'nonce', cfg.nonce );
				fd.append( 'post', box.getAttribute( 'data-post' ) );
				fd.append( 'vote', b.getAttribute( 'data-vote' ) );
				fetch( cfg.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' } )
					.then( function ( r ) { return r.json(); } )
					.then( function ( res ) {
						try { window.localStorage.setItem( key, '1' ); } catch ( e ) {}
						if ( res && res.success ) {
							box.querySelector( '[data-avg]' ).textContent = res.data.avg;
							box.querySelector( '[data-count]' ).textContent = res.data.count;
						}
						var lab = box.querySelector( '.article-rating__label' );
						if ( lab ) { lab.textContent = cfg.i18n.thanks || lab.textContent; }
					} )
					.catch( function () {} );
			} );
		} );
	} );
} )();

/* Newsletter popup */
( function () {
	'use strict';
	var pop = document.getElementById( 'nl-pop' );
	if ( ! pop ) { return; }
	var cfg = ( window.CSGAME && window.CSGAME.popup ) || { delay: 15, days: 7, exit: true };
	var KEY = 'csgame_nl_seen';
	var lastFocus = null;
	var opened = false;

	var store = {
		get: function () { try { return parseInt( window.localStorage.getItem( KEY ) || '0', 10 ); } catch ( e ) { return 0; } },
		set: function () { try { window.localStorage.setItem( KEY, String( Date.now() ) ); } catch ( e ) {} }
	};
	var params = new URLSearchParams( window.location.search );
	var forced = params.get( 'newsletter' ) === '1' || ( !! pop.getAttribute( 'data-state' ) && location.hash !== '#odber' );
	var recently = store.get() && ( Date.now() - store.get() ) < cfg.days * 864e5;

	function focusables() {
		return Array.prototype.slice.call( pop.querySelectorAll( 'button, input:not([type="hidden"]):not(.hp), a[href]' ) ).filter( function ( el ) { return el.offsetParent !== null; } );
	}
	function open() {
		if ( opened ) { return; }
		opened = true;
		lastFocus = document.activeElement;
		pop.hidden = false;
		document.documentElement.classList.add( 'nl-lock' );
		store.set();
		var email = pop.querySelector( 'input[type="email"]' );
		setTimeout( function () { ( email || pop.querySelector( '.nl-pop__close' ) ).focus(); }, 50 );
	}
	function close() {
		pop.hidden = true;
		document.documentElement.classList.remove( 'nl-lock' );
		if ( lastFocus && lastFocus.focus ) { lastFocus.focus(); }
		if ( params.has( 'newsletter' ) && window.history.replaceState ) {
			params.delete( 'newsletter' );
			var q = params.toString();
			history.replaceState( null, '', location.pathname + ( q ? '?' + q : '' ) + location.hash );
		}
	}

	pop.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( '[data-nl-close]' ) ) { close(); }
	} );
	document.addEventListener( 'keydown', function ( e ) {
		if ( pop.hidden ) { return; }
		if ( 'Escape' === e.key ) { close(); }
		if ( 'Tab' === e.key ) {
			var f = focusables();
			if ( ! f.length ) { return; }
			if ( e.shiftKey && document.activeElement === f[0] ) { e.preventDefault(); f[ f.length - 1 ].focus(); }
			else if ( ! e.shiftKey && document.activeElement === f[ f.length - 1 ] ) { e.preventDefault(); f[0].focus(); }
		}
	} );

	if ( forced ) { open(); return; }
	if ( recently ) { return; }
	if ( cfg.delay > 0 ) { setTimeout( open, cfg.delay * 1000 ); }
	if ( cfg.exit && window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
		document.addEventListener( 'mouseout', function ( e ) {
			if ( ! e.relatedTarget && e.clientY <= 0 ) { open(); }
		} );
	}
} )();
