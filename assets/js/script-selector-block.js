( function( blocks, element ) {
    const el = element.createElement;
	const c = {
		label: 'rstr-block-editor-label',
		fieldgroup: 'rstr-block-editor-fieldgroup'
	};

    blocks.registerBlockType( 'serbian-transliteration/script-selector', {
        title: rstr_block_settings.labels.script_selector.title,
        icon: 'translation',
        category: 'widgets',
        attributes: {
            displayType: { type: 'string', default: 'inline' },
            separator:    { type: 'string', default: '|' },
            cyr_caption:  { type: 'string', default: rstr_block_settings.labels.cyrillic },
            lat_caption:  { type: 'string', default: rstr_block_settings.labels.latin },
        },
        edit: function( props ) {
            return el(
                'div',
                {
					className: 'rstr-block-editor-container'
				},
                [
					// Script selector
					el( 'div', {className: c.fieldgroup},
						el(
							'label',
							{
								className: c.label + ' select',
								for: 'rstr-block-editor-select-script'
							},
							rstr_block_settings.labels.display_type
						),
						el(
							'select',
							{
								id: 'rstr-block-editor-select-script',
								value: props.attributes.displayType,
								onChange: function( e ) {
									props.setAttributes( { displayType: e.target.value } );
								},
							},
							el( 'option', { value: 'inline' },     rstr_block_settings.labels.script_selector.option[0] ),
							el( 'option', { value: 'select' },     rstr_block_settings.labels.script_selector.option[1] ),
							el( 'option', { value: 'list' },       rstr_block_settings.labels.script_selector.option[2] ),
							el( 'option', { value: 'list_items' }, rstr_block_settings.labels.script_selector.option[3] )
						)
					),
                    // Conditional separator field (for 'inline')
                    props.attributes.displayType === 'inline' && el( 'div', {className: c.fieldgroup},
                        el( 'label', {
								className: c.label + ' input',
								for: 'rstr-block-editor-input-separator'
							}, rstr_block_settings.labels.separator ),
                        el( 'input', {
                            type: 'text',
                            value: props.attributes.separator,
                            onChange: function( e ) {
                                props.setAttributes( { separator: e.target.value } );
                            },
							id: 'rstr-block-editor-input-separator'
                        } )
                    ),

                    // Cyrillic caption input
                    el( 'div', {className: c.fieldgroup},
                        el( 'label', {
								className: c.label + ' input',
								for: 'rstr-block-editor-input-cyr-caption'
							}, rstr_block_settings.labels.cyrillic_caption ),
                        el( 'input', {
                            type: 'text',
                            value: props.attributes.cyr_caption,
                            onChange: function( e ) {
                                props.setAttributes( { cyr_caption: e.target.value } );
                            },
							id: 'rstr-block-editor-input-cyr-caption'
                        } )
                    ),

                    // Latin caption input
                    el( 'div', {className: c.fieldgroup},
                        el( 'label', {
								className: c.label + ' input',
								for: 'rstr-block-editor-input-lat-caption'
							}, rstr_block_settings.labels.latin_caption ),
                        el( 'input', {
                            type: 'text',
                            value: props.attributes.lat_caption,
                            onChange: function( e ) {
                                props.setAttributes( { lat_caption: e.target.value } );
                            },
							id: 'rstr-block-editor-input-lat-caption'
                        } )
                    )
                ]
            );
        },
        save: function() {
            return null;
        },
    } );
} )( window.wp.blocks, window.wp.element );
