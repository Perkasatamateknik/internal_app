<?php $company = $this->Xin_model->read_company_setting_info(1); ?>
<?php $favicon = base_url() . 'uploads/logo/favicon/' . $company[0]->favicon; ?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $title; ?></title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>">
	<link rel="stylesheet" href="styles.css" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
	<style>
		:root {
			--blue: #1e70cd;
			--indigo: #6610f2;
			--purple: #6f42c1;
			--pink: #e83e8c;
			--red: #d9534f;
			--orange: #FEB744;
			--yellow: #FFD950;
			--green: #02BC77;
			--teal: #20c997;
			--cyan: #28c3d7;
			--white: #fff;
			--gray: rgba(24, 28, 33, 0.5);
			--gray-dark: rgba(24, 28, 33, 0.8);
			--primary: #6610f2;
			--secondary: #8897AA;
			--success: #02BC77;
			--info: #28c3d7;
			--warning: #FFD950;
			--danger: #d9534f;
			--light: rgba(24, 28, 33, 0.06);
			--dark: rgba(24, 28, 33, 0.9);
			--breakpoint-xs: 0;
			--breakpoint-sm: 576px;
			--breakpoint-md: 768px;
			--breakpoint-lg: 992px;
			--breakpoint-xl: 1200px;
			--font-family-sans-serif: "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
			--font-family-monospace: "SFMono-Regular", Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace
		}

		*,
		*::before,
		*::after {
			box-sizing: border-box
		}

		html {
			font-family: 'Plus Jakarta Sans', sans-serif;
			/* background: #D9D9D925; */
			line-height: 1.15;
			-webkit-text-size-adjust: 100%;
			-webkit-tap-highlight-color: rgba(24, 28, 33, 0)
		}

		article,
		aside,
		figcaption,
		figure,
		footer,
		header,
		hgroup,
		main,
		nav,
		section {
			display: block
		}

		body {
			margin: 0 auto;
			font-family: 'Plus Jakarta Sans', "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
			font-size: .894rem;
			font-weight: 400;
			line-height: 1.47;
			color: #4E5155;
			text-align: left;
		}

		[tabindex="-1"]:focus {
			outline: 0 !important
		}

		hr {
			box-sizing: content-box;
			height: 0;
			overflow: visible
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			margin-top: 0;
			margin-bottom: 1rem
		}

		p {
			margin-top: 0;
			margin-bottom: 1rem
		}

		abbr[title],
		abbr[data-original-title] {
			text-decoration: underline;
			-webkit-text-decoration: underline dotted;
			text-decoration: underline dotted;
			cursor: help;
			border-bottom: 0;
			-webkit-text-decoration-skip-ink: none;
			text-decoration-skip-ink: none
		}

		address {
			margin-bottom: 1rem;
			font-style: normal;
			line-height: inherit
		}

		ol,
		ul,
		dl {
			margin-top: 0;
			margin-bottom: 1rem
		}

		ol ol,
		ul ul,
		ol ul,
		ul ol {
			margin-bottom: 0
		}

		dt {
			font-weight: 700
		}

		dd {
			margin-bottom: .5rem;
			margin-left: 0
		}

		blockquote {
			margin: 0 0 1rem
		}

		b,
		strong {
			font-weight: 900
		}

		small {
			font-size: 80%
		}

		sub,
		sup {
			position: relative;
			font-size: 75%;
			line-height: 0;
			vertical-align: baseline
		}

		sub {
			bottom: -.25em
		}

		sup {
			top: -.5em
		}

		a {
			color: #1e70cd;
			text-decoration: none;
			background-color: transparent
		}

		a:hover {
			color: #3c8ae2;
			text-decoration: none
		}

		a:not([href]):not([tabindex]) {
			color: inherit;
			text-decoration: none
		}

		a:not([href]):not([tabindex]):hover,
		a:not([href]):not([tabindex]):focus {
			color: inherit;
			text-decoration: none
		}

		a:not([href]):not([tabindex]):focus {
			outline: 0
		}

		pre,
		code,
		kbd,
		samp {
			font-family: "SFMono-Regular", Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
			font-size: 1em
		}

		pre {
			margin-top: 0;
			margin-bottom: 1rem;
			overflow: auto
		}

		figure {
			margin: 0 0 1rem
		}

		img {
			vertical-align: middle;
			border-style: none
		}

		svg {
			overflow: hidden;
			vertical-align: middle
		}

		table {
			border-collapse: collapse
		}

		caption {
			padding-top: .625rem;
			padding-bottom: .625rem;
			color: #a3a4a6;
			text-align: left;
			caption-side: bottom
		}

		th {
			text-align: inherit
		}

		label {
			display: initial;
			margin-bottom: .5rem
		}

		[hidden] {
			display: none !important
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6,
		.h1,
		.h2,
		.h3,
		.h4,
		.h5,
		.h6 {
			margin-bottom: 1rem;
			font-weight: 500;
			line-height: 1.1
		}

		h1,
		.h1 {
			font-size: 2.25rem
		}

		h2,
		.h2 {
			font-size: 1.813rem
		}

		h3,
		.h3 {
			font-size: 1.563rem
		}

		h4,
		.h4 {
			font-size: 1.313rem
		}

		h5,
		.h5 {
			font-size: 1rem
		}

		h6,
		.h6 {
			font-size: .894rem
		}



		hr {
			margin-top: 1rem;
			margin-bottom: 1rem;
			border: 0;
			border-top: 1px solid rgba(24, 28, 33, 0.06)
		}

		small,
		.small {
			font-size: 85%;
			font-weight: 400
		}

		mark,
		.mark {
			padding: .2em;
			background-color: #fcf8e3
		}

		.list-unstyled {
			padding-left: 0;
			list-style: none
		}

		.list-inline {
			padding-left: 0;
			list-style: none
		}

		.list-inline-item {
			display: inline-block
		}

		.list-inline-item:not(:last-child) {
			margin-right: .5rem
		}

		.initialism {
			font-size: 90%;
			text-transform: uppercase
		}

		.blockquote {
			margin-bottom: 1rem;
			font-size: 1.1175rem
		}

		.blockquote-footer {
			display: block;
			font-size: 85%;
			color: #a3a4a6
		}

		.blockquote-footer::before {
			content: "\2014\00A0"
		}

		.img-fluid {
			max-width: 100%;
			height: auto
		}

		.img-thumbnail {
			padding: 0;
			background-color: rgba(0, 0, 0, 0);
			border: 0px solid rgba(24, 28, 33, 0.2);
			border-radius: 0px;
			max-width: 100%;
			height: auto
		}

		.figure {
			display: inline-block
		}

		.figure-img {
			margin-bottom: .5rem;
			line-height: 1
		}

		.figure-caption {
			font-size: 90%;
			color: #a3a4a6
		}

		code {
			font-size: 87.5%;
			color: #e83e8c;
			word-break: break-word
		}

		a>code {
			color: inherit
		}

		kbd {
			padding: .2rem .4rem;
			font-size: 87.5%;
			color: #fff;
			background-color: rgba(24, 28, 33, 0.9);
			border-radius: .25rem
		}

		kbd kbd {
			padding: 0;
			font-size: 100%;
			font-weight: 700
		}

		pre {
			display: block;
			font-size: 87.5%;
			color: rgba(24, 28, 33, 0.9)
		}

		pre code {
			font-size: inherit;
			color: inherit;
			word-break: normal
		}

		.pre-scrollable {
			max-height: 340px;
			overflow-y: scroll
		}

		.container {
			width: 100%;
			padding-right: .75rem;
			padding-left: .75rem;
			margin-right: auto;
			margin-left: auto
		}

		@media (min-width: 576px) {
			.container {
				max-width: 540px
			}
		}

		@media (min-width: 768px) {
			.container {
				max-width: 720px
			}
		}

		@media (min-width: 992px) {
			.container {
				max-width: 960px
			}
		}

		@media (min-width: 1200px) {
			.container {
				max-width: 1140px
			}
		}

		.container-fluid {
			width: 100%;
			/* width: 210mm;
			height: 297mm; */
			margin: 0 auto;
			padding-right: .75rem;
			padding-left: .75rem;
		}

		.row {
			display: -ms-flexbox;
			display: flex;
			-ms-flex-wrap: wrap;
			flex-wrap: wrap;
			margin-right: -.75rem;
			margin-left: -.75rem
		}

		.no-gutters {
			margin-right: 0;
			margin-left: 0
		}

		.no-gutters>.col,
		.no-gutters>[class*="col-"] {
			padding-right: 0;
			padding-left: 0
		}

		.col-1,
		.col-2,
		.col-3,
		.col-4,
		.col-5,
		.col-6,
		.col-7,
		.col-8,
		.col-9,
		.col-10,
		.col-11,
		.col-12,
		.col,
		.col-auto,
		.col-sm-1,
		.col-sm-2,
		.col-sm-3,
		.col-sm-4,
		.col-sm-5,
		.col-sm-6,
		.col-sm-7,
		.col-sm-8,
		.col-sm-9,
		.col-sm-10,
		.col-sm-11,
		.col-sm-12,
		.col-sm,
		.col-sm-auto,
		.col-md-1,
		.col-md-2,
		.col-md-3,
		.col-md-4,
		.col-md-5,
		.col-md-6,
		.col-md-7,
		.col-md-8,
		.col-md-9,
		.col-md-10,
		.col-md-11,
		.col-md-12,
		.col-md,
		.col-md-auto,
		.col-lg-1,
		.col-lg-2,
		.col-lg-3,
		.col-lg-4,
		.col-lg-5,
		.col-lg-6,
		.col-lg-7,
		.col-lg-8,
		.col-lg-9,
		.col-lg-10,
		.col-lg-11,
		.col-lg-12,
		.col-lg,
		.col-lg-auto,
		.col-xl-1,
		.col-xl-2,
		.col-xl-3,
		.col-xl-4,
		.col-xl-5,
		.col-xl-6,
		.col-xl-7,
		.col-xl-8,
		.col-xl-9,
		.col-xl-10,
		.col-xl-11,
		.col-xl-12,
		.col-xl,
		.col-xl-auto {
			position: relative;
			width: 100%;
			padding-right: .75rem;
			padding-left: .75rem
		}

		.col {
			-ms-flex-preferred-size: 0;
			flex-basis: 0;
			-ms-flex-positive: 1;
			flex-grow: 1;
			max-width: 100%
		}

		.col-auto {
			-ms-flex: 0 0 auto;
			flex: 0 0 auto;
			width: auto;
			max-width: 100%
		}

		.col-1 {
			-ms-flex: 0 0 8.33333%;
			flex: 0 0 8.33333%;
			max-width: 8.33333%
		}

		.col-2 {
			-ms-flex: 0 0 16.66667%;
			flex: 0 0 16.66667%;
			max-width: 16.66667%
		}

		.col-3 {
			-ms-flex: 0 0 25%;
			flex: 0 0 25%;
			max-width: 25%
		}

		.col-4 {
			-ms-flex: 0 0 33.33333%;
			flex: 0 0 33.33333%;
			max-width: 33.33333%
		}

		.col-5 {
			-ms-flex: 0 0 41.66667%;
			flex: 0 0 41.66667%;
			max-width: 41.66667%
		}

		.col-6 {
			-ms-flex: 0 0 50%;
			flex: 0 0 50%;
			max-width: 50%
		}

		.col-7 {
			-ms-flex: 0 0 58.33333%;
			flex: 0 0 58.33333%;
			max-width: 58.33333%
		}

		.col-8 {
			-ms-flex: 0 0 66.66667%;
			flex: 0 0 66.66667%;
			max-width: 66.66667%
		}

		.col-9 {
			-ms-flex: 0 0 75%;
			flex: 0 0 75%;
			max-width: 75%
		}

		.col-10 {
			-ms-flex: 0 0 83.33333%;
			flex: 0 0 83.33333%;
			max-width: 83.33333%
		}

		.col-11 {
			-ms-flex: 0 0 91.66667%;
			flex: 0 0 91.66667%;
			max-width: 91.66667%
		}

		.col-12 {
			-ms-flex: 0 0 100%;
			flex: 0 0 100%;
			max-width: 100%
		}

		.order-first {
			-ms-flex-order: -1;
			order: -1
		}

		.order-last {
			-ms-flex-order: 13;
			order: 13
		}

		.order-0 {
			-ms-flex-order: 0;
			order: 0
		}

		.order-1 {
			-ms-flex-order: 1;
			order: 1
		}

		.order-2 {
			-ms-flex-order: 2;
			order: 2
		}

		.order-3 {
			-ms-flex-order: 3;
			order: 3
		}

		.order-4 {
			-ms-flex-order: 4;
			order: 4
		}

		.order-5 {
			-ms-flex-order: 5;
			order: 5
		}

		.order-6 {
			-ms-flex-order: 6;
			order: 6
		}

		.order-7 {
			-ms-flex-order: 7;
			order: 7
		}

		.order-8 {
			-ms-flex-order: 8;
			order: 8
		}

		.order-9 {
			-ms-flex-order: 9;
			order: 9
		}

		.order-10 {
			-ms-flex-order: 10;
			order: 10
		}

		.order-11 {
			-ms-flex-order: 11;
			order: 11
		}

		.order-12 {
			-ms-flex-order: 12;
			order: 12
		}

		.offset-1 {
			margin-left: 8.33333%
		}

		.offset-2 {
			margin-left: 16.66667%
		}

		.offset-3 {
			margin-left: 25%
		}

		.offset-4 {
			margin-left: 33.33333%
		}

		.offset-5 {
			margin-left: 41.66667%
		}

		.offset-6 {
			margin-left: 50%
		}

		.offset-7 {
			margin-left: 58.33333%
		}

		.offset-8 {
			margin-left: 66.66667%
		}

		.offset-9 {
			margin-left: 75%
		}

		.offset-10 {
			margin-left: 83.33333%
		}

		.offset-11 {
			margin-left: 91.66667%
		}

		@media (min-width: 576px) {
			.col-sm {
				-ms-flex-preferred-size: 0;
				flex-basis: 0;
				-ms-flex-positive: 1;
				flex-grow: 1;
				max-width: 100%
			}

			.col-sm-auto {
				-ms-flex: 0 0 auto;
				flex: 0 0 auto;
				width: auto;
				max-width: 100%
			}

			.col-sm-1 {
				-ms-flex: 0 0 8.33333%;
				flex: 0 0 8.33333%;
				max-width: 8.33333%
			}

			.col-sm-2 {
				-ms-flex: 0 0 16.66667%;
				flex: 0 0 16.66667%;
				max-width: 16.66667%
			}

			.col-sm-3 {
				-ms-flex: 0 0 25%;
				flex: 0 0 25%;
				max-width: 25%
			}

			.col-sm-4 {
				-ms-flex: 0 0 33.33333%;
				flex: 0 0 33.33333%;
				max-width: 33.33333%
			}

			.col-sm-5 {
				-ms-flex: 0 0 41.66667%;
				flex: 0 0 41.66667%;
				max-width: 41.66667%
			}

			.col-sm-6 {
				-ms-flex: 0 0 50%;
				flex: 0 0 50%;
				max-width: 50%
			}

			.col-sm-7 {
				-ms-flex: 0 0 58.33333%;
				flex: 0 0 58.33333%;
				max-width: 58.33333%
			}

			.col-sm-8 {
				-ms-flex: 0 0 66.66667%;
				flex: 0 0 66.66667%;
				max-width: 66.66667%
			}

			.col-sm-9 {
				-ms-flex: 0 0 75%;
				flex: 0 0 75%;
				max-width: 75%
			}

			.col-sm-10 {
				-ms-flex: 0 0 83.33333%;
				flex: 0 0 83.33333%;
				max-width: 83.33333%
			}

			.col-sm-11 {
				-ms-flex: 0 0 91.66667%;
				flex: 0 0 91.66667%;
				max-width: 91.66667%
			}

			.col-sm-12 {
				-ms-flex: 0 0 100%;
				flex: 0 0 100%;
				max-width: 100%
			}

			.order-sm-first {
				-ms-flex-order: -1;
				order: -1
			}

			.order-sm-last {
				-ms-flex-order: 13;
				order: 13
			}

			.order-sm-0 {
				-ms-flex-order: 0;
				order: 0
			}

			.order-sm-1 {
				-ms-flex-order: 1;
				order: 1
			}

			.order-sm-2 {
				-ms-flex-order: 2;
				order: 2
			}

			.order-sm-3 {
				-ms-flex-order: 3;
				order: 3
			}

			.order-sm-4 {
				-ms-flex-order: 4;
				order: 4
			}

			.order-sm-5 {
				-ms-flex-order: 5;
				order: 5
			}

			.order-sm-6 {
				-ms-flex-order: 6;
				order: 6
			}

			.order-sm-7 {
				-ms-flex-order: 7;
				order: 7
			}

			.order-sm-8 {
				-ms-flex-order: 8;
				order: 8
			}

			.order-sm-9 {
				-ms-flex-order: 9;
				order: 9
			}

			.order-sm-10 {
				-ms-flex-order: 10;
				order: 10
			}

			.order-sm-11 {
				-ms-flex-order: 11;
				order: 11
			}

			.order-sm-12 {
				-ms-flex-order: 12;
				order: 12
			}

			.offset-sm-0 {
				margin-left: 0
			}

			.offset-sm-1 {
				margin-left: 8.33333%
			}

			.offset-sm-2 {
				margin-left: 16.66667%
			}

			.offset-sm-3 {
				margin-left: 25%
			}

			.offset-sm-4 {
				margin-left: 33.33333%
			}

			.offset-sm-5 {
				margin-left: 41.66667%
			}

			.offset-sm-6 {
				margin-left: 50%
			}

			.offset-sm-7 {
				margin-left: 58.33333%
			}

			.offset-sm-8 {
				margin-left: 66.66667%
			}

			.offset-sm-9 {
				margin-left: 75%
			}

			.offset-sm-10 {
				margin-left: 83.33333%
			}

			.offset-sm-11 {
				margin-left: 91.66667%
			}
		}

		@media (min-width: 768px) {
			.col-md {
				-ms-flex-preferred-size: 0;
				flex-basis: 0;
				-ms-flex-positive: 1;
				flex-grow: 1;
				max-width: 100%
			}

			.col-md-auto {
				-ms-flex: 0 0 auto;
				flex: 0 0 auto;
				width: auto;
				max-width: 100%
			}

			.col-md-1 {
				-ms-flex: 0 0 8.33333%;
				flex: 0 0 8.33333%;
				max-width: 8.33333%
			}

			.col-md-2 {
				-ms-flex: 0 0 16.66667%;
				flex: 0 0 16.66667%;
				max-width: 16.66667%
			}

			.col-md-3 {
				-ms-flex: 0 0 25%;
				flex: 0 0 25%;
				max-width: 25%
			}

			.col-md-4 {
				-ms-flex: 0 0 33.33333%;
				flex: 0 0 33.33333%;
				max-width: 33.33333%
			}

			.col-md-5 {
				-ms-flex: 0 0 41.66667%;
				flex: 0 0 41.66667%;
				max-width: 41.66667%
			}

			.col-md-6 {
				-ms-flex: 0 0 50%;
				flex: 0 0 50%;
				max-width: 50%
			}

			.col-md-7 {
				-ms-flex: 0 0 58.33333%;
				flex: 0 0 58.33333%;
				max-width: 58.33333%
			}

			.col-md-8 {
				-ms-flex: 0 0 66.66667%;
				flex: 0 0 66.66667%;
				max-width: 66.66667%
			}

			.col-md-9 {
				-ms-flex: 0 0 75%;
				flex: 0 0 75%;
				max-width: 75%
			}

			.col-md-10 {
				-ms-flex: 0 0 83.33333%;
				flex: 0 0 83.33333%;
				max-width: 83.33333%
			}

			.col-md-11 {
				-ms-flex: 0 0 91.66667%;
				flex: 0 0 91.66667%;
				max-width: 91.66667%
			}

			.col-md-12 {
				-ms-flex: 0 0 100%;
				flex: 0 0 100%;
				max-width: 100%
			}

			.order-md-first {
				-ms-flex-order: -1;
				order: -1
			}

			.order-md-last {
				-ms-flex-order: 13;
				order: 13
			}

			.order-md-0 {
				-ms-flex-order: 0;
				order: 0
			}

			.order-md-1 {
				-ms-flex-order: 1;
				order: 1
			}

			.order-md-2 {
				-ms-flex-order: 2;
				order: 2
			}

			.order-md-3 {
				-ms-flex-order: 3;
				order: 3
			}

			.order-md-4 {
				-ms-flex-order: 4;
				order: 4
			}

			.order-md-5 {
				-ms-flex-order: 5;
				order: 5
			}

			.order-md-6 {
				-ms-flex-order: 6;
				order: 6
			}

			.order-md-7 {
				-ms-flex-order: 7;
				order: 7
			}

			.order-md-8 {
				-ms-flex-order: 8;
				order: 8
			}

			.order-md-9 {
				-ms-flex-order: 9;
				order: 9
			}

			.order-md-10 {
				-ms-flex-order: 10;
				order: 10
			}

			.order-md-11 {
				-ms-flex-order: 11;
				order: 11
			}

			.order-md-12 {
				-ms-flex-order: 12;
				order: 12
			}

			.offset-md-0 {
				margin-left: 0
			}

			.offset-md-1 {
				margin-left: 8.33333%
			}

			.offset-md-2 {
				margin-left: 16.66667%
			}

			.offset-md-3 {
				margin-left: 25%
			}

			.offset-md-4 {
				margin-left: 33.33333%
			}

			.offset-md-5 {
				margin-left: 41.66667%
			}

			.offset-md-6 {
				margin-left: 50%
			}

			.offset-md-7 {
				margin-left: 58.33333%
			}

			.offset-md-8 {
				margin-left: 66.66667%
			}

			.offset-md-9 {
				margin-left: 75%
			}

			.offset-md-10 {
				margin-left: 83.33333%
			}

			.offset-md-11 {
				margin-left: 91.66667%
			}
		}

		@media (min-width: 992px) {
			.col-lg {
				-ms-flex-preferred-size: 0;
				flex-basis: 0;
				-ms-flex-positive: 1;
				flex-grow: 1;
				max-width: 100%
			}

			.col-lg-auto {
				-ms-flex: 0 0 auto;
				flex: 0 0 auto;
				width: auto;
				max-width: 100%
			}

			.col-lg-1 {
				-ms-flex: 0 0 8.33333%;
				flex: 0 0 8.33333%;
				max-width: 8.33333%
			}

			.col-lg-2 {
				-ms-flex: 0 0 16.66667%;
				flex: 0 0 16.66667%;
				max-width: 16.66667%
			}

			.col-lg-3 {
				-ms-flex: 0 0 25%;
				flex: 0 0 25%;
				max-width: 25%
			}

			.col-lg-4 {
				-ms-flex: 0 0 33.33333%;
				flex: 0 0 33.33333%;
				max-width: 33.33333%
			}

			.col-lg-5 {
				-ms-flex: 0 0 41.66667%;
				flex: 0 0 41.66667%;
				max-width: 41.66667%
			}

			.col-lg-6 {
				-ms-flex: 0 0 50%;
				flex: 0 0 50%;
				max-width: 50%
			}

			.col-lg-7 {
				-ms-flex: 0 0 58.33333%;
				flex: 0 0 58.33333%;
				max-width: 58.33333%
			}

			.col-lg-8 {
				-ms-flex: 0 0 66.66667%;
				flex: 0 0 66.66667%;
				max-width: 66.66667%
			}

			.col-lg-9 {
				-ms-flex: 0 0 75%;
				flex: 0 0 75%;
				max-width: 75%
			}

			.col-lg-10 {
				-ms-flex: 0 0 83.33333%;
				flex: 0 0 83.33333%;
				max-width: 83.33333%
			}

			.col-lg-11 {
				-ms-flex: 0 0 91.66667%;
				flex: 0 0 91.66667%;
				max-width: 91.66667%
			}

			.col-lg-12 {
				-ms-flex: 0 0 100%;
				flex: 0 0 100%;
				max-width: 100%
			}

			.order-lg-first {
				-ms-flex-order: -1;
				order: -1
			}

			.order-lg-last {
				-ms-flex-order: 13;
				order: 13
			}

			.order-lg-0 {
				-ms-flex-order: 0;
				order: 0
			}

			.order-lg-1 {
				-ms-flex-order: 1;
				order: 1
			}

			.order-lg-2 {
				-ms-flex-order: 2;
				order: 2
			}

			.order-lg-3 {
				-ms-flex-order: 3;
				order: 3
			}

			.order-lg-4 {
				-ms-flex-order: 4;
				order: 4
			}

			.order-lg-5 {
				-ms-flex-order: 5;
				order: 5
			}

			.order-lg-6 {
				-ms-flex-order: 6;
				order: 6
			}

			.order-lg-7 {
				-ms-flex-order: 7;
				order: 7
			}

			.order-lg-8 {
				-ms-flex-order: 8;
				order: 8
			}

			.order-lg-9 {
				-ms-flex-order: 9;
				order: 9
			}

			.order-lg-10 {
				-ms-flex-order: 10;
				order: 10
			}

			.order-lg-11 {
				-ms-flex-order: 11;
				order: 11
			}

			.order-lg-12 {
				-ms-flex-order: 12;
				order: 12
			}

			.offset-lg-0 {
				margin-left: 0
			}

			.offset-lg-1 {
				margin-left: 8.33333%
			}

			.offset-lg-2 {
				margin-left: 16.66667%
			}

			.offset-lg-3 {
				margin-left: 25%
			}

			.offset-lg-4 {
				margin-left: 33.33333%
			}

			.offset-lg-5 {
				margin-left: 41.66667%
			}

			.offset-lg-6 {
				margin-left: 50%
			}

			.offset-lg-7 {
				margin-left: 58.33333%
			}

			.offset-lg-8 {
				margin-left: 66.66667%
			}

			.offset-lg-9 {
				margin-left: 75%
			}

			.offset-lg-10 {
				margin-left: 83.33333%
			}

			.offset-lg-11 {
				margin-left: 91.66667%
			}
		}

		@media (min-width: 1200px) {
			.col-xl {
				-ms-flex-preferred-size: 0;
				flex-basis: 0;
				-ms-flex-positive: 1;
				flex-grow: 1;
				max-width: 100%
			}

			.col-xl-auto {
				-ms-flex: 0 0 auto;
				flex: 0 0 auto;
				width: auto;
				max-width: 100%
			}

			.col-xl-1 {
				-ms-flex: 0 0 8.33333%;
				flex: 0 0 8.33333%;
				max-width: 8.33333%
			}

			.col-xl-2 {
				-ms-flex: 0 0 16.66667%;
				flex: 0 0 16.66667%;
				max-width: 16.66667%
			}

			.col-xl-3 {
				-ms-flex: 0 0 25%;
				flex: 0 0 25%;
				max-width: 25%
			}

			.col-xl-4 {
				-ms-flex: 0 0 33.33333%;
				flex: 0 0 33.33333%;
				max-width: 33.33333%
			}

			.col-xl-5 {
				-ms-flex: 0 0 41.66667%;
				flex: 0 0 41.66667%;
				max-width: 41.66667%
			}

			.col-xl-6 {
				-ms-flex: 0 0 50%;
				flex: 0 0 50%;
				max-width: 50%
			}

			.col-xl-7 {
				-ms-flex: 0 0 58.33333%;
				flex: 0 0 58.33333%;
				max-width: 58.33333%
			}

			.col-xl-8 {
				-ms-flex: 0 0 66.66667%;
				flex: 0 0 66.66667%;
				max-width: 66.66667%
			}

			.col-xl-9 {
				-ms-flex: 0 0 75%;
				flex: 0 0 75%;
				max-width: 75%
			}

			.col-xl-10 {
				-ms-flex: 0 0 83.33333%;
				flex: 0 0 83.33333%;
				max-width: 83.33333%
			}

			.col-xl-11 {
				-ms-flex: 0 0 91.66667%;
				flex: 0 0 91.66667%;
				max-width: 91.66667%
			}

			.col-xl-12 {
				-ms-flex: 0 0 100%;
				flex: 0 0 100%;
				max-width: 100%
			}

			.order-xl-first {
				-ms-flex-order: -1;
				order: -1
			}

			.order-xl-last {
				-ms-flex-order: 13;
				order: 13
			}

			.order-xl-0 {
				-ms-flex-order: 0;
				order: 0
			}

			.order-xl-1 {
				-ms-flex-order: 1;
				order: 1
			}

			.order-xl-2 {
				-ms-flex-order: 2;
				order: 2
			}

			.order-xl-3 {
				-ms-flex-order: 3;
				order: 3
			}

			.order-xl-4 {
				-ms-flex-order: 4;
				order: 4
			}

			.order-xl-5 {
				-ms-flex-order: 5;
				order: 5
			}

			.order-xl-6 {
				-ms-flex-order: 6;
				order: 6
			}

			.order-xl-7 {
				-ms-flex-order: 7;
				order: 7
			}

			.order-xl-8 {
				-ms-flex-order: 8;
				order: 8
			}

			.order-xl-9 {
				-ms-flex-order: 9;
				order: 9
			}

			.order-xl-10 {
				-ms-flex-order: 10;
				order: 10
			}

			.order-xl-11 {
				-ms-flex-order: 11;
				order: 11
			}

			.order-xl-12 {
				-ms-flex-order: 12;
				order: 12
			}

			.offset-xl-0 {
				margin-left: 0
			}

			.offset-xl-1 {
				margin-left: 8.33333%
			}

			.offset-xl-2 {
				margin-left: 16.66667%
			}

			.offset-xl-3 {
				margin-left: 25%
			}

			.offset-xl-4 {
				margin-left: 33.33333%
			}

			.offset-xl-5 {
				margin-left: 41.66667%
			}

			.offset-xl-6 {
				margin-left: 50%
			}

			.offset-xl-7 {
				margin-left: 58.33333%
			}

			.offset-xl-8 {
				margin-left: 66.66667%
			}

			.offset-xl-9 {
				margin-left: 75%
			}

			.offset-xl-10 {
				margin-left: 83.33333%
			}

			.offset-xl-11 {
				margin-left: 91.66667%
			}
		}

		.table {
			width: 100%;
			margin-bottom: 1rem;
			color: #4E5155
		}

		.table th,
		.table td {
			padding: .625rem;
			vertical-align: top;
			border-top: 1px solid #e8e8e9
		}

		.table thead th {
			vertical-align: bottom;
			border-bottom: 2px solid #e8e8e9
		}

		.table tbody+tbody {
			border-top: 2px solid #e8e8e9
		}

		.table-sm th,
		.table-sm td {
			padding: .3125rem
		}

		.table-bordered {
			border: 1px solid #e8e8e9
		}

		.table-bordered th,
		.table-bordered td {
			border: 1px solid #e8e8e9
		}

		.table-bordered thead th,
		.table-bordered thead td {
			border-bottom-width: 2px
		}

		.table-borderless th,
		.table-borderless td,
		.table-borderless thead th,
		.table-borderless tbody+tbody {
			border: 0
		}

		.table-striped tbody tr:nth-of-type(odd) {
			background-color: rgba(24, 28, 33, 0.025)
		}

		.table-hover tbody tr:hover {
			color: #4E5155;
			background-color: rgba(24, 28, 33, 0.035)
		}

		.table .thead-dark th {
			color: #fff;
			background-color: rgba(24, 28, 33, 0.9);
			border-color: #3f454a
		}

		.table .thead-light th {
			color: #4E5155;
			background-color: rgba(24, 28, 33, 0.03);
			border-color: #e8e8e9
		}

		.table-dark {
			color: #fff;
			background-color: rgba(24, 28, 33, 0.9)
		}

		.table-dark th,
		.table-dark td,
		.table-dark thead th {
			border-color: #3f454a
		}

		.table-dark.table-bordered {
			border: 0
		}

		.table-dark.table-striped tbody tr:nth-of-type(odd) {
			background-color: rgba(255, 255, 255, 0.03)
		}

		.table-dark.table-hover tbody tr:hover {
			color: #fff;
			background-color: rgba(255, 255, 255, 0.05)
		}

		@media (max-width: 575.98px) {
			.table-responsive-sm {
				display: block;
				width: 100%;
				overflow-x: auto;
				-webkit-overflow-scrolling: touch
			}

			.table-responsive-sm>.table-bordered {
				border: 0
			}
		}

		@media (max-width: 767.98px) {
			.table-responsive-md {
				display: block;
				width: 100%;
				overflow-x: auto;
				-webkit-overflow-scrolling: touch
			}

			.table-responsive-md>.table-bordered {
				border: 0
			}
		}

		@media (max-width: 991.98px) {
			.table-responsive-lg {
				display: block;
				width: 100%;
				overflow-x: auto;
				-webkit-overflow-scrolling: touch
			}

			.table-responsive-lg>.table-bordered {
				border: 0
			}
		}

		@media (max-width: 1199.98px) {
			.table-responsive-xl {
				display: block;
				width: 100%;
				overflow-x: auto;
				-webkit-overflow-scrolling: touch
			}

			.table-responsive-xl>.table-bordered {
				border: 0
			}
		}

		.table-responsive {
			display: block;
			width: 100%;
			overflow-x: auto;
			-webkit-overflow-scrolling: touch
		}

		.table-responsive>.table-bordered {
			border: 0
		}

		.form-control {
			display: block;
			width: 100%;
			height: calc(1.54em + .876rem + 2px);
			padding: .438rem .875rem;
			font-size: .894rem;
			font-weight: 400;
			line-height: 1.54;
			color: #4E5155;
			background-color: #fff;
			background-clip: padding-box;
			border: 1px solid rgba(24, 28, 33, 0.1);
			border-radius: .25rem;
			transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out
		}

		@media (prefers-reduced-motion: reduce) {
			.form-control {
				transition: none
			}
		}

		.form-control::-ms-expand {
			background-color: transparent;
			border: 0
		}

		.form-control:focus {
			color: #4E5155;
			background-color: #fff;
			border-color: #b389f9;
			outline: 0;
			box-shadow: none
		}

		.form-control::-webkit-input-placeholder {
			color: #babbbc;
			opacity: 1
		}

		.form-control::-moz-placeholder {
			color: #babbbc;
			opacity: 1
		}

		.form-control:-ms-input-placeholder {
			color: #babbbc;
			opacity: 1
		}

		.form-control::-ms-input-placeholder {
			color: #babbbc;
			opacity: 1
		}

		.form-control::placeholder {
			color: #babbbc;
			opacity: 1
		}

		.form-control:disabled,
		.form-control[readonly] {
			background-color: #f1f1f2;
			opacity: 1
		}

		select.form-control:focus::-ms-value {
			color: #4E5155;
			background-color: #fff
		}

		.form-control-file,
		.form-control-range {
			display: block;
			width: 100%
		}

		.col-form-label {
			padding-top: calc(.438rem + 1px);
			padding-bottom: calc(.438rem + 1px);
			margin-bottom: 0;
			font-size: inherit;
			line-height: 1.54
		}

		.col-form-label-lg {
			padding-top: calc(.75rem + 1px);
			padding-bottom: calc(.75rem + 1px);
			font-size: 1rem;
			line-height: 1.5
		}

		.col-form-label-sm {
			padding-top: calc(.188rem + 1px);
			padding-bottom: calc(.188rem + 1px);
			font-size: .75rem;
			line-height: 1.5
		}

		.form-control-plaintext {
			display: block;
			width: 100%;
			padding-top: .438rem;
			padding-bottom: .438rem;
			margin-bottom: 0;
			line-height: 1.54;
			color: #4E5155;
			background-color: transparent;
			border: solid transparent;
			border-width: 1px 0
		}

		.form-control-plaintext.form-control-sm,
		.form-control-plaintext.form-control-lg {
			padding-right: 0;
			padding-left: 0
		}

		.form-control-sm {
			height: calc(1.5em + .376rem + 2px);
			padding: .188rem .625rem;
			font-size: .75rem;
			line-height: 1.5;
			border-radius: .25rem
		}

		.form-control-lg {
			height: calc(1.5em + 1.5rem + 2px);
			padding: .75rem 1.25rem;
			font-size: 1rem;
			line-height: 1.5;
			border-radius: .25rem
		}

		select.form-control[size],
		select.form-control[multiple] {
			height: auto
		}

		textarea.form-control {
			height: auto
		}

		.form-group {
			margin-bottom: 1rem
		}

		.form-text {
			display: block;
			margin-top: .25rem
		}

		.form-row {
			display: -ms-flexbox;
			display: flex;
			-ms-flex-wrap: wrap;
			flex-wrap: wrap;
			margin-right: -5px;
			margin-left: -5px
		}

		.form-row>.col,
		.form-row>[class*="col-"] {
			padding-right: 5px;
			padding-left: 5px
		}

		.form-check {
			position: relative;
			display: block;
			padding-left: 1.25rem
		}

		.form-check-input {
			position: absolute;
			margin-top: .3rem;
			margin-left: -1.25rem
		}

		.form-check-input:disabled~.form-check-label {
			color: #a3a4a6
		}

		.form-check-label {
			margin-bottom: 0
		}

		.form-check-inline {
			display: -ms-inline-flexbox;
			display: inline-flex;
			-ms-flex-align: center;
			align-items: center;
			padding-left: 0;
			margin-right: .75rem
		}

		.form-check-inline .form-check-input {
			position: static;
			margin-top: 0;
			margin-right: .3125rem;
			margin-left: 0
		}

		.form-inline {
			display: -ms-flexbox;
			display: flex;
			-ms-flex-flow: row wrap;
			flex-flow: row wrap;
			-ms-flex-align: center;
			align-items: center
		}

		.form-inline .form-check {
			width: 100%
		}

		@media (min-width: 576px) {
			.form-inline label {
				display: -ms-flexbox;
				display: flex;
				-ms-flex-align: center;
				align-items: center;
				-ms-flex-pack: center;
				justify-content: center;
				margin-bottom: 0
			}

			.form-inline .form-group {
				display: -ms-flexbox;
				display: flex;
				-ms-flex: 0 0 auto;
				flex: 0 0 auto;
				-ms-flex-flow: row wrap;
				flex-flow: row wrap;
				-ms-flex-align: center;
				align-items: center;
				margin-bottom: 0
			}

			.form-inline .form-control {
				display: inline-block;
				width: auto;
				vertical-align: middle
			}

			.form-inline .form-control-plaintext {
				display: inline-block
			}

			.form-inline .input-group,
			.form-inline .custom-select {
				width: auto
			}

			.form-inline .form-check {
				display: -ms-flexbox;
				display: flex;
				-ms-flex-align: center;
				align-items: center;
				-ms-flex-pack: center;
				justify-content: center;
				width: auto;
				padding-left: 0
			}

			.form-inline .form-check-input {
				position: relative;
				-ms-flex-negative: 0;
				flex-shrink: 0;
				margin-top: 0;
				margin-right: .25rem;
				margin-left: 0
			}

			.form-inline .custom-control {
				-ms-flex-align: center;
				align-items: center;
				-ms-flex-pack: center;
				justify-content: center
			}

			.form-inline .custom-control-label {
				margin-bottom: 0
			}
		}

		.btn {
			display: inline-block;
			font-weight: 400;
			color: #4E5155;
			text-align: center;
			vertical-align: middle;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
			background-color: transparent;
			border: 1px solid transparent;
			padding: .438rem 1.125rem;
			font-size: .894rem;
			line-height: 1.54;
			border-radius: .25rem;
			transition: all 0.2s ease-in-out
		}

		@media (prefers-reduced-motion: reduce) {
			.btn {
				transition: none
			}
		}

		.btn:hover {
			color: #4E5155;
			text-decoration: none
		}

		.btn:focus,
		.btn.focus {
			outline: 0;
			box-shadow: 0 0 0 2px rgba(102, 16, 242, 0.25)
		}

		.btn.disabled,
		.btn:disabled {
			opacity: .65
		}

		a.btn.disabled,
		fieldset:disabled a.btn {
			pointer-events: none
		}

		.btn-link {
			font-weight: 400;
			color: #1e70cd;
			text-decoration: none
		}

		.btn-link:hover {
			color: #3c8ae2;
			text-decoration: none
		}

		.btn-link:focus,
		.btn-link.focus {
			text-decoration: none;
			box-shadow: none
		}

		.btn-link:disabled,
		.btn-link.disabled {
			color: rgba(24, 28, 33, 0.5);
			pointer-events: none
		}

		.btn-lg,
		.btn-group-lg>.btn {
			padding: .75rem 1.5rem;
			font-size: 1rem;
			line-height: 1.5;
			border-radius: .25rem
		}

		.btn-sm,
		.btn-group-sm>.btn {
			padding: .188rem .6875rem;
			font-size: .75rem;
			line-height: 1.5;
			border-radius: .25rem
		}

		.btn-block {
			display: block;
			width: 100%
		}

		.btn-block+.btn-block {
			margin-top: .5rem
		}

		input[type="submit"].btn-block,
		input[type="reset"].btn-block,
		input[type="button"].btn-block {
			width: 100%
		}

		.fade {
			transition: opacity 0.15s linear
		}

		@media (prefers-reduced-motion: reduce) {
			.fade {
				transition: none
			}
		}

		.fade:not(.show) {
			opacity: 0
		}

		.collapse:not(.show) {
			display: none
		}

		.collapsing {
			position: relative;
			height: 0;
			overflow: hidden;
			transition: height 0.35s ease
		}

		@media (prefers-reduced-motion: reduce) {
			.collapsing {
				transition: none
			}
		}

		.dropup,
		.dropright,
		.dropdown,
		.dropleft {
			position: relative
		}

		.dropdown-toggle {
			white-space: nowrap
		}

		.dropdown-toggle::after {
			display: inline-block;
			margin-left: .5em;
			vertical-align: middle;
			content: "";
			margin-top: -.28em;
			width: .42em;
			height: .42em;
			border: 1px solid;
			border-top: 0;
			border-left: 0;
			-webkit-transform: rotate(45deg);
			transform: rotate(45deg)
		}

		.dropdown-toggle:empty::after {
			margin-left: 0
		}

		.dropdown-menu {
			position: absolute;
			top: 100%;
			left: 0;
			z-index: 1000;
			display: none;
			float: left;
			min-width: 10rem;
			padding: .3125rem 0;
			margin: .125rem 0 0;
			font-size: .894rem;
			color: #4E5155;
			text-align: left;
			list-style: none;
			background-color: #fff;
			background-clip: padding-box;
			border: 1px solid rgba(24, 28, 33, 0.05);
			border-radius: .25rem
		}

		.dropdown-menu-left {
			right: auto;
			left: 0
		}

		.dropdown-menu-right {
			right: 0;
			left: auto
		}

		@media (min-width: 576px) {
			.dropdown-menu-sm-left {
				right: auto;
				left: 0
			}

			.dropdown-menu-sm-right {
				right: 0;
				left: auto
			}
		}

		@media (min-width: 768px) {
			.dropdown-menu-md-left {
				right: auto;
				left: 0
			}

			.dropdown-menu-md-right {
				right: 0;
				left: auto
			}
		}

		@media (min-width: 992px) {
			.dropdown-menu-lg-left {
				right: auto;
				left: 0
			}

			.dropdown-menu-lg-right {
				right: 0;
				left: auto
			}
		}

		@media (min-width: 1200px) {
			.dropdown-menu-xl-left {
				right: auto;
				left: 0
			}

			.dropdown-menu-xl-right {
				right: 0;
				left: auto
			}
		}

		.dropup .dropdown-menu {
			top: auto;
			bottom: 100%;
			margin-top: 0;
			margin-bottom: .125rem
		}

		.dropup .dropdown-toggle::after {
			display: inline-block;
			margin-left: .5em;
			vertical-align: middle;
			content: "";
			margin-top: -0;
			width: .42em;
			height: .42em;
			border: 1px solid;
			border-bottom: 0;
			border-left: 0;
			-webkit-transform: rotate(-45deg);
			transform: rotate(-45deg)
		}

		.dropup .dropdown-toggle:empty::after {
			margin-left: 0
		}

		.dropright .dropdown-menu {
			top: 0;
			right: auto;
			left: 100%;
			margin-top: 0;
			margin-left: .125rem
		}

		.dropright .dropdown-toggle::after {
			display: inline-block;
			margin-left: .5em;
			vertical-align: middle;
			content: "";
			margin-top: -.21em;
			width: .42em;
			height: .42em;
			border: 1px solid;
			border-top: 0;
			border-left: 0;
			-webkit-transform: rotate(-45deg);
			transform: rotate(-45deg)
		}

		.dropright .dropdown-toggle:empty::after {
			margin-left: 0
		}

		.dropright .dropdown-toggle::after {
			vertical-align: 0
		}

		.dropleft .dropdown-menu {
			top: 0;
			right: 100%;
			left: auto;
			margin-top: 0;
			margin-right: .125rem
		}

		.dropleft .dropdown-toggle::after {
			display: inline-block;
			margin-left: .5em;
			vertical-align: middle;
			content: ""
		}

		.dropleft .dropdown-toggle::after {
			display: none
		}

		.dropleft .dropdown-toggle::before {
			display: inline-block;
			margin-right: .5em;
			vertical-align: middle;
			content: "";
			margin-top: -.21em;
			width: .42em;
			height: .42em;
			border: 1px solid;
			border-top: 0;
			border-right: 0;
			-webkit-transform: rotate(45deg);
			transform: rotate(45deg)
		}

		.dropleft .dropdown-toggle:empty::after {
			margin-left: 0
		}

		.dropleft .dropdown-toggle::before {
			vertical-align: 0
		}

		.dropdown-menu[x-placement^="top"],
		.dropdown-menu[x-placement^="right"],
		.dropdown-menu[x-placement^="bottom"],
		.dropdown-menu[x-placement^="left"] {
			right: auto;
			bottom: auto
		}

		.dropdown-divider {
			height: 0;
			margin: .5rem 0;
			overflow: hidden;
			border-top: 1px solid rgba(24, 28, 33, 0.05)
		}

		.dropdown-item {
			display: block;
			width: 100%;
			padding: .438rem 1.25rem;
			clear: both;
			font-weight: 400;
			color: #4E5155;
			text-align: inherit;
			white-space: nowrap;
			background-color: transparent;
			border: 0
		}

		.dropdown-item:hover,
		.dropdown-item:focus {
			color: #4E5155;
			text-decoration: none;
			background-color: rgba(24, 28, 33, 0.03)
		}

		.dropdown-item.active,
		.dropdown-item:active {
			color: #fff;
			text-decoration: none;
			background-color: #6610f2
		}

		.dropdown-item.disabled,
		.dropdown-item:disabled {
			color: #d1d2d3;
			pointer-events: none;
			background-color: transparent
		}

		.dropdown-menu.show {
			display: block
		}

		.dropdown-header {
			display: block;
			padding: .3125rem 1.25rem;
			margin-bottom: 0;
			font-size: .75rem;
			color: #a3a4a6;
			white-space: nowrap
		}

		.dropdown-item-text {
			display: block;
			padding: .438rem 1.25rem;
			color: #4E5155
		}

		.btn-group,
		.btn-group-vertical {
			position: relative;
			display: -ms-inline-flexbox;
			display: inline-flex;
			vertical-align: middle
		}

		.btn-group>.btn,
		.btn-group-vertical>.btn {
			position: relative;
			-ms-flex: 1 1 auto;
			flex: 1 1 auto
		}

		.btn-group>.btn:hover,
		.btn-group-vertical>.btn:hover {
			z-index: 1
		}

		.btn-group>.btn:focus,
		.btn-group>.btn:active,
		.btn-group>.btn.active,
		.btn-group-vertical>.btn:focus,
		.btn-group-vertical>.btn:active,
		.btn-group-vertical>.btn.active {
			z-index: 1
		}

		.btn-toolbar {
			display: -ms-flexbox;
			display: flex;
			-ms-flex-wrap: wrap;
			flex-wrap: wrap;
			-ms-flex-pack: start;
			justify-content: flex-start
		}

		.btn-toolbar .input-group {
			width: auto
		}

		.btn-group>.btn:not(:first-child),
		.btn-group>.btn-group:not(:first-child) {
			margin-left: -1px
		}

		.btn-group>.btn:not(:last-child):not(.dropdown-toggle),
		.btn-group>.btn-group:not(:last-child)>.btn {
			border-top-right-radius: 0;
			border-bottom-right-radius: 0
		}

		.btn-group>.btn:not(:first-child),
		.btn-group>.btn-group:not(:first-child)>.btn {
			border-top-left-radius: 0;
			border-bottom-left-radius: 0
		}

		.dropdown-toggle-split {
			padding-right: .84375rem;
			padding-left: .84375rem
		}

		.dropdown-toggle-split::after,
		.dropup .dropdown-toggle-split::after,
		.dropright .dropdown-toggle-split::after {
			margin-left: 0
		}

		.dropleft .dropdown-toggle-split::before {
			margin-right: 0
		}

		.btn-sm+.dropdown-toggle-split,
		.btn-group-sm>.btn+.dropdown-toggle-split {
			padding-right: .51562rem;
			padding-left: .51562rem
		}

		.btn-lg+.dropdown-toggle-split,
		.btn-group-lg>.btn+.dropdown-toggle-split {
			padding-right: 1.125rem;
			padding-left: 1.125rem
		}

		.btn-group-vertical {
			-ms-flex-direction: column;
			flex-direction: column;
			-ms-flex-align: start;
			align-items: flex-start;
			-ms-flex-pack: center;
			justify-content: center
		}

		.btn-group-vertical>.btn,
		.btn-group-vertical>.btn-group {
			width: 100%
		}

		.btn-group-vertical>.btn:not(:first-child),
		.btn-group-vertical>.btn-group:not(:first-child) {
			margin-top: -1px
		}

		.btn-group-vertical>.btn:not(:last-child):not(.dropdown-toggle),
		.btn-group-vertical>.btn-group:not(:last-child)>.btn {
			border-bottom-right-radius: 0;
			border-bottom-left-radius: 0
		}

		.btn-group-vertical>.btn:not(:first-child),
		.btn-group-vertical>.btn-group:not(:first-child)>.btn {
			border-top-left-radius: 0;
			border-top-right-radius: 0
		}

		.btn-group-toggle>.btn,
		.btn-group-toggle>.btn-group>.btn {
			margin-bottom: 0
		}

		.btn-group-toggle>.btn input[type="radio"],
		.btn-group-toggle>.btn input[type="checkbox"],
		.btn-group-toggle>.btn-group>.btn input[type="radio"],
		.btn-group-toggle>.btn-group>.btn input[type="checkbox"] {
			position: absolute;
			clip: rect(0, 0, 0, 0);
			pointer-events: none
		}

		.input-group {
			position: relative;
			display: -ms-flexbox;
			display: flex;
			-ms-flex-wrap: wrap;
			flex-wrap: wrap;
			-ms-flex-align: stretch;
			align-items: stretch;
			width: 100%
		}

		.card {
			position: relative;
			display: -ms-flexbox;
			display: flex;
			-ms-flex-direction: column;
			flex-direction: column;
			min-width: 0;
			word-wrap: break-word;
			background-color: #fff;
			background-clip: border-box;
			border: 1px solid rgba(24, 28, 33, 0.06);
			/* border-radius: .25rem */
			border-radius: 1rem
		}

		.card>hr {
			margin-right: 0;
			margin-left: 0
		}

		.card>.list-group:first-child .list-group-item:first-child {
			border-top-left-radius: .25rem;
			border-top-right-radius: .25rem
		}

		.card>.list-group:last-child .list-group-item:last-child {
			border-bottom-right-radius: .25rem;
			border-bottom-left-radius: .25rem
		}

		.card-body {
			-ms-flex: 1 1 auto;
			flex: 1 1 auto;
			padding: 1.5rem
		}

		.card-title {
			margin-bottom: .875rem
		}

		.card-subtitle {
			margin-top: -.4375rem;
			margin-bottom: 0
		}

		.card-text:last-child {
			margin-bottom: 0
		}

		.card-link:hover {
			text-decoration: none
		}

		.card-link+.card-link {
			margin-left: 1.5rem
		}

		.card-header {
			padding: .875rem 1.5rem;
			margin-bottom: 0;
			background-color: rgba(0, 0, 0, 0);
			border-bottom: 1px solid rgba(24, 28, 33, 0.06)
		}

		.card-header:first-child {
			border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0
		}

		.card-header+.list-group .list-group-item:first-child {
			border-top: 0
		}

		.card-footer {
			padding: .875rem 1.5rem;
			background-color: rgba(0, 0, 0, 0);
			border-top: 1px solid rgba(24, 28, 33, 0.06)
		}

		.card-footer:last-child {
			border-radius: 0 0 calc(.25rem - 1px) calc(.25rem - 1px)
		}

		.card-header-tabs {
			margin-right: -.75rem;
			margin-bottom: -.875rem;
			margin-left: -.75rem;
			border-bottom: 0
		}

		.card-header-pills {
			margin-right: -.75rem;
			margin-left: -.75rem
		}

		.card-img-overlay {
			position: absolute;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			padding: 1.5rem
		}

		.card-img {
			width: 100%;
			border-radius: calc(.25rem - 1px)
		}

		.card-img-top {
			width: 100%;
			border-top-left-radius: calc(.25rem - 1px);
			border-top-right-radius: calc(.25rem - 1px)
		}

		.card-img-bottom {
			width: 100%;
			border-bottom-right-radius: calc(.25rem - 1px);
			border-bottom-left-radius: calc(.25rem - 1px)
		}

		.card-deck {
			display: -ms-flexbox;
			display: flex;
			-ms-flex-direction: column;
			flex-direction: column
		}

		.card-deck .card {
			margin-bottom: .75rem
		}

		@media (min-width: 576px) {
			.card-deck {
				-ms-flex-flow: row wrap;
				flex-flow: row wrap;
				margin-right: -.75rem;
				margin-left: -.75rem
			}

			.card-deck .card {
				display: -ms-flexbox;
				display: flex;
				-ms-flex: 1 0 0%;
				flex: 1 0 0%;
				-ms-flex-direction: column;
				flex-direction: column;
				margin-right: .75rem;
				margin-bottom: 0;
				margin-left: .75rem
			}
		}

		.card-group {
			display: -ms-flexbox;
			display: flex;
			-ms-flex-direction: column;
			flex-direction: column
		}

		.card-group>.card {
			margin-bottom: .75rem
		}

		@media (min-width: 576px) {
			.card-group {
				-ms-flex-flow: row wrap;
				flex-flow: row wrap
			}

			.card-group>.card {
				-ms-flex: 1 0 0%;
				flex: 1 0 0%;
				margin-bottom: 0
			}

			.card-group>.card+.card {
				margin-left: 0;
				border-left: 0
			}

			.card-group>.card:not(:last-child) {
				border-top-right-radius: 0;
				border-bottom-right-radius: 0
			}

			.card-group>.card:not(:last-child) .card-img-top,
			.card-group>.card:not(:last-child) .card-header {
				border-top-right-radius: 0
			}

			.card-group>.card:not(:last-child) .card-img-bottom,
			.card-group>.card:not(:last-child) .card-footer {
				border-bottom-right-radius: 0
			}

			.card-group>.card:not(:first-child) {
				border-top-left-radius: 0;
				border-bottom-left-radius: 0
			}

			.card-group>.card:not(:first-child) .card-img-top,
			.card-group>.card:not(:first-child) .card-header {
				border-top-left-radius: 0
			}

			.card-group>.card:not(:first-child) .card-img-bottom,
			.card-group>.card:not(:first-child) .card-footer {
				border-bottom-left-radius: 0
			}
		}

		.card-columns .card {
			margin-bottom: .875rem
		}

		@media (min-width: 576px) {
			.card-columns {
				-webkit-column-count: 3;
				-moz-column-count: 3;
				column-count: 3;
				-webkit-column-gap: 1.5rem;
				-moz-column-gap: 1.5rem;
				column-gap: 1.5rem;
				orphans: 1;
				widows: 1
			}

			.card-columns .card {
				display: inline-block;
				width: 100%
			}
		}

		.badge {
			display: inline-block;
			padding: .25em .417em;
			font-size: .858em;
			font-weight: 500;
			line-height: 1;
			text-align: center;
			white-space: nowrap;
			vertical-align: baseline;
			border-radius: .125rem;
			transition: all 0.2s ease-in-out
		}

		@media (prefers-reduced-motion: reduce) {
			.badge {
				transition: none
			}
		}

		a.badge:hover,
		a.badge:focus {
			text-decoration: none
		}

		.badge:empty {
			display: none
		}

		.btn .badge {
			position: relative;
			top: -1px
		}

		.badge-pill {
			padding-right: .583em;
			padding-left: .583em;
			border-radius: 10rem
		}

		.jumbotron {
			padding: 3rem 1.5rem;
			margin-bottom: 3rem;
			background-color: rgba(24, 28, 33, 0.1);
			border-radius: .25rem
		}

		@media (min-width: 576px) {
			.jumbotron {
				padding: 6rem 3rem
			}
		}

		.jumbotron-fluid {
			padding-right: 0;
			padding-left: 0;
			border-radius: 0
		}

		.alert {
			position: relative;
			padding: 1rem 1rem;
			margin-bottom: 1rem;
			border: 1px solid transparent;
			border-radius: .25rem
		}

		.alert-heading {
			color: inherit
		}

		.alert-link {
			font-weight: 700
		}

		.alert-dismissible {
			padding-right: 3.341rem
		}

		.alert-dismissible .close {
			position: absolute;
			top: 0;
			right: 0;
			padding: 1rem 1rem;
			color: inherit
		}

		.float-left {
			float: left !important
		}

		.float-right {
			float: right !important
		}

		.float-none {
			float: none !important
		}

		@media (min-width: 576px) {
			.float-sm-left {
				float: left !important
			}

			.float-sm-right {
				float: right !important
			}

			.float-sm-none {
				float: none !important
			}
		}

		@media (min-width: 768px) {
			.float-md-left {
				float: left !important
			}

			.float-md-right {
				float: right !important
			}

			.float-md-none {
				float: none !important
			}
		}

		@media (min-width: 992px) {
			.float-lg-left {
				float: left !important
			}

			.float-lg-right {
				float: right !important
			}

			.float-lg-none {
				float: none !important
			}
		}

		@media (min-width: 1200px) {
			.float-xl-left {
				float: left !important
			}

			.float-xl-right {
				float: right !important
			}

			.float-xl-none {
				float: none !important
			}
		}

		.overflow-auto {
			overflow: auto !important
		}

		.overflow-hidden {
			overflow: hidden !important
		}

		.overflow-scroll {
			overflow: scroll !important
		}

		.overflow-visible {
			overflow: visible !important
		}

		.position-static {
			position: static !important
		}

		.position-relative {
			position: relative !important
		}

		.position-absolute {
			position: absolute !important
		}

		.position-fixed {
			position: fixed !important
		}

		.position-sticky {
			position: -webkit-sticky !important;
			position: sticky !important
		}

		.fixed-top {
			position: fixed;
			top: 0;
			right: 0;
			left: 0;
			z-index: 1030
		}

		.fixed-bottom {
			position: fixed;
			right: 0;
			bottom: 0;
			left: 0;
			z-index: 1030
		}

		@supports ((position: -webkit-sticky) or (position: sticky)) {
			.sticky-top {
				position: -webkit-sticky;
				position: sticky;
				top: 0;
				z-index: 1020
			}
		}

		.sr-only {
			position: absolute;
			width: 1px;
			height: 1px;
			padding: 0;
			overflow: hidden;
			clip: rect(0, 0, 0, 0);
			white-space: nowrap;
			border: 0
		}

		.sr-only-focusable:active,
		.sr-only-focusable:focus {
			position: static;
			width: auto;
			height: auto;
			overflow: visible;
			clip: auto;
			white-space: normal
		}

		.shadow-sm {
			box-shadow: 0 0.125rem 0.25rem rgba(24, 28, 33, 0.075) !important
		}

		.shadow {
			box-shadow: 0 0.5rem 1rem rgba(24, 28, 33, 0.15) !important
		}

		.shadow-lg {
			box-shadow: 0 1rem 3rem rgba(24, 28, 33, 0.175) !important
		}

		.shadow-none {
			box-shadow: none !important
		}

		.w-25 {
			width: 25% !important
		}

		.w-50 {
			width: 50% !important
		}

		.w-75 {
			width: 75% !important
		}

		.w-100 {
			width: 100% !important
		}

		.w-auto {
			width: auto !important
		}

		.h-25 {
			height: 25% !important
		}

		.h-50 {
			height: 50% !important
		}

		.h-75 {
			height: 75% !important
		}

		.h-100 {
			height: 100% !important
		}

		.h-auto {
			height: auto !important
		}

		.mw-100 {
			max-width: 100% !important
		}

		.mh-100 {
			max-height: 100% !important
		}

		.min-vw-100 {
			min-width: 100vw !important
		}

		.min-vh-100 {
			min-height: 100vh !important
		}

		.vw-100 {
			width: 100vw !important
		}

		.vh-100 {
			height: 100vh !important
		}

		.text-monospace {
			font-family: "SFMono-Regular", Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important
		}

		.text-justify {
			text-align: justify !important
		}

		.text-wrap {
			white-space: normal !important
		}

		.text-nowrap {
			white-space: nowrap !important
		}

		.text-truncate {
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap
		}

		.text-left {
			text-align: left !important
		}

		.text-right {
			text-align: right !important
		}

		.text-center {
			text-align: center !important
		}

		@media (min-width: 576px) {
			.text-sm-left {
				text-align: left !important
			}

			.text-sm-right {
				text-align: right !important
			}

			.text-sm-center {
				text-align: center !important
			}
		}

		@media (min-width: 768px) {
			.text-md-left {
				text-align: left !important
			}

			.text-md-right {
				text-align: right !important
			}

			.text-md-center {
				text-align: center !important
			}
		}

		@media (min-width: 992px) {
			.text-lg-left {
				text-align: left !important
			}

			.text-lg-right {
				text-align: right !important
			}

			.text-lg-center {
				text-align: center !important
			}
		}

		@media (min-width: 1200px) {
			.text-xl-left {
				text-align: left !important
			}

			.text-xl-right {
				text-align: right !important
			}

			.text-xl-center {
				text-align: center !important
			}
		}

		.text-lowercase {
			text-transform: lowercase !important
		}

		.text-uppercase {
			text-transform: uppercase !important
		}

		.text-capitalize {
			text-transform: capitalize !important
		}

		.font-weight-light {
			font-weight: 300 !important
		}

		.font-weight-lighter {
			font-weight: 100 !important
		}

		.font-weight-normal {
			font-weight: 400 !important
		}

		.font-weight-bold {
			font-weight: 700 !important
		}

		.font-weight-bolder {
			font-weight: 900 !important
		}

		.font-italic {
			font-style: italic !important
		}

		.text-white {
			color: #fff !important
		}

		.text-primary {
			color: #6610f2 !important
		}

		a.text-primary:hover,
		a.text-primary:focus {
			color: #4709ac !important
		}

		.text-secondary {
			color: #8897AA !important
		}

		a.text-secondary:hover,
		a.text-secondary:focus {
			color: #607186 !important
		}

		.text-success {
			color: #02BC77 !important
		}

		a.text-success:hover,
		a.text-success:focus {
			color: #017047 !important
		}

		.text-info {
			color: #28c3d7 !important
		}

		a.text-info:hover,
		a.text-info:focus {
			color: #1c8997 !important
		}

		.text-warning {
			color: #FFD950 !important
		}

		a.text-warning:hover,
		a.text-warning:focus {
			color: #ffc804 !important
		}

		.text-danger {
			color: #d9534f !important
		}

		a.text-danger:hover,
		a.text-danger:focus {
			color: #b52b27 !important
		}

		.text-light {
			color: rgba(24, 28, 33, 0.06) !important
		}

		a.text-light:hover,
		a.text-light:focus {
			color: rgba(0, 0, 0, 0.06) !important
		}

		.text-dark {
			color: rgba(24, 28, 33, 0.9) !important
		}

		a.text-dark:hover,
		a.text-dark:focus {
			color: rgba(0, 0, 0, 0.9) !important
		}

		.text-body {
			color: #4E5155 !important
		}

		.text-muted {
			color: #a3a4a6 !important
		}

		.text-black-50 {
			color: rgba(24, 28, 33, 0.5) !important
		}

		.text-white-50 {
			color: rgba(255, 255, 255, 0.5) !important
		}

		.text-hide {
			font: 0/0 a;
			color: transparent;
			text-shadow: none;
			background-color: transparent;
			border: 0
		}

		.text-decoration-none {
			text-decoration: none !important
		}

		.text-break {
			word-break: break-word !important;
			overflow-wrap: break-word !important
		}

		.text-reset {
			color: inherit !important
		}

		.visible {
			visibility: visible !important
		}

		.invisible {
			visibility: hidden !important
		}

		@media print {

			*,
			*::before,
			*::after {
				text-shadow: none !important;
				box-shadow: none !important
			}

			a:not(.btn) {
				text-decoration: underline
			}

			abbr[title]::after {
				content: " (" attr(title) ")"
			}

			pre {
				white-space: pre-wrap !important
			}

			pre,
			blockquote {
				border: 1px solid rgba(24, 28, 33, 0.4);
				page-break-inside: avoid
			}

			thead {
				display: table-header-group
			}

			tr,
			img {
				page-break-inside: avoid
			}

			p,
			h2,
			h3 {
				orphans: 3;
				widows: 3
			}

			h2,
			h3 {
				page-break-after: avoid
			}

			@page {
				size: a3
			}

			body {
				min-width: 992px !important
			}

			.container {
				min-width: 992px !important
			}

			.navbar {
				display: none
			}

			.badge {
				border: 1px solid #181C21
			}

			.table {
				border-collapse: collapse !important
			}

			.table td,
			.table th {
				background-color: #fff !important
			}

			.table-bordered th,
			.table-bordered td {
				border: 1px solid rgba(24, 28, 33, 0.2) !important
			}

			.table-dark {
				color: inherit
			}

			.table-dark th,
			.table-dark td,
			.table-dark thead th,
			.table-dark tbody+tbody {
				border-color: #e8e8e9
			}

			.table .thead-dark th {
				color: inherit;
				border-color: #e8e8e9
			}
		}
	</style>
</head>

<body>
	<div class="container-fluid">
		<div class="header">
			<img src="https://i.ibb.co/hK0nJxB/Transfer-Print.png" alt="Transfer Print" class="img-fluid" width="100%" draggable="false" />
		</div>
		<br />
		<div class="card mx-0">
			<div class="card-body">
				<table class="table table-sm table-borderless">
					<tr>
						<td colspan="2">
							<h4>Halo</h4>
						</td>
					</tr>
					<tr>
						<td>
							<label><?php echo $this->lang->line('ms_purchase_number'); ?></label><br />
							<strong><?= $record->pr_number; ?></strong>
						</td>
						<td>
							<label><?php echo $this->lang->line('xin_p_priority'); ?></label><br />
							<strong><?= priority_stats($record->priority_status); ?></strong>
						</td>

					</tr>
					<tr>
						<td>
							<label><?php echo $this->lang->line('ms_purchase_pic'); ?></label><br />
							<strong><?= $record->added_by; ?></strong>
						</td>
						<td>
							<label><?php echo $this->lang->line('xin_department_name'); ?></label><br />
							<strong><?= $record->department; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<label><?php echo $this->lang->line('ms_purchase_issue_date');
									?></label><br />
							<strong><?= $this->Xin_model->set_date_format($record->issue_date);
									?></strong>
						</td>
						<td>
							<label><?php echo $this->lang->line('ms_purchase_due_approval_date');
									?></label><br />
							<strong><?= $this->Xin_model->set_date_format($record->due_approval_date);
									?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label><?php echo $this->lang->line('ms_purpose'); ?></label><br />
							<strong><?= $record->purpose; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label><?php echo $this->lang->line('ms_purchase_ref_delivery_name');
									?></label><br />
							<strong><?= $record->ref_expedition_name ?? "--"; ?></strong>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<br>
		<div class="card mx-0">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<table class="table table-striped table" id="ms_table_items">
							<thead>
								<tr>
									<th><?php echo $this->lang->line('xin_id_no'); ?></th>
									<th><?php echo $this->lang->line('xin_title_item'); ?></th>
									<th><?php echo $this->lang->line('xin_project'); ?></th>
									<th>
										<?php echo $this->lang->line('ms_ref_title_unit_price'); ?>
									</th>
									<th><?php echo $this->lang->line('xin_title_qty'); ?></th>
									<th style="min-width: 150px">
										<?php echo $this->lang->line('xin_title_sub_total'); ?>
									</th>
								</tr>
							</thead>
							<tbody id="formRow">
								<?php foreach ($records as $k => $r) { ?>
									<tr>
										<td><?= $k += 1; ?></td>
										<td><?= $r[0]; ?></td>
										<td><?= $r[1]; ?></td>
										<td><?= $r[2]; ?></td>
										<td><?= $r[3]; ?></td>
										<td><?= $r[4]; ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
					<div class="col-md-12">
						<br><br>
					</div>
					<div class="col-md-8 col-sm-8">
						<div class="form-group">
							<label for=""><?php echo $this->lang->line('ms_notes'); ?></label>
							<div class="purporse">
								<?= $record->notes; ?>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-4">
						<table class="table table-sm table-borderless">
							<tr>
								<th><?= $this->lang->line('ms_delivery_fee'); ?></th>
								<td><strong><?= $this->Xin_model->currency_sign($record->ref_delivery_fee); ?></strong></td>
							</tr>
							<tr>
								<th><?= $this->lang->line('xin_title_total'); ?></th>
								<td><strong class="text-danger"><?= $this->Xin_model->currency_sign($record->amount + $record->ref_delivery_fee); ?></strong></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		window.print();
	</script>
</body>


</html>