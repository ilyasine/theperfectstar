
jQuery(document).ready(function ($) {
	// Define language options
	const languages = {
		en: 'English',
		fr: 'French'
	};

	// Initialize current language
	let currentLanguage = kmc_lang;

	// Function to update UI with the selected language strings
	function updateUI(language) {
		$('#welcome-text').text(languageData[language]['Welcome to your tepunareomaori account activation space.']);
		$('#activate-access-desc').text(languageData[language]['activate_access_desc']);
		$('#activate-btn button.single_add_to_cart_button').text(languageData[language]['Activate now']);
		$('#TPRM_-access-desc').text(languageData[language]['To fully enjoy this educational resource, we invite you to activate your account by clicking on the « Activate Now » button and proceed with the online payment.']);
		$('#license-desc').text(languageData[language]['If you have a license acquired on our platform or from a partner library, please activate it by entering the license code in the field below.']);
		$('#license-placeholder').attr('placeholder', languageData[language]['License code']);
		$('#activate-license-btn').attr('value', languageData[language]['Activate']);
		$('#lost-license-link').text(languageData[language]['I have lost my license code.']);
		$('#TPRM_Erros').text(languageData[language]['Code is empty']);
	}

	// Load language data from JSON files
	let languageData = {};

	$.when(
		$.getJSON(json_lang_folder + 'en.json', function (data) {
			languageData['en'] = data;
		}),
		$.getJSON(json_lang_folder + 'fr.json', function (data) {
			languageData['fr'] = data;
		})
	).done(function () {
		updateUI(currentLanguage); // Set initial UI with default language
	});
	// Language switcher event handler
	$('.language-switch').on('click', function () {
		const selectedLanguage = $(this).data('language');
		currentLanguage = selectedLanguage;
		updateUI(selectedLanguage);
		$('.language-switch > img').removeClass('active');
		$(this).children(":first").toggleClass("active");

	});

	$('.language-switch > img').removeClass('active');
	$('.language-switch').each(function () {
		if ($(this).data('language') === currentLanguage) {
			$(this).children(":first").addClass('active');
		}
	});


});
