var gulp = require('gulp'),
    sass = require('gulp-sass'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    sourcemaps = require('gulp-sourcemaps'),
    merge = require('merge-stream'),


    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    rename = require('gulp-rename'),

    imagemin = require('gulp-imagemin'),
    pngquant = require('imagemin-pngquant');

var vendor_js = [
    './resources/assets/js/vendor/jquery-2.2.2.min.js',
    './resources/assets/js/vendor/foundation.min.js',
    './resources/assets/js/datatable/jquery.dataTables.min.js',
    './resources/assets/js/vendor/responsive-tables.js',
    './resources/assets/js/vendor/moment.min.js',
    './resources/assets/js/vendor/number-format.js',
    './resources/assets/js/vendor/classie.js',
    './resources/assets/js/vendor/selectFx.js',
    './resources/assets/js/vendor/jquery.json-viewer.js'
];

var vendor_css = [
    './resources/assets/css/vendor/foundation.min.css',
    './resources/assets/css/vendor/jquery.dataTables.min.css',
    './resources/assets/css/vendor/responsive-tables.css',
    './resources/assets/css/vendor/cs-skin-elastic.css',
    './resources/assets/css/vendor/cs-select.css',
    './resources/assets/css/vendor/jquery.json-viewer.css',
];

var custom_js = [
    './resources/assets/js/custom/api.js',
    './resources/assets/js/custom/ui.js'
];

var custom_chartJs = [
    './resources/assets/js/charts/custom/lineChart-homepage.js',
    './resources/assets/js/charts/custom/lineChart-rest.js',
    './resources/assets/js/charts/custom/horizontal-barChart.js',
    './resources/assets/js/charts/custom/barChart-contract.js',
    './resources/assets/js/charts/custom/lineChart-header.js',
];

var vendor_chartJs = [
    './resources/assets/js/charts/vendor/d3.min.js',
];

gulp.task('compress-vendorJs', function () {
    return gulp.src(vendor_js)
        .pipe(concat('vendors.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js'))
        .pipe(notify({message: 'Vendor javascript files successfully compressed.'}));
});

gulp.task('compress-vendorChartJs', function () {
    return gulp.src(vendor_chartJs)
        .pipe(concat('vendorChart.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js'))
        .pipe(notify({message: 'Vendor chartJS successfully compressed.'}));
});

gulp.task('compress-vendorCSS', function () {
    return gulp.src(vendor_css)
        .pipe(concat('vendors.css'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglifycss())
        .pipe(gulp.dest('./public/css'))
        .pipe(notify({message: 'Vendor css files successfully compressed.'}));
});

/**
 * Compile files from  css folder
 */
gulp.task('sass', function () {

    var frontend = gulp.src('./resources/assets/css/custom/app.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(postcss([autoprefixer({browsers: ['last 30 versions', '> 1%', 'ie 8', 'ie 7']})]))
        .pipe(sourcemaps.write('./maps'))
        .pipe(uglifycss({
            "max-line-len": 80
        }))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./public/css/'));
});

gulp.task('compress-img', function() {
    return gulp.src('./resources/assets/images/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            //use: [pngquant()]
        }))
        .pipe(gulp.dest('./public/images'));
});

gulp.task('js', function () {
    return gulp.src(custom_js)
        .pipe(concat('app.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js'))
        .pipe(notify({message: 'Custom javascripts successfully compressed.'}));
});

gulp.task('chartJs', function () {
    return gulp.src(custom_chartJs)
        .pipe(concat('customChart.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js'))
        .pipe(notify({message: 'Custom charts successfully compressed.'}));
});

/*
 * Watch scss files for changes & recompile
 */
gulp.task('watch-sass', function () {
    gulp.watch('./resources/assets/css/**/*.scss', ['sass']);
});

/*
 * Watch javascript files for changes & recompile
 */
gulp.task('watch-js', function () {
    gulp.watch('./resources/assets/js/**/*.js', ['js']);
    gulp.watch('./resources/assets/js/**/**/*.js', ['chartJs']);
});

gulp.task('watch-chartJs', function () {
    gulp.watch('./resources/assets/js/**/**/*.js', ['chartJs']);
});
/*
 * Default task, running just `gulp` will compile the sass,
 */
gulp.task('default', ['sass', 'compress-img', 'js', 'watch-sass', 'watch-js', 'watch-chartJs']);
