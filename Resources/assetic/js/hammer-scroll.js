/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*global jQuery*/
/*global window*/
/*global navigator*/
/*global document*/
/*global CSSMatrix*/
/*global WebKitCSSMatrix*/
/*global MSCSSMatrix*/
/*global Hammer*/
/*global HammerScroll*/
/*global CustomEvent*/

/**
 * @param {jQuery} $
 *
 * @typedef {HammerScroll} HammerScroll
 */
(function ($) {
    'use strict';

    /**
     * Check if is a mobile device.
     *
     * @returns {boolean}
     *
     * @private
     */
    function mobileCheck() {
        return Boolean(navigator.userAgent.match(/Android|iPhone|iPad|iPod|IEMobile|BlackBerry|Opera Mini/i));
    }

    /**
     * Check is browser is IE.
     *
     * @return Boolean
     */
    function isIE() {
        return -1 !== navigator.userAgent.toLowerCase().indexOf('trident');
    }

    /**
     * Changes the css transition configuration on target element.
     *
     * @param {HammerScroll} self         The hammer scroll instance
     * @param {jQuery}       $target      The element to edited
     * @param {string}       [transition] The css transition configuration of target
     *
     * @private
     */
    function changeTransition(self, $target, transition) {
        if (undefined === transition) {
            transition = 'transform ' + self.options.inertiaDuration + 's';

            if (null !== self.options.inertiaFunction) {
                transition += ' ' + self.options.inertiaFunction;
            }
        }

        if ('' === transition) {
            $target.css('-webkit-transition', transition);
            $target.css('transition', transition);
        }

        $target.get(0).style['-webkit-transition'] = 'none' === transition ? transition : '-webkit-' + transition;
        $target.get(0).style.transition = transition;
    }

    /**
     * Changes the css transform configuration on target element.
     *
     * @param {jQuery} $target   The element to edited
     * @param {string} transform The css transform configuration of target
     *
     * @private
     */
    function changeTransform($target, transform) {
        $target.css('-webkit-transform', transform);
        $target.css('transform', transform);
    }

    /**
     * Refreshs the scrollbar position.
     *
     * @param {HammerScroll} self           The hammer scroll instance
     * @param {boolean}      usedTransition Used the transition
     * @param {number}       position       The new position of content
     *
     * @private
     */
    function refreshScrollbarPosition(self, usedTransition, position) {
        if (undefined === self.$scrollbar) {
            return;
        }

        var useScroll = self.options.useScroll,
            wrapperHeight = self.$element.innerHeight(),
            contentHeight = useScroll ? self.$element.get(0).scrollHeight : self.$content.outerHeight(),
            percentScroll = (useScroll ? position : -position) / (contentHeight - wrapperHeight),
            delta = Math.round(percentScroll * (wrapperHeight - self.$scrollbar.outerHeight()));

        changeTransition(self, self.$scrollbar, usedTransition ? undefined : 'none');
        changeTransform(self.$scrollbar, 'translate3d(0px, ' + delta + 'px, 0px)');
    }

    /**
     * Refresh the sticky header on end of scroll inertia transition.
     *
     * @param {jQuery.Event|Event} event
     *
     * @typedef {HammerScroll} Event.data The hammer scroll instance
     *
     * @private
     */
    function dragTransitionEnd(event) {
        var self = event.data,
            top = self.options.useScroll ? self.$element.scrollTop() : self.$content.position().top;

        self.$content.off('transitionend msTransitionEnd oTransitionEnd', null, self, dragTransitionEnd);
        refreshScrollbarPosition(self, true, top);

        if (undefined !== self.stickyHeader) {
            self.stickyHeader.checkPosition();
        }
    }

    /**
     * Get the vertical position of target element.
     *
     * @param {jQuery} $target
     *
     * @returns {number}
     *
     * @private
     */
    function getPosition($target) {
        var transformCss = $target.css('transform'),
            transform = {e: 0, f: 0},
            reMatrix,
            match;

        if (transformCss) {
            if ('function' === typeof CSSMatrix) {
                transform = new CSSMatrix(transformCss);

            } else if ('function' === typeof WebKitCSSMatrix) {
                transform = new WebKitCSSMatrix(transformCss);

            } else if ('function' === typeof MSCSSMatrix) {
                transform = new MSCSSMatrix(transformCss);

            } else {
                reMatrix = /matrix\(\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*,\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/;
                match = transformCss.match(reMatrix);

                if (match) {
                    transform.e = parseInt(match[1], 10);
                    transform.f = parseInt(match[2], 10);
                }
            }
        }

        return transform.f;
    }

    /**
     * Limits the vertical value with top or bottom wrapper position (with or
     * without the max bounce).
     *
     * @param {HammerScroll} self        The hammer scroll instance
     * @param {number}       delta
     * @param {number}       maxBounce
     * @param {boolean}      [inertia]
     * @param {number}       [velocity]
     *
     * @returns {number} The limited vertical value
     *
     * @private
     */
    function limitVerticalValue(self, delta, maxBounce, inertia, velocity) {
        var useScroll = self.options.useScroll,
            dragStartPosition = 'dragStartPosition',
            wrapperHeight,
            height,
            maxScroll,
            vertical,
            inertiaVal;

        if (undefined === self.dragStartPosition) {
            self[dragStartPosition] = useScroll ? -self.$element.scrollTop() : getPosition(self.$content);
        }

        wrapperHeight = self.$element.innerHeight();
        height = useScroll ? self.$element.get(0).scrollHeight : self.$content.outerHeight();
        maxScroll = height - wrapperHeight + maxBounce;
        vertical = -Math.round(delta + self.dragStartPosition);

        // inertia
        if (inertia) {
            inertiaVal = -delta * velocity * (1 + self.options.inertiaVelocity);
            vertical = Math.round(vertical + inertiaVal);
        }

        // top bounce
        if (vertical < -maxBounce) {
            vertical = -maxBounce;

            // bottom bounce with scroll
        } else if (height > wrapperHeight) {
            if (vertical > maxScroll) {
                vertical = maxScroll;
            }

            // bottom bounce without scroll
        } else {
            if (0 === maxBounce) {
                vertical = 0;

            } else if (vertical > maxBounce) {
                vertical = maxBounce;
            }
        }

        return vertical;
    }

    /**
     * Wraps the content.
     *
     * @param {HammerScroll} self The hammer scroll instance
     *
     * @returns {jQuery} The content
     *
     * @private
     */
    function wrapContent(self) {
        if (self.options.nativeScroll && !self.options.scrollbar) {
            return self.$element;
        }

        var sbDiv,
            $content = $([
                '<div class="' + self.options.contentWrapperClass + '"></div>'
            ].join(''));

        if (self.options.useScroll) {
            self.$element.before($content);
            $content.append(self.$element);

            if (self.options.nativeScroll && !isIE()) {
                sbDiv = document.createElement("div");
                sbDiv.style.width = '100px';
                sbDiv.style.height = '100px';
                sbDiv.style.overflow = 'scroll';
                sbDiv.style.position = '100px';
                sbDiv.style.top = '-9999px';

                document.body.appendChild(sbDiv);
                $content.css('overflow-x', 'hidden');
                self.$element.css('margin-right', -(sbDiv.offsetWidth - sbDiv.clientWidth) + 'px');
                document.body.removeChild(sbDiv);
            }

        } else {
            self.$element.children().each(function () {
                $content.append(this);
            });

            self.$element.append($content);
        }

        return $content;
    }

    /**
     * Unwraps the content.
     *
     * @param {HammerScroll} self The hammer scroll instance
     *
     * @returns null
     *
     * @private
     */
    function unwrapContent(self) {
        if (self.options.nativeScroll && !self.options.scrollbar) {
            return null;
        }

        if (self.options.useScroll) {
            if (1 === self.$content.find(self.$element).size()) {
                self.$content.before(self.$element);
            }

        } else {
            self.$content.children().each(function () {
                self.$element.append(this);
            });
        }

        self.$content.remove();

        return null;
    }

    /**
     * Creates the scrollbar.
     *
     * @param {HammerScroll} self The hammer scroll instance
     *
     * @returns {jQuery} The scrollbar
     *
     * @private
     */
    function generateScrollbar(self) {
        var $scrollbar = $('<div class="hammer-scrollbar"></div>');

        if (self.options.scrollbarInverse) {
            $scrollbar.addClass('hammer-scroll-inverse');
        }

        if (self.options.useScroll) {
            self.$content.prepend($scrollbar);

        } else {
            self.$element.prepend($scrollbar);
        }

        return $scrollbar;
    }

    /**
     * Action on native scrolling.
     *
     * @param {jQuery.Event|Event} event
     *
     * @typedef {HammerScroll} Event.data The hammer scroll instance
     *
     * @private
     */
    function onScrolling(event) {
        var self = event.data;

        refreshScrollbarPosition(self, false, self.$element.scrollTop());

        if (undefined !== self.stickyHeader) {
            self.stickyHeader.checkPosition();
        }
    }

    /**
     * Action on mouse scroll event.
     *
     * @param {jQuery.Event|Event} event
     *
     * @typedef {HammerScroll} Event.data The hammer scroll instance
     *
     * @private
     */
    function onMouseScrollNative(event) {
        var self = event.data,
            delta = (event.originalEvent.type === 'DOMMouseScroll' ?
                    event.originalEvent.detail * -40 :
                    event.originalEvent.wheelDelta),
            position = self.$element.scrollTop(),
            maxPosition = self.$element.get(0).scrollHeight - self.$element.innerHeight();

        if (!self.options.nativeScroll) {
            self.$element.scrollTop(self.$element.scrollTop() - delta);

        } else {
            if ((delta > 0 && position <= 0) || (delta < 0 && position >= maxPosition)) {
                event.stopPropagation();
                event.preventDefault();
            }
        }
    }

    /**
     * Action on mouse scroll event.
     *
     * @param {jQuery.Event|Event} event
     *
     * @typedef {HammerScroll} Event.data The hammer scroll instance
     *
     * @private
     */
    function onMouseScrollCssTransform(event) {
        var self = event.data,
            position = -self.$content.position().top,
            wrapperHeight = self.$element.innerHeight(),
            contentHeight = self.$content.outerHeight(),
            delta = (event.originalEvent.type === 'DOMMouseScroll' ?
                    event.originalEvent.detail * -40 :
                    event.originalEvent.wheelDelta);

        event.stopPropagation();
        event.preventDefault();

        position -= delta;
        position = Math.max(position, 0);

        if (self.$content.outerHeight() <= self.$element.innerHeight()) {
            position = 0;

        } else if ((contentHeight - position) < wrapperHeight) {
            position = contentHeight - wrapperHeight;
        }

        changeTransition(self, self.$content, 'none');
        changeTransform(self.$content, 'translate3d(0px, ' + -position + 'px, 0px)');
        refreshScrollbarPosition(self, false, -position);

        if (undefined !== self.stickyHeader) {
            self.stickyHeader.checkPosition();
        }
    }

    /**
     * Action on mouse scroll event.
     *
     * @param {jQuery.Event|Event} event
     *
     * @typedef {HammerScroll} Event.data The hammer scroll instance
     *
     * @private
     */
    function onMouseScroll(event) {
        var self = event.data;

        if (self.options.useScroll) {
            onMouseScrollNative(event);

        } else {
            onMouseScrollCssTransform(event);
        }
    }

    /**
     * Prevent scroll event (blocks the scroll on the tab keyboard event with
     * the item is outside the wrapper).
     *
     * @param {jQuery.Event|Event} event
     *
     * @private
     */
    function preventScroll(event) {
        $(event.target).eq(0).scrollTop(0);
    }

    // HAMMER SCROLL CLASS DEFINITION
    // ==============================

    /**
     * @constructor
     *
     * @param {string|elements|object|jQuery} element
     * @param {object}                        options
     *
     * @this HammerScroll
     */
    var HammerScroll = function (element, options) {
        this.guid     = jQuery.guid;
        this.options  = $.extend({}, HammerScroll.DEFAULTS, options);
        this.$element = $(element).eq(0);

        if (this.options.hammerStickyHeader && $.fn.stickyheader) {
            this.stickyHeader = this.$element.stickyheader().data('st.stickyheader');
        }

        if (!this.options.useScroll) {
            this.options.nativeScroll = false;
            this.$element.scrollTop(0);
            $(window).on('resize.st.hammerscroll' + this.guid, null, this, this.resizeScroll);
            this.$element.on('scroll.st.hammerscroll', preventScroll);
        }

        if (this.options.nativeScroll) {
            this.options.eventDelegated = true;

            if (isIE() || mobileCheck()) {
                this.options.scrollbar = false;

            } else {
                this.$element.on('scroll.st.hammerscroll', null, this, onScrolling);
            }

        } else {
            this.$element.css('overflow-y', 'hidden');

            if (null !== this.$element.css('right')) {
                this.$element.css('right', 0);
            }
        }

        this.$content = wrapContent(this);

        if (this.options.scrollbar) {
            this.$scrollbar = generateScrollbar(this);
            this.resizeScrollbar();

            $(window).on('resize.st.hammerscroll-bar' + this.guid, null, this, this.resizeScrollbar);
        }

        this.$element.on('DOMMouseScroll.st.hammerscroll mousewheel.st.hammerscroll', null, this, onMouseScroll);

        if (!this.options.eventDelegated) {
            this.hammer = new Hammer(this.$element.get(0), {
                tap: false,
                transform: false,
                release: false,
                hold: false,
                swipe: false,
                drag_block_vertical: true,
                drag_lock_to_axis: true,
                drag_min_distance: 5
            })

                .on('drag', null, this, this.onDrag)
                .on('dragend', null, this, this.onDragEnd);
        }

        if (this.options.useScroll && null !== this.options.scrollTop && 0 < this.options.scrollTop) {
            this.scrollTop(this.options.scrollTop);
        }
    },
        old;

    /**
     * Defaults options.
     *
     * @type {object}
     */
    HammerScroll.DEFAULTS = {
        contentWrapperClass: 'hammer-scroll-content',
        maxBounce:           100,
        eventDelegated:      false,
        hammerStickyHeader:  false,
        inertiaVelocity:     0.7,
        inertiaDuration:     0.2,
        inertiaFunction:     'ease',
        scrollbar:           true,
        scrollbarInverse:    false,
        scrollbarMinHeight:  14,
        useScroll:           false,
        nativeScroll:        false,
        scrollTop:           null
    };

    /**
     * On drag action.
     *
     * @param {Event} event The hammer event
     *
     * @typedef {object} Event.gesture The hammer object event
     *
     * @this HammerScroll
     */
    HammerScroll.prototype.onDrag = function (event) {
        var vertical;

        if (Hammer.DIRECTION_UP === event.gesture.direction || Hammer.DIRECTION_DOWN === event.gesture.direction) {
            event.preventDefault();
            event.stopPropagation();

            if (this.options.useScroll) {
                vertical = limitVerticalValue(this, event.gesture.deltaY, 0);

                this.$element.scrollTop(vertical);
                refreshScrollbarPosition(this, false, this.$element.scrollTop());

            } else {
                vertical = limitVerticalValue(this, event.gesture.deltaY, this.options.maxBounce);

                changeTransition(this, this.$content, 'none');
                changeTransform(this.$content, 'translate3d(0px, ' + -vertical + 'px, 0px)');
                refreshScrollbarPosition(this, false, -vertical);
            }

            if (undefined !== this.stickyHeader) {
                this.stickyHeader.checkPosition();
            }
        }
    };

    /**
     * On drag end action.
     *
     * @param {Event} event The hammer event
     *
     * @typedef {object} Event.gesture The hammer object event
     *
     * @this HammerScroll
     */
    HammerScroll.prototype.onDragEnd = function (event) {
        var self = this,
            dragStartPosition = 'dragStartPosition',
            vertical;

        if (this.options.useScroll) {
            if (Hammer.DIRECTION_UP === event.gesture.direction || Hammer.DIRECTION_DOWN === event.gesture.direction) {
                event.preventDefault();
                event.stopPropagation();

                vertical = limitVerticalValue(self, event.gesture.deltaY, 0, true, event.gesture.velocityY);

                this.$element.animate({
                    scrollTop: vertical
                }, this.options.inertiaDuration * 1000, function () {
                    var event = new CustomEvent('dragendanimate');
                    event.data = self;
                    dragTransitionEnd(event);
                });

                refreshScrollbarPosition(this, true, vertical);
            }

        } else {
            changeTransition(this, this.$content);

            if (Hammer.DIRECTION_UP === event.gesture.direction || Hammer.DIRECTION_DOWN === event.gesture.direction) {
                event.preventDefault();
                event.stopPropagation();

                vertical = limitVerticalValue(this, event.gesture.deltaY, 0, true, event.gesture.velocityY);

                this.$content.on('transitionend msTransitionEnd oTransitionEnd', null, this, dragTransitionEnd);
                changeTransform(this.$content, 'translate3d(0px, ' + -vertical + 'px, 0px)');
                refreshScrollbarPosition(this, true, -vertical);
            }
        }

        delete this[dragStartPosition];
    };

    /**
     * Destroy instance.
     *
     * @this HammerScroll
     */
    HammerScroll.prototype.destroy = function () {
        this.$content = unwrapContent(this);
        this.$element.css('overflow-y', '');
        this.$element.off('scroll.st.hammerscroll', null, onScrolling);
        this.$element.off('DOMMouseScroll.st.hammerscroll mousewheel.st.hammerscroll', null, onMouseScroll);
        $(window).off('resize.st.hammerscroll' + this.guid, this.resizeScroll);
        $(window).off('resize.st.hammerscroll-bar' + this.guid, this.resizeScrollbar);
        this.$element.off('scroll.st.hammerscroll', preventScroll);

        if (!this.options.eventDelegated) {
            this.hammer.dispose();
        }

        if (undefined !== this.stickyHeader) {
            this.stickyHeader.destroy();
        }

        this.$element.removeData('st.hammerscroll');
    };

    /**
     * Resizes the scoll content.
     * Moves the content on bottom if the bottom content is above of bottom
     * wrapper.
     *
     * @param {jQuery.Event|Event} [event]
     *
     * @typedef {HammerScroll} Event.data The hammer scroll instance
     *
     * @this HammerScroll
     */
    HammerScroll.prototype.resizeScroll = function (event) {
        var self = (undefined !== event) ? event.data : this,
            position,
            bottomPosition,
            maxBottom;

        if (self.options.useScroll) {
            return;
        }

        position = self.$content.position().top;

        if (position >= 0) {
            changeTransition(self, self.$content, 'none');
            changeTransform(self.$content, 'translate3d(0px, 0px, 0px)');

            return;
        }

        bottomPosition = position + self.$content.outerHeight();
        maxBottom = self.$element.innerHeight();

        if (bottomPosition < maxBottom) {
            position += maxBottom - bottomPosition;

            changeTransition(self, self.$content, 'none');
            changeTransform(self.$content, 'translate3d(0px, ' + position + 'px, 0px)');
        }
    };

    /**
     * Resizes the scrollbar.
     *
     * @param {jQuery.Event|Event} [event]
     *
     * @typedef {HammerScroll} Event.data The hammer scroll instance
     *
     * @this HammerScroll
     */
    HammerScroll.prototype.resizeScrollbar = function (event) {
        var self = (undefined !== event) ? event.data : this,
            useScroll,
            wrapperHeight,
            contentHeight,
            height,
            top;

        if (undefined === self.$scrollbar) {
            return;
        }

        useScroll = self.options.useScroll;
        wrapperHeight = self.$element.innerHeight();
        contentHeight = useScroll ? self.$element.get(0).scrollHeight : self.$content.outerHeight();
        height = Math.max(self.options.scrollbarMinHeight, Math.round(wrapperHeight * Math.min(wrapperHeight / contentHeight, 1)));
        top = useScroll ? self.$element.scrollTop() : self.$content.position().top;

        if (height < wrapperHeight) {
            self.$scrollbar.addClass('hammer-scroll-active');

        } else {
            self.$scrollbar.removeClass('hammer-scroll-active');
        }

        self.$scrollbar.height(height);
        refreshScrollbarPosition(self, false, top);
    };

    /**
     * Scroll the content.
     *
     * @param {number} top
     *
     * @this HammerScroll
     */
    HammerScroll.prototype.scrollTop = function (top) {
        var vertical;

        if (this.options.useScroll) {
            this.$element.scrollTop(top);
            refreshScrollbarPosition(this, false, this.$element.scrollTop());

        } else {
            vertical = limitVerticalValue(this, top, 0);

            changeTransition(this, this.$content, 'none');
            changeTransform(this.$content, 'translate3d(0px, ' + -vertical + 'px, 0px)');
            refreshScrollbarPosition(this, false, -vertical);
        }
    };


    // HAMMER SCROLL PLUGIN DEFINITION
    // ===============================

    old = $.fn.hammerScroll;

    $.fn.hammerScroll = function (option, value) {
        return this.each(function () {
            var $this   = $(this),
                data    = $this.data('st.hammerscroll'),
                options = typeof option === 'object' && option;

            if (!data && option === 'destroy') {
                return;
            }

            if (!data) {
                $this.data('st.hammerscroll', (data = new HammerScroll(this, options)));
            }

            if (typeof option === 'string') {
                data[option](value);
            }
        });
    };

    $.fn.hammerScroll.Constructor = HammerScroll;


    // HAMMER SCROLL NO CONFLICT
    // =========================

    $.fn.hammerScroll.noConflict = function () {
        $.fn.hammerScroll = old;

        return this;
    };


    // HAMMER SCROLL DATA-API
    // ======================

    $(window).on('load', function () {
        $('[data-hammer-scroll="true"]').each(function () {
            var $this = $(this);
            $this.hammerScroll($this.data());
        });
    });

}(jQuery));
