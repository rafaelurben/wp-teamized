(function (blocks, element, blockEditor, components, i18n) {
    const el = element.createElement;
    const InspectorControls = blockEditor.InspectorControls;
    const useBlockProps = blockEditor.useBlockProps;
    const TextControl = components.TextControl;
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
