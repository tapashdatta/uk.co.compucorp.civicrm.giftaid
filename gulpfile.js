var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var autoprefixer = require('gulp-autoprefixer');
var notify = require('gulp-notify');

/**
 * Default task
 */
gulp.task('default', ['css', 'js', 'watch']);

/**
 * Watch
 */
gulp.task('watch', function () {
  gulp.watch('resources/**/*.scss', ['css']);
  gulp.watch('resources/**/*.js', ['js']);
});

/**
 * CSS tasks
 */
gulp.task('css', function () {
  return gulp.src('resources/**/*.scss')
    .pipe(sass({outputStyle: 'expanded'}).on('error', sass.logError))
    .pipe(concat('dist.css'))
    .pipe(autoprefixer())
    .pipe(gulp.dest('resources/css'))
    .pipe(notify({message: 'CSS tasks complete.'}));
});

/**
 * JavaScript tasks
 */
gulp.task('js', function () {
  // placeholder for future
});