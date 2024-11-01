const {__} = wp.i18n;

const {
	addFilter
} = wp.hooks;

const {
	PanelBody,
	TextControl,
	SelectControl
} = wp.components;

const {
	Fragment
} = wp.element;

const {
	createHigherOrderComponent
} = wp.compose;

const {
	InspectorControls
} = wp.editor;

export const addUoEnrollmentKeyRegistrationSettings = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		// Check if we have to do something
		if (props.name == 'uncanny-learndash-groups/uo-groups-enrollment-key-registration' && props.isSelected) {
			return (
				<Fragment>
					<BlockEdit {...props} />
					<InspectorControls>

						<PanelBody title={__('Enrollment Key Registratiopn Settings', 'uncanny-learndash-groups')}>
							<TextControl label={__('redirect')} value={props.attributes.redirect} type='text'
										 onChange={(value) => {
											 props.setAttributes({redirect: value});
										 }}/>
							<TextControl label={__('code_optional')} value={props.attributes.code_optional} type='text'
										 onChange={(value) => {
											 props.setAttributes({code_optional: value});
										 }}/>
							<TextControl label={__('auto_login')} value={props.attributes.auto_login} type='text'
										 onChange={(value) => {
											 props.setAttributes({auto_login: value});
										 }}/>
							<TextControl label={__('role')} value={props.attributes.role} type='text'
										 onChange={(value) => {
											 props.setAttributes({role: value});
										 }}/>
						</PanelBody>

					</InspectorControls>
				</Fragment>
			);
		}

		return <BlockEdit {...props} />;
	};
}, 'addUoEnrollmentKeyRegistrationSettings');

addFilter(
	'editor.BlockEdit',
	'uncanny-learndash-groups/uo-groups-enrollment-key-registration',
	addUoEnrollmentKeyRegistrationSettings
);
