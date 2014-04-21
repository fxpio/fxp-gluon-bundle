/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

+function ($) {
    'use strict';

    // HAMMER SCROLL CLASS DEFINITION
    // ==============================

    /**
     * @constructor
     *
     * @param htmlString|Element|Array|jQuery element
     * @param Array                           options
     *
     * @this
     */
    var HammerScroll = function (element, options) {
        this.guid     = jQuery.guid;
        this.options  = $.extend({}, HammerScroll.DEFAULTS, options);
        this.$element = $(element);

        if (this.options.hammerStickyHeader && $.fn.stickyheader) {
            this.stickyHeader = this.$element.stickyheader().data('st.stickyheader');
        }

        if (!this.options.useScroll) {
            this.options.nativeScroll = false;
            this.$element.scrollTop(0);
            $(window).on('resize.st.hammerscroll' + this.guid, $.proxy(this.resizeScroll, this));
            this.$element.on('scroll.st.hammerscroll', $.proxy(preventScroll, this));
        }

        if (this.options.nativeScroll) {
            this.options.eventDelegated = true;

            if (isIE() || mobileCheck()) {
                this.options.scrollbar = false;

            } else {
                this.$element.on('scroll.st.hammerscroll', $.proxy(onScrolling, this));
            }

        } else {
            this.$element.css('overflow-y', 'hidden');

            if (null != this.$element.css('right')) {
                this.$element.css('right', 0);
            }
        }

        this.$content = wrapContent.apply(this);

        if (this.options.scrollbar) {
            this.$scrollbar = generateScrollbar.apply(this);
            this.resizeScrollbar();

            $(window).on('resize.st.hammerscroll-bar' + this.guid, $.proxy(this.resizeScrollbar, this));
        }

        this.$element.on('DOMMouseScroll.st.hammerscroll mousewheel.st.hammerscroll', $.proxy(onMouseScroll, this));

        if (!this.options.eventDelegated) {
            this.hammer = new Hammer(this.$element.get(0), {
                tap: false,
                transform: false,
                release: false,
                hold: false,
                swipe: false,
                drag_block_vertical: true,
                drag_lock_to_axis: false,
                drag_min_distance: 3
            })

            .on('drag', $.proxy(this.onDrag, this))
            .on('dragend', $.proxy(this.onDragEnd, this));
        }

        if (this.options.useScroll && null != this.options.scrollTop && 0 < this.options.scrollTop) {
            this.scrollTop(this.options.scrollTop);
        }
    };

    /**
     * Defaults options.
     *
     * @type Array
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
     * @param Event event The hammer event
     */
    HammerScroll.prototype.onDrag = function (event) {
        if ('up' == event.gesture.direction || 'down' == event.gesture.direction) {
            event.preventDefault();
            event.stopPropagation();

            if (this.options.useScroll) {
                var vertical = $.proxy(limitVerticalValue, this)(event.gesture.deltaY, 0);

                this.$element.scrollTop(vertical);
                $.proxy(refreshScrollbarPosition, this)(false, this.$element.scrollTop());

            } else {
                var vertical = $.proxy(limitVerticalValue, this)(event.gesture.deltaY, this.options.maxBounce);

                $.proxy(changeTransition, this)(this.$content, 'none');
                $.proxy(changeTransform, this)(this.$content, 'translate3d(0px, ' + -vertical + 'px, 0px)');
                $.proxy(refreshScrollbarPosition, this)(false, -vertical);
            }

            if (undefined != this.stickyHeader) {
                this.stickyHeader.checkPosition();
            }
        }
    };

    /**
     * On drag end action.
     * 
     * @param Event event The hammer event
     */
    HammerScroll.prototype.onDragEnd = function (event) {
        if (this.options.useScroll) {
            if ('up' == event.gesture.direction || 'down' == event.gesture.direction) {
                event.preventDefault();
                event.stopPropagation();

                var vertical = $.proxy(limitVerticalValue, this)(event.gesture.deltaY, 0, true, event.gesture.velocityY);

                this.$element.animate({
                    scrollTop: vertical
                }, this.options.inertiaDuration * 1000, $.proxy(dragTransitionEnd, this));

                $.proxy(refreshScrollbarPosition, this)(true, vertical);
            }

        } else {
            $.proxy(changeTransition, this)(this.$content);

            if ('up' == event.gesture.direction || 'down' == event.gesture.direction) {
                event.preventDefault();
                event.stopPropagation();

                var vertical = $.proxy(limitVerticalValue, this)(event.gesture.deltaY, 0, true, event.gesture.velocityY);

                this.$content.on('transitionend msTransitionEnd oTransitionEnd', $.proxy(dragTransitionEnd, this));
                $.proxy(changeTransform, this)(this.$content, 'translate3d(0px, ' + -vertical + 'px, 0px)');
                $.proxy(refreshScrollbarPosition, this)(true, -vertical);
            }
        }

        delete this.dragStartPosition;
    };

    /**
     * Destroy instance.
     *
     * @this
     */
    HammerScroll.prototype.destroy = function () {
        this.$content = unwrapContent.apply(this);
        this.$element.css('overflow-y', '');
        this.$element.off('scroll.st.hammerscroll', $.proxy(onScrolling, this));
        this.$element.off('DOMMouseScroll.st.hammerscroll mousewheel.st.hammerscroll', $.proxy(onMouseScroll, this));
        $(window).off('resize.st.hammerscroll' + this.guid, $.proxy(this.resizeScroll, this));
        $(window).off('resize.st.hammerscroll-bar' + this.guid, $.proxy(this.resizeScrollbar, this));
        this.$element.off('scroll.st.hammerscroll', $.proxy(preventScroll, this));

        if (!this.options.eventDelegated) {
            this.hammer.dispose();
        }

        if (undefined != this.stickyHeader) {
            this.stickyHeader.destroy();
        }

        this.$element.removeData('st.hammerscroll');
    };

    /**
     * Resizes the scoll content.
     * Moves the content on bottom if the bottom content is above of bottom
     * wrapper.
     *
     * @this
     */
    HammerScroll.prototype.resizeScroll = function () {
        if (this.options.useScroll) {
            return;
        }

        var position = this.$content.position()['top'];

        if (position >= 0) {
            $.proxy(changeTransition, this)(this.$content, 'none');
            $.proxy(changeTransform, this)(this.$content, 'translate3d(0px, 0px, 0px)');

            return;
        }

        var bottomPosition = position + this.$content.outerHeight();
        var maxBottom = this.$element.innerHeight();

        if (bottomPosition < maxBottom) {
            position += maxBottom - bottomPosition;

            $.proxy(changeTransition, this)(this.$content, 'none');
            $.proxy(changeTransform, this)(this.$content, 'translate3d(0px, ' + position + 'px, 0px)');
        }
    };

    /**
     * Resizes the scrollbar.
     *
     * @this
     */
    HammerScroll.prototype.resizeScrollbar = function () {
        if (undefined == this.$scrollbar) {
            return;
        }

        var useScroll = this.options.useScroll;
        var wrapperHeight = this.$element.innerHeight();
        var contentHeight = useScroll ? this.$element.get(0).scrollHeight : this.$content.outerHeight();
        var height = Math.max(this.options.scrollbarMinHeight, Math.round(wrapperHeight * Math.min(wrapperHeight / contentHeight, 1)));
        var top = useScroll ? this.$element.scrollTop() : this.$content.position()['top'];

        if (height < wrapperHeight) {
            this.$scrollbar.addClass('hammer-scroll-active');

        } else {
            this.$scrollbar.removeClass('hammer-scroll-active');
        }

        this.$scrollbar.height(height);
        $.proxy(refreshScrollbarPosition, this)(false, top);
    };

    /**
     * Scroll the content.
     *
     * @param Number top
     *
     * @this
     */
    HammerScroll.prototype.scrollTop = function (top) {
        if (this.options.useScroll) {
            this.$element.scrollTop(top);
            $.proxy(refreshScrollbarPosition, this)(false, this.$element.scrollTop());

        } else {
            var vertical = $.proxy(limitVerticalValue, this)(event.gesture.deltaY, 0);

            $.proxy(changeTransition, this)(this.$content, 'none');
            $.proxy(changeTransform, this)(this.$content, 'translate3d(0px, ' + -vertical + 'px, 0px)');
            $.proxy(refreshScrollbarPosition, this)(false, -vertical);
        }
    };

    /**
     * Refresh the sticky header on end of scroll inertia transition.
     *
     * @this
     * @private
     */
    function dragTransitionEnd () {
        var top = this.options.useScroll ? this.$element.scrollTop() : this.$content.position()['top'];

        this.$content.off('transitionend msTransitionEnd oTransitionEnd', $.proxy(dragTransitionEnd, this));
        $.proxy(refreshScrollbarPosition, this)(true, top);

        if (undefined != this.stickyHeader) {
            this.stickyHeader.checkPosition();
        }
    }

    /**
     * Limits the vertical value with top or bottom wrapper position (with or
     * without the max bounce).
     *
     * @param Integer delta
     * @param Integer maxBounce
     * @param Boolean inertia
     * @param Integer velocity
     *
     * @return Integer The limited vertical value
     *
     * @this
     * @private
     */
    function limitVerticalValue (delta, maxBounce, inertia, velocity) {
        var useScroll = this.options.useScroll;

        if (undefined == this.dragStartPosition) {
            this.dragStartPosition = useScroll ? -this.$element.scrollTop() : getPosition(this.$content);
        }

        var wrapperHeight = this.$element.innerHeight();
        var height = useScroll ? this.$element.get(0).scrollHeight : this.$content.outerHeight();
        var maxScroll = height - wrapperHeight + maxBounce;
        var vertical = -Math.round(delta + this.dragStartPosition);

        // inertia
        if (inertia) {
            var inertiaVal = -delta * velocity * (1 + this.options.inertiaVelocity);
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
            if (0 == maxBounce) {
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
     * @return jQuery The content
     *
     * @this
     * @private
     */
    function wrapContent () {
        if (this.options.nativeScroll && !this.options.scrollbar) {
            return this.$element;
        }

        var $content = $([
            '<div class="' + this.options.contentWrapperClass + '"></div>'
        ].join(''));

        if (this.options.useScroll) {
            this.$element.before($content);
            $content.append(this.$element);

            if (this.options.nativeScroll && !isIE()) {
                var sbDiv = document.createElement("div");
                sbDiv.style.width = '100px';
                sbDiv.style.height = '100px';
                sbDiv.style.overflow = 'scroll';
                sbDiv.style.position = '100px';
                sbDiv.style.top = '-9999px';

                document.body.appendChild(sbDiv);
                $content.css('overflow-x', 'hidden');
                this.$element.css('margin-right', -(sbDiv.offsetWidth - sbDiv.clientWidth) + 'px');
                document.body.removeChild(sbDiv);
            }

        } else {
            this.$element.children().each(function () {
                $content.append(this);
            });

            this.$element.append($content);
        }

        return $content;
    }

    /**
     * Check is browser is IE.
     *
     * @return Boolean
     */
    function isIE () {
        return -1 != navigator.userAgent.toLowerCase().indexOf('trident');
    }

    /**
     * Unwraps the content.
     *
     * @return null
     *
     * @this
     * @private
     */
    function unwrapContent () {
        var self = this;

        if (this.options.nativeScroll && !this.options.scrollbar) {
            return null;
        }

        if (this.options.useScroll) {
            if (1 == this.$content.find(this.$element).size()) {
                this.$content.before(this.$element);
            }

        } else {
            this.$content.children().each(function () {
                self.$element.append(this);
            });
        }

        this.$content.remove();

        return null;
    }

    /**
     * Creates the scrollbar.
     *
     * @return jQuery The scrollbar
     *
     * @this
     * @private
     */
    function generateScrollbar () {
        var $scrollbar = $('<div class="hammer-scrollbar"></div>');

        if (this.options.scrollbarInverse) {
            $scrollbar.addClass('hammer-scroll-inverse');
        }

        if (this.options.useScroll) {
            this.$content.prepend($scrollbar);

        } else {
            this.$element.prepend($scrollbar);
        }

        return $scrollbar;
    }

    /**
     * Refreshs the scrollbar position.
     *
     * @param Boolean usedTransition Used the transition
     * @param Decimal position       The new position of content
     *
     * @return jQuery The content
     *
     * @this
     * @private
     */
    function refreshScrollbarPosition (usedTransition, position) {
        if (undefined == this.$scrollbar) {
            return;
        }

        var useScroll = this.options.useScroll;
        var wrapperHeight = this.$element.innerHeight();
        var contentHeight = useScroll ? this.$element.get(0).scrollHeight : this.$content.outerHeight();
        var percentScroll = (useScroll ? position : -position) / (contentHeight - wrapperHeight);
        var delta = Math.round(percentScroll * (wrapperHeight - this.$scrollbar.outerHeight()));

        $.proxy(changeTransition, this)(this.$scrollbar, usedTransition ? undefined : 'none');
        $.proxy(changeTransform, this)(this.$scrollbar, 'translate3d(0px, ' + delta + 'px, 0px)');
    }

    /**
     * Action on native scrolling.
     *
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function onScrolling (event) {
        $.proxy(refreshScrollbarPosition, this)(false, this.$element.scrollTop());

        if (undefined != this.stickyHeader) {
            this.stickyHeader.checkPosition();
        }
    }

    /**
     * Action on mouse scroll event.
     *
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function onMouseScroll (event) {
        if (this.options.useScroll) {
            $.proxy(onMouseScrollNative, this)(event);

        } else {
            $.proxy(onMouseScrollCssTransform, this)(event);
        }
    }

    /**
     * Action on mouse scroll event.
     *
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function onMouseScrollNative (event) {
        var delta = (event.originalEvent.type == 'DOMMouseScroll' ?
                event.originalEvent.detail * -40 :
                    event.originalEvent.wheelDelta);
        var position = this.$element.scrollTop();
        var maxPosition = this.$element.get(0).scrollHeight - this.$element.innerHeight();

        if (!this.options.nativeScroll) {
            this.$element.scrollTop(this.$element.scrollTop() - delta);

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
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function onMouseScrollCssTransform (event) {
        var position = -this.$content.position()['top'];
        var wrapperHeight = this.$element.innerHeight();
        var contentHeight = this.$content.outerHeight();
        var delta = (event.originalEvent.type == 'DOMMouseScroll' ?
                event.originalEvent.detail * -40 :
                event.originalEvent.wheelDelta);

        event.stopPropagation();
        event.preventDefault();

        position -= delta;
        position = Math.max(position, 0);

        if (this.$content.outerHeight() <= this.$element.innerHeight()) {
            position = 0;

        } else if ((contentHeight - position) < wrapperHeight) {
            position = contentHeight - wrapperHeight;
        }

        $.proxy(changeTransition, this)(this.$content, 'none');
        $.proxy(changeTransform, this)(this.$content, 'translate3d(0px, ' + -position + 'px, 0px)');
        $.proxy(refreshScrollbarPosition, this)(false, -position);

        if (undefined != this.stickyHeader) {
            this.stickyHeader.checkPosition();
        }
    }

    /**
     * Prevent scroll event (blocks the scroll on the tab keyboard event with
     * the item is outside the wrapper).
     *
     * @param jQuery.Event event
     *
     * @this
     * @private
     */
    function preventScroll (event) {
        $(event.target).scrollTop(0);
    }

    /**
     * Get the vertical position of target element.
     *
     * @param jQuery $target
     *
     * @return Integer
     *
     * @this
     * @private
     */
    function getPosition ($target) {
        var transformCss = $target.css('transform');
        var transform = {e: 0, f: 0};

        if (transformCss) {
            if ('function' === typeof CSSMatrix) {
                transform = new CSSMatrix(transformCss);

            } else if ('function' === typeof WebKitCSSMatrix) {
                transform = new WebKitCSSMatrix(transformCss);

            } else if ('function' === typeof MSCSSMatrix) {
                transform = new MSCSSMatrix(transformCss);

            } else {
                var reMatrix = /matrix\(\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*\,\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/;
                var match = transformCss.match(reMatrix);

                if (match) {
                    transform.e = parseInt(match[1]);
                    transform.f = parseInt(match[2]);
                }
            }
        }

        return transform.f;
    }

    /**
     * Changes the css transition configuration on target element.
     *
     * @param jQuery $target    The element to edited
     * @param String transition The css transition configuration of target
     *
     * @this
     * @private
     */
    function changeTransition ($target, transition) {
        if (undefined == transition) {
            transition = 'transform ' + this.options.inertiaDuration + 's';

            if (null != this.options.inertiaFunction) {
                transition += ' ' + this.options.inertiaFunction;
            }
        }

        if ('' == transition) {
            $target.css('-webkit-transition', transition);
            $target.css('transition', transition);
        }

        $target.get(0).style['-webkit-transition'] = 'none' == transition ? transition : '-webkit-' + transition;
        $target.get(0).style['transition'] = transition;
    }

    /**
     * Changes the css transform configuration on target element.
     *
     * @param jQuery $target   The element to edited
     * @param String transform The css transform configuration of target
     *
     * @this
     * @private
     */
    function changeTransform ($target, transform) {
        $target.css('-webkit-transform', transform);
        $target.css('transform', transform);
    }

    /**
     * Check if is a mobile device.
     *
     * @return Boolean
     *
     * @this
     * @private
     */
    function mobileCheck () {
        var check = false;

        (function (a) {
            if(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) {
                check = true;
            }

        })(navigator.userAgent || navigator.vendor || window.opera);

        return check;
    }


    // HAMMER SCROLL PLUGIN DEFINITION
    // ===============================

    var old = $.fn.hammerScroll;

    $.fn.hammerScroll = function (option, _relatedTarget) {
        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('st.hammerscroll');
            var options = typeof option == 'object' && option;

            if (!data && option == 'destroy') {
                return;
            }

            if (!data) {
                $this.data('st.hammerscroll', (data = new HammerScroll(this, options)));
            }

            if (typeof option == 'string') {
                data[option](_relatedTarget);
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

}(jQuery);
