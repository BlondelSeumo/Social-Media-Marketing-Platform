/* ===========================================================
 * trumbowyg.pasteimage.js v1.0
 * Basic base64 paste plugin for Trumbowyg
 * http://alex-d.github.com/Trumbowyg
 * ===========================================================
 * Author : Alexandre Demode (Alex-D)
 *          Twitter : @AlexandreDemode
 *          Website : alex-d.fr
 */

(function ($) {
    'use strict';

    $.extend(true, $.trumbowyg, {
        plugins: {
            pasteImage: {
                init: function (trumbowyg) {
                    trumbowyg.pasteHandlers.push(function (pasteEvent) {
                        try {
                            var items = (pasteEvent.originalEvent || pasteEvent).clipboardData.items,
                                mustPreventDefault = false,
                                reader;

                            for (var i = items.length - 1; i >= 0; i -= 1) {
                                if (items[i].type.match(/^image\//)) {

                                	/* checks image size */
                                	var file = items[i].getAsFile();

                                	if (file.size > 307200) {
                                		var warning_message = 'The image size should not be greater than 100KB. If you want to use large image, please use the <strong>Insert Image</strong> button above.';
                                		alertify.alert('Warning!', warning_message);
			                            return;
			                        }

                                    reader = new FileReader();
                                    /* jshint -W083 */
                                    reader.onloadend = function (event) {
                                        trumbowyg.execCmd('insertImage', event.target.result, false, true);
                                    };
                                    /* jshint +W083 */
                                    reader.readAsDataURL(items[i].getAsFile());

                                    mustPreventDefault = true;
                                }
                            }

                            if (mustPreventDefault) {
                                pasteEvent.stopPropagation();
                                pasteEvent.preventDefault();
                            }
                        } catch (c) {
                        }
                    });
                }
            }
        }
    });
})(jQuery);