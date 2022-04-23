import gulp from 'gulp'
import del from 'del'
import dartSass from 'sass'
import gulpSass from 'gulp-sass'
import cleanCSS from 'gulp-clean-css'
import rename from 'gulp-rename'
import babel from 'gulp-babel'
import terser from 'gulp-terser'
import concat from 'gulp-concat'
import sourcemaps from 'gulp-sourcemaps'
import autoprefixer from 'gulp-autoprefixer'
import imagemin from 'gulp-imagemin'
import webp from 'gulp-webp'
import htmlmin from 'gulp-htmlmin'
import webphtml from 'gulp-webp-html'
import fileinclude from 'gulp-file-include'
import newer from 'gulp-newer'
import browsersync from 'browser-sync'

const prj_folder = 'oxy-project',
	src_folder = 'src'

const { src, dest, watch, series, parallel } = gulp,
	sass = gulpSass(dartSass),
	{ init, write } = sourcemaps

const path = {
	src: {
		html: [src_folder + '/*.html', '!' + src_folder + '/_*.html'],
		css: src_folder + '/sass/style.sass',
		js: src_folder + '/js/index.js',
		img: src_folder + '/img/**/*',
	},
	watch: {
		html: src_folder + '/**/*.html',
		css: [src_folder + '/sass/**/*.sass', src_folder + '/sass/**/*.scss'],
		js: src_folder + '/js/**/*.js',
		img: src_folder + '/img/**/*',
	},
	build: {
		html: prj_folder + '/',
		css: prj_folder + '/css/',
		js: prj_folder + '/js/',
		img: prj_folder + '/img/',
	},
	clean: './' + prj_folder + '/',
}

//clean task
function cleanTask() {
	return del(path.clean)
}

//browsersync task
function browsersyncTask() {
	browsersync.init({
		proxy: {
			target: 'http://localhost/Oxy-Project/oxy-project/',
		},
		tunnel: true,
		notify: false,
		online: true,
	})
}

//html task
function htmlTask() {
	return src(path.src.html)
		.pipe(fileinclude())
		.pipe(webphtml())
		.pipe(htmlmin({ collapseWhitespace: true }))
		.pipe(dest(path.build.html))
		.pipe(browsersync.stream())
}

//style task
function styleTask() {
	return src(path.src.css)
		.pipe(init())
		.pipe(sass().on('error', sass.logError))
		.pipe(
			autoprefixer({
				cascade: false,
			})
		)
		.pipe(
			cleanCSS({
				level: 2,
			})
		)
		.pipe(
			rename({
				basename: 'style',
				suffix: '.min',
			})
		)
		.pipe(write('.'))
		.pipe(dest(path.build.css))
		.pipe(browsersync.stream())
}

//js task
function jsTask() {
	return src(path.src.js)
		.pipe(init())
		.pipe(
			babel({
				presets: ['@babel/preset-env'],
			})
		)
		.pipe(terser())
		.pipe(concat('main.min.js'))
		.pipe(write('.'))
		.pipe(dest(path.build.js))
		.pipe(browsersync.stream())
}

//images task
function imagesTask() {
	return src(path.src.img)
		.pipe(newer(path.build.img))
		.pipe(
			webp({
				quality: 80,
			})
		)
		.pipe(dest(path.build.img))
		.pipe(src(path.src.img))
		.pipe(
			imagemin({
				progressive: true,
				svgoPlugins: [{ removeViewBox: false }],
				interlaced: true,
				optimizationLevel: 3,
			})
		)
		.pipe(dest(path.build.img))
}

//watch task
function watchTask() {
	watch(path.build.html).on('change', browsersync.reload)
	watch(path.watch.html, htmlTask)
	watch(path.watch.css, styleTask)
	watch(path.watch.js, jsTask)
	watch(path.watch.img, imagesTask)
}

//build
const _build = series(cleanTask, parallel(htmlTask, styleTask, jsTask, imagesTask))

//watch
const _watch = parallel(_build, watchTask, browsersyncTask)

//gulp tasks
export const _clean = cleanTask
export const _html = htmlTask
export const _style = styleTask
export const _js = jsTask
export const _img = imagesTask
export const __build = _build
export const __watch = _watch

//default gulp task
const _default = _watch
export { _default as default }
