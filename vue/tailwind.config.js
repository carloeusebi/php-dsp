/** @type {import('tailwindcss').Config} */
export default {
	content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
	safelist: [
		'bg-orange-100',
		'border-orange-500',
		'text-orange-700',
		'bg-teal-100',
		'border-teal-500',
		'text-teal-700',
		'bg-blue-100',
		'border-blue-500',
		'text-blue-700',
		'text-green-700',
		'hover:text-green-800',
		'left-0',
		'right-0',
		'right-6',
		'left-6',
		'absolute',
		'bg-blue-700',
		'bg-red-700',
		'hover:bg-blue-800',
		'hover:bg-red-800',
		'bg-green-700',
		'hover:bg-green-800',
		'text-gray',
		'grid-cols-1',
		'grid-cols-2',
		'grid-cols-3',
		'grid-cols-4',
		'grid-cols-5',
		'grid-cols-6',
		'grid-cols-7',
		'grid-cols-8',
		'grid-cols-9',
		'grid-cols-10',
	],
	theme: {
		extend: {
			colors: {
				primary: '#6ecc84',
				secondary: '#254d32',
				bg: '#e9ecef',
			},
		},
	},
	plugins: [],
};
