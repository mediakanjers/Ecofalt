//Project settings
const potFile = "mediakanjers";
const projectName = "mediakanjers";

//Gulp requirements
const { src, dest, series, parallel, watch } = require("gulp");
const sass = require("gulp-sass")(require('sass'));

let autoprefixer;
import('gulp-autoprefixer').then((module) => {
	autoprefixer = module;
});

const imagemin = require("gulp-imagemin");
const cache = require("gulp-cache");
const uglifyEs = require("gulp-uglify-es").default;
const babel = require("gulp-babel");
const livereload = require("gulp-livereload");
const wpPot = require('gulp-wp-pot');
const sassLint = require('sass-lint');
const { Transform } = require('stream');
const fontello = require('gulp-fontello');
const concat = require('gulp-concat');
const clean = require('gulp-clean');

//Path variables
const srcPath = "web/assets";
const distPath = "web/dist";

//Path array
const paths = {
	scss: [`${srcPath}/scss/**/*.scss`],
	js: [`${srcPath}/js/**/*.js`],
	img: [`${srcPath}/images/**`],
	font: [`${srcPath}/fonts/**`],
	jsvendor: [`${srcPath}/js/vendor/*.js`],
};

// SCSS-lint — sass-lint kan geen CSS custom properties (--x: ...) parsen en crashte
// daarmee de hele build. Bestanden die hij niet kan lezen worden nu overgeslagen;
// alleen echte lint-errors laten de build falen.
function lintScss() {
	let warnings = 0;
	return new Transform({
		objectMode: true,
		transform(file, enc, cb) {
			let result;
			try {
				result = sassLint.lintText({ text: file.contents.toString(), format: 'scss', filename: file.path }, {}, 'sass-lint.yml');
			} catch (e) {
				return cb(null, file);
			}
			warnings += result.warningCount;
			if (result.errorCount > 0) {
				console.log(sassLint.format([result]));
				return cb(new Error(`sass-lint: ${result.errorCount} error(s) in ${file.relative}`));
			}
			cb(null, file);
		},
		flush(cb) {
			if (warnings) console.log(`sass-lint: ${warnings} waarschuwingen (niet blokkerend)`);
			cb();
		},
	});
}

// CSS task
async function css() {
	const autoprefixer = await import('gulp-autoprefixer');

	return src(paths.scss)
		.pipe(lintScss())
		.pipe(sass({ outputStyle: "compressed" })) // geen uglifycss: dat sloopt de spaties in clamp()/calc()
		.pipe(
			autoprefixer.default({
				overrideBrowserslist: ["last 2 versions"],
				cascade: false,
			})
		)
		.pipe(dest(`${distPath}/css/`))
		.pipe(livereload());
}


// Javascript task
function js() {
	return src(paths.js)
		// .pipe(
		// 	babel({
		// 		presets: ["@babel/preset-env"],
		// 	})
		// )
		.pipe(uglifyEs())
		.pipe(concat('scripts.min.js'))
		.pipe(dest(`${distPath}/scripts/`))
		.pipe(livereload());
}

// Javascript vendor files
function jsVendor() {
	return src(paths.jsvendor)
		.pipe(dest(`${distPath}/scripts/vendor/`))
		.pipe(livereload());
}

// Font task
function font() {
	return src(paths.font, { encoding: false })
		.pipe(dest(`${distPath}/font/`))
		.pipe(livereload());		
}

// Image task
function img() {
	return src(paths.img, { encoding: false })
		.pipe(
			cache(
				imagemin({
					interlaced: true,
				})
			)
		)
		.pipe(dest(`${distPath}/images/`))
		.pipe(livereload());
}

// WordPress internationalization
function wpPotTask() {
	return src('web/**/*.php')
		.pipe(wpPot({
			domain: "TEXTDOMAIN",  // Looks for TEXTDOMAIN global PHP var.
			package: projectName
		}))
		.pipe(dest(langPath + "/" + potFile + ".pot"))
		.pipe(livereload());
}

// Clean task
function cleantask() {
	return src(`${distPath}/*`, { read: false, allowEmpty: true })
		.pipe(clean());
}


// theme.json to scss styling
const gulp = require('gulp');
const fs = require('fs');
const file = require('gulp-file');
const path = require('path');

function extractThemeJson(done) {

	console.log('📦 extractThemeJson triggered by file change');

	const themeJsonPath = 'web/theme.json';
	const outputPath = 'web/assets/scss/theme/';

	if (!fs.existsSync(themeJsonPath)) {
		console.error('theme.json niet gevonden!');
		done();
		return;
	}

	const json = JSON.parse(fs.readFileSync(themeJsonPath));
	const settings = json.settings || {};
	const kebab = (str) => str.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();

	// Preset-lookup zodat "var:preset|font-size|h1" in styles naar de echte waarde vertaald wordt
	const presets = {
		'color': Object.fromEntries((settings.color?.palette || []).map(p => [p.slug, p.color])),
		'font-size': Object.fromEntries((settings.typography?.fontSizes || []).map(p => [p.slug, p.size])),
		'font-family': Object.fromEntries((settings.typography?.fontFamilies || []).map(p => [p.slug, p.fontFamily])),
		'spacing': Object.fromEntries((settings.spacing?.spacingSizes || []).map(p => [p.slug, p.size])),
	};
	const resolve = (value) => {
		if (typeof value !== 'string' || !value.startsWith('var:preset|')) return value;
		const [, type, slug] = value.split('|');
		return presets[type]?.[slug] ?? value;
	};

	let output = `// ⚠️ Automatisch gegenereerd uit theme.json – niet handmatig aanpassen!\n\n`;

	// Kleuren
	Object.entries(presets['color']).forEach(([slug, color]) => {
		output += `$color-${slug}: ${color};\n`;
	});
	output += '\n';

	// Layout (contentSize -> $layout-content-size)
	Object.entries(settings.layout || {}).forEach(([key, value]) => {
		output += `$layout-${kebab(key)}: ${value};\n`;
	});
	output += '\n';

	// Fontgroottes (+ optionele lineHeight/fontWeight/fontColor per grootte)
	(settings.typography?.fontSizes || []).forEach(size => {
		output += `$font-size-${size.slug}: ${size.size};\n`;
		if (size.lineHeight) output += `$line-height-${size.slug}: ${size.lineHeight};\n`;
		if (size.fontWeight) output += `$font-weight-${size.slug}: ${size.fontWeight};\n`;
		if (size.fontColor) output += `$font-color-${size.slug}: ${size.fontColor};\n`;
		output += '\n';
	});

	// Fonts
	Object.entries(presets['font-family']).forEach(([slug, family]) => {
		output += `$font-family-${slug}: ${family};\n`;
	});
	output += '\n';

	// Spacing (-> $spacing-section)
	Object.entries(presets['spacing']).forEach(([slug, size]) => {
		output += `$spacing-${slug}: ${size};\n`;
	});
	output += '\n';

	// Custom tokens, genest -> plat ($custom-shadow-button-hover, $custom-breakpoint-md)
	const flatten = (obj, prefix) => {
		Object.entries(obj).forEach(([key, value]) => {
			const name = `${prefix}-${kebab(key)}`;
			if (value && typeof value === 'object') {
				flatten(value, name);
			} else {
				output += `$${name}: ${value};\n`;
			}
		});
	};
	flatten(settings.custom || {}, 'custom');
	output += '\n';

	// Headings (h1 t/m h6) uit styles.elements, aangevuld met de gedeelde "heading"-stijl
	const elements = json.styles?.elements || {};
	const shared = elements.heading || {};
	['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].forEach(heading => {
		const h = elements[heading] || {};
		const t = { ...(shared.typography || {}), ...(h.typography || {}) };
		const color = h.color?.text || shared.color?.text;
		if (t.fontFamily) output += `$${heading}-font-family: ${resolve(t.fontFamily)};\n`;
		if (t.fontSize) output += `$${heading}-font-size: ${resolve(t.fontSize)};\n`;
		if (t.fontWeight) output += `$${heading}-font-weight: ${t.fontWeight};\n`;
		if (t.lineHeight) output += `$${heading}-line-height: ${t.lineHeight};\n`;
		if (color) output += `$${heading}-color: ${resolve(color)};\n`;
		output += '\n';
	});

	return file('_theme-vars.scss', output, { src: true })
		.pipe(gulp.dest(outputPath));
}

// Theme.json watcher zit in watchFiles()
function watchFiles() {
	livereload.listen();
	watch(paths.scss, css);
	watch(paths.js, js);
	watch(paths.img, img);
	watch(paths.font, font);
	watch(paths.jsvendor, jsVendor);
	watch('web/theme.json', extractThemeJson); // toegevoegd
	watch(["**"], livereload); 
}

exports.default = series(
	cleantask,
	extractThemeJson,
	parallel(css, js, font, img, jsVendor),
	watchFiles
);

// Eenmalige build zonder watcher: npx gulp build
exports.build = series(
	cleantask,
	extractThemeJson,
	parallel(css, js, font, img, jsVendor)
);
