const gulp         = require('gulp'),
      sass         = require('gulp-sass'),
      autoprefixer = require('gulp-autoprefixer'),
      browserSync  = require('browser-sync'),
      cssnano      = require('gulp-cssnano'),
      rename       = require('gulp-rename'),
      imagemin     = require('gulp-imagemin'),
      pngquant     = require('imagemin-pngquant'),
      imgCompress  = require('imagemin-jpeg-recompress'),
      del          = require('del');
      // concat = require ('gulp-concat') js
      // uglify = require ('gulp-uglifyjs') js

gulp.task('sass', function() {
    return gulp.src('web/css/sass/**/*.scss')
        .pipe(sass())
        .pipe( autoprefixer(['last 15 versions', '> 1%', 'ie 8', 'ie 7'], {cascade: true}) )
        .pipe(gulp.dest('web/css'))
        .pipe(cssnano())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('web/css'))
        .pipe(browserSync.stream());
});

gulp.task('img', function() {
    return gulp.src('imgShortTemp/**/*')
        .pipe(imagemin([
            imgCompress({
                loops: 4,
                min: 70,
                max: 80,
                quality: 'high'
            }),
            pngquant({
                strip: true,
                quality: [0.3, 0.4]
            }),
            imagemin.gifsicle({interlaced: true}),
            imagemin.jpegtran({progressive: true}),
            imagemin.optipng({optimizationLevel: 5}),
            imagemin.svgo({
                plugins: [
                    {removeViewBox: true},
                    {cleanupIDs: false}
                ]
            })
        ]))
        .pipe(gulp.dest('web/css/images'));
});

// Чиcтим папку с картинками
gulp.task('clean', function(done) {
    return del('imgShortTemp/**/*');
});

gulp.task('server', gulp.series('sass', 'img', 'clean', function() {
    browserSync.init({
        proxy: 'http://sub.sdamex.loc/',
        host: 'sub.sdamex.loc',
        open: 'external',
        notify: false
    });

    gulp.watch('web/css/sass/**/*.scss', gulp.series('sass'));
    gulp.watch('web/scripts/**/*.js', browserSync.reload);
    gulp.watch([
        'views/**/*.php',
        'modules/**/views/**/*.php',
        'widgets/**/*.php',
    ]).on("change", browserSync.reload);
}));
