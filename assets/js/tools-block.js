wp.richText.registerFormatType('transliteration-tool/latin', {
    title: RSTR_TOOL.label.toLatin,
    tagName: 'mark',
    className: 'transliterate-to-latin',
    icon: 'editor-textcolor',
    edit({ isActive, value, onChange }) {
        return wp.element.createElement(
            wp.editor.RichTextToolbarButton, 
            {
                icon: 'editor-textcolor',
                title: RSTR_TOOL.label.toLatin,
                onClick: () => {
                    const content = wp.richText.getTextContent(value);
                    jQuery.ajax({
                        type: 'POST',
                        url: RSTR_TOOL.ajax,
                        data: {
                            action: 'rstr_transliteration_letters',
                            mode: 'cyr_to_lat',
                            nonce: RSTR_TOOL.nonce,
                            value: content,
                            rstr_skip: true
                        }
                    }).done(function (data) {
                        const newContent = wp.richText.create({
                            html: data
                        });
                        onChange(newContent);
                    });
                },
                isActive,
            }
        );
    },
});

wp.richText.registerFormatType('transliteration-tool/cyrillic', {
    title: RSTR_TOOL.label.toCyrillic,
    tagName: 'mark',
    className: 'transliterate-to-cyrillic',
    icon: 'editor-textcolor',
    edit({ isActive, value, onChange }) {
        return wp.element.createElement(
            wp.editor.RichTextToolbarButton, 
            {
                icon: 'editor-textcolor',
                title: RSTR_TOOL.label.toCyrillic,
                onClick: () => {
                    const content = wp.richText.getTextContent(value);
                    jQuery.ajax({
                        type: 'POST',
                        url: RSTR_TOOL.ajax,
                        data: {
                            action: 'rstr_transliteration_letters',
                            mode: 'lat_to_cyr',
                            nonce: RSTR_TOOL.nonce,
                            value: content,
                            rstr_skip: true
                        }
                    }).done(function (data) {
                        const newContent = wp.richText.create({
                            html: data
                        });
                        onChange(newContent);
                    });
                },
                isActive,
            }
        );
    },
});
