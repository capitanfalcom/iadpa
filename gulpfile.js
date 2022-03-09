const gulp = require('gulp');

function deploy() {
    const path = 'D:\\wamp64\\www\\iadpa';
    console.log("Configurando: " + path);
    return gulp.src('public_html/**', { dot: true })
        .pipe(gulp.dest(path));
}


function watch(done) {
    gulp.watch('public_html/**', deploy);
    done();
}

exports.default = gulp.series(deploy, watch);