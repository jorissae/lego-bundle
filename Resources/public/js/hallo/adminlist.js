$(function () {
    var converter = new Showdown.converter();
    var htmlize = function (content) {
        return converter.makeHtml(content);
    };
    
    var plugins = {
            'halloformat': {},
            'halloheadings': {},
            'hallolists': {},
            'halloreundo': {}    
        };


    if (typeof datalist != 'undefined') {
	if (datalist.length > 0) {
            plugins['2leinsertdata'] = {elements: datalist};
        }
    }
    
    jQuery('.editable').hallo({
        plugins: plugins,
        editable: true,
        placeholder: 'Saisir mon texte',
        toolbar: 'halloToolbarFixed'
    });

    jQuery('.editable').bind('hallodeactivated', function (event, data) {
        jQuery('.editable').html(htmlize(data.getContents()));
        jQuery('#' + jQuery('.editable').next().attr('id')).val(htmlize(data.getContents()));
    });

    jQuery('.editable').bind('halloactivated', function (event, data) {
        jQuery('.editable').html(htmlize(data.getContents()));
    });

    var markdownize = function (content) {
        var html = content.split("\n").map($.trim).filter(function (line) {
            return line != "";
        }).join("\n");
        return toMarkdown(html);
    };

    if (jQuery('.editable').length) {
        jQuery('.btn-primary').bind('click', function () {
            markdown = markdownize(jQuery('#' + jQuery('.editable').next().attr('id')).val());
            jQuery('#' + jQuery('.editable').next().attr('id')).val(markdown);
        });
    }

});
