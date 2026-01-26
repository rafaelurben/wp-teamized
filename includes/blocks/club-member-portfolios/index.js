(function (blocks, element, blockEditor, components, i18n) {
    const el = element.createElement;
    const InspectorControls = blockEditor.InspectorControls;
    const useBlockProps = blockEditor.useBlockProps;
    const TextControl = components.TextControl;
    const ToggleControl = components.ToggleControl;
    const PanelBody = components.PanelBody;
    const __ = i18n.__;

    blocks.registerBlockType('teamized/club-member-portfolios', {
        edit: function (props) {
            const attributes = props.attributes;
            const setAttributes = props.setAttributes;
            const blockProps = useBlockProps();

            return el('div', blockProps,
                el(InspectorControls, {key: 'inspector'},
                    el(PanelBody, {title: __('Settings', 'wp-teamized'), initialOpen: true},
                        el(TextControl, {
                            label: __('API URL', 'wp-teamized'),
                            value: attributes.apiUrl,
                            onChange: newUrl => setAttributes({apiUrl: newUrl}),
                            help: __('Enter the API URL to fetch club member portfolios', 'wp-teamized'),
                        }),
                        el(TextControl, {
                            label: __('Default Image 1 URL', 'wp-teamized'),
                            value: attributes.defaultImage1Url,
                            onChange: newUrl => setAttributes({defaultImage1Url: newUrl}),
                            help: __('Fallback image URL when a member has no image 1', 'wp-teamized'),
                        }),
                        el(TextControl, {
                            label: __('Default Image 2 URL', 'wp-teamized'),
                            value: attributes.defaultImage2Url,
                            onChange: newUrl => setAttributes({defaultImage2Url: newUrl}),
                            help: __('Fallback image URL when a member has no image 2', 'wp-teamized'),
                        }),
                        el(ToggleControl, {
                            label: __('Show Title and Description', 'wp-teamized'),
                            checked: attributes.showTitleAndDescription,
                            onChange: newValue => setAttributes({showTitleAndDescription: newValue}),
                            help: __('Display the title and description from the API response', 'wp-teamized'),
                        })
                    )
                ),
                el('div', {className: 'components-placeholder'},
                    el('div', {className: 'components-placeholder__label'},
                        el('span', {className: 'dashicons dashicons-groups'}),
                        __('Teamized club member portfolios', 'wp-teamized')
                    ),
                    el('div', {className: 'components-placeholder__fieldset'},
                        attributes.apiUrl
                            ? el('p', {},
                                __('API URL: ', 'wp-teamized'),
                                el('strong', {}, attributes.apiUrl)
                            )
                            : el('p', {},
                                __('Please configure the API URL in the block settings (right sidebar).', 'wp-teamized')
                            )
                    )
                )
            );
        },

        save: function () {
            // Dynamic block, rendered by PHP
            return null;
        },
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n
);
