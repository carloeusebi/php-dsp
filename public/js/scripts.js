// ! navbar open and close
const hamburgerMenu = document.getElementById('hamburger-menu');
const topNavbar = document.getElementById('top-navbar');

hamburgerMenu.addEventListener('click', () => {
	hamburgerMenu.classList.toggle('open');
});

// ! Closing email response popup
const closeResponse = document.querySelector('.fa-xmark');
const response = document.querySelector('.response');

if (closeResponse !== null) {
	closeResponse.addEventListener('click', () => {
		response.style.opacity = '0';

		// it waits for animation to complete, then it hides popup
		response.addEventListener(
			'animationend',
			() => {
				response.style.visibility = 'hidden';
			},
			{ once: true }
		);
	});
}

// ! Button is greyed out if checkbox is not checked
const normativeCheckbox = document.getElementById('norm-cb');
const formButton = document.getElementById('formButton');

// checks at page load if normative cb is checked, it should not be checked but sometimes it is when an attempt to send an email returns an error
if (normativeCheckbox.checked) {
	formButton.classList.remove('unclickable');
}

normativeCheckbox.addEventListener('change', () => {
	if (normativeCheckbox.checked) {
		formButton.classList.remove('unclickable');
	} else {
		formButton.classList.add('unclickable');
	}
});

// ! after submit button click changes cursor to loading
const form = document.getElementById('contact-form');
const loader = document.querySelector('.fa-spin');

form.addEventListener('submit', () => {
	document.body.classList.add('wait');
	loader.style.display = 'block';
});
