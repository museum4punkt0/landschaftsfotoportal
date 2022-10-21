var itemDiff = {
    currentRevision: null,
    historicRevision: null,

    init: function (current, historic) {
        console.log('current: ' + current + ' / historic: ' + historic);
        this.currentRevision = current;
        if (historic) {
            this.historicRevision = historic;
        }
        else {
            this.historicRevision = this.getMostRecentNonDraftRevision();
        }
        //console.log('current: ' + this.currentRevision + ' / historic: ' + this.historicRevision);

        this.selectComparedRevision();
        this.fillHistorySelectText();
        this.resetHighlighting();
        this.highlightCurrentRevisions(this.currentRevision);
        this.highlightHistoricRevisions(this.historicRevision);
        this.addMapMarker();
        this.startDiff();
    },

    getMostRecentNonDraftRevision: function () {
        var selector = '#comparedRevisionSelect option';
        var revs = $(selector).map((index, option) => parseInt(option.value)).sort((a, b) => (b-a));
        return revs[0];
    },

    selectComparedRevision: function () {
        var selector = '#comparedRevisionSelect option[value="' + this.historicRevision + '"]';
        $(selector).prop("selected", "true");
    },

    fillHistorySelectText: function () {
        var selector = '.revision-detail-select option';
        $(selector).each(function () {
            var optionText = $(this).data('content') + $(this).data('meta');
            $($(this)).text(optionText);
        });
    },

    resetHighlighting: function () {
        var selector = '.revision-detail-select option';
        $(selector).each(function () {
            $($(this)).css("background-color", "");
            $($(this)).prop("selected", "false");
        });
    },

    highlightCurrentRevisions: function (revision) {
        var selector = '.revision-detail-select option[value="' + revision + '"]';
        $(selector).each(function () {
            $($(this)).css("background-color", "#ecf6f9");
            $($(this)).prop("selected", "true");
        });
    },

    highlightHistoricRevisions: function (revision) {
        var selector = '.revision-detail-select option[value="' + revision + '"]';
        $(selector).each(function () {
            $($(this)).css("background-color", "#cce7ff");
        });
    },

    addMapMarker: function () {
        // Check for existing map object
        if (!osm_map.map) {
            return false;
        }
        var columnLat = $('input.location_lat').data('column');
        var columnLon = $('input.location_lon').data('column');
        var imagePath = $('#map').data('image-path');
        var lat = this.getHistoricContent(columnLat, '', this.historicRevision);
        var lon = this.getHistoricContent(columnLon, '', this.historicRevision);
        //console.log(lat + '/' + lon);
        // Remove old marker if it exists
        osm_map.removeMarker('historicMarker');
        osm_map.addMarker(lon, lat, imagePath + 'dot.svg', '#cce7ff', 1.0, 'historicMarker');
        osm_map.moveMapToFeatureExtent();
    },

    startDiff: function () {
        var t = this; // define variable in this Scope
        var selector = '[id^="fieldsInput"][type!="hidden"],[name="menu_title"],[name="page_title"],[name="public"]';
        $(selector).each(function () {
            var hc = t.getHistoricContent($(this).data('column'), $(this).data('type'), t.historicRevision);
            var cc = t.getcurrentContent($(this).data('column'), $(this).data('type'));
            var selector2 = this;

            switch ($(this).data('type')) {
                case "boolean":
                    selector2 = '[name^="fields"] + label';
                    break;
                case "daterange":
                    selector2 = '#fieldsInput-' + $(this).data('column');
                    selector2 += ',#fieldsInput-' + $(this).data('column') + '-end';
                    break;
                case "image":
                    // Change image and link for compared revision
                    $('#comparedRevisionImageFilename-' + $(this).data('column')).text(hc);
                    var link = $('#comparedRevisionImageLink-' + $(this).data('column'));
                    var image = $('#comparedRevisionImage-' + $(this).data('column'));
                    $('#comparedRevisionImageLink-' + $(this).data('column')).data('img-source', link.data('path') + hc);
                    $('#comparedRevisionImage-' + $(this).data('column')).attr('src', image.data('path') + hc);
                    break;
            }
            if (t.isEqual(cc, hc)) {
                $(selector2).css("background-color", "#99ff99");
            }
            else {
                $(selector2).css("background-color", "#ffff99");
            }
        });
    },

    getcurrentContent: function (column, type) {
        // This is just a default selector, may be overwritten in switch block!
        var selector = '#fieldsInput-' + column;
        var content = "";

        switch (type) {
            case "menu_title":
                selector = 'input[name="menu_title"]';
                content = $(selector).val().trim();
                break;
            case "page_title":
                selector = 'input[name="page_title"]';
                content = $(selector).val().trim();
                break;
            case "public":
                selector = 'select[name="public"] :selected';
                content = $(selector).val();
                break;
            case "relation":
                if ($('#fieldsHiddenInput-' + column).val() == '') {
                    content = '';
                }
                else {
                    content = $(selector).val().trim();
                }
                break;
            case "list":
                selector += ' :selected';
                if ($(selector).val() == '') {
                    content = '';
                }
                else {
                    content = $(selector).text().trim();
                }
                //console.log(content);
                break;
            case "multi_list":
                selector += ' :selected';
                if ($(selector).val() == '') {
                    content = '';
                }
                else {
                    $(selector).each(function () {
                        content += $(this).text().trim() + '; ';
                    });
                    content = content.replace(/\s/g, '');
                }
                //console.log(content);
                break;
            case "boolean":
                content = $(selector).prop('checked') ? 1 : 0;
                break;
            case "integer":
            case "float":
            case "textarea":
            case "string":
            case "date":
                content = $(selector).val().trim();
                break;
            case "daterange":
                content = $(selector).val() + '-' + $(selector + '-end').val();
                //console.log(content);
                break;
            case "image":
                selector = '#fieldsInput-' + column + '-filename';
                content = $(selector).val();
                break;
        }
        //console.log('col curr ' + column + ': ' + content);
        return content;
    },

    getHistoricContent: function (column, type, revision) {
        var selector = '.revision-detail-select[data-column="' + column + '"] option[value="' + revision + '"]';
        var content = $(selector).data('content');
        // TODO: get rid of these silly white spaces from form_history_detail.blade.php
        if (content) {
            switch (type) {
                case "multi_list":
                case "daterange":
                    content = content.replace(/\s/g, '');
                    break;
                default:
                    content = content.trim();
            }
        }
        //console.log('col hist ' + column + ': ' + content);
        return content;
    },

    isEqual: function (current, historic) {
        if (current == historic) {
            return true;
        }
        else {
            return false;
        }
    }
}

export default itemDiff;
