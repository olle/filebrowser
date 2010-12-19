/*
 * The MIT License
 * 
 * Copyright (c) 2008-2009 Olle Törnström studiomediatech.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author  Olle Törnström olle[at]studiomediatech[dot]com
 * @since   2009-09-27
 */
(function ($) {
	var settings = {};
	var callback = function () {};
	$.fn.filebrowser = function (options, fn) {
		settings = $.extend({}, {
				'handler' : 'php/jquery.filebrowser.php',
				'dir' : '/tmp'
		}, options || {});
		callback = fn || function () {};
		return $(this).each(function () {
			$.fn.filebrowser.init(this);
		});
	};
	$.fn.filebrowser.init = function (el) {
		$.post(settings.handler, {
				'dir' : settings.dir
		}, function (data) {
			$(el).append(data).find('ul').addClass('root').find('li').click($.fn.filebrowser.browse);
		});
	};
	$.fn.filebrowser.browse = function (ev) {
		ev.stopPropagation();
		var el = this;
		$('.file.selected,.folder.selected').removeClass('selected');
		if ($(el).hasClass('file')) {
			$(el).addClass('selected');
			callback(el);
		} else {
			if ($(el).hasClass('open')) {
				$(el).addClass('selected').removeClass('open').find('ul').remove();
				callback(el);
			} else {
				$.post(settings.handler, {
						dir : $(el).attr('rel')
				}, function (data) {
					$(el).find('ul').remove();					
					if ($(data).find('li').length) {
						$(el).append(data)
								.removeClass('empty')
								.find('li')
								.click($.fn.filebrowser.browse);
					} else {
						$(el).addClass('empty');
					}
					$(el).addClass('selected open');
					callback(el);
				});
			}
		}
	};
	$.fn.filebrowser.update = function (el) {
		if (typeof el !== 'undefined')
			$(el).removeClass('open').click();
		else
			$.fn.filebrowser.init($('ul.root').parent().empty().get(0));
	};
})(jQuery);