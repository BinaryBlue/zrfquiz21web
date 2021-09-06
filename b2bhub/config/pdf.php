<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => 'B2B Solvers',
	'subject'               => 'Diganta Invoice PDF',
	'keywords'              => 'Jasim Uddin',
	'creator'               => 'Jasim Uddin',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('../temp/'),
	'custom_font_path' => base_path('resources/fonts/'),
	'custom_font_data' => [
		'blueerp' => [
			'R'  => 'kalpurush.ttf',
			//'B'  => 'SutonnyMJ Bold.ttf',
			'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
			'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
		],
		'fontawesome' => [
			'R'  => 'fontawesome-webfont.ttf',
		]
	],

	
];
